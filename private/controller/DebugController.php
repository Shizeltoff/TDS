<?php
class DebugController extends Controller{

	public function index(){
	    $session = $this->session->read();
	    $this->set('tab',$session);
	    if(isset($this->request->data->variable)){
			$this->set('nom',$this->request->data->variable);
			$this->set('data',$this->session->read($this->request->data->variable));
	    }
	    else{
	      $this->set('nom','Variable de session a afficher');
	      $this->set('data','Sélectionner une variable a afficher'); 
	    }
	}
  
	public function testRequete(){
        $date_deb = '2013-07-01';
        $date_fin = '2013-07-05';
        $this->loadModel('Queries');
        $test = $this->Queries->find(
            'suivi',
            array('conditions'=>array('p_date_deb'=>array('between',$date_deb,$date_fin),
            						   'p_login'=>'pierre'))
        );
		$this->set('requete',$test);
	}


   // public function createListing($conges){
   public function createListing(){
      $listing ="";
      $conges = $this->session->read('tmpconges');
      if(!empty($conges)){
        foreach ($conges as $key => $value) {
          $listing.=$this->createInfoAbs($value);
        } 
        return $listing;   
      }
      else{
        return "";
      }
   }

	public function errormsg(){
		}
}