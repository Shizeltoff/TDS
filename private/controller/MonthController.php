<?php
class MonthController extends Controller{


	/**
	 * Affichage de la vue mensuelle
	 * @param int $month timestamp du mois à afficher
	 */
	public function index($month=null){
		if($month==null){
			$month = getCurrentTimestamp();
		}
		// $limits = getMonthLimits($month);
		// // debug(returnMonthDays($month));die();
		// $jour = 'Les limites du mois en cours sont : '.$limits[0] .' '.$limits[1]; 
		// $this->getUserConges('guigui',$month);
		$table = $this->createMonthTable();
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


	public function createMonthTable(){
		$table =array();
		$table['mois'] = '<th></th>';
		$table['user'] = '<th scope="row">PALLIET</th>';
		for ($i=1; $i <=35 ; $i++) { 
			$table['mois'] .= '<td>'.$i.'</td>';
			$table['user'] .='<td>'.$i.'</td>';
		}
		return $table;
	}
}