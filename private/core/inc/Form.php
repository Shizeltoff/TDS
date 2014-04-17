<?php

class Form {

  public $controller;
  public $errors = array();

  private function attributes($attributes=array()) {
    $html = '';
    foreach($attributes as $k => $v) {
      $html .= " $k=\"$v\"";
    }
    return $html;
  }

  private function htmlInput($name, $type, $attributes=array(), $defaultValue='') {
    $html = '';
    $value = $this->value($name, $defaultValue);//isset($this->controller->request->data->$name) ? $this->controller->request->data->$name : '';
    if($type == 'checkbox') {
      $value = $this->value($name, 0);
      $html .= '<input type="hidden" name="'.$name.'" value="0">';
      $html .= '<input type="'.$type.'" id="field'.$name.'" name="'.$name.'"'.$this->attributes($attributes).' value="'.$defaultValue.'"'.($value==$defaultValue ? ' checked="checked"' : '').'>';
    } else {
      $html .= '<input type="'.$type.'" id="field'.$name.'" name="'.$name.'"'.$this->attributes($attributes).' value="'.$value.'">';
    }
    return $html;
  }

  private function label($name, $value) {
    if(empty($value)) { return ''; }
    return "<label for=\"field$name\">$value</label>";
  }

  private function value($name, $default='') {
    return isset($this->controller->request->data->$name)
      ? $this->controller->request->data->$name
      : $default;
  }

  public function __construct($controller) {
    $this->controller = $controller;
  }

  public function input($name, $type, $label, $attributes=array(), $defaultValue='') {
    if($type == 'hidden') {
      return $this->htmlInput($name, $type, $attributes, $defaultValue);
    } else {
      $error = false;
      $classError = '';
      if(isset($this->errors[$name])) {
        $error = $this->errors[$name];
        $classError = ' input-error';
      }
      $htmlInput = $this->htmlInput($name, $type, $attributes, $defaultValue);
      $label = $this->label($name, $label);
      if(in_array($type, array('checkbox', 'radio'))) {
        return '<p class="clearfix'.$classError.'"><span class="input">'.$htmlInput.$label.'</span></p>';
      } else {
        return '<p class="clearfix'.$classError.'">'.$label.'<span class="input">'.$htmlInput.'</span>'.($error ? '<span class="form-help">'.$error.'</span>' : '').'</p>';
      }
    }
  }

  public function select($name, $label, $values=array(), $attributes=array(), $defaultValue=0) {
    $error = false;
    $classError = '';
    if(isset($this->errors[$name])) {
      $error = $this->errors[$name];
      $classError = ' input-error';
    }
    $value = $this->value($name, $defaultValue);
    $html = '<p class="clearfix">'.$this->label($name, $label).'<span class="input">';
    $html .= '<select id="field'.$name.'" name="'.$name.'"'.$this->attributes($attributes).'>';
    foreach($values as $k => $v) {
      if(is_array($value)) {
        $selected = in_array($k, $value) ? ' selected="selected"' : '';
      } else {
        $selected = $value==$k ? ' selected="selected"' : '';
      }
      $html .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>'.LN;
    }
    $html .= '</select></span>'.($error ? '<span class="form-help">'.$error.'</span>' : '').'</p>';
    return $html;
  }

  public function textarea($name, $label, $attributes=array(), $defaultValue='') {
    $error = false;
    $classError = '';
    if(isset($this->errors[$name])) {
      $error = $this->errors[$name];
      $classError = ' input-error';
    }
    $value = $this->value($name, $defaultValue);
    return '<p class="clearfix'.$classError.'">'.$this->label($name, $label).'<span class="input"><textarea id="field'.$name.'" name="'.$name.'"'.$this->attributes($attributes).'>'.htmlspecialchars($value, ENT_COMPAT, 'utf-8').'</textarea></span>'.($error ? '<span class="form-help">'.$error.'</span>' : '').'</p>';
  }

  public function submit($value, $attributes=array()) {
    $submit='<p><span class="input submit"><input type="submit" value="'.$value.'"';
    foreach ($attributes as $key => $value) {
      $submit .= $key.'='.$value;
    }
    $submit.='></span></p>';
    return $submit;
    // return '<p><span class="input submit"><input type="submit" value="'.$value.'"'.$attributes.'></span></p>';
  }

};