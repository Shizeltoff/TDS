<?php
class MembresController extends Controller{
    
    private $ldap_con;

    /**
     * Rendu de la vue login
     */
    public  function login(){
        $this->layouts = array('login_page','default');
        $args = func_get_args();
        $str_args = implode('/',$args);
        $this->FillCache();
        if($this->request->data){
            $this->loadModel('Queries');
            $login = $this->request->data->login;
            $passw = $this->request->data->password;
            if(!Conf::USE_LDAP){
                $hashed = hashPwd($passw);
                $usr = $this->Queries->find(
                    'default',
                    array('conditions'=>array('u_login'=>$login,'u_passwd'=>$hashed)),
                    Model::FETCH_ONE||Model::FETCH_OBJ
                );
                if(empty($usr)){
                    $msg = "Login ou mot de passe incorrect ! ";
                    $this->session->setFlash($msg,'error');
                    $this->redirect('membres/login');  
                }
                else{
                    $this->collectData($usr);
                    Log::dbWrite($usr->u_login,'login');

                    $this->redirect($str_args);
                }
            }
            else{
                // Connexion LDAP
                $this->ldap_con = new AuthLDAP;
                $this->ldap_con->connect();
                if(!$this->ldap_con->searchUser($login)){
                    $msg = "Login inconnu ! ";
                    $this->session->setFlash($msg,'error');
                    $this->redirect('membres/login');
                }
                else{
                    if(!$this->ldap_con->bindUser($passw)){
                    $msg = "Mot de passe incorrect ! ";
                    $this->session->setFlash($msg,'error');
                    $this->redirect('membres/login');
                    }
                    else{
                        $usr = $this->Queries->find(
                            'default',
                            array('conditions'=>array('u_login'=>$login)),
                            Model::FETCH_ONE||Model::FETCH_OBJ
                        );
                        if(empty($usr)){
                            $msg = "Utilisateur inconnu dans la base de PHP_CONGES ! ";
                            $this->session->setFlash($msg,'error');
                            $this->redirect('membres/login');
                        }
                        else{
                            $this->collectData($usr);
                            // $this->writeLog('login');
                            Log::dbWrite($usr->u_login,'login');
                            $this->redirect($str_args);
                        }
                    }
                }
            }
        }   
        $this->set('referer',$str_args);
    }
    

    /**
     * Fonction de déconnexion utilisateur
     */    
    public function logout() {
        $temp = $this->session->read('tmpconges');
        if(!empty($temp)){
            $this->redirect('/validation/confirm');
        }
        else{ 
            $msg = "Déconnexion réussie ! ";
            $this->session->setFlash($msg,'success');    
            Log::dbWrite($this->session->read('login'),'logout');
            // foreach ($_SESSION as $key => $value) {
            //     $this->session->delete($key);
            // }
            session_destroy();
            $this->redirect('membres/login');
        }
    }
    
    /**
     * Rendu de la vue définition d'un jour off
     */
    public function defJoff(){
        $this->set('layoutTitle','Définir un jour off périodique');        
        if($this->request->data){
            if($this->request->data->datedeb=="" || $this->request->data->datefin==""){
                $msg = "Attention ! Erreur au niveau des dates";
                $this->session->setFlash($msg,'error');
                $this->redirect('/membres/defJoff');
            }
            else{
                $this->loadModel('Queries');
                $joursoff = $this->Queries->find('joff',
                        array('conditions'=>array('p_type'=>6,'p_commentaire'=>Conf::JOFF)),
                        Model::FETCH_OBJ
                    );
                if(!empty($joursoff)){
                    $msg = "Vous avez déjà défini des jours off pour cette période";
                    $this->session->setFlash($msg,'error');
                    $this->redirect('/membres/defJoff');                    
                }
                else{
                    $now =date('Y-m-d H:i:s');            
                    if($this->request->data->mat1 == 'all'){
                        $mat1_deb = 'am';
                        $mat1_fin = 'pm';
                        $date1 =  setJoffDays($this->request->data->datedeb,$this->request->data->datefin,$this->request->data->joff1);
                        $interval = 1;
                        $date2=array();
                    }
                    else{
                        $mat1_deb = $this->request->data->mat1;
                        $mat1_fin = $mat1_deb;
                        $mat2_deb = $this->request->data->mat2;
                        $date1 =  setJoffDays($this->request->data->datedeb,$this->request->data->datefin,$this->request->data->joff1);
                        $date2 =  setJoffDays($this->request->data->datedeb,$this->request->data->datefin,$this->request->data->joff2);
                        $interval= 0.5;
                    }   
                    $type = '6'; // jour off -> requete pour récupérer le n° du type d'absence ?
                    // $etat = 'ok';   
                    $now =new dateTime();            
                    $now = $now->format('Y-m-d H:i:s');
                    $num = null;
                    $commentaire = CONF::JOFF;
                    $this->loadModel('Queries');
                    $this->Queries->table='conges_periode';
                    $this->Queries->primaryKey='p_num';
                    $login = $this->session->read('login');
                    foreach ($date1 as $k => $v) {
                        $conge = new Absence($login,$v,$mat1_deb,$v,$mat1_fin,$type,$interval,$commentaire,'ok',$now,$num);
                        $d = $conge->obj2Arr();
                        $this->Queries->save($d);
                        Log::dbWrite($this->session->read('login'),'ajout',$conge);
                    }
                    $msg = 'Votre jour off a bien été défini.';
                    if(!empty($date2)){  
                        foreach ($date2 as $k => $v) {
                            $conge = new Absence($login,$v,$mat2_deb,$v,$mat2_fin,$type,$interval,$commentaire,'ok',$now,$num);
                            $d = $conge->obj2Arr();
                            $this->Queries->save($d);
                            Log::dbWrite($this->session->read('login'),'ajout',$conge);
                        }
                            $msg = 'Vos deux demis journées off ont bien été définies.';
                    }
                    $this->session->setFlash($msg,'success');
                    $this->redirect('/default/index');
                }
            }
        }
    }

