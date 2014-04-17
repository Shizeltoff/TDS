<?php 

class Panier {
	
	private $session;
	private $id;

	function __construct($session){
		$this->session = $session;
	}

	public function create(){
		if(!$this->session->read('panier')) {
			$this->session->write('panier', array(
				'verrou' => false,
				'absences' => array()
			));
		}
		return true;
	}

	public function addConges($absence){
		array_push($_SESSION['panier']['absences'], $absence);
	}

	public function deleteConge($id){
		unset($_SESSION['panier']['absences'][$id]);
	}
	
};
?>