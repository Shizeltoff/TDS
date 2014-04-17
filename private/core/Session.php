<?php
class Session {
    
    public function __construct(){
        if(!isset($_SESSION)){
            session_start();
        }
    }
    
    /**
     * Lecture d'une variable de session
     * @param string $key
     *
     */
    public function read($key=null){
        
        if ($key==null){
            return $_SESSION;
        }
        else{
            if (! array_key_exists($key,$_SESSION)){
                return false;
            }else{
                return $_SESSION[$key];
            }
        }
    }
    
    /**
     * Ecriture d'une variable de session
     * @param string $key nom de la variable
     * @param unknown_type $value sa valeur
     */    
    public function write($key,$value) {
        $_SESSION[$key]=$value;
    }

    /**
     * Suppression d'une variable de session
     * @param string $key nom de la variable a supprimer
     */
    public function delete($key){
        if (array_key_exists($key,$_SESSION)){
            unset($_SESSION[$key]);
        }
    }

    /**
     * Création d'une variable de session flash
     * @param string $data Message 
     * @param string $type Type de message (success,error,warning)
     */    
    public function setFlash($data,$type='success'){  
        $this->write('flash',array('message'=>$data, 'type'=>$type));
    }
    
    /**
     * Lecture de la variable flash puis suppression.
     */
    public function flash(){

        $s='';
        if(isset($_SESSION['flash'])){
            $frType = array(
                'success' => 'Succès',
                'error' => 'Erreur',
                'warning' => 'Attention',
                'info' => 'Information'
                );
            // $s='<p class="'.$_SESSION['flash']['type'].' animated pulse">'.$_SESSION['flash']['message'].'</p>';
            $s='<p data-type="'.$frType[$_SESSION['flash']['type']].'" data-class="'.$_SESSION['flash']['type'].'" class="'.$_SESSION['flash']['type'].' animable">'.$_SESSION['flash']['message'].'</p>';
            $this->delete('flash');
        }
        return $s;
    }
    
}
?>