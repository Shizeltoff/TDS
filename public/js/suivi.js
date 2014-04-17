var BASE_URL=""; // En dév
// var BASE_URL="/TDS/public"; // En prod
$(function(){
        $('#year').change(function(){
            var $annee = this.value;
            var $type_abs = $("#type_abs").val();
            var $ordre = getSelectedRadioValue("ordre");
            $("#content").empty();            
            $.post(BASE_URL+"/ajax/suivi/suivi", {type_abs : $type_abs , annee : $annee, ordre : $ordre},function(data){    
                $("#content").fadeIn().append(data);
                });
        });
    })

function ToogleOrder(ordre){
    var $type_abs = $("#type_abs").val();
    var $annee = $("#year").val();
    $("#content").empty();                
    $.post(BASE_URL+"/ajax/suivi/suivi", {type_abs : $type_abs , annee : $annee, ordre : ordre},function(data){    
            $("#content").fadeIn().append(data);
            });
}
function getSelectedRadioValue (rbname){
    var tab = document.getElementsByName(rbname);
    for (i=0;i<tab.length;i++){
            if (tab[i].checked==true) {
                return tab[i].value;
            }
        }  
}