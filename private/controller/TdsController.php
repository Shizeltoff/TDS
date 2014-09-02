<?php
class TdsController extends Controller{

    
    /**
     * Vue de la page tds/index.php
     */    
    public function index(){}
    
    
    /**
     * Vue de la page tds/newAbsence.php
     */
    public function newAbsence(){
      $this->mail->configure();
      $this->set('layoutTitle','Définir un congé de plusieurs jours');
      $this->set('type_abs',$this->session->read('type_abs'));
      if($this->request->data){
        $debut = $this->request->data->deb;
        $fin = $this->request->data->fin;
        $demi_deb = $this->request->data->demi_deb;
        $demi_fin = $this->request->data->demi_fin;
        $commentaire = $this->request->data->com;

        // debug($this->request->data->joff);die();
        if ($this->request->data->joff != 0){
          $joff = $this->request->data->joff;
          $commentaire .="+Joff";
        }
        else{
          $joff="false";
        }
        // Vérification de la cohérence des dates : début < fin.
        $interval = calcJours($debut,$demi_deb,$fin,$demi_fin,$joff,$this->cache->read('ferie'));
        if($interval<0){
           $msg= 'Incohérence des dates! Veuillez vérifier';
           $this->session->setFlash($msg,'error');
        }
        else{
          // Vérification de l'existence d'un congé sur la même période.
          if(!$this->verifConge($debut,$fin,$demi_deb,$demi_fin)){
            $msg = "ATTENTION !  Votre demande de congé se chevauche avec un congé existant. Demande annulée !";
            $this->session->setFlash($msg,'error');
          }
          else{
            $nb_jours = $this->request->data->nb_jours;
            $type = $this->request->data->type;
            if( getYear(toSql($debut)) == date('Y') &&(!$this->verifSolde($nb_jours,$type))){
                $msg = "ATTENTION ! Votre solde pour ce type de congé est insuffisant ! Demande annulée !";
                $this->session->setFlash($msg,'error');
                $this->redirect('/tds/newAbsence');
            }
              $now =new dateTime();            
              $now = $now->format('Y-m-d H:i:s');
              $etat =$this->testAbsType($type);
              $conge = new Absence($this->session->read('login'),toSql($debut),$demi_deb,toSql($fin),$demi_fin,$type,$nb_jours,$commentaire,$etat,$now,'');
              if($joff){
                $new_joffs = getJoffDays($conge);
              }
              $this->addConge($conge);
              $msg = 'Votre demande de congé du '.textDate($debut).' '.textDemi($demi_deb).' au '.textDate($fin).' '.textDemi($demi_fin).' a bien été envoyé';
              $this->session->setFlash($msg,'success');
              $this->redirect('/tds/edittds');
            
          }
        }    
      } 
    }
    
    /**
     * Vue de la page tds/edittds.php
     * @param timestamp $week Timestamp de la semaine à afficher.
     */
    public function edittds($week = null){
        if($week === null){
          // $week = getCurrentWeek();
          $week = getCurrentTimestamp();
        }
        else{
           $week=intval($week);
        }
        $this->mail->configure();
        $this->set('table',$this->createWeekTable(setWeek($week)));  
        $this->set('logusr',$this->session->read('user'));
    }

