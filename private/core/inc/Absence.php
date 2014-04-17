<?php

class Absence{
    
    public $p_login;
    public $p_date_deb;
    public $p_date_fin;
    public $p_demi_jour_deb;
    public $p_demi_jour_fin;
    public $p_nb_jours;
    public $p_commentaire=null;
    public $p_type;
    public $p_etat;
    public $p_edition_id = null;
    public $p_motif_refus = null;
    public $p_date_demande = null;
    public $p_date_traitement = null;
    public $p_fermeture_id = null;
    public $p_num;
    public $collision=false;


    public function __construct()
    {        
      $num = func_num_args();
      if($num != 0){
        if($num == 1){
          if(is_object(func_get_arg(0))){
            $this->_createFromObject(func_get_arg(0));
          } 
        }
        else{
          $this->p_login= func_get_arg(0);
          $this->p_date_deb= func_get_arg(1);
          $this->p_demi_jour_deb= func_get_arg(2);
          $this->p_date_fin= func_get_arg(3);
          $this->p_demi_jour_fin= func_get_arg(4);
          $this->p_type= func_get_arg(5);
          $this->p_nb_jours= func_get_arg(6);
          $this->p_commentaire= func_get_arg(7);
          $this->p_etat= func_get_arg(8);
          $this->p_date_demande= func_get_arg(9);
          $this->p_num = func_get_arg(10);
        }
      }
    }

    public function _createFromObject($c){
        $this->p_login= $c->p_login;
        $this->p_date_deb= $c->p_date_deb;
        $this->p_date_fin= $c->p_date_fin;
        $this->p_demi_jour_deb= $c->p_demi_jour_deb;
        $this->p_demi_jour_fin= $c->p_demi_jour_fin;
        $this->p_type= $c->p_type;
        $this->p_nb_jours= $c->p_nb_jours;
        $this->p_commentaire= $c->p_commentaire;
        $this->p_etat= $c->p_etat;
        $this->p_date_demande= $c->p_date_demande;
        $this->p_num = $c->p_num;
    }

    /**
     * Vérifie la collision entre le congé et un autre.
     * @param object $abs congé avec lequel on vérifie la collision
     * @return bool True si collision, False sinon
     */
    public function collide($abs){
            if(($this->p_date_deb==$abs->p_date_deb) && ($this->p_date_fin==$abs->p_date_fin) && ($this->p_demi_jour_deb==$abs->p_demi_jour_deb) && ($this->p_demi_jour_fin==$abs->p_demi_jour_fin)){
              $this->collision=true;
              return true;
            }
            else{
              $this->collision=false;
              return false;
            }
    }

    /**
     * Créer une ligne contenant toutes les informations d'un congé.
     * @param array $type_abs tableau contenant les types d'absence
     * @return HTMLCode Ligne <li> contenant les infos congés.
     */
    public function createListing($type_abs){
        if($this->p_type=="0"){
            $abs_libelle = "Présence";
        }
        else{
            foreach ($type_abs as $k => $v) {
                if($v->ta_id == $this->p_type){
                  $abs_libelle = $v->ta_libelle;
                }
            }
        }
        $listing = "<li> - ".$abs_libelle;
        $listing .= ' du '.textDate($this->p_date_deb).' '.textDemi($this->p_demi_jour_deb);
        $listing .= ' au '.textDate($this->p_date_fin).' '.textDemi($this->p_demi_jour_fin);
        $listing .= ' ('.$this->p_nb_jours.' jours)';
        $listing .= "</li>";
        return $listing;
    }


    /**
     * Transforme l'objet en tableau
     * @return array tableau corespondant à l'objet.
     */
    public function obj2Arr(){
        $d = array(
              'p_login' => $this->p_login,
              'p_date_deb' => $this->p_date_deb,
              'p_demi_jour_deb' => $this->p_demi_jour_deb,
              'p_date_fin' => $this->p_date_fin,
              'p_demi_jour_fin' => $this->p_demi_jour_fin,
              'p_type'=> $this->p_type,
              'p_nb_jours'=> $this->p_nb_jours,
              'p_commentaire'=> $this->p_commentaire,
              'p_etat' => $this->p_etat,
              'p_date_demande' => $this->p_date_demande,
              'p_num'=>$this->p_num
             );
        return $d;
    }


