<?php
class CalendarController extends Controller{

	/**
	 * Affichage de la vue au mois
	 */
	public function monthView($month=null){
		if($month === null){
          $month = getCurrentTimestamp();
        }
        else{
           $month=intval($month);
        }
		$this->layouts = array('main','default');
		$this->set('table',$this->createMonthTable(setMonth($month)));
	}
	
	public function createMonthTable($month){

		$table['user'] = '<tr><th scope="row">'.strtoupper($this->session->read('user')->u_nom).'</th>';

		return $table;
	}


	public function exportIcal(){
		$titre = "Exporter vos congés au format ics";
		$this->set('titre',$titre);
		$this->set('layoutTitle',$titre);
		$annees = getYears();
		$this->set('annees',$annees);
		$this->set('current_year',date('Y'));

		if($this->request->data){
			$bornes = getYearLimits($annees[$this->request->data->year]);
			$this->loadModel('Queries');
			$conges = $this->Queries->find(
                    'suivi',
                    array('conditions'=>array(
                                             'p_login'=>$this->session->read('login'),
                                             'p_date_deb'=>array('>',$bornes['first']),
                                             'p_date_fin'=>array('<',$bornes['last'])),
                    ),
                    Model::FETCH_OBJ
    		);
    		if(empty($conges)){
				$msg = "Vous n'avez pas de congés pour l'année sélectionnée.";
    			$this->session->setFlash($msg,'error');
    			$this->redirect('/calendar/exportVcal');
    		}else{
    			$user = $this->session->read('user');
    			$type_abs = $this->session->read('type_abs');
    			$filename = 'exportICS_'.$user->u_nom.'_'.$annees[$this->request->data->year].'.ics';
    			$txt = "BEGIN:VCALENDAR\r\n";
    			$txt .= "PRODID:-//TDS 1.0.1\r\n";
				$txt .= "VERSION:2.0\r\n";
    			if(!file_exists($filename)){
    				$file = fopen($filename, 'w+');
    			}
    			foreach ($conges as $k => $c) {
    				foreach ($type_abs as $t => $a) {
    					if($a->ta_id ==$c->p_type){
    						$type = $a->ta_libelle;
    					}
    				}
    				$now = $this->dateToCal(time());
    				$now = substr($now,0,8);
    				$now .= 'System/Localtime';
    				$dates =$this->formatIcalDate($c);
					$txt .= "\r\nBEGIN:VEVENT";
					$txt .= "\r\nDTSTAMP:".$now;
					$txt .= "\r\nORGANIZER;CN=".$user->u_login.":MAILTO:";
					$txt .= "\r\nCREATED:".$now;
					$txt .= "\r\nUID:TDS";
					$txt .= "\r\nSEQUENCE:0";
					$txt .= "\r\nLAST-MODIFIED:".$now;
					$txt .= "\r\nSUMMARY:".$type;
					$txt .= "\r\nCLASS:PUBLIC";
					$txt .= "\r\nPRIORITY:1";
					$txt .= "\r\nDTSTART:".$dates['start'];
					$txt .= "\r\nDTEND:".$dates['end'];
					$txt .= "\r\nTRANSP:OPAQUE";
					$txt .= "\r\nEND:VEVENT\r\n";
    			}
				$txt .= "\r\nEND:VCALENDAR";
				file_put_contents($filename, $txt);
				$this->redirect('/calendar/export/'.$filename);
    		}
		}
	}

	public function export($filename){
		$this->layouts=array();
		if(file_exists($filename)){
			$taille = filesize($filename);
			header('Content-type: application/ics');
			header('Content-length: '.$taille);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			readfile($filename);
			unlink($filename);
		}	
		else{
			$msg = "Une erreur est survenue lors de l'export.Contacter l'administrateur";
			$this->session->setFlash($msg,'error');
			$this->redirect('/calendar/exportIcal');
		}
	}

	/**
	 * Formate les dates de début et de fin d'un congé pour l'export ical
	 * @param object $abs Congé 
	 * @return array
	 */
	public function formatIcalDate($abs){
		$dstart = explode('-',$abs->p_date_deb);
		$dend = explode('-',$abs->p_date_fin);
		if($abs->p_demi_jour_deb =="am"){
			$dates['start'] = $dstart[0].$dstart[1].$dstart[2].'T083000Z';
		}
		else{
			$dates['start'] = $dstart[0].$dstart[1].$dstart[2].'T130000Z';
		}
		if($abs->p_demi_jour_fin =="am"){
			$dates['end'] = $dend[0].$dend[1].$dend[2].'T130000Z';
		}
		else{
			$dates['end'] = $dend[0].$dend[1].$dend[2].'T173000Z';
		}
		return $dates;
	}

	/**
	 * Formate la date en fonction du timestamp
	 */
	public function dateToCal($timestamp) {
		return date('Ymd\THis\Z', $timestamp);
	}
	 
	// Escapes a string of characters

	/**
	 *
	 */
	public function escapeString($string) {
		return preg_replace('/([\,;])/','\\\$1', $string);
	}
	
}