    /**
     * Edition d'un congé
     * @param int $id_conge
     * @return void
     */
    public function editconge($type_abs,$id_conge=null){
		$this->mail->configure();
		$this->loadModel('Queries');
        $conge = $this->Queries->find(
                    'suivi',
                    array('conditions'=>array('p_num'=>$id_conge)),
                    Model::FETCH_ONE|Model::FETCH_OBJ
        );
        if(!empty($conge)){
          $tab_abs=array();
          $tab_cp=array();
          foreach ($this->session->read('type_abs') as $key => $v) {
            if(in_array($v->ta_id, $this->session->read('isConge'))){
              array_push($tab_cp, $v);
            }else{
              array_push($tab_abs, $v);
            }
          }
          $this->set('conge',$conge);
          if($type_abs=="cp"){
            $titre = "Edition d'un congé";
            $this->set('type_abs',$tab_cp);
          }else{
            $titre = "Edition d'une absence";
            $this->set('type_abs',$tab_abs);
          }
          $this->set('layoutTitle',$titre);
          $this->set('titre',$titre);
          $nb = calcJours($conge->p_date_deb,$conge->p_demi_jour_deb,$conge->p_date_fin,$conge->p_demi_jour_fin,"false",$this->cache->read('ferie'));
          if($nb == $conge->p_nb_jours){
            $checked = "";
          }else{
            $checked = "checked";
          }
          $input_joff='<input type="checkbox" id="joff" name="joff" value="1" '.$checked.' onChange="CalculJour();">';
          $this->set('input_joff',$input_joff);
        }

        if (isset($this->request->data->cancel)) {
            $this->redirect('/suivi/suivi/'.$type_abs);
        }
        elseif(isset($this->request->data->valid)) {
            $debut = $this->request->data->deb;
            $fin = $this->request->data->fin;
            $type = $this->request->data->type;
            $demi_deb = $this->request->data->demi_deb;
            $demi_fin = $this->request->data->demi_fin;
            $commentaire = $this->request->data->com;
            $etat = $this->request->data->etat;
            $now =date('Y-m-d H:i:s');  
            $nb_jours = $this->request->data->nb_jours;
            if ($this->request->data->joff != 0){
              $joff = $this->request->data->joff;
              $commentaire .="+Joff";
            }
            else{
              $joff="false";
            }
            $interval = calcJours($debut,$demi_deb,$fin,$demi_fin,$joff,$this->cache->read('ferie'));
            if($interval<0){
               $msg= 'Incohérence des dates! Veuillez vérifier';
               $this->session->setFlash($msg,'error');
            }
            else{
				// Vérification de l'existence d'un congé sur la même période.
	            if(!$this->verifConge($debut,$fin,$demi_deb,$demi_fin)){
	              $msg = "ATTENTION !  Votre demande de congé se chevauche avec un congé existant. Edition annulée !";
	              $this->session->setFlash($msg,'error');
	            }
	            else{
	              $nb_jours = $this->request->data->nb_jours;
	              $type = $this->request->data->type;
	              if( getYear($debut) == date('Y') && !$this->verifSolde($nb_jours,$type) ){
	                $msg = "ATTENTION ! Votre solde pour ce type de congé est insuffisant ! Edition annulée !";
	                $this->session->setFlash($msg,'error');
	                $this->redirect('/suivi/suivi/'.$type_abs);
	              }
	              $now =new dateTime();            
	              $now = $now->format('Y-m-d H:i:s');
	              $etat =$this->testAbsType($type);
	              $conge = new Absence($this->session->read('login'),$debut,$demi_deb,$fin,$demi_fin,$type,$nb_jours,$commentaire,$etat,$now,$id_conge);
	              $this->addConge($conge);
	                  // $this->Queries->save($d);
	              Log::dbWrite($this->session->read('login'),'edition',$conge);
	              $msg = "Votre congé a été modifié.";
	              $this->session->setFlash($msg,'success');
	              $this->redirect('/suivi/suivi/'.$type_abs);
	            }
            }
        }
    }
    
    /**
     * Suppresion d'un congé
     * @param int $id_conge id du congé à supprimer
     * @param string $flag flag permettant de connaitre la page depuis laquelle on demande la suppression
     * @return void
     */
    public function deleteconge($id_conge=null,$flag="tds",$annee=null){
        $this->loadModel('Queries');
        $conge = $this->Queries->find(
                                      'del_conge',
                                      array('conditions'=>array('p_num'=>$id_conge,'p_login'=>$this->session->read('login'))),
                                      Model::FETCH_OBJ|Model::FETCH_ONE
              );
        if(!empty($conge)){
          $conge->p_type <= 2 ? $type_abs='cp':$type_abs='abs';
          if($conge->p_type > 2){
            $type_abs='abs';
          }
          else{
            if($conge->p_etat == "ok"){
              // $this->session->setFlash("Vous n'êtes pas autorisé à supprimer ce congé !\nMerci de contacter votre responsable",'error');
              break;
            }
            $type_abs = 'cp';
          }
          $this->delConge($id_conge);
          $this->session->setFlash('Votre congé a bien été supprimé','success');
          if($flag=="suivi"){
            $this->redirect('/suivi/suivi/'.$type_abs.'/'.$annee);  
          }
        }
        else{
          $this->session->setFlash("C'est pas bien d'essayer de supprimer les congés des collègues ! ! !",'error');
          $this->redirect($str_args);  
        }        
    }

    /**
     * Fonction de suppression d'un congé
     * @param int $id id du congé à supprimer
     * @param string $type type du congé à supprimer(base temporaire ou permanente)
     */
    public function delConge($id=null){
      $this->loadModel('Queries');
      $c = $this->Queries->find('suivi',array("conditions"=>array('p_num'=>$id)),Model::FETCH_ONE|Model::FETCH_OBJ);
      $conge = new Absence($c);
      $this->Queries->table = 'conges_periode';
      $this->Queries->primaryKey = 'p_num';
      $this->Queries->realdelete($id);
      Log::dbWrite($this->session->read('login'),'suppression',$conge);
    }

    /**
     * Enlève un congé temporaire du tableau de session
     * @param object $conge Congé a enlever
     */
    public function removeFromSession($conge){
      $table = $this->session->read('tmpconges');
      foreach ($table as $key => $v) {
          if($v->p_date_deb==$conge->p_date_deb && 
             $v->p_date_fin==$conge->p_date_fin &&
             $v->p_demi_jour_deb==$conge->p_demi_jour_deb &&
             $v->p_demi_jour_fin==$conge->p_demi_jour_fin &&
             $v->p_type==$conge->p_type){
            unset($_SESSION['tmpconges'][$key]);
          }
      }
    }

