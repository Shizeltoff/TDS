<?php 
class ValidationController extends Controller{


  public $modified_ids = array();
  // public function __construct(){
  //   parent::__construct();
  //   $this->loadModel->Queries;
  // }


  public function configureMail(){
     $this->mail->responsables = $this->session->read('mails_reponsables');
     $this->mail->user = $this->session->read('user');
     $this->mail->headers = $this->session->read('mail_headers');
  }

  /**
    * Valide tous les congés temporaires présent pour un l'utilisateur loggué
    * @param none
    * @return none
    */
  public function validate($action){
    $this->configureMail();
    $tmpconges = $this->session->read('tmpconges');
    usort($tmpconges, 'compareAbs');
    $this->loadModel('Queries');
    foreach ($tmpconges as $k => $abs) {
      if($abs->p_num!=''){
        if(is_array($abs->p_num)){
          foreach ($abs->p_num as $key => $v) {
            if(array_key_exists($v, $this->modified_ids)){
              $v = $this->modified_ids[$v];
            }
            if($v!=''){
              $conge = $this->getCongeFromId($v);
              if($abs->isReplacing($conge)){
                if($abs->p_type==0){
                  $this->delAbs($conge);
                }
              }
              else{
                $this->editAbs($abs,$conge);
              }
            }
          }
          $abs->p_num ='';      
        }
        else{
          if(array_key_exists($abs->p_num, $this->modified_ids)){
            $abs->p_num = $this->modified_ids[$abs->p_num];
            // debug($abs->p_num);die();
          }

          $conge = $this->getCongeFromId($abs->p_num);
          if(!$abs->isReplacing($conge)){
            $this->editAbs($abs,$conge);
            $abs->p_num ='';
          }
          else{
            if($abs->p_type==0){
              $this->delAbs($conge);
            }
          }
        }
      }
      if($abs->p_type != 0){ // Vérifie que ce n'est pas une suppression de congé via le TDS
        $this->saveAbs($abs,"ajout");
      }
      unset($_SESSION['tmpconges'][$k]);
    }
    if(in_array($abs->p_type, $this->session->read('isConge'))){
      $this->Mail->sendMail('mail_new_demande',$abs);
    }
    $this->session->setFlash('Vos congés ont été envoyés','success');
    if($action=="tds"){
      $this->redirect('/tds/edittds');
    }
    else{
      $this->redirect('/membres/logout');
    }
  }

   /**
    * Supprime tous les congés temporaires présent pour un l'utilisateur loggué
    * @param string $page Action qui suit l'effacement (retour au tds ou déconnexion)
    * @return none
    */
  public function erase($page){
    $this->session->delete('tmpconges');
    $this->session->write('tmpconges',array());
    if($page=='tds'){
      $this->redirect('/tds/edittds');
    }
    elseif ($page == 'logout') {
      $this->redirect('/membres/logout');
    }
  }
    
   /**
    * Page de demande de confirmation d'envoi des demandes
    * @param none
    * @return none
    */
  public function confirm(){
    $listing ="<ul>";
    $tmpconges = $this->session->read('tmpconges');
    $type_abs = $this->session->read('type_abs');
    foreach ( $tmpconges as $key => $abs) {
      $listing.= $abs->createListing($type_abs);
    }
    $listing .= "</ul>";
    $this->set('listing',$listing);
  }


  /**
   * Sauvegarde le nouveau congé dans la base
   * @param AbsenceObject
   * @return none
   */
  public function saveAbs($abs,$action){
      $this->loadModel('Queries');
      $this->Queries->table='conges_periode';
      $this->Queries->primaryKey='p_num';
      $d = $abs->obj2Arr();
      $this->Queries->save($d);
      Log::dbWrite($this->session->read('login'),$action,$abs);
  }

