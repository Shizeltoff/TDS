var BASE_URL=""; // En dév
// var BASE_URL="/TDS/public"; // En prod
$(function() {
        $.datepicker.setDefaults( $.datepicker.regional[ "fr" ] );
        var datepickers = $('.datepicker').datepicker({
            dateFormat : 'dd-mm-yy',
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
        if(type_conge!="1" && type_conge!="2" && type_conge!="11"){
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
        $.post(BASE_URL+"/ajax/tds/calculDateDiff", {date_deb: datedeb,  date_fin : datefin},
            function(data){
                temp = $("#joff").is(":checked");
                data = parseFloat(data);
                if(data<4){
                    $("#joff").prop('checked', false);
                    $("#joff").prop('disabled',true);
                    joff="false"
                }else{
                    $("#joff").prop('disabled',false);
                    $("#joff").prop('checked', temp);
                    joff = $("#joff").is(":checked");

                }
                $.post(BASE_URL+"/ajax/tds/calculJours", {date_deb: datedeb, demi_deb : demi_deb, date_fin : datefin,demi_fin : demi_fin , joff : joff},
                    function(donnee){
                        $("#jours").empty();
                        $("#jours").append(donnee); 
                        $("#nb_jours").val(donnee); 
                    });
            });

    }