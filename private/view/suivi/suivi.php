<input type="hidden" id="type_abs" value="<?php echo $type_abs; ?>">
<fieldset>
  <legend><?php echo $titre; ?></legend> 
<p class="titre">Année :
    <select id="year" name="year">
<?php
    foreach($annees as $k=>$v){
        if($v==$current_year){
            echo '<option selected>'.$v.'</option>';                
        }else
        {
            echo '<option>'.$v.'</option>';
        }
    }
?>
    </select>
    <span>Trier par date :</span>
<?php if($ordre=="asc") : ?>
    Croissante
    <input type="radio" name="ordre" value="asc"  onChange="ToogleOrder('asc');" checked>
    <input type="radio" name="ordre" value="desc" onChange="ToogleOrder('desc');">
    Décroissante
<?php else: ?>
    Croissante
    <input type="radio" name="ordre" value="asc"  onChange="ToogleOrder('asc');">
    <input type="radio" name="ordre" value="desc" onChange="ToogleOrder('desc');" checked>
    Décroissante
<?php endif; ?>
</p>

<table class="suivi" id="suivi">
    <thead>
        <tr>
           <th>Du</th>
           <th>Au</th>
           <th>Type</th>
           <th>Nb jours</th>
           <th>Etat</th>
           <th>Commentaires</th>
           <th colspan="2">Actions</th>
        </tr>
    </thead>
    <tbody id="liste">
<?php 
if(!is_string($suivi)) :
  foreach ($suivi as $key => $value):
    $encours = false;
    !in_array($value->p_type, $_SESSION['isConge']) ? $encours = true : $encours = false;
    $is_past = isPast($value);
?>          
        <tr>
            <td>
<?php   echo textDate($value->p_date_deb).' - '.textDemi($value->p_demi_jour_deb);?>
            </td>
            <td>
<?php   echo textDate($value->p_date_fin).' - '.textDemi($value->p_demi_jour_fin);?>
            </td>
            <td>
<?php if(($value->p_type)!=0): ?>
<?php   echo ucwords($_SESSION['type_abs'][($value->p_type)]->ta_libelle);?>
<?php endif; ?>
            </td>
            <td>
                <?php echo $value->p_nb_jours;?>
            </td>
<?php
        if($value->p_etat==='ok'){
            echo '<td class="valide"></td>';
            }
        elseif($value->p_etat==='refus'){
            echo '<td class="refus"></td>';
        }
        elseif($value->p_etat==='demande'){
            echo '<td class="demande"></td>';
            $encours = true;
            }
        elseif($value->p_etat==='annul'){
            echo '<td class="retrait"></td>';
            $is_past = true;
            }
        elseif ($value->p_etat ==="ajout") {
            echo '<td class="ajout"></td>';
            $is_past = true;
        }
        else{
            echo '<td class="inconnu"></td>';
        }
?>
            <td id="com">
<?php if(($value->p_etat==='annul' || $value->p_etat==='refus' )&& hsc($value->p_commentaire)=='') : ?>
<?php echo "<i>Motif de l'annulation: inconnu</i>"; ?>
<?php else:  echo wrap(hsc($value->p_commentaire));?>
<?php endif; ?>
            </td>
<?php if(!$is_past): ?>
    <?php   if($encours):?>
            <td>
                <a href="<?php echo Router::url('tds/editconge/'.$type_abs.'/'.$value->p_num);?>" class="edit css3button"></a>
            </td>
            <td>
                <a href="<?php echo Router::url('suivi/delConfirm/'.$value->p_num.'/'.$current_year);?>" class="suppr css3button"></a>
            </td>
    <?php else: ?>
            <td colspan="2">
                <a href="<?php echo Router::url('suivi/demAnnul/'.$value->p_num); ?>" class="css3button">Demander l'annulation</a>
            </td>
    <?php endif; ?>
<?php else: ?>
            <td colspan="2"></td>
<?php endif; ?>
       </tr>
<?php endforeach; ?>
<?php elseif (is_string($suivi)):?>
        <tr>
          <td class="congefree" colspan="8">
<?php   echo $suivi;?>
          </td>
        </tr>
<?php endif;?>
   </tbody>
</table>

<table class="legende">
  <tr>
    <td>Légende : </td>
    <td class="demande">Demande en cours</td>
    <td class="valide">Congé validé</td>
    <td class="refus">Congé refusé</td>
    <td class="ajout">Congé ajouté</td>
    <td class="retrait">Congé annulé</td>
  </tr>
</table>

<script type="text/javascript" src="<?php echo Router::webroot("/js/suivi.js")?>"></script>