  /**
   * Edition d'un congé existant
   * @param AbsenceObject $abs congé modifiant
   * @param AbsenceObject $conge congé à éditer
   * @return none Congé modifié.
   */
  public function editAbs($abs,$c){
    $ferie = $this->cache->read('ferie');
    if($abs->isOverwriting($c)){
    // debug('je supprime');die();
      $this->delAbs($c);
    }
    elseif ($abs->p_date_deb >= $c->p_date_deb && $abs->p_date_fin <= $c->p_date_fin) {
      if($abs->p_date_deb==$c->p_date_deb){
        if ($abs->isFullDay()) { 
          if($c->p_demi_jour_deb=='pm'){
            $c->p_nb_jours = $c->p_nb_jours-0.5;
          }else{
            $c->p_nb_jours = $c->p_nb_jours-1;
          }
          $c->p_date_deb = getSideDate($c->p_date_deb,1);
          $c->p_demi_jour_deb='am';
        }

        elseif($abs->isMorning()){
          $c->p_demi_jour_deb = 'pm';
          $c->p_nb_jours = $c->p_nb_jours-0.5;
        }
        elseif($abs->isAfternoon()){
          if($c->p_demi_jour_deb=='pm'){
            $c->p_demi_jour_deb ='am';
            $c->p_nb_jours = $c->p_nb_jours-0.5;
            $c->p_date_deb = getSideDate($c->p_date_deb,1);
          }
          else{
            $c2 = new Absence($c);
            $c->p_date_fin = $abs->p_date_deb;
            $c->p_demi_jour_fin='am';
            $c2->p_date_deb = getSideDate($abs->p_date_deb,1);
            $c2->p_demi_jour_deb='am';
            $c2->p_num=null;
            $c->p_nb_jours = calcJours($c->p_date_deb,$c->p_demi_jour_deb, $c->p_date_fin, $c->p_demi_jour_fin,"false",$ferie);
            $c2->p_nb_jours = calcJours($c2->p_date_deb,$c2->p_demi_jour_deb, $c2->p_date_fin, $c2->p_demi_jour_fin,"false",$ferie);
          }
        }
      }
      elseif ($abs->p_date_deb==$c->p_date_fin) {
        if($abs->isFullDay()){
          if($c->p_demi_jour_fin=='am'){
            $c->p_nb_jours = $c->p_nb_jours-0.5;
          }else{
            $c->p_nb_jours = $c->p_nb_jours-1;
          }
          $c->p_date_fin = getSideDate($c->p_date_fin,-1);
          $c->p_demi_jour_fin='pm';
        }
        elseif ($abs->isAfternoon()) {
          $c->p_demi_jour_fin='am';
          $c->p_nb_jours = $c->p_nb_jours-0.5;  
        }
        elseif($abs->isMorning()){
          if($c->p_demi_jour_fin=='am'){
            $c->p_nb_jours = $c->p_nb_jours-0.5;
            $c->p_date_fin = getSideDate($c->p_date_fin,-1);
            $c->p_demi_jour_fin='pm';
          }
          else{
            $c2 = new Absence($c);
            $c->p_date_fin = getSideDate($abs->p_date_deb,-1);
            $c->p_demi_jour_fin='pm';
            $c2->p_date_deb = $abs->p_date_deb;
            $c2->p_demi_jour_deb='pm';
            $c2->p_num=null;
            $c->p_nb_jours = calcJours($c->p_date_deb,$c->p_demi_jour_deb, $c->p_date_fin, $c->p_demi_jour_fin,"false",$ferie);
            $c2->p_nb_jours = calcJours($c2->p_date_deb,$c2->p_demi_jour_deb, $c2->p_date_fin, $c2->p_demi_jour_fin,"false",$ferie);
          }
        }
      }
      else{
        $c2 = new Absence($c);
        if($abs->isFullDay()){
          $c->p_date_fin = getSideDate($abs->p_date_deb,-1);
          $c->p_demi_jour_fin='pm';
          $c2->p_date_deb = getSideDate($abs->p_date_fin,1);
          $c2->p_demi_jour_deb = 'am';
         }
        elseif ($abs->isMorning()) {
          $c->p_date_fin = getSideDate($abs->p_date_deb,-1);
          $c->p_demi_jour_fin='pm';
          $c2->p_date_deb = $abs->p_date_deb;
          $c2->p_demi_jour_deb='pm';
        }
        elseif ($abs->isAfternoon()) {
          $c->p_date_fin = $abs->p_date_deb;
          $c->p_demi_jour_fin='am';
          $c2->p_date_deb = getSideDate($abs->p_date_fin,1);
          $c2->p_demi_jour_deb = 'am';
        }
        else{
          if($abs->p_demi_jour_deb=='am'){
            $c->p_date_fin = getSideDate($abs->p_date_deb,-1);
            $c->p_demi_jour_fin='pm';
          }
          elseif($abs->p_demi_jour_deb=='pm'){
            $c->p_date_fin = $abs->p_date_deb;
            $c->p_demi_jour_fin='am';
          }
          if($abs->p_demi_jour_fin=='am'){
            $c2->p_date_deb = $abs->p_date_fin;
            $c2->p_demi_jour_deb='pm';
          }
          elseif($abs->p_demi_jour_fin=='pm'){
            $c2->p_date_deb = getSideDate($abs->p_date_fin,1);
            $c2->p_demi_jour_deb='am';
          }
        }
        $c->p_nb_jours = calcJours($c->p_date_deb,$c->p_demi_jour_deb, $c->p_date_fin, $c->p_demi_jour_fin,"false",$ferie);
        $c2->p_nb_jours = calcJours($c2->p_date_deb,$c2->p_demi_jour_deb, $c2->p_date_fin, $c2->p_demi_jour_fin,"false",$ferie);
        $c2->p_num = null;
      }
    }
    else{
      if(($abs->p_date_deb < $c->p_date_deb) && ($c->p_date_deb < $abs->p_date_fin) && $abs->p_date_fin <= $c->p_date_fin){
        if($abs->p_demi_jour_fin=='am'){
          $c->p_date_deb = $abs->p_date_fin;
          $c->p_demi_jour_deb='pm';
        }else{
          $c->p_date_deb = getSideDate($abs->p_date_fin,1);
          $c->p_demi_jour_deb='am';
        }
        $c->p_nb_jours = calcJours($c->p_date_deb,$c->p_demi_jour_deb, $c->p_date_fin, $c->p_demi_jour_fin,"false",$ferie);
      }
      elseif ($abs->p_date_fin > $c->p_date_fin && $c->p_date_deb<= $abs->p_date_deb && $abs->p_date_deb <=$c->p_date_fin) {
        if($abs->p_demi_jour_deb=='am'){
          $c->p_date_fin = getSideDate($abs->p_date_deb,-1);
          $c->p_demi_jour_fin='pm';
        }
        else{
        // debug('on est ici');die();
          $c->p_date_fin = $abs->p_date_deb;
          $c->p_demi_jour_fin='am';
        }
        $c->p_nb_jours = calcJours($c->p_date_deb,$c->p_demi_jour_deb, $c->p_date_fin, $c->p_demi_jour_fin,"false",$ferie);

      }

    }
    $this->saveAbs($c,"edition");
    if(isset($c2)){
      $this->saveAbs($c2,"ajout");
      // debug($c2->p_num);die();
      $this->modified_ids[$c->p_num] = $c2->p_num;
    }
  }  



