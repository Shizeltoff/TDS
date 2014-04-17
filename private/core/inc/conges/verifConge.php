<?php 

	/**
	 * 
	 * @param
	 * @return
	 */
	function VerifConge($date_deb,$demi_deb, $date_fin, $demi_fin){
		$date1 = new DateTime($date_deb);
		$date2 = new DateTime($date_fin);
		$infos_date_deb = explode('-',date("y-W",strtotime($date_deb)));
		$infos_date_fin = explode('-',date("y-W", strtotime($date_fin)));
		$year_deb = $infos_date_deb[0];
		$week_deb = $infos_date_deb[1];
		$year_fin = $infos_date_fin[0];
		$week_fin = $infos_date_fin[1];
		$interval =  date_diff($date1, $date2);
		$interval = $interval->format('%R%a');
		$dif = floatval($interval);
		if ($demi_deb == $demi_fin) {
			$dif = $dif+0.5;
		} else {
			$dif = $dif+1;
		}
		$week_diff =$week_fin-$week_deb;
		if($week_diff>0){
			// $week_diff<2 ? $dif = $dif - (2*$week_diff) : $dif = $dif-(3*$week_diff);
			$dif = $dif - (2*$week_diff);
		}
		return $dif;
	}


?>