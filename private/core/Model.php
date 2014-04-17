<?php
class Model{
	public static $_db;
	public $db;
	public $table;
	public $primaryKey = 'id';
	public $id;
	public $queries=array();

	const FETCH_ONE=1; 			//Retourne 1 seul résultat
	const FETCH_ARRAY=2;		//Retourne le résultat sous forme de tableau
	const FETCH_OBJ=4;			//Retourne le résultat sous forme d'objet
	

	public function __construct(){
		
		$conf= Conf::$database[Conf::$useddb];
		if(!isset(Model::$_db)){
			try{
				$pdo= new PDO('mysql:host='.$conf['host'].';dbname='.$conf['dbname'].';',$conf['user'],$conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				Model::$_db = $pdo;
				$this->db = $pdo;
			}
			catch (PDOException $e){
				if (Conf::DEBUG > 0 ) {
					$msg = 'Erreur PDO dans '. $e->getFile() .' L.' . $e->getLine() . ' : ' . $e->getMessage();
					die($msg);
				}
				else{
					die('<h1>SITE TEMPORAIREMENT INDISPONIBLE</h1>');
					throw $e;
				}
			}
		}
		else 
		{
			$this->db = Model::$_db;
		}
		if (!isset($this->table)){
			$this->table = strtolower(plural(get_class($this)));
			//$this->table = strtolower(get_class($this));
		}
		if(!isset($this->primaryKey)){
			$this->primaryKey = strtolower(get_class($this)).'_id';
		}
	}
	
	/**
	 * Suppression définitive d'un utilisateur.
	 * @param unknown_type $id  id de l'utilisateur à dévalider
	 * @return bool succès/echec de la suppresion.
	 */
	public function realdelete($id){
		
		$sql='delete from '.$this->table.' where '.$this->primaryKey.'= :id';
		$prep= $this->db->prepare($sql);
		return $prep->execute(array('id'=>$id));
	}
	
	/**
	 * Suppression non définitive d'un utilisateur.
	 * @param unknown_type $id id de l'utilisateur à dévalider
	 * @return bool succès/echec de la dévalidation de l'utilisateur
         */
	public function delete($id){
			
		$sql='update '.$this->table.'set valid=0 where '.$this->primaryKey.'= :id';
		$prep= $this->db->prepare($sql);
		return $prep->execute(array('id'=>$id));
	}
	
	/**
	 * Execution d'une recherche en base sql
	 * @param string $query Nom de la requête du model à exectuer
	 * @param array $params tableau des éléments de tri (conditions, ordres)
	 * @param unknown_type $fetchstyle type d'élement retourné (par défaut OBJET)
	 */
	public function find($query,$params=array(),$fetchstyle=self::FETCH_OBJ){
		
		if(!isset($this->queries[$query]))
		{
			return false;
		}
		$sql=$this->queries[$query];
		$values=array();
		if(isset($params['conditions'])){
			$sql .=' where ';
			if(!is_array($params['conditions'])){
				$sql .= $params['conditions'];
			}else{
				$conds =array();
				foreach ($params['conditions'] as $k => $v) {
					if(is_numeric($k)){
						$conds[]=$v;
					}else{
						// $param =array_pop(explode('.',$k));
						$preparam = explode('.',$k);
						$param = array_pop($preparam);
						if(!is_array($v)){
							$conds[]="$k=:$param";
							$values[$param]=$v;
						}
						else{
							if($v[0] =='between' && count($v)==3){
								// $conds[]="$k {$v[0]}:$param1 and :$param2";
								$conds[]="$k {$v[0]} '{$v[1]}' and '{$v[2]}'";
							}
							else{
								$conds[]="$k{$v[0]}:$param";
								$values[$param]=$v[1];
							}
						}
					}
				}
				$sql .=implode(' and ', $conds);
			}
		}
		
		if(isset($params['order'])){
			$sql .= ' order by ';
			if(!is_array($params['order'])){
				$sql .= $params['order'];
			}else{
				$sql .= implode(', ', $params['order']);
			}
		}
		if(isset($params['sens'])){
			if(!is_array($params['sens'])){
				$sql .= ' '.$params['sens'];
			}else{
				$sql .= implode(', ', $params['sens']);
			}
		}
		if(isset($params['limit'])){
			$sql .= ' limit ';
			if(!is_array($params['limit'])){
				$sql .= $params['limit'];
			}else{
				$sql .= implode(', ', $params['limit']);
			}
		}
		
		try{
			$prep = $this->db->prepare($sql);
			// debug($prep);die();
			$prep->execute($values);
		}
		catch (PDOException $e){
			debug($e);
			return false;
		}
		
		if ($fetchstyle & self::FETCH_ARRAY){
			$prep->setFetchMode(PDO::FETCH_ASSOC);
		}
		else{
			$prep->setFetchMode(PDO::FETCH_OBJ);
		}
		if ($fetchstyle & self::FETCH_ONE){
			return $prep->fetch();
		}
		else{
			return $prep->fetchAll();
		}
	}

	/**
	 * Ecriture dans la base de données
	 * @param array $data tableau des données à sauvegarder (de type clé,valeur)
	 */
	public function save($data){

		$key = $this->primaryKey;
		$fields = array();
		$d = array();
		$keys= array();

		if(is_array($key)){
			foreach ($key as $k => $v) {
					$keys[]="$v=:$v";
				}	
			foreach ($data as $k => $v) {
				if (!in_array($k, $key)){
					$fields[]="$k=:$k";
					$d[":$k"]=$v;
				}
			}
			$lastid=false;
			$flag=0;
//			if(!in_array(array_values($key),$data))
			foreach ($key as $k => $v) {	
				if(!empty($data[$v])){
					$flag+=1;
				}
			}
			if($flag>0){
				
				$sql='update '.$this->table.' set '. implode(',', $fields).' where '. implode(' and ', $keys) ;
				foreach ($key as $k => $v) {
					$d[":$v"] = $data[$v];

				}
			}else{
				$sql = 'insert into '. $this->table .' set '. implode(',',$fields);
				
				debug($sql);die();
			}
		}
		else{	
			foreach ($data as $k => $v) {
				if ($k != $key){
					$fields[]="$k=:$k";
					$d[":$k"]=$v;
				}
			}
			$lastid=false;
			// if(!empty($data->$key)){		//Test si la clé primaire existe et est non nulle.
			if(!empty($data[$key])){		//Test si la clé primaire existe et est non nulle.
				$sql='update '.$this->table.' set '. implode(',', $fields).' where '.$key.'=:'.$key ;
				/*$this->id = $data->$key;
				$d[":$key"]=$data->$key;*/
				$this->id = $data[$key];
				$d[":$key"] = $data[$key];
			}else{
				/*if(isset ($data->$key)){
					unset($data->$key);
				}*/
				$sql = 'insert into '. $this->table .' set '. implode(',',$fields);
				$lastid= true;
			}
		}
		
		try{
			$prep=$this->db->prepare($sql);
			$prep->execute($d);
		}
		catch(PDOException $e){
			if(Conf::DEBUG > 0) {
				debug($e);
			}
			return false;
		}
		
		$r= $prep->errorInfo();
		if ($lastid){
			$this->id = $this->db->lastInsertId();
		}
		return $r[0]==='00000';
	}
	
	/**
	 * Chargement du modèle associé au controleur
	 * @param $name modèle à charger
	 */
	public static function autoload($name){
		require MODEL_PATH.DS.$name.'.php';
	}
	
	/**
	 *
	 *
	 *
	 */
	public function validates($data, $scheme='default') {
		$errors = array();
		if(empty($this->validate[$scheme])) { return true; }
		foreach($this->validate[$scheme] as $k => $v) {
			  if(!isset($data->$k)) {
				$errors[$k] = $v['message'];
			  } else {
				if($v['rule'] == 'notEmpty') {
				  if(empty($data->$k)) {
					$errors[$k] = $v['message'];
				  }
				} elseif($v['rule'] == 'numeric') {
				  if(!is_numeric($data->$k)) {
					$errors[$k] = $v['message'];
				  }
				} elseif($v['rule'] == 'positiveInt') {
				  if( (!is_numeric($data->$k)) || ($data->$k < 1) ) {
					$errors[$k] = $v['message'];
				  }
				} elseif($v['rule'] == 'date') {
				  if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data->$k)) {
					$errors[$k] = $v['message'];
				  } else {
					try {
					  $dt = new DateTime($data->$k);
					} catch(Exception $e) {
					  $errors[$k] = $v['message'];
					}
				  }
				} elseif($v['rule'] == 'datetime') {
				  try {
					$dt = new DateTime($data->$k);
				  } catch(Exception $e) {
					$errors[$k] = $v['message'];
				  }
				} elseif($v['rule'] == 'regexp') {
				  if(!preg_match($v['regexp'], $data->$k)) {
					$errors[$k] = $v['message'];
				  }
				} // Et ainsi de suite avec tous les validateurs
			  }
			}
			$this->errors += $errors;
			if(isset($this->form)) {
			  $this->form->errors += $errors;
			}
			return empty($errors);
		}
};
	
spl_autoload_register(array('Model','autoload'));