    /**
     * Mise à jour du solde de congé
     * @param string $action - Action a effectuer credit/debit
     * @param string $user - Utilisateur concerné par la mise à jour
     * @param int $type_abs - Type d'abscence
     * @param int $nb_jours - Nombre de jours à ajouter/enlever
     * @return void
     */
    public function updatesolde($user,$type_abs,$nb_jours){
        $this->loadModel('Queries');
        $usr_solde = $this->Queries->find(
                    'solde',
                    array('conditions'=>array('su_login'=>$user)),
                    Model::FETCH_OBJ
                    );
        if(!empty($usr_solde)){
          foreach ($usr_solde as $k => $v) {
            if($v->su_abs_id == $type_abs){
                $solde_val = floatval($v->su_solde) + $nb_jours;
                $reliquat_val =floatval($v->su_reliquat);
            }
          }
        }

        $this->Queries->table ='conges_solde_user';
        $this->Queries->primaryKey=array('su_login','su_abs_id');
        $d=array(
            'su_login' =>$user,
            'su_abs_id'=>$type_abs,
            'su_reliquat'=>$reliquat_val,
            'su_solde'=>$solde_val
            );
        $this->Queries->save($d);

        $solde = $this->Queries->find(
            'solde',
            array('conditions'=>array('su_login'=>$user)),
            Model::FETCH_OBJ
        );
        if(!empty($solde)){
            foreach ($solde as $k => $v) {
                if($v->su_abs_id == 1){
                    $cle = 'solde_cp';
                }
                elseif ($v->su_abs_id == 2) {
                    $cle = 'solde_rtt';
                }
                else{
                    $cle = '';
                }
                $this->session->write($cle, ($v->su_solde + $v->su_reliquat));
            }
        }
    }
    
    /**
     * Test de l'état d'un congé
     * @param string $type
     * @return string $etat
     */
    public function testAbsType($type){
      $etat = '';
      if(in_array($type, $_SESSION['isConge'])){
        $etat='demande';
      }else{        
        $etat='ok';
      }
      return $etat;
    }
    
    /**
     * Calcul du nombre de jours de congés pris - fonction AJAX
     * @param date $date_deb
     * @param date $date_fin
     * @param string $demi_deb
     * @param string $demi_fin
     * @return str  
     */
    public function ajax_calculJours(){
        if (empty($_POST['date_deb'])||empty($_POST['date_fin'])){
          $this->set('data',"");
        }
        else{
          $this->set('data',calcJours($_POST['date_deb'],$_POST['demi_deb'],$_POST['date_fin'],$_POST['demi_fin'],$_POST['joff'],$this->cache->read('ferie')));
        }
    }

    public function ajax_calculDateDiff(){
      if (empty($_POST['date_deb'])||empty($_POST['date_fin'])){
          $this->set('data',"");
        }
        else{
          $date1 = new DateTime($_POST['date_deb']);
          $date2 = new DateTime($_POST['date_fin']);
          $interval =  date_diff($date1, $date2);
        // En dev
          // $interval = $interval->format('%R%a'); // Pour PHP > 5.2
          // $dif = floatval($interval);
        // En Prod
          $dif=$interval/(3600*24);  
          $this->set('data',$dif);
        }
    }
    
    /**
     * Retourne le tableau de service rempli - fonction AJAX
     */
    public function ajax_createWeek(){
        $this->layouts=array('ajax');
        $w = $this->createWeekTable(setWeek($_POST['semaine']));
        $this->set('w',$w);
    }
        
