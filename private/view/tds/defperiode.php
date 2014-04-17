<script>
    $(function() {
        $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
        var datepickers = $('.datepicker').datepicker({
            dateFormat : 'yy-mm-dd',
            minDate : 0,
            beforeShowDay: $.datepicker.noWeekends,
            firstDay : 1 ,
            onSelect : function(date){
                var option = this.id == 'datepickerDeb' ? 'minDate' : 'maxDate';
                datepickers.not('#'+this.id).datepicker('option',option,date);
                CalculJour();
                }
        });
        $('#calDeb').click(function(){
            $("#datepickerDeb").focus();
            $("#datepickerDeb").click();
        })
        $('#calFin').click(function(){
            $("#datepickerFin").focus();
            $("#datepickerFin").click();
        })
    });
    
    function getSelectedRadioValue(rbname){
        var tab = document.getElementsByName(rbname);
        for (i=0;i<tab.length;i++){
                if (tab[i].checked==true) {
                    return tab[i].value;
                }
            }   
    }
    
    function CalculJour(){
        var datedeb = $("#datepickerDeb").val();
        var datefin = $("#datepickerFin").val();      
        var demi_deb = getSelectedRadioValue("demi_deb");
        var demi_fin = getSelectedRadioValue("demi_fin");
        var type_conge =  $("#type").val();
        if (type_conge!="1" && type_conge!="2" && type_conge!="11") {
            $("#joff").prop('disabled',true);
            joff="false";
        }else{
            $("#joff").prop('disabled',false);
            joff = $("#joff").is(":checked");
        };
        if (datedeb==datefin) {
                if (demi_deb=="pm") {                
                    $("#demi_fin_am").prop('disabled',true);
                    $("#demi_fin_pm").prop('checked',true);
                }
                else{
                    $("#demi_fin_am").prop('disabled',false);                        
                };
        }
        else{
            $("#demi_fin_am").prop('disabled',false);                        
        };
        $.post("/ajax/tds/calculJours", {date_deb: datedeb, demi_deb : demi_deb, date_fin : datefin,demi_fin : demi_fin , joff : joff},
            function(data){
                $("#nb_jours").val(data); 
            });
    }
</script>    
    <fieldset id="period">
    <legend>Définir une période longue d'absence</legend>
        <form method='post' action="<?php Router::url('/tds/defperiode/');?>">
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
                    <td><input type="number" step="0.5" min="0" value="" name="nb_jours" id="nb_jours"></td>
                </tr>
                <!--<tr class="row">-->
                <tr>
                    <td class="col">Type d'abscence :</td>
                    <td class="col">
                        <select name="type" id ="type" onChange="CalculJour();">
    <?php
            foreach ($type_abs as $k => $v):
    ?>
                    <option value="<?php echo $v->ta_id;?>"><?php echo $v->ta_libelle;?></option>
                    <!-- <input type="radio" name="type" value="<?php // echo $v->ta_id;?>" id="<?php // echo $v->ta_short_libelle;?>" > -->
                    <!-- <label for="<?php //echo $v->ta_short_libelle;?>"><?php// echo $v->ta_libelle;?></label> -->
    <?php   endforeach;
    ?>
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
<!--</div>-->
