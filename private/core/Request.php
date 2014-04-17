<?php

Class Request{
	
	public $url;
	public $controller = false;
	public $action = false;
	public $data = false;
	public $params = array();
	public $prefix = false;

	public function __construct(){
		$this->url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
		//$this->url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
		if(!empty($_POST)){
			$this->data = new stdClass;
			foreach($_POST as $k => $v){
				$this->data->$k = $v;
			}
		}
	}
}

