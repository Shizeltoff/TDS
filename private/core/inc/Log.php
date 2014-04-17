<?php

class Log {

  const NONE      = 1;
  const CORE      = 2;
  const LOGIN     = 4;
  const DATABASE  = 8;
  const EMAILING  = 16;
  const ADMIN     = 512;
  const THREAT    = 1024;

  const OFF 		= 0;
  const DEBUG 	= 1;
  const INFO 		= 2;
  const WARN 		= 3;
  const ERROR 	= 4;
  const FATAL 	= 5;

  /**
   * @static array $accepted Categories & associated levels accepted for logging.
   */
  protected static $accepted = array();

  /**
   * @static string $buffer The log's buffer.
   */
  protected static $buffer = '';

  /**
   * @static array $categories Category names.
   */
  protected static $categories = array(
    self::NONE => 'NONE',
    self::CORE => 'CORE',
    self::LOGIN => 'LOGIN',
    self::DATABASE => 'DATABASE',
    self::EMAILING => 'EMAILING',
    self::ADMIN => 'ADMIN',
    self::THREAT => 'THREAT'
  );

  /**
   * @static array $levels Level names.
   */
  protected static $levels = array(
    self::DEBUG=> 'DEBUG',
    self::INFO => 'INFO',
    self::WARN => 'WARN',
    self::ERROR => 'ERROR',
    self::FATAL => 'FATAL'
  );

  /**
   * @static Set category & associated minimum level logging.
   * @param int $category Category ID
   * @param int $level Level ID
   */
  public static function accept($category, $level) {
    if(!array_key_exists($category, self::$categories)) {
      $category = 0;
    }
    if(!array_key_exists($level, self::$levels)) {
      $level = 0;
    }
    self::$accepted[$category] = $level;
  }

  /**
   * @static Echoes HTML formatted buffer content to standard output.
   */
  public static function dump() {
    if(!self::isEmpty()) {
      echo '<pre class="log">'.hsc(self::$buffer).'</pre>'.LN;
    }
  }

  /**
   * @static Return category name.
   * @param int $category Category ID
   * @return string Category name
   */
  public static function getCategory($category) {
    if(!array_key_exists($category, self::$categories)) {
      return 'UNKNOWN';
    }
    return self::$categories[$category];
  }

  /**
   * @static Return level name
   * @param int $level Level ID
   * @return string Level name
   */
  public static function getLevel($level) {
    if(!array_key_exists($level, self::$levels)) {
      return 'UNKNOWN';
    }
    return self::$levels[$level];
  }

  /**
   * @static Return buffer emptyness.
   * @return bool True if the buffer is empty
   */
  public static function isEmpty() {
    return strlen(self::$buffer) == 0;
  }

  /**
   * @static Writes a message in the log (file & buffer).
   * @param string $message Message to write
   */
  public static function write($category, $level, $message) {
    if(array_key_exists($category, self::$accepted)) {
      if($level >= self::$accepted[$category]) {
        $dt = new ExDateTime;
        $backtrace = debug_backtrace();
        $message = "{$dt->mysqlDateTime()} [".Log::getCategory($category)."] [".Log::getLevel($level)."] $message at line {$backtrace[0]['line']} in {$backtrace[0]['file']}".LN;
        $file = LOG_PATH.DS.$dt->mysqlDateTimeStamp().'.log';
        file_put_contents($file, $message, FILE_APPEND);
        self::$buffer .= $message;
      }
    }
  }


  /**
   * Ecrit le log en fonction du type d'action
   * @param string $login user login
   * @param string $action action effectuée (connexion,déconnexion, demande, saisie, edition,suppression)
   * @param AbsenceObject $conge congé soumis à l'action
  */
  public static function dbWrite($login, $action, $conge=null){
        $model = new CongesLog;
        $now =new dateTime();            
        $now = $now->format('Y-m-d H:i:s');
        if($conge != null){
          $msg = $conge->infoLog();
        }else{
          $msg='';
        }
        if($action == "login"){
            $comment = "Connexion de ".$login;
            $etat='';
            $num=0;
        }
        elseif ($action == "logout") {
            $comment = "Déconnexion de ".$login;
            $etat='';
            $num=0;
        }
        elseif ($action == "ajout") {
          $etat = $conge->p_etat;
          $num = $conge->p_num;
          if(in_array($conge->p_type, $_SESSION['isConge'])){
            $comment = "Demande de congés";
          }else{          
            $comment = "Saisie de congés";
          }
        }
        elseif ($action=="edition") {
          $comment = "Modification de demande";
          $etat = $conge->p_etat;
          $num = $conge->p_num;
        }
        elseif($action == "suppression"){
          $comment = "Suppression de demande num ".$conge->p_num;
          $etat='';
          $msg ='';
          $num = $conge->p_num;
        }
        $d = array('log_p_num'=>$num,
                'log_user_login_par'=>$login,
                'log_user_login_pour'=>$login,
                'log_etat'=>$etat,
                'log_comment'=>$comment.' '.$msg,
                'log_date'=>$now);
        $model->save($d);
  }

};
