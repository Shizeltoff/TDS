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
      	debug(setDays(getFirstDayOfMonth($month),31));die();
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
      	// $jours = getMonthDays($month);
        $types = $this->cache->read('type_abs');
        $params = fillDays($conges,$jours,$types,$this->cache->read('ferie'));
        // debug($params);die();
	}

	/**
	 * Créer la vue mensuelle d'un mois donné. 
	 * @param int $month timestamp du mois demandé
	 * @return array 
	 */
	public function createMonthTable($month){
		$table =array();
		$tmp =returnMonthDays($month);
		$table['mois'] = $tmp['mois'];
		$jours = $tmp['jours'];
		$shjours = $tmp['shjours'];
		$nb_jours = $tmp['nb_jours'];
		$table['descr_mois'] = '<th></th>';
		$table['user'] = '<th scope="row">PALLIET</th>';
		$table['others'] = '<tr><th scope="row">dude</th>';
		foreach ($shjours as $key => $value) {
			$table['descr_mois'] .= '<td>'.$value.' '.$key.'</td>';
			$table['user'] .='<td><div class="am_off pm_ca"></div></td>';
			$table['others'] .='<td><div class="am_taf pm_mi"></div></td>';
		}
		$table['user'] .='</tr>';
		$table['others'] .='</tr>';
		return $table;
	}
}