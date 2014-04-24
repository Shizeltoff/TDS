<?php
class MonthController extends Controller{


	/**
	 * Affichage de la vue mensuelle
	 * @param int $month timestamp du mois à afficher
	 */
	public function index($month=null){
		$this->layouts = array('main','default');
		if($month==null){
			$month = getCurrentTimestamp();
		}
		$table = $this->createMonthTable($month);
		$this->set('table',$table);
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

	/**
	 * Créer la vue mensuelle d'un mois donné. 
	 * @param int $month timestamp du mois demandé
	 * @return array 
	 */
	public function createMonthTable($month){
		$table =array();
        $types = $this->cache->read('type_abs');
		$tmp =returnMonthDays($month);
		$table['mois'] = $tmp['mois'];
		$jours = $tmp['dates'];
		$nb_jours = $tmp['nb_jours'];
		$table['month_detail'] = '<th></th>';
		foreach ($tmp['jours'] as $key => $value) {
				$table['month_detail'] .= '<td>'.$value.' '.$key.'</td>';
		}
		$table['all'] = '';
		foreach ($this->session->read('group_logins') as $l => $n) {
			$conges = $this->getUserConges($l,$month);
	        $params = fillDays($conges,$jours,$types,$this->cache->read('ferie'));
	        $classe = $params['classe'];
	        // $ids = $params['ids'];
	        // $etat = $params['etat'];
			$table['all'] .= '<tr><th scope="row">'.strtoupper($n).'</th>';
	        $bulle = $params['bulles'];
			foreach ($jours as $key => $value) {
				$table['all'] .='<td><div class="'.$classe[$key].'">'.$bulle[$key].'</div></td>';
			}
			$table['all'] .= '</tr>';
		}
		return $table;
	}
}