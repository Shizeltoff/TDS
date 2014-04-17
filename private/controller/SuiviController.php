<?php
class SuiviController extends Controller{

    /**
     * Rendu de la vue suivi
     * Liste les congés de type CP et RTT
     */
    public function suivi($type_abs,$annee = null,$order = 'desc'){
        $titre = 'Suivi des ';
        if($type_abs=="abs"){
            $titre .= "absences";
        }else{
            $titre .= "congés";
        }
        $this->set('layoutTitle',$titre);
        $this->set('titre',$titre);
        $conges = array();
        $this->set('annees',getYears());        
        if(!isset($annee)){
            $this->set('current_year',date('Y'));
            $bornes = getYearLimits(date('Y'));        
        }
        else{
            $this->set('current_year',$annee);
            $bornes = getYearLimits($annee);        
        }
        $first = $bornes['first'];
        $last = $bornes['last'];
        $this->loadModel('Queries');
        $types_conges = $this->session->read('isConge');
        $tab="(";
        foreach ($types_conges as $k => $v) {
          $tab.= $v.',';
        }
        $tab = substr($tab,0, -1);
        $tab.=')';
        $type_abs=='cp'? $p_type = 'p_type IN '.$tab : $p_type = 'p_type NOT IN '.$tab;
        $conges = $this->Queries->find(
                    'suivi',
                    array('conditions'=>array(
                                             'p_login'=>$this->session->read('login'),
                                             $p_type,
                                             'p_date_fin'=>array('>=',$first),
                                             'p_date_deb'=>array('<=',$last)),
                      'order'=>'p_date_deb ' . $order
                      /*'sens'=>$order*/
                    ),
                    Model::FETCH_OBJ
        );
        
        if(!empty($conges)){
            $this->set('suivi',$conges);
            $this->set('type_abs',$type_abs);
            $this->set('ordre',$order);
            $this->set('debug',Conf::DEBUG);
        }
        
        else{
            $this->set('ordre',$order);
            $type_abs=="abs"? $msg="Aucune absence ":$msg="Aucun congé ";
            $msg .= "à afficher pour cette année.";
            $this->set('suivi',$msg);
            $this->set('type_abs',$type_abs);

        }
    }
    
    /**
     * Rendu de la vue solde
     * Affiche le solde d'un utilisateur
     */
    public function solde(){
        $this->set('layoutTitle','Solde des congés');
        $annee = date('Y');
        $this->loadModel('Queries');
        $conges = $this->Queries->find(
                    'solde',
                    array('conditions'=>array('su_login'=>$this->session->read('login'))),
                    Model::FETCH_OBJ
                    );
        if(!empty($conges)){
            $this->set('droit',$conges);
            $this->set('annee',$annee);
        }
    }

    /**
     * Rendu de la vue impr
     * Création de la page a imprimer
     */
    public function impr(){
        $this->layouts=array('menu2','main','default');
    }

    /**
     * Fonction AJAX
     * Appel de la vue suivi avec l'année passée en paramètre.
     * @param string $_POST['annee'] Année demandée
     * @return vue du suivi des congés
     */
    public function ajax_suivi(){
        $this->suivi($_POST['type_abs'] , $_POST['annee'], $_POST['ordre']);
    }

    /**
     * Page de confirmation de suppression 
     * @param int $id numéro du congé a supprimer
     * @return none Redirection vers la bonne page en fonction du choix
     */
    public function delConfirm($id=null,$annee){
        $this->loadModel('Queries');
        $this->set('annee',$annee); 
        $c = $this->Queries->find('suivi',
                                        array('conditions'=>array('p_login'=>$this->session->read('login'),'p_num'=>$id)),
                                        Model::FETCH_OBJ|Model::FETCH_ONE);
        if(!empty($c)){
          $conge = new Absence($c);  
          $typabs =$this->session->read('type_abs');
          $libelle = $typabs[$conge->p_type -1]->ta_libelle;
          $this->set('libelle',$libelle);
          $this->set('conge',$conge);
          $this->set('id',$id);
          $conge->p_type<=2? $type_abs='cp':$type_abs='abs';
          $this->set('type_abs',$type_abs);
        }
        else{
            $erreur = "<p>Vous n'êtes pas autorisé à supprimer ce congé !<br>";
            $erreur .= "Vous allez être redirigé dans 3 secondes</p>";
            $this->set('conge',$erreur);
            sleep(2);
            $this->redirect('suivi/suivi/abs');
        }
    }

    /**
     * Demande l'annulation du congé auprès du responsable.
     * @param string $id_conge id du congé dont l'annulation est souhaitée.
     */
    public function demAnnul($id_conge=null){
        $this->loadModel('Queries');
        $c = $this->Queries->find('suivi',
                                        array('conditions'=>array('p_login'=>$this->session->read('login'),'p_num'=>$id_conge)),
                                        Model::FETCH_OBJ|Model::FETCH_ONE);
        if(!empty($c)){
            $conge = new Absence($c);
            $this->set('conge',$conge);
            $this->set('id',$id_conge);
            $typabs =$this->session->read('type_abs');
            $libelle = $typabs[$conge->p_type -1]->ta_libelle;
            $this->set('libelle',$libelle);
        }
        else{
            $erreur = "<p>Vous ne pouvez pas annuler ce congé car il ne vous appartient pas!<br>";
            $erreur .= "Vous allez être redirigé dans 3 secondes</p>";
            $this->set('conge',$erreur);
            $this->set('id',"");
            $this->set('libelle',"");

            sleep(2);
            $this->redirect('suivi/suivi/abs');
        }

    }


    /**
     * Envoi le mail d'annulation d'un congé
     * @param string $id_conge id du congé dont l'annulation est souhaitée.
     */
    public function annulAbs($id_abs){
      $this->mail->configure();
      $this->loadModel('Queries');
      $c = $this->Queries->find('suivi',array("conditions"=>array('p_num'=>$id_abs)),Model::FETCH_ONE|Model::FETCH_OBJ);
      $conge = new Absence($c);
      $this->mail->sendMail('mail_annulation_demande',$conge);
      if(in_array($conge->p_type, $this->session->read('isConge'))){
        $abs = 'cp';
      }
      else{
        $abs = 'abs';
      }
      $year = getYear($conge->p_date_deb);
      $msg = "Votre demande d'annulation a bien été envoyée";
      $this->session->setFlash($msg,'success');
      $this->redirect('suivi/suivi/'.$abs.'/'.$year);
    }
    
}