<?php

setcookie(LC_TIME,'fra');
date_default_timezone_set('Europe/Paris');
error_reporting(E_ALL);

define("LN",PHP_EOL);
define("DS", DIRECTORY_SEPARATOR);

define("WEBROOT", dirname(__FILE__));    //nom du fichier courant.
define("ROOT", dirname(WEBROOT));    
define("PRIVATE_PATH", ROOT.DS.'private');
define("CONFIG_PATH", PRIVATE_PATH.DS.'config');
define("CORE_PATH", PRIVATE_PATH.DS.'core');
define("MODEL_PATH", PRIVATE_PATH.DS.'model');
$base =dirname($_SERVER["SCRIPT_NAME"]);
define ("BASE_URL",$base == DS ? '' : $base);
// define ("BASE_URL",'/TDS/public');
define("CACHE_PATH",PRIVATE_PATH.DS.'cache');
include (CORE_PATH.DS.'includes.php');
include (CONFIG_PATH.DS.'Conf.php');

//try{
//	new Dispatcher;
//}catch(PDO exception e){
//	// à traîter
//}
new Dispatcher;
