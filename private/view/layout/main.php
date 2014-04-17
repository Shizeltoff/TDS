<header class="clearfix">
<h1 class="left m-reset p-reset"><a href="<?php echo Router::url('tds/edittds');?>">PHP CONGES DES IESSA</a></h1>
<?php if(isset($_SESSION)): ?>
<a class="right logout" href="<?php echo Router::url('membres/logout'); ?>"></a>
<h2 class="right m-reset p-reset"><?php echo $this->session->read('user')->u_prenom.' '.$this->session->read('user')->u_nom;?></h2>
<h2 class="center"><?php echo 'Solde : CP = '.$this->session->read('solde_cp').' - RTT = '.$this->session->read('solde_rtt').' ';?></h2>
<?php endif; ?>
</header>
<?php    echo $this->session->flash();?>
<div id="main" class="row">
<?php
    echo $layoutContent;
?>
</div>
<!-- <footer id="footer">
    <h1 class="m-reset p-reset">Développé par et pour le ST du CRNA Nord.</h1>
</footer> -->