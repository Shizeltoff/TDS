<nav id="menu" role="navigation" class="col">
    <ul class="menu2">
        <li><a class="tds" href="<?php echo Router::url('tds/edittds'); ?>">TDS</a></li>
        <li><a href="<?php echo Router::url('tds/newAbsence'); ?>">Nouveau congé/absence</a></li>   
        <li><a class="hist" href="<?php echo Router::url('suivi/suivi/cp'); ?>">Suivi des congés</a></li>
        <li><a class="hist" href="<?php echo Router::url('suivi/suivi/abs'); ?>">Suivi des absences</a></li>
        <li><a href="<?php echo Router::url('suivi/solde');?>">Consulter le solde</a></li>
        <li><a class="profile" href="<?php echo Router::url('membres/defJoff'); ?>">Définir un jour off periodique</a></li>
    <?php if($_SESSION['login']=='guillaume.palliet' || $_SESSION['login']=='sebastien.heurtier' ): ?>
        <li><a href="<?php echo Router::url('debug/index'); ?>">Débugguage</a></li>
    <?php endif;?>
    </ul>
</nav>
<section id="content" class="col">
    <?php echo $layoutContent;?>
</section>