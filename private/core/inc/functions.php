<?php
    /**
     * Affichage d'une variable
     * @param unknown_type $v
     * @return affichage de la variable
     */ 
    function debug($v){
  		if(Conf::DEBUG >0){    
  			$backtrace = debug_backtrace();
  			$firstbt = array_shift($backtrace);
  			echo '<div id="debug">';
  			echo '<p>'.$firstbt['file'].' ligne '.$firstbt['line'].' fonction '.$firstbt['function'].'</p>';
  			if(!empty($backtrace)){
  			echo '<ol class="debug">';
  			foreach($backtrace as $noeud=>$item){
  				if(isset($item['file'])){
  				echo '<li>'.$item['file'].' ligne '.$item['line'].'</li>';
  				}
  				else{
  				echo'<li>'.$item['function'].'</li>';
  				}
  			}
  			echo '</ol>';
  			}
  			echo "</div>";
  			echo "<pre>";
  			if($v === true){echo '<i>TRUE</i>';}
  			elseif($v === false){echo '<i>FALSE</i>';}
  			elseif($v === null){echo '<i>NULL</i>';}
  			else{
  				print_r($v);	
  			}
  			echo "</pre>";
  		}
    }
	
	/**
	 * Hashage du password
	 * @param string $str Mot de passe a hasher
	 * @return int mot de passe hashé avec les SELs 
	 */
	function hashPwd($str){
		//return sha1(Conf::SEL1.$str.Conf::SEL2);
		return md5($str);
	}
  /**
   * Met au pluriel un mot
   * @param string $str
   * @return string chaine au pluriel
   */ 
  function plural($str){
  	return $str."s";
  	}

  /**
   * transforme une chaine de caractère en code hexa
   * @param string $string
   * @return hex @hex
   */
  function strToHex($string){
      $hex='';
      for ($i=0; $i < strlen($string); $i++)
      {
          $hex .= dechex(ord($string[$i]));
      }
      return $hex;
  }

  /**
   * Echappe les caractères spéciaux
   * @param string $chaine chaine à échapper
   * @return string Chaine avec tous les caractères spéciaux échappés
   */
  function hsc($chaine){
   return htmlspecialchars($chaine);
  }
  
  /**
   * Tronque la chaine de caractère 
   * @param string $chaine
   * @return string Chaine de caractère tronquée 
   */    
  function wrap($chaine){
    $max_chars = 35;
    if(strlen($chaine)>$max_chars){
      $chaine = substr($chaine, 0, $max_chars-3);
      // $space_pos = strrpos($chaine, " ");
      // $chaine = substr($chaine, 0, $space_pos-3);
      $chaine .= "...";
    }
    return $chaine;
  }
  
  /**
   * Met la date en format littéraire
   * @param string $date
   * @return string date réécrite
   */
  function textDate($date){
      $d = explode('-',$date);

      $small_mois=array(
              "01"=>"Jan",
              "02"=>"Fév",
              "03"=>"Mar",
              "04"=>"Avr",
              "05"=>"Mai",
              "06"=>"Jui",
              "07"=>"Jui",
              "08"=>"Aou",
              "09"=>"Sep",
              "10"=>"Oct",
              "11"=>"Nov",
              "12"=>"Déc",
                  );
      
      if(strlen($d[2])==4){
        //affichage de la notification après ajout d'une nouvelle absence
        $str =  $d[0].' '.$small_mois[$d[1]].' '.$d[2];
      }
      else{
        // affichage dans le tableau de suivi
        $str =  $d[2].' '.$small_mois[$d[1]].' '.$d[0];
      }
      return $str;
  }

  function toSql($date){
    $d = explode('-',$date);
    return $d[2].'-'.$d[1].'-'.$d[0];
  }

  /**
   * Transpose "am" et "pm" en toutes lettres
   * @param string $demi
   * @return string 
   */
  function textDemi($demi,$taille = "long"){
      if($taille=="long"){
        $libelle_demi = array("am"=>"matin","pm"=>"après-midi");
      }
      else{
        $libelle_demi = array("am"=>"mat","pm"=>"apm");

      }
      return $libelle_demi[$demi];
  }
  /**
   * Met la date dans le bon ordre
   * @param string $date
   * @return string 
   */
  function formatDate($date){
      return implode('-',array_reverse(explode('-',$date)));
  }

  /**
   * Fonction setWeek avec le mktime
   */
  function setWeek($tmstp){
      $timestamp = GetMondayTimestamp($tmstp);
      $semaine = setDays($timestamp,5);
      $semaine['sem'] = date('W',$timestamp);
      $semaine['tmstp'] = $timestamp;
      $semaine['moisannee'] = getWeekDescription($timestamp);

      return $semaine;
  }

  /**
    * Retourne l'intitulé de la semaine en cours au format MOIS ANNEE
    * @param timestamp $mondayTmstp Timestamp du premier jour de la semaine affichée.
    * @return string Chaine de caractère donnant le(s) mois et l'année de la semaine affichée
    **/
  function getWeekDescription($mondayTmstp){
    $mois_num=array(
          "01"=>"Janvier",
          "02"=>"Février",
          "03"=>"Mars",
          "04"=>"Avril",
          "05"=>"Mai",
          "06"=>"Juin",
          "07"=>"Juillet",
          "08"=>"Aout",
          "09"=>"Septembre",
          "10"=>"Octobre",
          "11"=>"Novembre",
          "12"=>"Décembre",
      );
    $friday = GetFridayTimestamp($mondayTmstp);
    $mois_lun = date('m',$mondayTmstp);
    $mois_ven = date('m',$friday);
    $year_lun = date('Y',$mondayTmstp);
    $year_ven = date('Y',$friday);
    if($mois_lun != $mois_ven){
      if($year_lun!=$year_ven){
        return $mois_num[$mois_lun].' '.$year_lun.' / '.$mois_num[$mois_ven].' '.$year_ven;
      }
      else{
        return $mois_num[$mois_lun].' / '.$mois_num[$mois_ven].' '.$year_lun;
      }
    }
    else{
      return $mois_num[$mois_lun].' '.$year_lun;
    }
  }

  /**
   * Retourne la liste des jours depuis le timestamp de début, et pour un nombre de jours défini
   * @param timestamp $starttimestamp Timestamp du premier jour de la période demandée.
   * @param int $maxdays Nombre de jours 
   * @return
   **/
  function setDays($starttimestamp,$maxdays = 5){
      $day = date('d',$starttimestamp);
      $month = date('m',$starttimestamp);
      $year = date('Y',$starttimestamp);
      $lodays = array('Mon'=>'lun','Tue'=>'mar','Wed'=>'mer','Thu'=>'jeu','Fri'=>'ven');
      $shdays = array('Mon'=>'lu','Tue'=>'ma','Wed'=>'me','Thu'=>'je','Fri'=>'ve');
      $days = array();
      $today = getCurrentTimestamp();
      $d = date('d',$today);
      $m = date('m',$today);
      $y = date('Y',$today);
      $t = mktime(0,0,0,$m,$d,$y);
      $days['today']='';
      for ($i=0; $i < $maxdays ; $i++) {  
          $mktime = mktime(0,0,0,$month,$day+$i,$year);
          $days[$lodays[date('D',$mktime)]] = date('Y-m-d', $mktime);
          $days[$shdays[date('D',$mktime)]] = date('d', $mktime);
          if($mktime == $t){
            $days['today']=$shdays[date('D',$mktime)];
          }
      }
      return $days;
  }
  /**
    * Retourne la semaine courante
    * @return string N° de la semaine courante
    */
  function getCurrentWeek(){
    $current = new DateTime();
    $current = $current->format('W');
    return $current;
  }

  /**
    * Retourne le timestamp de la semaine courante
    * @return string N° de la semaine courante
    */
  function getCurrentTimestamp(){
    return time();
  }


  /**
   * Retourne le jour courant au format MySql
   * @return string jour au format Y-m-d h:i:s
   */
  function getCurrentDay(){
    $current = new DateTime();
    $current = $current->format('Y-m-d H:i:s');
    return $current;   
  }

  /**
   * Retourne le premier et dernier jour du mois.
   * @param string  $month numéro du mois
   * @return array Tableau avec date début et date fin du mois. 
   */
  function getMonthLimits($tmstp){
    $month = date('m',$tmstp);
    $year = date('Y',$tmstp);
    $first = date('Y-m-d',mktime(0,0,0,$month,1,$year));
    $last = date('Y-m-d',mktime(0,0,0,$month+1,1,$year)-1);
    return [$first,$last];
  }
  /**
   * Rempli un tableau de dates
   * @param string $jour
   * @return array $dates - tableau de tous les $jour de l'année
   */
  function getJoffDate($datedeb,$datefin,$jour){
    $dates=array();
  	$days= array(
  		     'lu'=>'monday',
  		     'ma'=>'tuesday',
  		     'me'=>'wednesday',
  		     'je'=>'thursday',
  		     've'=>'friday'
  		     );
      	$i=0;
  	$first = new DateTime;
  	$last = new DateTime;
  	$joff = new DateTime;
  	foreach($days as $sd=>$ld){
  	    if($jour == $sd){
  		$first->modify('last '.$ld);
  		$last->modify('last '.$ld.' of december');
  		while($joff->format("Y-m-d") != $last->format("Y-m-d")){
  		    $joff->modify($first->format("Y-m-d").'+'.$i.' week');
  		    array_push($dates,$joff->format("Y-m-d"));
  		    $i++;
  		}
  	    }
  	}
      return $dates;
  }

  /**
   * Rempli un tableau des dates de jour off dans la periode définie.
   * @param string $datedeb Date définissant le début de la période
   * @param string $datefin Date définissant la fin de la période
   * @param string $jour Jour demandé
   * @return array $dates - tableau de tous les $jour de l'année
   */
  function setJoffDays($datedeb,$datefin,$jour){
      $dates=array();
      $days= array('lu','ma','me','je','ve');
      // $deb = DateTime::createFromFormat('Y-m-d',$datedeb);
      $deb = new DateTime($datedeb);
      // $tmstplundi = GetMondayTimestamp($deb->getTimestamp());
      $tmstplundi = GetMondayTimestamp(strtotime($datedeb));
      $day = date('d',$tmstplundi);
      $month = date('m',$tmstplundi);
      $year = date('Y',$tmstplundi);
      foreach ($days as $key => $value) {
          if($jour==$value){
              $premier = date('Y-m-d',mktime(0,0,0,$month,$day+$key,$year));
              $tmstp = mktime(0,0,0,$month,$day+$key,$year);
          }
      }
      $fin =new DateTime($datefin);
      // $fin = DateTime::createFromFormat('Y-m-d',$datefin);
      while($premier <= $fin->format('Y-m-d')){
          array_push($dates,$premier);
          $day = date('d',$tmstp);
          $month = date('m',$tmstp);
          $year = date('Y',$tmstp);
          $premier = date('Y-m-d',mktime(0,0,0,$month,$day+7,$year));
          $tmstp = mktime(0,0,0,$month,$day+7,$year);
      }
      return $dates;
  }
  
  /**
   * Retourne le timestamp du lundi de la semaine passée en paramètre
   * @param timestamp $timestamp Timestamp d'un jour d'une semaine.
   * @return timestamp Timestamp du lundi de la même semaine que le jour donné.
   */
  function GetMondayTimestamp($timestamp){
      $jour = date('N',$timestamp); //retourne le numéro du jour 1=lundi
      if($jour>1){
          $lundi = $jour;
          $day = date('d',$timestamp);
          $month = date('m',$timestamp);
          $year = date('Y',$timestamp);
          $i=1;
          while($lundi>1){
              $jourmoinsn = mktime(0,0,0,$month,$day-$i,$year);
              $lundi = date('N',$jourmoinsn);
              $i++;
          }
          $timestamp = $jourmoinsn;
      }
      return $timestamp;
  }

  /**
   * Retourne le timestamp du vendredi de la semaine qui inclue le timestamp
   * @param timestamp $monTmstp Timestamp du lundi de la semaine.
   * @return timestamp Timestamp du vendredi de la même semaine.
   */
  function GetFridayTimestamp($monTmstp){
    $day = date('d',$monTmstp);
    $month = date('m',$monTmstp);
    $year = date('Y',$monTmstp);
    return mktime(0,0,0,$month,$day+4,$year);
  }

  /**
   * Retourne le 1er janvier et le 31 décembre d'une année donnée
   * @param string $year
   * @return array $annee tableau a 2 éléments : first, last
   */
  function getYearLimits($year){
    $annee=array();
    $annee['first'] = $year.'-01-01';
    $annee['last'] = $year.'-12-31';
    return $annee;
  }
  
  /**
   * Retourne l'année de la date passée en paramètre
   * @param string $date Date
   * @return string Année de la date 
   */
  function getYear($date){
      return substr($date,0,4);
      // return substr($date, -4);
  }

  /**
   * Retourne un tableau d'années de 2013(date de création de l'outil) à l'année courante
   * @return array Tableau des années
   */
  function getYears(){
  	$annees = array();
  	$current = date('Y');
  	$i='2013'; //Première année
  	while($i<=strval($current+1)){
  	    array_push($annees,strval($i));
  	    $i++;
  	}
  	return $annees;
  }
   
  /**
   * Récupère toutes les dates comprises entre 2 dates
   * @param str $sdate date de début
   * @param str $edate date de fin
   * @return array $dates tableau des dates comprises dans l'intervalle
   */
  function getDatesBetween($sdate,$edate){
      $dates = array();
      $start = new DateTime($sdate);
      $end = new DateTime($edate);
      $interval =  date_diff($start, $end);
      $interval=$interval->days/(3600*24); //Dev
			// $interval=$interval/(3600*24); //prod
      //$interval = $interval->format('%R%a');
      $tmp = new DateTime();
      for ($i=0; $i <= $interval ; $i++) { 
          $tmp->modify($sdate.' + '.$i.' day');
          array_push($dates,$tmp->format('Y-m-d') );
      }
      return $dates;
  }

  /**
   * Récupère les jours off à poser automatiquement
   * Appeler si prise en compte du jour off dans le décompte des jours d'un congé.
   * @param object $conge
   * @return array 
   */
  function getJoffDays($conge){
    $dates=array();
    return $dates;
  }



  function isPast($conge){
      $now = new DateTime('now +2 days');
      $fin = new DateTIme($conge->p_date_fin);
      if($now > $fin){
          return true;
      }
      else{
          return false;
      }
  }