    /**
     * Création de la semaine de congés 
     * @param array $semaine
     * @return array $table
     */
    public function createWeekTable($semaine){
        $table_day = array('lun'=>$semaine['lun'],
                        'mar'=>$semaine['mar'],
                        'mer'=>$semaine['mer'],
                        'jeu'=>$semaine['jeu'],
                        'ven'=>$semaine['ven']
                        );

        $days=array('lu'=>'Lundi','ma'=>'Mardi','me'=>'Mercredi','je'=>'Jeudi','ve'=>'Vendredi');
        $table['tmstp'] = $semaine['tmstp'];
        $tmp=$this->getWeekConges($table_day,$this->session->read('login'),$table['tmstp']);
        $classe = $tmp['classe'];
        $ids = $tmp['ids'];
        $etat = $tmp['etat'];
        $bulle = $tmp['bulles'];
        $table['is_tmpconge'] = $tmp['is_tmpconge'];
        $table['listing'] = $tmp['listing'];
        $table['moisannee'] = $semaine['moisannee'];
        // $table['moisannee'] = '<th colspan="6">'.$semaine['moisannee'].'</th>';
        $table['semaine']= '<th></th>';
        foreach ($days as $key => $value) {
          if($semaine['today']==$key){
            $table['semaine'].='<th class="today">'.$value.' '.$semaine[$key].'</th>';
          }else{
            $table['semaine'].='<th>'.$value.' '.$semaine[$key].'</th>';
          }
        }
        $table['user'] = '<tr id="usrweek"><th scope="row">'.strtoupper($this->session->read('user')->u_nom).'</th>';
        foreach ($table_day as $j => $value) {
            $table['user'] .= '<td class="usr"><div class="'.$classe[$j].'" data-id-am="'.$ids[$j]['am'].'" data-id-pm="'.$ids[$j]['pm'].'" data-date="'.$semaine[$j].'" data-etat-am="'.$etat[$j]['am'].'" data-etat-pm="'.$etat[$j]['pm'].'">'.$bulle[$j].'</div></td>';
          }
        $table['user'] .= '</tr>';
        $table['empty'] = '<tr class="empty"><td colspan="6">&nbsp;</td></tr>';
        $table['others'] = '';
        foreach ($this->session->read('group_users') as $key => $usr) {
          $temp=$this->getWeekConges($table_day,$usr->u_login,$table['tmstp']);
          $c = $temp['classe'];
          $ids = $temp['ids'];
          $bulle = $temp['bulles'];
          $table['others'] .='<tr><th scope="row">'.strtoupper($usr->u_nom).'</th>'; 
          foreach ($table_day as $j => $value) {
            $table['others'].='<td><div class="'.$c[$j].'" data-id-am="'.$ids[$j]['am'].'" data-id-pm="'.$ids[$j]['pm'].'" data-date="'.$semaine[$j].'" data-etat-am="'.$etat[$j]['am'].'" data-etat-pm="'.$etat[$j]['pm'].'">'.$bulle[$j].'</div></td>';
          }
          $table['others'].='</tr>';
        }

        $table['completeweek'] =$table['user'].$table['empty'].$table['others'];
        $table['real_sem'] = $semaine['sem'];
        $table['sem'] = $semaine['sem'];
        $table['printline'] = "<a href=".Router::url('/tds/printWeek/'.$semaine['tmstp'])." target='_blank' class='css3button'>Imprimer</a>";
        $table['monthview'] = "<a href=".Router::url('/month/index/mois:'.$semaine['moisnumeric'])." class='css3button'>Vue mensuelle</a>";
        return $table;
    }

    /**
     * Récupère les congés d'un utilisateur pour une semaine donnée
     * @param array $jours tableau des dates de la semaine demandée lun->ven
     * @param string $user login de l'utilisateur
     * @param int $timestamp timestamp de la semaine demandée,utilisé pour les liens
     * @return array $classe
     */
    public function getWeekConges($jours,$user,$timestamp){
        
        foreach ($jours as $k => $v) {
          $tmp_class[$k] = array('am'=>' am_taf','pm'=>' pm_taf');  
          $ids[$k]['am']='';
          $ids[$k]['pm']='';
          $etat[$k]['am']='';
          $etat[$k]['pm']='';
          $bulles[$k]['am']='';
          $bulles[$k]['pm']='';
        }
        $listing="";
        $this->loadModel('Queries');
        $conges = $this->Queries->find(
                'suivi',
                array("conditions"=>array(
                            'p_login' => $user,
                            'p_date_deb' => array('<=',$jours['ven']),
                            'p_date_fin' => array('>=',$jours['lun']),
                    )),
                Model::FETCH_OBJ
            );
        foreach ($conges as $k => $c) {
          if($c->p_etat != "ok" && $c->p_etat != "demande" ){
            unset($conges[$k]);
          }
        }
        if($user==$this->session->read('login')){
          $tmp_conges = getPeriodTempConges($this->session->read('tmpconges'),$jours['lun'],$jours['ven']);
        }
        else{
          $tmpconges= array();
        }

        $types = $this->cache->read('type_abs');
        foreach ($jours as $k => $v) {
            if(in_array($v, $this->cache->read('ferie'))){
              $classe[$k]="ferie";
              $bulles[$k]['am']='';
              $bulles[$k]['pm']='';
            }else{
              $classe[$k] = substr($tmp_class[$k]['am'].$tmp_class[$k]['pm'],1);
            }
            $bulles[$k]='';
        }
        $ferie = array('classe'=>$classe, 'ids'=>$ids , 'bulles'=>$bulles);
        if(!empty($conges)){
          $base_params = fillDays($conges,$jours,$types,$this->cache->read('ferie'));
          // $base_params = $this->fillDays($conges,$jours,$types);
          if(!empty($tmp_conges)){
            $tmp_params = fillDays($tmp_conges,$jours,$types,$this->cache->read('ferie'));
            // $tmp_params = $this->fillDays($tmp_conges,$jours,$types);
            foreach ($tmp_params as $key => $value) {
              if($key == 'demi_classes'){
                foreach ($value as $k => $v) {
                  if($tmp_params['classe'][$k]!='ferie'){
                    $f = 0;
                    if($v['am'] != ' am_taf'){
                      $base_params['demi_classes'][$k]['am'] = $v['am'];
                      $base_params['demi_bulles'][$k]['am'] = $tmp_params['demi_bulles'][$k]['am'];
                      $f = 1;
                    }else{
                      if($tmp_params['ids'][$k]['am']!=""){
                        $base_params['demi_classes'][$k]['am'] = $v['am'];
                        $base_params['demi_bulles'][$k]['am'] = $tmp_params['demi_bulles'][$k]['am'];
                        $f = 1;
                      }
                    }
                    if($v['pm'] != ' pm_taf'){
                      $base_params['demi_classes'][$k]['pm'] = $v['pm'];
                      $base_params['demi_bulles'][$k]['pm'] = $tmp_params['demi_bulles'][$k]['pm'];
                      $f = 1;
                    }else{
                      if($tmp_params['ids'][$k]['pm']!=""){
                        $base_params['demi_classes'][$k]['pm'] = $v['pm'];
                        $base_params['demi_bulles'][$k]['pm'] = $tmp_params['demi_bulles'][$k]['pm'];
                        $f = 1;
                      }
                    }
                    $base_params['classe'][$k]= substr($base_params['demi_classes'][$k]['am'].$base_params['demi_classes'][$k]['pm'],1);                
                    if($f==1){
                      $base_params['bulles'][$k] = '<span>'.$base_params['demi_bulles'][$k]['am'].$base_params['demi_bulles'][$k]['pm'].'</span>';
                    }                     
                  }
                }
              }
            }
            $is_tmpconge=1;
          }
          else{
            $is_tmpconge=0;
          }
          $params = $base_params;
        }
        elseif (empty($conges) && !empty($tmp_conges)) {
          $tmp_params = $this->fillDays($tmp_conges,$jours,$types);
          foreach ($jours as $k => $v) {
            if(($tmp_params['demi_bulles'][$k]['am']=='' && $tmp_params['demi_bulles'][$k]['pm']='')){
              $tmp_params['bulles'][$k]='';
            }
            $tmp_params['ids'][$k] = array('am'=>'','pm'=>'');
            $tmp_params['etat'][$k] = array('am'=>'','pm'=>'');
          }
          $params = $tmp_params;
          $is_tmpconge=1;
        }
        else{
          $params = $ferie;
          foreach ($jours as $k => $v) {
            $params['etat'][$k] = array('am'=>'','pm'=>'');
          }  

          $is_tmpconge=0;
          $listing.="";

        }     
        $params['listing'] = createListe($timestamp);
        if($params['listing'] == ""){
          $params['is_tmpconge']=0;
        }
        else{
          $params['is_tmpconge']=1;
        }
        return $params;
    }

