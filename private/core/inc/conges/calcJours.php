<?php 
	/**
   * Calcule le nombres de jours pris
   * @param string $date_deb date de début
   * @param string $demi_deb matinée de début
   * @param string $date_fin date de fin 
   * @param string $demi_fin matinée de fin
   * @param string $joff (true ou false)
   * @param array $feries Talbeau des jours féries
   * @return int
   */
  function calcJours($date_deb,$demi_deb, $date_fin, $demi_fin,$joff,$feries)
	{
        $dif=0;
        $date1 = new DateTime($date_deb);
        $date2 = new DateTime($date_fin);
        $infos_date_deb = explode('-',date("y-W",strtotime($date_deb)));
        $infos_date_fin = explode('-',date("y-W", strtotime($date_fin)));
        $year_deb = $infos_date_deb[0];
        $week_deb = $infos_date_deb[1];
        $year_fin = $infos_date_fin[0];
        $week_fin = $infos_date_fin[1];
        $interval =  date_diff($date1, $date2);
        // $dif=$interval/(3600*24);               // en prod
        $interval = $interval->format('%R%a');   // en dev
        $dif = floatval($interval);              // en dev
        if($dif >= 0){
          if ($demi_deb =="am") {
              if($demi_fin=="am"){
                  $dif +=0.5;
              }else{
                  $dif +=1;
              }        
          }else{
              if($demi_fin=="pm"){
                   $dif += 0.5;
              }
          }
      
          $week_diff =$week_fin-$week_deb;
          if($joff=="false"){
            if($week_diff>0) { 
              $dif = $dif- $week_diff*2;
            }
          }
          else{
            if($week_diff == 0){
              $dif = $dif-1;
            }
            elseif($week_diff>0){
                $dif = $dif - $week_diff*3 - 1 ;  
            }
          }   

          // test si un jour férié existe dans la période.
          $dates = getDatesBetween($date_deb,$date_fin);
              foreach ($dates as $k => $d) {
                if(in_array($d, $feries)){
                  $dif = $dif-1;
                }
              }
          if($dif<0){
            $dif=0;
          }
        }
		return $dif;
	}
?>