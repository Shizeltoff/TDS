<?php

Class Controller{

	public $request;
	private $vars = array();
	private $rendered = false;
	public $layouts = array('menu2','main','default');
	public $form;
	public $mail;
	protected $cache;

	public function __construct($request){
		$this->session = new Session;
		$this->request = $request;
		$this->form = new Form($this);
		$this->cache = new Cache(CACHE_PATH,43200);
		$this->mail = new Mail;
		include (CONFIG_PATH.DS.'hook.php');
	}


	/**
	 * Appel à la vue pour la gestion des erreurs 404.
	 * @param string $message  - message d'erreur.
	 */
	public function e404 ($message){
		header('HTTP/1.0 404 NOT FOUND');
		if(Conf::DEBUG>0){
				$this->set('message',$message);		
		}else{
				$this->set('message','');		
		}
		$this->render('/errors/404');
		exit();
	}

	/**
	 * Appel à la vue pour la gestion des erreurs 403.
	 * @param string $message  - message d'erreur.
	 */
	public function e403 ($message){
		header('HTTP/1.0 403 FORBIDDEN');
		$this->render('/errors/403');
		exit();
	}

	/**
	 * Appel à la vue pour la gestion des erreurs 401.
	 * @param string $message  - message d'erreur.
	 */
	public function e401 ($message){
		header('HTTP/1.0 401 UNAUTHORIZED');
		$this->render('/errors/401');
		exit();
	}	
	/**
 	 *function loadModel($name)
 	 *Chargement du model associé au controlleur.
 	 *@param string $name - nom du modèle.
 	 */
	public function loadModel($name){
		if(!isset($this->$name)){
			$file = MODEL_PATH.DS.$name.'.php';
			require_once $file;
			$this->$name = new $name;
			if(isset($this->form)){
				$this->$name->form = $this->form;
			}
		}
		return $this->$name;
		
	}

	/**
	 * function redirect ($url, $code=0)
	 * Redirection d'url.
	 * $url url de redirection.
	 * $code code d'erreur associé à la redirection.
	 */
	public function redirect($url, $code=0){
		header('location: '.Router::url($url));
		exit();
	}

	/**
	 * Rend la vue associée au ctrl appelé.
	 * @param string $view - vue associée au ctrl.
	 */
	public function render($view){

		if($this->rendered){
			return false;
		}
		extract($this->vars);
		if(strpos($view,'/')===0){
			$view = PRIVATE_PATH.DS.'view'.$view.'.php';
		}else{
			$view = PRIVATE_PATH.DS.'view'.DS.$this->request->controller.DS.$view.'.php';
		}

		ob_start();
		if(!file_exists($view)){
			$this->e404('Pas de vue associee');
		}else{
			header('ContentType: text/html, charset=utf-8');
			require $view;
			$layoutContent=ob_get_clean();
			foreach ($this->layouts as $layout) {
				ob_start();
				require PRIVATE_PATH.DS.'view'.DS.'layout'.DS.$layout.'.php';
				$layoutContent = ob_get_clean();
			}
		echo $layoutContent;
		$this->rendered=true;
		}
	}

	/**
	 * Positionne les variables du controleur dans la vue.
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function set($key,$value=null){

		if(is_array($key)){
			$this->vars+= $key;
		}else{
			$this->vars[$key]=$value;
		}
	}
}
