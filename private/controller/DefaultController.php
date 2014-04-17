<?php

class DefaultController extends Controller{

    public function index(){
        // $this->layouts=array('menu','main','default');        
        $this->redirect('tds/edittds');
        //$this->layouts=array('menu2','main','default');
    }
   
    public function adm_index(){
        
    }

    
}
?>