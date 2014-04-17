<?php
class TempController extends Controller{

	/**
     * Liste les abscences rentrées dans le TDS - fonction AJAX.
     * @param date $_POST['date'] date sélectionnée
     * @param array(int) $_POST['id'] id des congés de la journée sélectionnée
     * @param string $_POST['com'] commentaire ajouté à la demande de congé.
     * @return HTMLCode ligne <li> avec le détail de l'abscence
     */
    public function ajax_calculConge(){
        // $this->layouts=array('ajax');
        $datas =array();
        $ligne='';
        $test_type='';
        $type_cp = 0;
        $date = $_POST['date'];               // date du jour modifié
        $type_am = $_POST['type'][0];         // type de congé demandé pour le matin (0 si pas de changement)
        $type_pm = $_POST['type'][1];         // type de congé demandé pour l'aprem
        $classe_am = $_POST['classe']['0'];   // classe du matin de la case cliquée
        $classe_pm = $_POST['classe']['1'];   // classe de l'aprem de la case cliquée
        $id_am = $_POST['id']['0'];           // id du congé du matin déjà présent dans la case cliquée
        $id_pm = $_POST['id']['1'];           // id du congé de l'aprem
        if($_POST['tooltip']!= ''){           // Récupération de l'infobulle existante.
          $old_tooltip = substr($_POST['tooltip'], 6,-7); //enlève la balise <span> de part et d'autre
          $infos = explode('<p>', $old_tooltip);
          array_shift($infos);
        }else{
          $infos[0]='';
        }
        $commentaire = $_POST['com'];          // Commentaire posté lors de la demande.

        if($type_am != '0' && $type_pm != '0'){       //Journée complète
          $demi = array('am','pm');
          $test_type = $type_am;
          if($id_am == $id_pm){
            $id = $id_am;
          }else{
            $id = array($id_am,$id_pm);
          }
          $nb_jours = 1;
          $flag = 'full';
        }
        else{
          if ($type_am == '0') {                      //Après-midi
            $demi = array('pm','pm');
            $type_am = 'taf';
            $test_type = $type_pm;
            $id = $id_pm;
            $nb_jours = 0.5;
            $flag = 'pm';
          }
          else{                                       //Matin
            $demi = array('am','am');
            $type_pm = 'taf';
            $test_type = $type_am;
            $nb_jours = 0.5;
            $id = $id_am;
            $flag='am'; 
          }
        }
        $type_abs = $this->session->read('type_abs');
        if ($test_type == 'taf') {
          $type_cp=0;
          $abs = new Absence($this->session->read('login'),$date,$demi[0],$date,$demi[1],$type_cp,$nb_jours,$commentaire,'ok',getCurrentDay(),$id);
          $abs = $this->checkAside($abs);
          $this->addTmpConge($abs);
          if($flag =="full"){
            $tooltip = '';
          }
          elseif ($flag == "am") {
            count($infos)>1 ? $tooltip = '<span><p>'.$infos[1].'</span>' : $tooltip = '';
          }else{
            count($infos)>1 ? $tooltip = '<span><p>'.$infos[0].'</span>' : $tooltip = '';
           }  
        }
        else{        
          foreach ($type_abs as $k => $v) {
            if($v->ta_short_libelle == strtoupper($test_type)){
              $type_line = $v->ta_libelle;
              $type_cp = $v->ta_id;
            }
          }
          if(in_array($type_cp, $_SESSION['isConge'])){
            $p_etat = 'demande';
          }else{
            $p_etat = 'ok';
          }
          $abs = new Absence($this->session->read('login'),$date,$demi[0],$date,$demi[1],$type_cp,$nb_jours,$commentaire,$p_etat,getCurrentDay(),$id);
          $abs = $this->checkAside($abs);
          $this->addTmpConge($abs);  
          if($flag == 'full'){
            $tooltip = '<span>'.setTooltip($abs).'</span>';
          }
          else{
            if($abs->collision && count($infos)>1){
              if($flag=="am"){
                $tooltip = '<span>'.setTooltip($abs).'<p>'.$infos[1].'</span>';
              }
              else{
                $tooltip = '<span><p>'.$infos[0].setTooltip($abs).'</span>';
              }
            }
            elseif ($abs->collision && $infos[0]==''){
              $tooltip = '<span>'.setTooltip($abs).'</span>';
            }
            else{
             if($flag=="am"){
                $tooltip = '<span>'.setTooltip($abs).'<p>'.$infos[0].'</span>';
              }
              else{
                $tooltip = '<span><p>'.$infos[0].setTooltip($abs).'</span>';
              } 
            }
          }
        }
        $datas['tooltip'] = $tooltip;
        $datas['listing']= createListe($_POST['timestamp']);
        // $datas['listing']= $this->createListe($_POST['timestamp']);
        $this->set('data',$datas);
    }



