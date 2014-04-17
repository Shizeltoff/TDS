<script type="text/javascript">
    $(document).ready(function(){
        $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
        var datepickers = $('.datepicker').datepicker({
            dateFormat : 'yy-mm-dd',
            minDate : 0,
            beforeShowDay: $.datepicker.noWeekends,
            firstDay : 1 ,
            onSelect : function(date){
                var option = this.id == 'datepickerDeb' ? 'minDate' : 'maxDate';
                datepickers.not('#'+this.id).datepicker('option',option,date);
                // CalculJour();
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
        $("#deuxieme").hide();
        $( "#dialog-confirm" ).hide();
        $( "#dialog-confirm" ).dialog({
            autoOpen: false,
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "Définir le jour off": function() {
                  $( this ).dialog( "close" );
                  return true;
                },
                Cancel: function() {
                  $( this ).dialog( "close" );
                  return false;
                }
            }
            });
        // $("#valider").click(function(){
        //     $( "#dialog-confirm" ).dialog( "open" );
        // });
        // var demi1 = getSelectedRadioValue("mat1");
    });
        
    function toogleAffichage(){
        var demi1 = getSelectedRadioValue("mat1");
        if((demi1 =="am")||(demi1=="pm")){
            $("#deuxieme").fadeIn();
        }
        else{
            $("#deuxieme").fadeOut();
        }
    }
    function getSelectedRadioValue (rbname){
        var tab = document.getElementsByName(rbname);
        for (i=0;i<tab.length;i++){
                if (tab[i].checked==true) {
                    return tab[i].value;
                }
            }  
    }
</script>

<div id="param">
    <fieldset>
        <legend>Définir le jour off sur une période</legend>
        <form method="POST" action="#">
            <div>
 <!--                <ul>
                    <li> -->
                    <p>
                        <span>Du :</span>
                        <input type="text" id="datepickerDeb" class="datepicker" name="datedeb">
                        <img id="calDeb" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier" >
                    </p>
<!--                     </li>
                    <li> -->
                    <p>    
                        <span>Au :</span>
                        <input type="text" id="datepickerFin" class="datepicker" name="datefin">
                        <img id="calFin" class="dpicker" src="<?php echo Router::webroot("img/calendrier.png");?>" alt="calendrier">
                    </p>
                    <!-- </li>
                </ul> -->
            </div>
            <div>
<!--                 <ul>
                    <li>-->
                    <p>    
                        <select name="joff1" id="jour1">
                            <option value ="lu">Lundi</option>
                            <option value ="ma">mardi</option>
                            <option value ="me">mercredi</option>
                            <option value ="je">jeudi</option>
                            <option value ="ve" selected="selected">Vendredi</option>
                        </select>
                        <input type="radio" name="mat1" id="mat1_am" value="am" onChange="toogleAffichage();">
                        <label for="mat1_am">AM</label>
                        <input type="radio" name="mat1" id="mat1_pm" value="pm" onChange="toogleAffichage();">
                        <label for="mat1_pm">PM</label>
                        <input type="radio" name="mat1" id="mat1_all" value="all" checked="checked"  onChange="toogleAffichage();">
                        <label for="mat1_all">Journée complète</label>
                    </p>

                    <p id="deuxieme">
                        <select name="joff2" id="jour2">
                            <option value ="lu" selected="selected">Lundi</option>
                            <option value ="ma">mardi</option>
                            <option value ="me">mercredi</option>
                            <option value ="je">jeudi</option>
                            <option value ="ve">Vendredi</option>
                        </select>
                        <input type="radio" name="mat2" id="mat2_am" value="am" onChange="toogleAffichage();">
                        <label for="mat2_am">AM</label>
                        <input type="radio" name="mat2" id="mat2_pm" value="pm" onChange="toogleAffichage();">
                        <label for="mat2_pm">PM</label>
                    </p>
        </div>
                            <input type="submit"  value="Sauvegarder ce choix" id="valider">

        </form>
    </fieldset>
</div>

<div id="dialog-confirm" title="Définir le jour off pour toute l'année ?">
    <p>Voulez-vous définir le <span id="jouroff"></span> comme jour off pour toute cette année ?</p>
</div>
