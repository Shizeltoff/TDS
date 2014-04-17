<fieldset>
	<legend>Demandes de congé présentes</legend>
	<table>
		<tr>
			<td colspan="2" class="warning ">Attention !  Vous avez des demandes de congés en attente d'envoi.</td>
			<br>
		</tr>
		<tr>
			<td colspan="2"><?php echo $listing; ?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">Que souhaitez-vous faire ?</td>
		</tr>
		<tr>
			<td><a href="<?php echo Router::url('/validation/validate/logout');?>"><button type="button" name="Envoyer" value="" class="css3button">Envoyer toutes les demandes</button></a></td>
			<td><a href="<?php echo Router::url('/validation/erase/logout');?>"><button type="button" name="Supprimer" value="" class="css3button">Se déconnecter</button></a></td>
		</tr>
	</table>
</fieldset>