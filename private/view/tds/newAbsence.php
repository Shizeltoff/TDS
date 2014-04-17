<fieldset id="period">
<legend>Définir une période longue d'absence</legend>
    <form method='post' action="<?php Router::url('/tds/newAbsence/');?>">
        <table>
            <!--<tr class="row">-->
            <tr>
                <td class="col">
                    <span>Du :</span></td>
                <td>
                    <input type="text" id="datepickerDeb" class="datepicker" name="deb" onChange="CalculJour();">
                    <img id="calDeb" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier" >
                </td>
                <td class="col">
                    <label for="demi_deb_am">08h30</label>                        
                    <input type="radio" value="am" id="demi_deb_am" name="demi_deb" checked="checked" onChange="CalculJour();"/>
                    <input type="radio" value="pm" id="demi_deb_pm" name="demi_deb" onChange="CalculJour();"/>
                    <label for="demi_deb_pm">13h00</label>                        
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col">
                    <span>Au :</span>
                </td>
                <td>
                    <input type="text" id="datepickerFin" class="datepicker" name="fin" onChange="CalculJour();">
                    <img id="calFin" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier">
                </td>
                <td class="col">
                    <label for="demi_fin_am">13h00</label>
                    <input type="radio" value="am" name="demi_fin" id="demi_fin_am" onChange="CalculJour();"/>
                    <input type="radio" value="pm" name="demi_fin" id="demi_fin_pm" checked="checked" onChange="CalculJour();"/>
                    <label for="demi_fin_pm">17h30</label> 
                </td>
            </tr>
            <tr>
                <td id="joff_row" class="col" colspan="2">Compter le(s) jour(s) off dans la demande : <!-- </td> -->
                    <input type="hidden" name="joff" value="0"><input type="checkbox" id="joff" checked="checked" name="joff" value="1" onChange="CalculJour();">
                </td>
            </tr>
            <tr>
                <td class="col">
                    Nombres de jours : 
                </td>
                <!-- <td><input type="text" step="0.5" min="0" value="" name="nb_jours" id="nb_jours" disabled></td> -->
                <td>
                    <span id="jours">0</span>
                    <input type="hidden" value="" name="nb_jours" id="nb_jours">
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col">Type d'abscence :</td>
                <td class="col">
                    <select name="type" id ="type" onChange="CalculJour();">
<?php   foreach ($type_abs as $k => $v): ?>
                <option value="<?php echo $v->ta_id;?>"><?php echo $v->ta_libelle;?></option>
<?php   endforeach;?>
                    </select>
                </td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col">Ajouter un commentaire :</td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col" colspan="2"><textarea name="com"></textarea></td>
            </tr>
            <!--<tr class="row">-->
            <tr>
                <td class="col" colspan="2" style="text-align: center;">
                    <input type="submit" id="demander" value="Poser le congé">
                </td>
            </tr>
        </table>
    </form>  
</fieldset>
<script type="text/javascript" src="<?php echo Router::webroot("/js/newAbsence.js")?>"></script>