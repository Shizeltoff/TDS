var BASE_URL=""; // En dév
// var BASE_URL="/TDS/public"; // En prod

$(document).ready(function(){
    $("#header").on('click',".weekbutton",function(){        
            tmstp = $("#timestamp").val();
            if($(this).attr("id")=="prev"){
                mois = -1;
            }else{
                mois = 1;
			}
            $.post(BASE_URL+"/ajax/month/changeMonth",{timestamp : tmstp, month : mois},function(data){
                $("#timestamp").val(data);
                grp = $("#groupe").val();   
                $.post(BASE_URL+'/ajax/month/createMonthTable',{month : data, groupe: grp},function(data){
                    $("#monthname").empty(); 
                    $("#monthname").append(data.mois); 
                	$("#mois").empty(); 
                	$("#mois").append(data.month_detail);
                	$("#all").empty(); 
                	$("#all").append(data.allusers);
                    $("#printline").attr('href',data.printline); 
                },"json")
                .error(function(data){
                	alert('oups');
                });
			});
    });
    $("#sections").change(function(){
        grp = this.value;
        mois = $("#timestamp").val();
        $.post(BASE_URL+'/ajax/month/createMonthTable',{month : mois, groupe: grp},function(data){
            $("#groupe").val(grp);
            $("#monthname").empty(); 
            $("#monthname").append(data.mois); 
            $("#mois").empty(); 
            $("#mois").append(data.month_detail);
            $("#all").empty(); 
            $("#all").append(data.allusers);
            $("#printline").attr('href',data.printline); 
            
        },"json")
        .error(function(data){
            console.log(data);
        });
    });

});