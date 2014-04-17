<fieldset>
	<legend>Confirmer la suppression</legend>
<?php if(is_string($conge)) : ?>
<?php 	echo $conge; ?>
<?php else : ?>
	<p>Etes-vous sûr(e) de vouloir supprimer le congé suivant : </p>
	<table>
		<tr>
			<td>Début</td>
			<td><?php echo textDate($conge->p_date_deb).' '.$conge->p_demi_jour_deb; ?></td>
		</tr>
		<tr>
			<td>Fin</td>
			<td><?php echo textDate($conge->p_date_fin).' '.$conge->p_demi_jour_fin; ?></td>
		</tr>
		<tr>
			<td>Nombre de jours</td>
			<td><?php echo $conge->p_nb_jours; ?></td>
		</tr>
		<tr>
			<td>Type</td>
			<td><?php echo $libelle; ?></td>
		</tr>
		<tr>
			<td><a href="<?php echo Router::url('tds/deleteconge/'.$id.'/suivi/'.$annee);?>"><button type="button" name="Supprimer" value="" class="css3button">Supprimer ce congé</button></a></td>
			<td><a href="<?php echo Router::url('suivi/suivi/'.$type_abs.'/'.$annee);?>"><button type="button" name="Supprimer" value="" class="css3button">Annuler</button></a></td>
		</tr>
	</table>
<?php endif; ?>
</fieldset>