    /** 
     * Créer l'info bulle correspondante à un congé.
     * @param object $conge Congé  
     * @return html infobulle construite
     */
    public function setTooltip($conge){
        $infobulle='<p>';
        if($conge->p_type!=0){
					$type_abs=$this->session->read('type_abs');
          $infobulle .= '<b>'.ucwords($type_abs[$conge->p_type]->ta_libelle).' - '.$conge->p_nb_jours.' jrs</b>';
        }
        if($conge->p_commentaire != ''){
          $infobulle .= '<br>'.hsc($conge->p_commentaire);
        }
        $infobulle .= '<br>Du '.$conge->p_date_deb.' '.$conge->p_demi_jour_deb;
        $infobulle .= '<br>Au '.$conge->p_date_fin.' '.$conge->p_demi_jour_fin;
        $infobulle .= '</p>';
        return $infobulle;
    }

    /**
     * Ajoute un congé temporaire dans le tableau de session
     * @param AbsenceObject $conge conge à ajouter
     */
    public function addTmpConge($conge){
      $tempConges = $this->session->read('tmpconges');
      array_push($_SESSION['tmpconges'],$conge);
      foreach ($tempConges as $k => $v) {
        if ($conge->collide($v) || $conge->isOverwriting($v)){
          unset($_SESSION['tmpconges'][$k]);
          if($conge->p_type=="0"){
            $this->removeFromSession($conge);
          }
        }
      }
    }

    /**
     * Suppression d'un congé temporaire
     * @param string $debut date de début du congé
     * @param string $demi_deb demi_journée de début 
     * @param string $fin date de fin
     * @param string $demi_fin demi_journée de fin
     * @param string $type id type du congé
     * @param int $timestamp timestamp de la semaine en cours d'affichage
     */
    public function delTmpConge($debut,$demi_deb,$fin,$demi_fin,$type,$timestamp){
      $tmp_conge = new Absence($this->session->read('login'),$debut,$demi_deb,$fin,$demi_fin,$type,"0","","ok","","");
      $this->removeFromSession($tmp_conge);
      $this->redirect('/tds/edittds/'.$timestamp);
    }

    /**
     * Enlève un congé temporaire du tableau de session
     * @param object $conge Congé a enlever
     */
    public function removeFromSession($conge){
      $table = $this->session->read('tmpconges');
      foreach ($table as $k => $v) {
          if($v->p_date_deb==$conge->p_date_deb && 
             $v->p_date_fin==$conge->p_date_fin &&
             $v->p_demi_jour_deb==$conge->p_demi_jour_deb &&
             $v->p_demi_jour_fin==$conge->p_demi_jour_fin &&
             $v->p_type==$conge->p_type){
            unset($_SESSION['tmpconges'][$k]);
          }
      }
    }

    public function checkAside($abs){
       $table = $this->session->read('tmpconges');
        if($abs->isFullDay()){
          $lconge = $this->getAbsAside($abs,-1,'pm',$table);
          $rconge = $this->getAbsAside($abs,1,'am',$table);
        }
        elseif($abs->isMorning()){
          $lconge = $this->getAbsAside($abs,-1,'pm',$table);
          $rconge = $this->getAbsAside($abs,0,'pm',$table);
        }
        else{
          $lconge = $this->getAbsAside($abs,0,'am',$table);
          $rconge = $this->getAbsAside($abs,1,'am',$table);
        }
      if($lconge != null){
        $abs = $abs->merge($lconge,'left');
        $this->removeFromSession($lconge);
      }
      if($rconge != null){
        $abs = $abs->merge($rconge,'right');
        $this->removeFromSession($rconge);
      }
      return $abs;
    }

    /**
     * Retourne la date correspondant à la date donnée - le delta.
     * @param string $date date de référence
     * @param int $delta delta
     * @return string date de réference +- delta.
     */
    public function getAbsAside($abs,$delta,$demi,$table){
        if($delta!=0){
          $date = getSideDate($abs->p_date_deb,$delta);
        }else{
          $date = $abs->p_date_deb;
        }
        $newconge = null;
        foreach ($table as $k => $v) {
          if($delta>0){
            if(($v->p_type==$abs->p_type) && ($v->p_date_deb == $date) && ($v->p_demi_jour_deb==$demi)){
              $newconge = $v;
            } 
          }
          elseif($delta<0){
            if(($v->p_type==$abs->p_type) && ($v->p_date_fin == $date) && ($v->p_demi_jour_fin==$demi)){
              $newconge = $v;
            }
          }
          elseif($delta==0 && $demi=="pm"){
            if(($v->p_type==$abs->p_type) && ($v->p_date_fin == $date) && ($v->p_demi_jour_deb==$demi)){
              $newconge = $v;
            }
          }
          else{
           if(($v->p_type==$abs->p_type) && ($v->p_date_fin == $date) && ($v->p_demi_jour_fin==$demi)){
              $newconge = $v;
            } 
          }
        }
        return $newconge;
    }

}
