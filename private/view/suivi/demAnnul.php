<fieldset>
	<legend>Confirmer la demande d'annulation de votre congé</legend>
<?php if(is_string($conge)) : ?>
<?php 	echo $conge; ?>
<?php else : ?>
	<p>Etes-vous sûr(e) de vouloir annuler le congé suivant : </p>
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
			<td>
				<a href="<?php  echo Router::url('suivi/annulAbs/'.$conge->p_num);?>">
					<button type="button" name="Supprimer" value="" class="css3button">Annuler ce congé</button>
				</a>
			</td>
			<td><a href="<?php echo Router::url('suivi/suivi/cp');?>"><button type="button" name="Supprimer" value="" class="css3button">Retour</button></a></td>
		</tr>
	</table>
<?php endif; ?>
</fieldset>