<?php
if (!isset($_SESSION['user'])) {
    if( ! ($this->request->controller == 'membres' && $this->request->action == 'login') && ! ($this->request->controller == 'month')) {
        	$this->redirect('membres/login');
        }
}

if($this->request->prefix == 'ajax'){
    $this->layouts=array();
}
?>