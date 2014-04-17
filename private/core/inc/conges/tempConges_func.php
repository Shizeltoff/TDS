<?php 

	/**
	 * Retourne les congés temporaires existant dans la période définie
	 * @param array $conges_temp
	 * @param string $lundi début de la période
	 * @param string $vendredi fin de la période
	 * @return array Tableau contenant les congés temporaire compris dans la période
	 */
	function getPeriodTempConges($conges_temp,$lundi,$vendredi){
		$tmp_conges =array();
		foreach ($conges_temp as $k => $v) {
			if(($v->p_date_deb <= $vendredi) && ($v->p_date_fin >= $lundi)){
				array_push($tmp_conges, $v);
			}
		}
		return $tmp_conges;
	}

  /** 
   * Créer l'info bulle correspondante à un congé.
   * @param object $conge Congé  
   * @return html infobulle construite
   */
  function setTooltip($conge){
      $infobulle='<p>';
      if($conge->p_type!=0){
        $infobulle .= '<b>'.ucwords($_SESSION['type_abs'][$conge->p_type]->ta_libelle).' - '.$conge->p_nb_jours.' jrs</b>';
      }
      if($conge->p_commentaire != ''){
        $infobulle .= '<br>'.hsc($conge->p_commentaire);
      }
      $infobulle .= '<br>Du '.textDate($conge->p_date_deb).' '.textDemi($conge->p_demi_jour_deb,'court');
      $infobulle .= '<br>Au '.textDate($conge->p_date_fin).' '.textDemi($conge->p_demi_jour_fin,'court');
      $infobulle .= '</p>';
      return $infobulle;
  }

  /**
    * Creer le listing des congés/absences temporaires demandés.
    * @param int Timestamp de la semaine en cours de vue.
    * @return string Liste sous forme ul>li avec tous les congés temporaires.
    **/   
  function createListe($timestamp){
    $listing ="";      
    $conges = $_SESSION['tmpconges'];
    if(!empty($conges)){
      foreach ($conges as $key => $value) {
        $listing.=createInfoAbs($value,$timestamp);
      } 
    }
    return $listing;   
 }

  /**
    * Creer l'info du congé demandé
    * @param object $abs congé dont on veut l'information
    * @param int $nb_jours
    * @param int $new_id
    * @return string  
    */
  function createInfoAbs($abs,$timestamp){
    $lien = '<a class="right" href="'.Router::url('/temp/delTmpConge/'.$abs->detail().'/'.$timestamp).'">Supprimer</a></li>';
    $small_abs = str_replace("</li>", $lien, $abs->createListing($_SESSION['type_abs']));
    return $small_abs;
  }

  /**
   * Retourne une date à +- le delta de la date donnée
   * @param string $date date de référence
   * @param int $delta 
   * @return string 
   */
  function getSideDate($date,$delta){
    //$new = DateTime::createFromFormat('Y-m-d',$date);
		$new = new DateTime($date);
		//$new->setTime(0,0,0);
    $new->modify($delta.' day');
    return $new->format('Y-m-d');
  }

  /**
   * Fonction de callback pour l'appel à usort() dans ValidationController/Validate
   * Permet de trier les absences par date de début croissante.
   */
  function compareAbs($a,$b){
    if($a->p_date_deb == $b->p_date_deb){
      if($a->p_demi_jour_deb =='am'){
        return -1;
      }else{
        return 1;
      }
    }
    elseif($a->p_date_deb < $b->p_date_deb){
      return -1;
    }
    else{
      return 1;
    }
  }
	
if(!function_exists('date_diff')){
	function date_diff($date1, $date2){
		$difference = $date2->format('U') - $date1->format('U');
		return $difference;
		//return gmdate('H:i:s',$difference);
	}
}
?>