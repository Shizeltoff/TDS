<?php
class Conf{
	
	const DEBUG=1;
	const USE_LDAP=0;
	const SMTP ="mail.lfpo.aviation-civile.gouv.fr";
	/* Definition de la page de redirection*/
	const DEFAULTCTRL = 'tds';
	const DEFAULTMETH = 'edittds';
	/**/
	const JOFF='Defini automatiquement';
	const BODY_ANNUL="__SENDER_NAME__ a sollicité une demande d'annulation d'un congé dans l'application de gestion des congés.\n\r
type = __TYPE_ABSENCE__   du __DATE_DEBUT__ au __DATE_FIN__ . 
Merci de consulter votre application php_conges : __URL_ACCUEIL_CONGES__\n\r
-------------------------------------------------------------------------------------------------------\n\r
Ceci est un message automatique.";

	// const SEL1 = 'wsdfgù';
	// const SEL2 = 'mùk_k';
	
	public static $database=array(
		
/********* A REMPLIR ************/
		'online'=>array(
			'host'=>'localhost',
			'dbname'=>'phpconges',
			'user'=>'phpconges',
			'password'=>'!phpconges!'
		),
		'local'=>array(
			'host'=>'localhost',
			'dbname'=>'dbconges',
			'user'=>'root',
			'password'=>'')
	);
	public static $useddb='local';

	public static $ldapconfig = array(
		'port' => 389,
		'server' => "ldap://ldapds.lfpo.aviation-civile.gouv.fr",	// ldap://172.16.89.247:389
		'protocol_version' => 3 ,   // 3 si version 3 , 0 sinon !
		'bupsvr' => "",
		'config_basedn' => "sn=ldap-crna-n, sn=n, sn=internes_si ,dc=aviation-civile, dc=gouv, dc=fr",
		//'config_basedn'      => "sn=t,sn=crna-n,sn=sna-rp,sn=do,sn=dsna,sn=dgac,sn=organigramme,sn=applications ,dc=aviation-civile, dc=gouv, dc=fr" ,
		'ldap_user'   => "uidnumber=******, sn=ldap-crna-n, sn=n, sn=internes_si ,dc=aviation-civile, dc=gouv, dc=fr" ,
		'ldap_pass'   => "motdepasse",
		'searchdn'    => "sn=ldap-crna-n, sn=n, sn=internes_si ,dc=aviation-civile, dc=gouv, dc=fr",
		//'searchdn'    => "sn=t,sn=crna-n, sn=sna-rp, sn=do, sn=dsna, sn=dgac, sn=organigramme, sn=applications, dc=aviation-civile, dc=gouv, dc=fr",
		'ldap_prenom' => "givenname", 
		'ldap_nom'    => "sn",
		'ldap_mail'   => "mail",
		'ldap_login'  => "uid",
		'ldap_nomaff' => "cn",
		'ldap_filtre' => "objectClass",
		'ldap_filrech'=> "angeliqueUser",
		//'ldap_filtre_complet' => "(|(sn=st,sn=crna-n*))",   
		//'ldap_filtre_complet' => "(|(cn=ODA))",
		'ldap_filtre_complet' => ""
	);
};


/**
 * Configuring log...
 */

Log::accept(Log::CORE, Log::INFO);
Log::accept(Log::DATABASE, Log::ERROR);


// Router::prefix('adm','guigui');
Router::prefix('usr','member');
Router::prefix('ajax','ajax');


Router::connect('calendrier','tds/edittds');
Router::connect('vue-mensuelle/:mois', 'month/index/mois:([0-9]+)-([0-9]+)');
Router::connect('vue-mensuelle', 'month/index');
Router::connect('nouvelle-absence','tds/newAbsence');
Router::connect('suivi-:type','suivi/suivi/type:([a-z]+)');
Router::connect('connexion','membres/login');
Router::connect('jour-off-periodique','membres/defJoff');
Router::connect('solde','suivi/solde');
Router::connect('export-ical','calendar/exportIcal');

?>