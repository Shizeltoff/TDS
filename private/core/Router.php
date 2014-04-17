<?php

class Router {

  public static $prefixes = array();
  public static $routes = array();

  public static function connect($redirect, $pattern) {
    $r = array();
    $r['params'] = array();
    $r['redirect'] = $redirect;
    $r['pattern'] = preg_replace('/([a-z0-9]+):([^\/]+)/', '${1}:(?P<${1}>${2})', $pattern);
    $r['pattern'] = '/^'.str_replace('/', '\/', $r['pattern']).'$/';
    $params = explode('/', $pattern);
    foreach ($params as $k=>$v) {
      if (strpos($v, ':')) {
        $subparam = explode(':', $v);
        $r['params'][$subparam[0]] = $subparam[1];
      } else {
        if ($k == 0) {
          $r['controller'] = $v;
        } elseif ($k == 1) {
          $r['action'] = $v;
        }
      }
    }

    $r['catcher'] = $redirect;
    foreach ($r['params'] as $k=>$v) {
      $r['catcher'] = str_replace(":$k", "(?P<$k>$v)", $r['catcher']);
    }
    $r['catcher'] = '/^'.str_replace('/', '\/', $r['catcher']).'$/';

    self::$routes[] = $r;
  }

  public static function parse($url, $request) {
    $url = trim($url, '/');
    foreach(self::$routes as $v) {
      if(preg_match($v['catcher'], $url, $match)) {
        $request->controller = $v['controller'];
        $request->action = $v['action'];
        foreach($v['params'] as $k => $dummy) {
          $request->params[$k] = $match[$k];
        }
        return $request;
      }
    }
    $items = explode('/', $url);
    if(array_key_exists($items[0], self::$prefixes)) {
      $request->prefix = self::$prefixes[$items[0]];
      array_shift($items);
    }
    $request->controller = !empty($items[0]) ? $items[0] : 'tds';
    $request->action = isset($items[1]) ? $items[1] : 'edittds';
    $request->params = array_slice($items, 2);
    return true;
  }

  public static function prefix($urlPrefix, $methodPrefix) {
    self::$prefixes[$urlPrefix] = $methodPrefix;
  }

  public static function url($url='') {
    $url = trim($url, '/');
    foreach(self::$routes as $v) {
      if(preg_match($v['pattern'], $url, $match)) {
        foreach($match as $k => $m) {
          if(!is_numeric($k)) {
            $v['redirect'] = str_replace(":$k", $m, $v['redirect']);
          }
        }
        return BASE_URL.'/'.$v['redirect'];
      }
    }
    foreach(self::$prefixes as $k => $v) {
      if(strpos($url, $v) === 0) {
        $url = str_replace($v, $k, $url);
      }
    }
    return BASE_URL.'/'.$url;
  }

  public static function webroot($url) {
    $url = trim($url, '/');
    return BASE_URL.'/'.$url;
  }

};