    /**
      * Retourne le timestamp de la semaine à voir.
      * @param int Timestamp de la semaine en cours de vue.
      * @param int $val   égal a +/-7 (changement semaine) ou +/-30 (changement mois)
      * @return int Timestamp de la nouvelle semaine demandée
      **/
    public function ajax_changeWeek(){
      $day = date('d',$_POST['timestamp']);
      $month = date('m',$_POST['timestamp']);
      $year = date('Y',$_POST['timestamp']);
      $this->set('data',mktime(0,0,0,$month,$day+$_POST['val'],$year));
    }

    /**
     * Remplit les jours de la semaine avec les congés de l'utilisateur
     * @param array $conges Liste des congés de l'utilisateur
     * @param array $jours Liste des jours de la période recherchée
     * @param array $types tableau d'association entre l'id du type de congé et son libellé
     * @return array tableau contenant les classes pour chaque jours,ainsi que les infos bulles et les ids des congés et leur état.
     **/
   public function fillDays($conges,$jours,$types){
      $types[0]="taf";
      foreach ($jours as $k => $v) {
                $tmp_class[$k] = array('am'=>' am_taf','pm'=>' pm_taf');  
                $ids[$k]['am']='';
                $ids[$k]['pm']='';
                $etat[$k]['am']='';
                $etat[$k]['pm']='';
                $bulles[$k]['am']='';
                $bulles[$k]['pm']='';
              }
      foreach ($conges as $k => $c) {
              if($c->p_etat != 'refus' && $c->p_etat != 'annul'){
                if(in_array($c->p_date_deb, $jours) && in_array($c->p_date_fin, $jours)){
            //le congé débute et termine dans la semaine
                  $jour_deb = array_search($c->p_date_deb, $jours);
                  $jour_fin = array_search($c->p_date_fin, $jours);
                  if($jour_deb == $jour_fin){               // Congé sur une seule journée
                    if($c->p_demi_jour_deb =='pm'){         // Après-midi
                      // $classe[$jour_deb] = 'pm_'.$types[$c->p_type];
                      $tmp_class[$jour_deb]['pm'] = ' pm_'.$types[$c->p_type];
                      $ids[$jour_deb]['pm'] = $c->p_num;
                      $etat[$jour_deb]['pm'] = $c->p_etat;
                      $bulles[$jour_deb]['pm'] = setTooltip($c);
                    }
                    else{
                      if($c->p_demi_jour_fin =='am'){       // matinée
                        $tmp_class[$jour_deb]['am'] = ' am_'.$types[$c->p_type];
                        $ids[$jour_deb]['am'] = $c->p_num;
                        $etat[$jour_deb]['am'] = $c->p_etat;
                        $bulles[$jour_deb]['am'] = setTooltip($c);
                      }
                      else{                                 //Journée complète
                         $tmp_class[$jour_deb]['am']= ' am_'.$types[$c->p_type];                      
                         $tmp_class[$jour_deb]['pm']= ' pm_'.$types[$c->p_type];                      
                        // $classe[$jour_deb] = $types[$c->p_type];                      
                        $ids[$jour_deb]['am'] = $c->p_num;
                        $etat[$jour_deb]['am'] = $c->p_etat;
                        $ids[$jour_deb]['pm'] = $c->p_num;
                        $etat[$jour_deb]['pm'] = $c->p_etat;
                        $bulles[$jour_deb]['am'] = setTooltip($c);
                      }
                    }
                  }
                  else{                                       //Congé sur plusieurs jours
                    foreach ($jours as $jour => $date) {
                      if(($c->p_date_deb <= $date) && ($date <= $c->p_date_fin)){
                        if($date == $c->p_date_deb){
                          if($c->p_demi_jour_deb =='pm'){           //Débute l'après-midi
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                            $ids[$jour]['pm'] = $c->p_num;
                            $etat[$jour]['pm'] = $c->p_etat;
                            $bulles[$jour]['pm'] = setTooltip($c);
                          }else{
                            $ids[$jour]['am'] = $c->p_num;
                            $ids[$jour]['pm'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $etat[$jour]['pm'] = $c->p_etat;
                            $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type]; 
                            $bulles[$jour]['am'] = setTooltip($c);
                            // $bulles[$jour]['pm'] = setTooltip($c);
                          }
                        }
                        elseif ($date == $c->p_date_fin) {
                          if($c->p_demi_jour_fin =='am'){           // termine fin de matinée
                            $tmp_class[$jour]['am'] =' am_'.$types[$c->p_type];
                            $ids[$jour]['am'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $bulles[$jour]['am'] = setTooltip($c);
                          }else{
                            $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                            $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                            $ids[$jour]['am'] = $c->p_num;
                            $etat[$jour]['am'] = $c->p_etat;
                            $ids[$jour]['pm'] = $c->p_num;  
                            $etat[$jour]['pm'] = $c->p_etat;  
                            $bulles[$jour]['am'] = setTooltip($c);
                            // $bulles[$jour]['pm'] = setTooltip($c);
                          }
                        }
                        else{
                          $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                          $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                          $bulles[$jour]['am'] = setTooltip($c);
                          $ids[$jour]['am'] = $c->p_num;
                          $ids[$jour]['pm'] = $c->p_num;
                          // $bulles[$jour]['pm'] = setTooltip($c);
                        }
                      }
                    }
                  }
                }
                elseif(in_array($c->p_date_deb, $jours) && !in_array($c->p_date_fin, $jours)){  // Le congé démarre dans la semaine et fini après
                  $jour_deb = array_search($c->p_date_deb, $jours);
                  foreach ($jours as $jour => $date) {
                    if(($c->p_date_deb <= $date ) && ( $date <= $c->p_date_fin)){ //
                      $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                      $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                      // $classe[$jour] = $types[$c->p_type];
                      $ids[$jour]['am'] = $c->p_num;
                      $etat[$jour]['am'] = $c->p_etat;
                      $ids[$jour]['pm'] = $c->p_num;
                      $etat[$jour]['pm'] = $c->p_etat;
                      $bulles[$jour]['am'] = setTooltip($c);
                      // $bulles[$jour]['pm'] = setTooltip($c);
                    }
                  }
                  if($c->p_demi_jour_deb =='pm'){           //Débute l'après-midi
                    $tmp_class[$jour_deb]['pm'] = ' pm_'.$types[$c->p_type];
                    $bulles[$jour]['pm'] = setTooltip($c);
                    // $classe[$jour_deb] =' pm_'.$types[$c->p_type];
                  }
                }
                elseif(! in_array($c->p_date_deb, $jours) && in_array($c->p_date_fin, $jours)){ // Le congé démarre la semaine d'avant et fini dans la semaine.
                  $jour_fin = array_search($c->p_date_fin, $jours);
                  foreach ($jours as $jour => $date) {
                    if(($c->p_date_deb<=$date)&&($date<=$c->p_date_fin)){ //
                      $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                      $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];  
                      // $classe[$jour] = $types[$c->p_type];
                      $ids[$jour]['am'] = $c->p_num;
                      $etat[$jour]['am'] = $c->p_etat;
                      $etat[$jour]['pm'] = $c->p_etat;
                      $ids[$jour]['pm'] = $c->p_num;
                      $bulles[$jour]['am'] = setTooltip($c);
                    }
                  }
                  if($c->p_demi_jour_fin =='am'){           // termine fin de matinée
                    $tmp_class[$jour_fin]['am'] = ' am_'.$types[$c->p_type];
                    $bulles[$jour_fin]['am'] = setTooltip($c);
                  }                                
                }
                else{  // Le congé débute et termine hors de la semaine affichée
                  foreach ($jours as $jour => $date) {
                    $tmp_class[$jour]['am'] = ' am_'.$types[$c->p_type];                      
                    $tmp_class[$jour]['pm'] = ' pm_'.$types[$c->p_type];
                    $ids[$jour]['am'] = $c->p_num;
                    $ids[$jour]['pm'] = $c->p_num;
                    $etat[$jour]['am'] = $c->p_etat;
                    $etat[$jour]['pm'] = $c->p_etat;
                    $bulles[$jour]['am'] = setTooltip($c);
                  }
                }
              }
            }
      foreach ($jours as $k => $v) {
            if(in_array($v, $this->cache->read('ferie'))){
              $classe[$k]="ferie";
            }else{
              $classe[$k] = substr($tmp_class[$k]['am'].$tmp_class[$k]['pm'],1);
            }
            $demi_bulles[$k]['am'] = $bulles[$k]['am'];
            $demi_bulles[$k]['pm'] = $bulles[$k]['pm'];
            if($bulles[$k]['am']=='' && $bulles[$k]['pm'] ==''){
              $bulles[$k]='';

            }else{
              if($bulles[$k]['pm'] ==''){
                $bulles[$k] = '<span>'.$bulles[$k]['am'].'</span>';                               
              }
              elseif($bulles[$k]['am'] ==''){
                $bulles[$k] = '<span>'.$bulles[$k]['pm'].'</span>';                               
              }
              else{
              $bulles[$k] = '<span>'.$bulles[$k]['am'].$bulles[$k]['pm'].'</span>';                               
              }
              
            }
        
        }
        $params = array('classe'=>$classe,'demi_classes'=>$tmp_class, 'ids'=>$ids , 'bulles'=>$bulles, 'demi_bulles'=>$demi_bulles,'etat'=>$etat);
        return $params;    
   }