  /**
   * Supprime un congé
   * @param AbsenceObject $c Congé à supprimer
   */
  public function delAbs($c){
    Log::dbWrite($this->session->read('login'),'suppression',$c);
    $this->Queries->table= "conges_periode";
    $this->Queries->primaryKey = 'p_num';
    $this->Queries->realdelete(intval($c->p_num));
  }

  /**
   * supprime le congé qui est remplacé par la nouvelle absence
   * @param AbsenceObjet $abs nouvelle absence
   */
  public function delCollisionAbs($abs){
    $this->loadModel('Queries');
    $this->Queries->table= "conges_periode";
    $this->Queries->primaryKey = 'p_num';
    if(is_array($abs->p_num)){
        foreach ($abs->p_num as $key => $v) {
          if($v!=''){
            $conge = $this->getCongeFromId($v);
            // $conge = $this->Queries->find('suivi',array("conditions"=>array('p_num'=>$v)),Model::FETCH_ONE||Model::FETCH_OBJ);
            Log::dbWrite($this->session->read('login'),'suppression',$conge);
            $this->Queries->realdelete($v);
          }
        }
      $abs->p_num=null;
    }
    return $abs;
  }

  /**
   * Récupère un congé par son id
   * @param int $id Id du congé a récuperer
   * @return Object Congé
   */
  public function getCongeFromId($id){
    // $this->loadModel('Queries');
    $c = $this->Queries->find('suivi',array("conditions"=>array('p_num'=>$id)),Model::FETCH_ONE|Model::FETCH_OBJ);
    $conge = new Absence($c);
    return $conge;
  }

  /**
   * Ecrit le log en fonction du type d'action
   * @param $action action effectuée (connexion,déconnexion, demande, saisie, edition,suppression)
   * @param $conge congé soumis à l'action
   */
  public function writeLog($action,$conge=null){
      $now =new dateTime();            
      $now = $now->format('Y-m-d H:i:s');
      $this->loadModel('Queries');
      if($conge->p_num == '' ){
        $conge->p_num = $this->returnId($conge);
      }
      // $msg = 'num '.$p_num.' (type '.$conge->p_type.') pour '.$conge->p_login.' ('.$conge->p_nb_jours.' jour(s)) (de '.$conge->p_date_deb.' '.$conge->p_demi_jour_deb.' à '.$conge->p_date_fin.' '.$conge->p_demi_jour_fin.')';
      $msg = $conge->infoLog();
      if($action == "ajout"){
        if(in_array($conge->p_type, $_SESSION['isConge'])){
          $comment = "Demande de congés";
        }else{          
          $comment = "Saisie de congés";
        }
      }
      elseif ($action=="edition") {
        $comment = "Modification de demande";
      }
      elseif($action == "suppression"){
        $comment = "Suppression de demande num ".$conge->p_num;
        $msg ='';
      }
      $user = $this->session->read('login');
        $d = array(
                'log_p_num'=>$conge->p_num,
                'log_user_login_par'=>$user,
                'log_user_login_pour'=>$user,
                'log_etat'=>$conge->p_etat,
                'log_comment'=>$comment.' '.$msg,
                'log_date'=>$now
                );
        $this->Queries->table = 'conges_logs';
        $this->Queries->primaryKey = 'log_id'; 
        $this->Queries->save($d);
        // $this->redirect($str_args);
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

}
