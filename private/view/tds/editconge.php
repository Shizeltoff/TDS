<fieldset id="period">
<legend><?php echo $titre; ?></legend>
    <form method='post' action="<?php Router::url('/tds/editconge/');?>">
        <table>
            <!--<tr class="row">-->
            <tr>
                <td class="col">
                    <span>Du :</span></td>
                <td><input type="text" id="datepickerDeb" class="datepicker" name="deb" value="<?php echo $conge->p_date_deb;?>" onChange="CalculJour();"><img id="calDeb" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier" ></td>
                <td class="col">
                    <label for="demi_deb_am">08h30</label> 
<?php if($conge->p_demi_jour_deb=='am'):?>
                    <input type="radio" value="am" id="demi_deb_am" name="demi_deb" checked="checked" onChange="CalculJour();"/>
                    <input type="radio" value="pm" id="demi_deb_pm" name="demi_deb" onChange="CalculJour();"/>
<?php else: ?>                        
                    <input type="radio" value="am" id="demi_deb_am" name="demi_deb"  onChange="CalculJour();"/>
                    <input type="radio" value="pm" id="demi_deb_pm" name="demi_deb" checked="checked" onChange="CalculJour();"/>
<?php endif; ?>
                    <label for="demi_deb_pm">13h00</label>                        
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col">
                    <span>Au :</span>
                </td>
                <td><input type="text" id="datepickerFin" class="datepicker" name="fin" value="<?php echo $conge->p_date_fin; ?>" onChange="CalculJour();"><img id="calFin" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier"></td>
                <td class="col">
                    <label for="demi_fin_am">13h00</label>
<?php if($conge->p_demi_jour_fin=='am'):?>                        
                    <input type="radio" value="am" name="demi_fin" id="demi_fin_am" checked="checked" onChange="CalculJour();"/>
                    <input type="radio" value="pm" name="demi_fin" id="demi_fin_pm" onChange="CalculJour();"/>
<?php else: ?>                        
                    <input type="radio" value="am" name="demi_fin" id="demi_fin_am" onChange="CalculJour();"/>
                    <input type="radio" value="pm" name="demi_fin" id="demi_fin_pm" checked="checked" onChange="CalculJour();"/>
<?php endif; ?>                        
                    <label for="demi_fin_pm">17h30</label> 
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <input type="hidden" name="joff" value="0">        
                <td class="col" colspan="2">Jour off pris en compte : <?php echo $input_joff; ?></td>
            </tr>
            <tr>
                <td class="col">
                    Nombres de jours : 
                </td>
                <td>
                    <input type="number" step="0.5" min="0" name="nb_jours" id="nb_jours" value="<?php echo $conge->p_nb_jours; ?>">
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col">Type d'abscence :</td>
                <td class="col">
                    <select name="type" id="type">
<?php foreach ($type_abs as $k => $v):
    if($conge->p_type==$v->ta_id):
?>
                <option value="<?php echo $v->ta_id;?>" selected="selected"><?php echo $v->ta_libelle;?></option>
<?php else: ?>
                <option value="<?php echo $v->ta_id;?>"><?php echo $v->ta_libelle;?></option>
<?php endif;
endforeach;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="col">Ajouter/modifier un commentaire :</td>
            </tr>
            <tr>
                <td class="col" colspan="2"><textarea id="com" name="com"><?php echo hsc($conge->p_commentaire); ?></textarea></td>
            </tr>
            <tr>
                <td class="col" colspan="2" style="text-align: center;">
                    <input type="hidden" name="etat" value="<?php echo $conge->p_etat; ?>">
                    <input type="submit" name="valid" value="Valider les modifications">
                    <input type="submit" name="cancel" value="Annuler">
                    <!-- <input type="submit" id="demander" value="Demander les congés"> -->
                </td>
            </tr>
        </table>
    </form>  
</fieldset>
<script type="text/javascript" src="<?php echo Router::webroot("/js/newAbsence.js")?>"></script>
