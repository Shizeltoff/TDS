<fieldset>
  <legend><?php echo $titre; ?></legend> 
<form method="post" action="<?php Router::url('/calendar/exportVcal');?>">
<?php echo $this->form->select('year',"Sélectionner l'année à exporter:",$annees,array(),$current_year); ?>
<?php echo $this->form->submit('Exporter');?>
</form>
</fieldset>