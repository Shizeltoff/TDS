<nav class="login">
	<fieldset><legend>Identification</legend>
		<form method='post' action="<?php Router::url('/membres/login/'.$referer);?>">
<?php echo $this->form->input('login','text','Identifiant');?>		
<?php echo $this->form->input('password','password','Mot de passe');?>		
<?php echo $this->form->submit('Connexion');?>
		</form>
	</fieldset>
</nav>
