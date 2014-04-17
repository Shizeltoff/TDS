var BASE_URL=""; // En dév
// var BASE_URL="/TDS/public"; // En prod
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