<?php
class MonthController extends Controller{

	private $authentificate = false;
	/**
	 * Affichage de la vue mensuelle
	 * @param string $month Mois à afficher mm-yyyy
	 * @param string $grp Groupe d'utilisateurs à afficher
	 */
	public function index($month=null, $grp="1"){
		if (isset($_SESSION['user'])) {
			$this->layouts = array('main','default');
			$users = $this->session->read('group_logins');
		}else{
			$this->layouts = array('offlinemain','default');
			$this->getAbsenceLabels();
			$users = $this->GetGroupUsers($grp);
		}
		$this->authentificate = true;
		$t = $this->GetMonthTimestamp($month);
		$table = $this->createMonthTable($t , $grp);
		$sections = $this->cache->read('sections');
		$this->set('sections',$sections);
		$this->set('grp',$grp);
		$this->set('table',$table);
	}

	/**
	 * Créer la vue mensuelle d'un mois donné. 
	 * @param int $month timestamp du mois demandé
	 * @param string $grp groupe des utilisateurs a afficher
	 * @return array 
	 */
	public function createMonthTable($month , $grp){
		$users = $this->GetGroupUsers($grp);
		$table =array();
        $types = $this->cache->read('type_abs');
		$tmp =returnMonthDays($month);
		$table['tmstp'] = $month;
		$table['mois'] = $tmp['mois'];
		$jours = $tmp['dates'];
		$nb_jours = $tmp['nb_jours'];
		$table['month_detail'] = '<th></th>';
		foreach ($tmp['jours'] as $key => $value) {
				$table['month_detail'] .= '<td>'.$value.' '.$key.'</td>';
		}
		$table['allusers'] = '';
		foreach ($users as $l => $n) {
			$conges = $this->getUserConges($l,$month);
	        $params = fillDays($conges,$jours,$types,$this->cache->read('ferie'));
	        $classe = $params['classe'];
	        // $ids = $params['ids'];
	        // $etat = $params['etat'];
			$table['allusers'] .= '<tr><th scope="row">'.strtoupper($n).'</th>';
	        $bulle = $params['bulles'];
			foreach ($jours as $key => $value) {
				$table['allusers'] .='<td><div class="'.$classe[$key].'">'.$bulle[$key].'</div></td>';
			}
			$table['allusers'] .= '</tr>';
		}
		$table['printline'] = 'month/printMonth/'.$month.'/'.$grp;
		return $table;
	}

	/*
	 * Recupère les congés d'un utilisateur donné.
	 * @param string $login login de l'utilisateur.
	 * @param int $month timestamp du mois à chercher
	 * @return array tableau d'objets Absences.
	 */
	public function getUserConges($login,$month){
		$limits = getMonthLimits($month);
		$first=$limits[0];
		$last=$limits[1];
		$this->loadModel('Queries');
        $conges = $this->Queries->find(
                'suivi',
                array("conditions"=>array(
                            'p_login' => $login,
                            'p_date_deb' => array('<=',$last),
                            'p_date_fin' => array('>=',$first),
                    )),
                Model::FETCH_OBJ
            );
        foreach ($conges as $k => $c) {
          if($c->p_etat != "ok" && $c->p_etat != "demande" ){
            unset($conges[$k]);
          }
        }
        return $conges;
	}

	/*
	 * Récupère les utilisateurs d'un groupe donné
	 * @param int $grp id du groupe a récupérer.
	 * @return array tableau des logins du groupe
	 */
	public function GetGroupUsers($grp){
		$users = array();
		$this->loadModel('Queries');
		$usrs = $this->Queries->find(
			'group_users',
			array("conditions"=>array('gu_gid'=>$grp)),
			Model::FETCH_OBJ
			);
		foreach ($usrs as $k => $v) {
			$users[$v->gu_login] = strtoupper($v->u_nom);
		}
		return $users;
	}

	public function GetMonthTimestamp($month){
		if($month==null){
			$t = getCurrentTimestamp();
		}else{
			$m = explode('-',$month);
			$t = mktime(0,0,0,$m[0],1,$m[1]);
		}
		return $t;
	}

	public function ajax_changeMonth(){
		$d = date('d',$_POST['timestamp']);
		$m = date('m',$_POST['timestamp']);
		$y = date('Y',$_POST['timestamp']);
		$this->set('data',mktime(0,0,0,$m + $_POST['month'],$d,$y));
	}

	public function ajax_createMonthTable(){
		//trouver le moyen de faire passer les utilisateurs ! 
		$m = $this->createMonthTable($_POST['month'],$_POST['groupe']);
        $this->set('data',$m);
	}


	public function getAbsenceLabels(){
		$this->loadModel('Queries');
		$type = $this->Queries->find('type_abs');
        if(!empty($type)){
            foreach ($type as $k => $v) {
                $new_typabs[$v->ta_id] = $v;
				if($v->ta_id == 6){
                    $new_typabs[$v->ta_id]->ta_libelle = "Jour OFF";
                }
            }
        }
        $this->session->write('type_abs',$new_typabs);
	}


	public function printMonth($month , $grp){
		$this->layouts=array('default');
		$sections = $this->cache->read('sections');
		$t = $this->createMonthTable($month,$grp);
		$this->set('nom_section',$sections[$grp]);
		$this->set('table',$t);
	}
}