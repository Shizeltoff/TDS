<?php

Class Dispatcher{

	public $request;

	public function __construct(){
		
		$this->request= new Request;
		Router::parse($this->request->url,$this->request);
		// debug($this->request);
		$controller = $this->loadController();
		$action  = str_replace('_', '', str_replace(' ', '', ucwords(str_replace('-',' ','_'.$this->request->action))));
			if ($this->request->prefix) {
				$action = $this->request->prefix.'_'.$action;
			}
			try{
				$method = new ReflectionMethod($controller, $action);
			} catch(ReflectionException $e) {
				# Si la méthode n'existe pas, une exception est levée
				$this->error('Le controller '.$this->request->controller.' n\'a pas de méthode '.$action);
			}
			# On teste si la méthode est publique et si elle est déclarée par le contrôleur enfant. Fonctionne même si la méthode est une surcharge d'une méthode parente.
			$class = $method->getDeclaringClass();
			if($class->name != get_class($controller) || !$method->isPublic()) {
				$this->error('Le controller '.$this->request->controller.' ne déclare pas de méthode '.$action.' publique.');
			}
			# Récupération du nombre d'arguments requis
			$numargs = $method->getNumberOfRequiredParameters();
			if($numargs > count($this->request->params)) {
				$this->error("Nombre d'arguments insuffisant");
			}
			# Appel de la méthode avec les arguments sous forme de tableau
			$method->invokeArgs($controller, $this->request->params);
			$controller->render($action);
		}

	/**
	 * Affichage d'un message d'erreur.
	 * @param string $message  message d'erreur
	 */
	public function error ($message =''){
	
		$controller = new Controller($this->request);
		$controller->e404($message);
	}

	/**
	 *Charge le controleur demandé dans l'url
	 */
	public function loadController(){
		$name = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->request->controller))).'Controller';
		$file = PRIVATE_PATH.DS.'controller'.DS.$name.'.php';
		if(!file_exists($file)){
			$this->error('Pas de controleur <i><b>'.$this->request->controller.'</b></i>');
		}else{
			require($file);
			$controller = new $name($this->request);
			return $controller;
		}
	}

}