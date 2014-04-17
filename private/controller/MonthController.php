<?php
class MonthController extends Controller{


	/**
	 * Affichage de la vue mensuelle
	 * @param string $month Numéro du mois à afficher
	 */
	public function index($month=null){
		if($month==null){
			$month = getCurrentMonth();
		}
		// $debut = ;
		// $fin = ; 
		// $month = $debut +' '+ $fin; 
		$this->set('mois',$month);
	}

	public function getUserConges($login,$month){
		$limits = getMonthLimits();

	}
}