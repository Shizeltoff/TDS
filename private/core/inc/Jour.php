<?php  
class Jour extends DateTime{
	
	protected $type; 

	/**
	 * Met la date de l'instance au format MySQL.
	 * @return string Date au format MySQL
	 */
	public function to_mysql(){
		return $this->format('Y-m-d H:i:s');
	}

	/**
	 * Encode en utf-8 la date
	 * @param $format
	 * @return string
	 */
	public function strftime($format){
		return utf8_encode(strftime(utf8_decode($format),$this->getTimestamp()));
	}

	/**
	 * Défini l'état de l'instance Jour
	 * @param unknown_type $state
	 * @return void
	 */
	public function setState ($state){
		$this->type = $state;
	}
};
?>