    /**
     * Ajoute un congé soit en base, soit en temporaire.
     * @param object $conge Congé à ajouter
     * @param string $flag flag déterminant si le congé est temporaire ou non.
     */
    public function addConge($conge){
      $d = $conge->obj2Arr();
      $this->loadModel('Queries');
      $this->Queries->table='conges_periode';
      $this->Queries->primaryKey='p_num';
      $this->Queries->save($d);
      $conge->p_num = $this->returnId($conge);
      Log::dbWrite($this->session->read('login'),'ajout',$conge);

      if(in_array($conge->p_type,$this->session->read('isConge'))){
        $this->mail->sendMail('mail_new_demande',$conge);
      }
    }


    public function addTmpConge($conge){
      $tempConges = $this->session->read('tmpconges');
      array_push($_SESSION['tmpconges'],$conge);
      foreach ($tempConges as $k => $v) {
        if ($conge->collide($v)){
          unset($_SESSION['tmpconges'][$k]);
          if($conge->p_type=="0"){
            $this->removeFromSession($conge);
          }
        }
      }
    }
    
    /**
     * Retourne l'id du congé
     * @param object $conge Congé dont on veut connaitre l'id
     * @return int 
     */
    public function returnId($conge){
      $this->loadModel('Queries');
      $id = $this->Queries->find('joff',
              array("conditions"=>array('p_login'=>$conge->p_login,
                                        'p_date_deb'=>$conge->p_date_deb,
                                        'p_date_fin'=>$conge->p_date_fin,
                                        'p_demi_jour_deb'=>$conge->p_demi_jour_deb,
                                        'p_demi_jour_fin'=>$conge->p_demi_jour_fin)
                                        // 'p_type' => intval($conge->p_type))
              ),
              Model::FETCH_OBJ||Model::FETCH_ONE
            );
      if(!empty($id)){
        return $id->p_num;
      }
      else{
        return '';
      }
    }

