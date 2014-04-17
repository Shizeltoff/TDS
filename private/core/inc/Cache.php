<?php

class Cache{
  
  const ONEHOUR = 60;
  const ONEMONTH = 43200;
  const UNLIMITED = 0;
  
  private $dirname;
  private $duration;
  
  public function __construct($dirname = CACHE_PATH,$duration = SELF::UNLIMITED){
    $this->dirname = $dirname;
    $this->duration = $duration;
  }
  
  public function read($key){
    $filename = $this->dirname.DS.$key;
    if(!file_exists($filename)){
      return false;
    }
    if($this->duration != self::UNLIMITED){
      $lifetime = (time()-filemtime($filename))/60;
      if($lifetime > $this->duration){
        $this->delete($key);
        return false;
      }
    }
    return unserialize(file_get_contents($filename));
  }
  
  public function write($key,$data){
    file_put_contents($this->dirname.DS.$key,serialize($data),FILE_APPEND);
  }
  
  public function delete($key){
    $filename = $this->dirname.DS.$key;
    if(file_exists($filename)){
      unlink($filename);
    }
  }

  public function isExpired($key){
    $filename = $this->dirname.DS.$key;
    $lifetime = (time()-filemtime($filename))/60;
    if($lifetime > self::ONEMONTH ){
        $this->delete($key);
        return true;
      }
    else{
      return false;
    }

  }
};

?>