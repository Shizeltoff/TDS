<nav class="login">
	<fieldset><legend>Identification</legend>
		<form method='post' action="<?php Router::url('/membres/login/'.$referer);?>">
<?php echo $this->form->input('login','text','Identifiant');?>		
<?php echo $this->form->input('password','password','Mot de passe');?>		
<?php echo $this->form->submit('Connexion');?>
		</form>
	</fieldset>
	<div class="outside">
		<p class="buttonlink">
			<a href="<?php echo Router::url('/month/index'); ?>" class="css3button">Voir le calendrier</a>
		</p>
	</div>
</nav>
