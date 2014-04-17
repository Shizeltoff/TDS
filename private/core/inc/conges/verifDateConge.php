<?php 
	function verifDateConge($date_deb,$demi_deb, $date_fin, $demi_fin){
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
        $interval = $interval->format('%R%a');
        $dif = floatval($interval);
        if($dif>0){
        	return true;
        }
        elseif ($dif<0) {
        	return false;
        }
        else{
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

        	if ($dif==0 || $dif<0) {
        		return false;
        	}
        	else{
        		return true;
        	}
        }
	}
 ?>