    /**
     * Vérifie l'existence ou non d'un congé dans la période demandée
     * @param string $date_deb date de début du congé demandé
     * @param string $date_fin date de fin du congé demandé
     * @return bool 
     */
    public function verifConge($date_deb,$date_fin,$demi_deb,$demi_fin){
      $flag = true;
      $this->loadModel('Queries');
      $conges=array();
      $conges_between = $this->Queries->find(
          'suivi',
          array('conditions'=>array('p_date_deb'=>array('between',$date_deb,$date_fin),
            'p_date_fin'=>array('<=',$date_fin),
            'p_login'=>$this->session->read('login')))
      );
      if(!empty($conges_between)){
        foreach ($conges_between as $key => $v) {
          array_push($conges, $v);
        }
      }
      $conges_start_before = $this->Queries->find(
          'suivi',
          array('conditions'=>array('p_date_deb'=>array('<=',$date_deb),
            'p_date_fin'=>array('between',$date_deb,$date_fin),
            'p_login'=>$this->session->read('login')))
      );
      if(!empty($conges_start_before)){
        foreach ($conges_start_before as $key => $v) {
          array_push($conges, $v);
        }
      }
      $conges_start_after = $this->Queries->find(
          'suivi',
          array('conditions'=>array('p_date_deb'=>$date_fin,
          // array('conditions'=>array('p_date_deb'=>array('>=',$date_fin),
            'p_login'=>$this->session->read('login')))
      );
      if(!empty($conges_start_after)){
        foreach ($conges_start_after as $key => $v) {
          array_push($conges, $v);
        }
      }
      if(empty($conges)){
        $flag = true;
      }
      else{
        foreach ($conges as $k => $c) {
          if($date_deb <= $c->p_date_deb && $c->p_date_fin <= $date_fin){  
            if($date_deb==$date_fin){
              if($demi_deb!=$c->p_demi_jour_deb){
                $flag = true;
              }
            }else{
              $flag = false;              
            }
            // un congé existe dans la période de congé demandé.
          }
          elseif($date_deb <= $c->p_date_deb && $c->p_date_deb == $date_fin){
            // congé demandé fini le jour ou le congé existant débute
            if($demi_fin=="am" && $c->p_demi_jour_deb!=$demi_fin){
              $flag = true;
            }
            else{
              $flag = false;
            }
          }
          elseif($date_deb == $c->p_date_fin && $date_fin >= $c->p_date_fin){
            if($demi_deb=="pm" && $demi_deb!=$c->p_demi_jour_fin){
              $flag = true;
            }
            else{
              $flag = false;
            }
          }
          else{
            $flag = true;
          }
        }
      }

      return $flag;
    } 

