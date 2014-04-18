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
		$jour = 'Les limites du mois en cours sont : '.$limits[0] .' '.$limits[1]; 
		$this->set('mois',$jour);
	}

	public function getUserConges($login,$month){
		$limits = getMonthLimits();
		$first=$limits[0];
		$last=$limits[1];
		$this->loadModel('Queries');
		// $this->Queries->find('');
	}
}