    /**
     * Retourne True si le congé est un jour complet, false si demi_journée
     * @return bool
     */
    public function isFullDay(){
      if(($this->p_date_deb==$this->p_date_fin)&&($this->p_demi_jour_fin=='pm' && $this->p_demi_jour_deb =='am')){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * Teste si l'absence est une demi-journée du matin
     * @return bool
     */
    public function isMorning(){
      if(($this->p_date_deb==$this->p_date_fin)&&($this->p_demi_jour_deb=="am")&&($this->p_demi_jour_fin=="am")){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * Teste si l'absence est une demi-journée de l'après-midi
     * @return bool
     */
    public function isAfternoon(){
      if(($this->p_date_deb==$this->p_date_fin)&&($this->p_demi_jour_deb=="pm")&&($this->p_demi_jour_fin=="pm")){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * Détermine si le congé est le même jour que l'absence déjà présente.
     * @param AbsenceObject $c 
     * @return bool
     */
    public function isSameDay($c){
      if($this->p_date_deb == $c->p_date_deb && $this->p_date_fin == $c->p_date_fin){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * Créer une chaine de caractère a ajouter dans une url
     * @return HTMLCode
     */
    public function detail(){
      $detail = $this->p_date_deb.'/'.$this->p_demi_jour_deb.'/'.$this->p_date_fin.'/'.$this->p_demi_jour_fin.'/'.$this->p_type;
      return $detail;
    }
    
    /**
     * Teste si l'absence écrase un congé
     * @param AbsenceObject $c
     * @return bool
     */
    public function isOverwriting($c){
      if($this->isSameDay($c)){ 
        if(!$c->isFullDay() && $this->isFullDay()){
          return true;
          }
        elseif($c->isFullDay() && !$this->isFullDay()){
          return false;
        }
      }
      else{
        if($this->p_date_deb <= $c->p_date_deb && $this->p_date_fin >= $c->p_date_fin){
          return true;
        }
        else{
          return false;
        }
      }
    }


    /**
     * Teste si l'absence est comprise dans le congé
     * @param AbsenceObject $c 
     * @return bool
     */
    public function isInsideAbs($c)
    {
      if($this->p_date_deb==$c->p_date_deb && $this->p_date_fin < $c->p_date_fin){
        if($c->p_demi_jour_deb=='am' && $this->p_demi_jour_deb='pm'){
          return true;
        }
        else{
          return false;
        }
      }
      elseif($this->p_date_deb > $c->p_date_deb && $this->p_date_fin < $c->p_date_fin){
        if($c->p_demi_jour_fin=='pm' && $this->p_demi_jour_fin=='am'){
          return true;
        }else{
          return false;
        }
      }
      else{
        if ($this->p_date_deb > $c->p_date_deb && $this->p_date_fin < $c->p_date_fin) {
          return true;
        }
        elseif ($this->p_date_deb >= $c->p_date_deb && $this->p_date_fin > $c->p_date_fin) {
          return false;
        }
        elseif ($this->p_date_deb < $c->p_date_deb && $this->p_date_fin <= $c->p_date_fin) {
          return false;
        }
      }
    }
    
    /**
     * Teste si l'absence remplace complètement un congé
     * @param AbsenceObject $c 
     * @return bool
     */
    public function isReplacing($c){
      if($this->p_date_deb == $c->p_date_deb && 
         $this->p_date_fin == $c->p_date_fin &&
         $this->p_demi_jour_deb == $c->p_demi_jour_deb &&
         $this->p_demi_jour_fin == $c->p_demi_jour_fin){
        return true;
      }
      else{
        return false;
      }
    }

    /**
     * Créer le message de Log pour le congé saisi ou modifié ou supprimé.
     * @return string 
     */
    public function infoLog(){
      $msg = 'num '.$this->p_num.' (type '.$this->p_type.') pour '.$this->p_login.' ('.$this->p_nb_jours.' jour(s)) (de '.$this->p_date_deb.' '.$this->p_demi_jour_deb.' à '.$this->p_date_fin.' '.$this->p_demi_jour_fin.')';
      return $msg;
    }


    public function merge($c,$side){
      $nb_jours = $this->p_nb_jours + $c->p_nb_jours;
      if($this->p_commentaire=='' && $c->p_commentaire==''){
        $com = '';
      }else{
        $com = $this->p_commentaire.' '.$c->p_commentaire;
      }
      if($this->p_num=='' && $c->p_num==''){
        $p_num = '';
      }
      elseif ($this->p_num == $c->p_num) {
        $p_num = $this->p_num;
      }
      else{ 
        if(is_array($this->p_num)){
          if(is_array($c->p_num)){
            foreach ($c->p_num as $key => $value) {
              array_push($this->p_num, $value);
            }
          }
          else{
            array_push($this->p_num, $c->p_num);
          }
          $p_num = $this->p_num;
        }
        else{
          $p_num = array($this->p_num);
          if(is_array($c->p_num)){
            foreach ($c->p_num as $k => $valeur) {
              array_push($p_num, $valeur);
            }
          }
          else{
            array_push($p_num, $c->p_num);
          }
        }
      }
      if($side=="left"){
        $new_abs = new Absence($this->p_login,$c->p_date_deb,$c->p_demi_jour_deb,$this->p_date_fin,$this->p_demi_jour_fin,$this->p_type,$nb_jours,$com,$this->p_etat,$this->p_date_demande,$p_num);
      }
      else{
        $new_abs = new Absence($this->p_login,$this->p_date_deb,$this->p_demi_jour_deb,$c->p_date_fin,$c->p_demi_jour_fin,$this->p_type,$nb_jours,$com,$this->p_etat,$this->p_date_demande,$p_num);
      }
      return $new_abs;
    }
}
?>