    /**
     * Fonction AJAX
     * Vérification du solde de congé d'un utilisateur lors de la demande 
     * @param int $nb_jours nombre de jours demandés
     * @param int $type type de congé demandé (cp/rtt)
     * @return bool ok/nok
     */
    public function verifSolde($nb_jours,$type){
        if(in_array($type, $this->session->read('isConge'))){
          $user = $this->session->read('login');
          $this->loadModel('Queries');
          $solde = $this->Queries->find(
                                        'solde',
                                        array("conditions"=>array('su_login'=>$user,'su_abs_id'=>$type)),
                                        Model::FETCH_OBJ|Model::FETCH_ONE
                                        );
          if(!empty($solde)){
              $total_solde = $solde->su_solde + $solde->su_reliquat;
              if($total_solde < $nb_jours ){
                  return false;
              }
              else{
                  return true;
              }
          }
        }
        else{
          return true;
        }
    }

    /**
     * Création de la page à imprimer
     * @param int $weektimestamp
     * @return none
     */
    public function printWeek($weektimestamp){
      $this->layouts=array('default','ajax');
      $gr = $this->cache->read('sections');
      $this->set('section',$gr[$this->session->read('user_group')]);      
      $this->set('table',$this->createPrintTable(setWeek($weektimestamp)));
    }


    /**
     * Création de la semaine de congés à imprimer
     * @param array $semaine
     * @return array $table
     */
    public function createPrintTable($semaine){
        $this->loadModel('Queries');
        $users = $this->Queries->find(
            'group_users',
            array(
                'conditions'=>array(
                    'gu_gid'=> $this->session->read('user_group'),
                ),
                'order'=>'u_nom'
            ),
            Model::FETCH_OBJ
        );
        $table = array('lun'=>$semaine['lun'],
                        'mar'=>$semaine['mar'],
                        'mer'=>$semaine['mer'],
                        'jeu'=>$semaine['jeu'],
                        'ven'=>$semaine['ven']
                        );
        $days=array('lu'=>'Lundi','ma'=>'Mardi','me'=>'Mercredi','je'=>'Jeudi','ve'=>'Vendredi');
        $table['tmstp'] = $semaine['tmstp'];
        $table['moisannee'] = $semaine['moisannee'];
        $table['semaine']= '<th></th>';
        foreach ($days as $key => $value) {
            $table['semaine'].='<th>'.$value.' '.$semaine[$key].'</th>';
        }
        $table['all'] = '';
        foreach ($users as $key => $usr) {
          $temp=$this->getWeekConges($table,$usr->u_login,$table['tmstp']);
          $c = $temp['classe'];
          $table['all'] .='<tr>
                                <th scope="row">'.strtoupper($usr->u_nom).'</th>
                                <td>
                                    <div class="'.$c['lun'].'" ></div></td><td>
                                    <div class="'.$c['mar'].'" ></div></td><td>
                                    <div class="'.$c['mer'].'" ></div></td><td>
                                    <div class="'.$c['jeu'].'" ></div></td><td>
                                    <div class="'.$c['ven'].'" ></div></td>
                              </tr>';                              
        }
        $table['sem'] = $semaine['sem'];
        return $table;
    }
}

