<?php 

class Mail{

	public $session;
	public $headers=array();
	public $responsables = array();
	public $user;
	public $user_nomprenom;
	public $mail_headers;
	private $subject;
	private $body;
	private $conge;
	
	protected $erreur=false;


	public function __construct(){
		$this->session = new Session;
		$this->mailer = new PHPMailer();
	}

	/**
	 * Configure les données de mail. 
	 */
	public function configure(){
      $this->responsables = $this->session->read('reponsables');
      $this->user = $this->session->read('user');
      $this->headers = $this->session->read('mail_headers');

      // $this->mailer->SetLanguage('fr',"../phpmailer/language");
      $this->mailer->CharSet = 'UTF-8';
      $this->mailer->IsSMTP();
      $this->mailer->Host = Conf::SMTP;
      $this->mailer->Port = 25;

	}


	/**
	 * Fonction d'envoi du mail de demande ou d'annulation
	 * @param string $type_mail de demande (demande/annulation)
	 * @param int $id_conge Numéro du congé
	 */
	public function sendMail($type_mail,$abs){
		$this->getHeaders($type_mail);
		$this->replaceData($abs);
		$this->mailer->From = $this->user->u_email; 
		$this->mailer->FromName = $this->user_nomprenom;
		$this->mailer->Body = $this->body;
		$this->mailer->Subject = $this->subject;
		foreach ($this->responsables as $k => $v) {
			$this->mailer->AddAddress($v->u_email,ucwords($v->u_prenom.' '.$v->u_nom));
		}
		if(!$this->mailer->send()){
			debug($this->mailer->ErrorInfo);die();
		}else{
			return true;
		}
	}

	/**
	 * Récupère les headers de mail en base
	 * @param string $type_mail de demande (demande/annulation)
	 */
	public function getHeaders($type_mail ='mail_new_demande'){
		if($type_mail == 'mail_annulation_demande'){
			$this->subject = "APPLI CONGES - Demande d'annulation d'un congé";
			$this->body = Conf::BODY_ANNUL;
		}	
		else{
			$hd = $this->session->read('mail_headers');
			foreach ($hd as $k => $h) {
				if($h->mail_nom == $type_mail){
					$this->subject = $h->mail_subject;
					$this->body = $h->mail_body;
					$this->body = wordwrap($this->body, 70, "\r\n");
				}
				else{
					$this->erreur = true;
				}
			}
		}
	}

	/*
	 * Remplace les différents champs du corps du message par les valeurs adéquates
	 * @param obj $abs Congé 
	 */
	public function replaceData($abs){
		$type_abs = $this->session->read('type_abs');
		foreach ($type_abs as $key => $v) {
			if($v->ta_id == $abs->p_type){
				$libelle = $v->ta_libelle;
			}
		}
		$this->body = str_replace('__TYPE_ABSENCE__', $libelle, $this->body);
		$this->body = str_replace('__DATE_DEBUT__', formatDate($abs->p_date_deb), $this->body);
		$this->body = str_replace('__DATE_FIN__', formatDate($abs->p_date_fin), $this->body);
		$this->user = $this->session->read('user');
		$this->user_nomprenom =  $this->user->u_prenom.' '. $this->user->u_nom;
		$user_lname = $this->user->u_nom;
		$user_name = $this->user->u_prenom;
		$this->body = str_replace("__SENDER_NAME__", $user_name.' '.$user_lname, $this->body);
		$this->body = str_replace("__URL_ACCUEIL_CONGES__","http://phpconges.crnan", $this->body);
	}
}