    /**
     *Collecte des informations sur l'utilisateur connecté
     *nom, prenom, login, groupe, responsable, groupe sous responsabilité
     *membres du même groupe,
     **/
    public function collectData($usr){
        $this->loadModel('Queries');
        // En Prod
        $usr->u_email = $usr->u_login."@aviation-civile.gouv.fr";
        $this->session->write('user',$usr);
        $login = $usr->u_login;
        $this->session->write('login',$login);

    /*Recupération du groupe de l'utilisateur */

        $groupe = $this->Queries->find(
            'user_group',
            array('conditions'=>array('gu_login'=>$login)),
            Model::FETCH_ONE|Model::FETCH_OBJ
            );
        $this->session->write('user_group',$groupe->gu_gid);

    /*Recupération des membres du groupe de l'utilisateur */
        $users = $this->Queries->find(
            'group_users',
            array(
                'conditions'=>array(
                    'gu_gid'=> $this->session->read('user_group'),
                    'gu_login'=>array('<>', $this->session->read('login'))
                ),
                'order'=>'gu_login'
            ),
            Model::FETCH_OBJ
        );
        $logins = [$login => $usr->u_nom];
        foreach ($users as $k => $u) {
            $logins[$u->u_login] = $u->u_nom;
        }
        $this->session->write('group_logins', $logins);
        $this->session->write('group_users', $users);

    /*  Recupération des adresses mail des responsables de l'utilisateur */
        $resps = $this->Queries->find('responsables',
                                      array('conditions'=>array('gr_gid'=>$this->session->read('user_group'))),
                                      Model::FETCH_OBJ);
        foreach ($resps as $key => $r) {
            if($r->gr_login=="ange.balliano"){
                $r->u_email = "ange.balliano-balestra@aviation-civile.gouv.fr" ;
            }
            else{
                $r->u_email = $r->gr_login.'@aviation-civile.gouv.fr';
            }

        }
        $this->session->write('reponsables',$resps);


    /*  Récupération du nom du groupe auquel appartient l'utilisateur  */
        $gr = $this->cache->read('sections');
        $usrgrp = $this->session->read('user_group');
        if(array_key_exists($usrgrp, $gr)){
            $this->session->write('user_group_name',$gr[$usrgrp]);
        }
        else{
            $this->session->write('user_group_name',"Pas d'entité définie");
        }
        // $gr = $this->Queries->find('sections',
        //                            array('conditions'=>array('g_gid'=>$this->session->read('user_group'))),
        //                            Model::FETCH_OBJ||Model::FETCH_ONE);
        // if(!empty($gr)){
        //     $this->session->write('user_group_name',$gr->g_groupename);
        // }else{
        //     $this->session->write('user_group_name',"Pas d'entité définie");
        // }

        /*************************************************************/
        /***     Récupération des infos congés de l'utilisateur    ***/

        $solde = $this->Queries->find(
                    'solde_user',
                    array('conditions'=>array('su_login'=>$login)),
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
    
    /*Recupération des types d'absences */
        $type = $this->Queries->find('type_abs');
        $_SESSION['isConge']=array();
        if(!empty($type)){
            foreach ($type as $k => $v) {
                $new_typabs[$v->ta_id] = $v;
				if($v->ta_id == 6){
                    $new_typabs[$v->ta_id]->ta_libelle = "Jour OFF";
                }
                $type_abs[$v->ta_id] =strtolower($v->ta_short_libelle);
                if($v->ta_type=="conges"){
                    array_push($_SESSION['isConge'], $v->ta_id);
                }
            }
            $this->session->write('type_abs',$new_typabs);
            if(!$this->cache->read('type_abs') || $this->cache->isExpired('type_abs')){
                $this->cache->write('type_abs',$type_abs);
            }   

        }

    /* Récupération des jours feriés si le fichier de cache n'existe pas.*/
        if(!$this->cache->read('ferie') || $this->cache->isExpired('ferie')){
        // if(!$this->cache->read('ferie')){
            $jours = $this->Queries->find('ferie');
            if(!empty($jours)){
                $feries=array();
                foreach ($jours as $key => $value) {
                    array_push($feries, $value->jf_date);
                }
                $this->cache->write('ferie',$feries);
            }    
        }

    /* Initialisation de la table de session pour les congés temporaires */
        $this->session->write('tmpconges',array());

    /* Récupération des en-têtes de mail */
        $headers = $this->Queries->find('headers');
        $this->session->write('mail_headers',$headers);

    /* Fin de la fonction collectDatas()    */
    }



    public function FillCache(){
        $this->loadModel('Queries');
        /* Récupération des jours feriés si le fichier de cache n'existe pas.*/
        if(!$this->cache->read('ferie') || $this->cache->isExpired('ferie')){
        // if(!$this->cache->read('ferie')){
            $jours = $this->Queries->find('ferie');
            if(!empty($jours)){
                $feries=array();
                foreach ($jours as $key => $value) {
                    array_push($feries, $value->jf_date);
                }
                $this->cache->write('ferie',$feries);
            }    
        }

        if(!$this->cache->read('sections') || $this->cache->isExpired('sections')){
            $sec = $this->Queries->find('sections');
            if(!empty($sec)){
                foreach ($sec as $key => $value) {
                    $sections[$value->g_gid] = $value->g_groupename ;
                }
            }
            $this->cache->write('sections',$sections);
        }

    }
}
