<?php

Class Router{ 

	private static $prefixes = array();
	
	/**
	 * Analyse de l'url pour remplir la request
	 */ 
	public static function parse($url,$request){
		$url = trim($url, '/');
		$param = explode ('/',$url);
		if(array_key_exists($param[0],self::$prefixes)){
			$request->prefix = self::$prefixes[$param[0]];
			array_shift($param); 		//supprime la ligne du tableau et remonte les autres
		}
		$request->controller = !empty($param[0]) ? $param[0] : Conf::DEFAULTCTRL;
		$request->action = !empty($param[1]) ? $param[1] : Conf::DEFAULTMETH;
		$request->params = array_slice($param, 2);
		return True;
	}
	
	/**
	 * Formate l'url
	 * @param string $url url demandée sous la forme /controlleur/vue
	 * @return string url complète 
	 */
	public static function webroot($url){
		$url = trim($url, '/');
		return BASE_URL.'/'.$url;
	}
	
	/**
	 * Teste si l'url contient un préfixe défini (adm,...).
	 * @param string $url 
	 * @return string url transformée selon le préfixe
	 */
	public static function url ($url=''){
		$url = trim($url, '/');
		foreach(self::$prefixes as $k => $v){
			if (strpos($url,$v) === 0){ 	//On teste si l'url débute par un des prefixes.
				$url = str_replace($v,$k,$url);
			}
		}
		return BASE_URL.'/'.$url;

	}
	
	/**
	 *
	 * @param string $prefix
	 * @param string $url_prefix 
	 */
	public static function prefix($prefix,$url_prefix){
		self::$prefixes[$url_prefix]=$prefix;
	}
}
