<?php 


class AuthLDAP{
	public $searchdn;
	public $basedn;
	public $ldap_server;
	public $ldap_server_backup;
	public $ldap_user;
	public $ldap_pass;
	public $ldap_group;
	public $ldap_login;
	public $user_login = "";
	public $user_password = "";
	public $user_auth  = 0;
  // liste des groupes autorisés
	public $auth_dn_groups;
  // liste des persones autorisées
	public $auth_users;
	public $id_connex;

	public function __construct(){
		$config = Conf::$ldapconfig;
		$this->ldap_server = $config['server'];
		$this->searchdn = $config['searchdn'];
	}

	/**
	 * Connexion au serveur LDAP 
	 * Récupération de l'identifiant de session
	 */
	public function connect(){
	    // $resource =ldap_connect($this->ldap_server) 
	    $this->id_connex =ldap_connect($this->ldap_server) 
	    			or die("Impossible de se connecter au serveur LDAP ".$this->ldap_server);
	    // return $resource;
  	}

  	/**
	 * Destruction de l'identifiant LDAP 
	 */
  	public function disconnect(){
  		ldap_close($this->id_connex);
  	}

  	/**
	 * Recherche d'un utilisateur dans l'annuaire LDAP par son login.
	 * @param string $login Utilisateur a rechercher.
 	 * @return bool 
	 */
  	public function searchUser($login){
		$search = ldap_search($this->id_connex, $this->searchdn,'uid='.$login);
    	$result = ldap_get_entries($this->id_connex, $search);
    	if($result['count']==0){
    		return false;
    	}
    	else{
    		$this->basedn = $result[0]['dn'];
    		return true;
    	}
  	}


  	/**
	 * Authentification de l'utilisateur avec son mot de passe.
 	 * @param string $password
 	 * @return bool 
	 */
  	public function bindUser($password){
  		if (!@ldap_bind($this->id_connex,$this->basedn,$password)){
  			return false;
  		}
  		else{
  			return true;
  		}

  	}

}; 

?>