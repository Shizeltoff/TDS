<fieldset>
  <legend>SOLDE DES CONGES PERSONNELS</legend>
<p class="titre">Solde pour l'année <?php echo $annee;?></p>

<table class="suivi">
    <!--<caption>Suivi des demandes de congés.</caption>-->
    <thead>
        <tr>
           <th>Type de congés</th>
           <th>Solde actuel</th>
           <th>dont reliquat</th>
           <th>Droit annuel</th>
        </tr>
    </thead>
 
    <tbody> <!-- Corps du tableau -->
<?php foreach ($droit as $k => $v):?>
  <tr>
    <td><?php echo $v->ta_libelle; ?></td>
    <td><?php echo $v->su_solde + $v->su_reliquat; ?></td>
    <td><?php echo $v->su_reliquat; ?></td>
    <td><?php echo $v->su_nb_an; ?></td>
  </tr>   
<?php endforeach; ?>
   </tbody>
</table>
<div>
<!--   <form method="post" action="<?php //echo Router::url('/suivi/impr')?>">
    <input type="hidden" name="annee" value="<?php //echo $annee;?>">
    <input id="imprim" type="submit" value="Imprimer un récap des congés" >
  </form> -->
  <!-- <p class="titre"><a href="<?php //echo Router::url('/suivi/impr')?>" class="css3button">Imprimer un récap des congés</a></p> -->
</div>
</fieldset>
<!-- <script type="text/javascript">
   // $('#imprim').prop('disabled',true);
   function ChangeYear(){
       $('#wrapper').notif({title:'Information', content:'Année changée!', icon:'', timeout:1500});
       $('#imprim').onsubmit(function(){
         alert('fonction temporairement désactivée');
         return false;
       });
   }
 </script>-->