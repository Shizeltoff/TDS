var BASE_URL=""; // En dév
// var BASE_URL="/TDS/public"; // En prod
var CONGE_ARR = ["am_ca","pm_ca","am_rtt","pm_rtt","am_rl","pm_rl","am_fct","pm_fct","am_ip","pm_ip"];
$(document).ready(function(){
        cell = "";
        clickflag = 0;
        var count = 0 ;
        $("#about").dialog({ autoOpen: false , width : "auto", modal : true });
        $("#selecteur").dialog({ autoOpen: false ,
                                 width : "auto",
                                 show:{effect : "blind" , duration:200},
                                 position:{my: "left top", at: "left bottom", of: $("#header")}});
       
        $("#about").on('dialogclose',function(event,ui){
            clickflag=0;
        });
        $("#error").dialog({ autoOpen: false, 
            width : "auto",
            modal : true,
            buttons: {
                OK: function() {
                    $(this).dialog( "close" );
                    }
                }
            });
        getCellClass();
        $("#header").on('click',".weekbutton",function(){        
            tmstp = $("#timestamp").val();
            if($(this).attr("id")=="prev"){
                jour = -7;
            }else{
                jour = 7;
            }

            $.post(BASE_URL+"/ajax/tds/changeWeek",{timestamp : tmstp , val : jour},function(data){
                $("#timestamp").val(data);
                sem=data;
                sem = $("#timestamp").val();                    
                $.post(BASE_URL+'/ajax/tds/createWeek',{semaine :sem},function(data){
                    // $("#week").val(data.real_sem);
                    $("#mois").empty();
                    $("#mois").append(data.moisannee);
                    $("#semaine").empty();
                    $("#semaine").append(data.semaine);
                    $("#liste").empty();
                    $("#liste").append(data.listing);
                    $("#print").empty();
                    $("#print").append(data.printline);

                    $("#weeknum").empty();
                    $("#weeknum").append("Semaine "+data.sem);
                    $("#flag").val(data.is_tmpconge);
                    $("#all").empty();
                    $("#all").append(data.completeweek);
                    getCellClass();
                    },"json");
            });
        });
    });


    function selectDate(){
       // if($('#selecteur').dialog('isOpen')===true){
       //  $('#selecteur').dialog('close');
       // }else{
       //  $('#selecteur').dialog('open');
       // } 
    };
    function isTempConge(){
       flag = $("#flag").val();
       if(flag == 0 || flag==""){
            $("#listing").hide();
       }
       else{
            $("#listing").show();
       };
    };

    function isValidated(class_dem,etat_exist,cl_ex){
        if(etat_exist[0]!="ok" && etat_exist[1]!="ok"){
            return false;
        }
        else{
            if(class_dem[0]!="0" && class_dem[1]!="0"){
                if($.inArray(cl_ex[0],CONGE_ARR)==-1 && $.inArray(cl_ex[1],CONGE_ARR)==-1){
                    return false;
                }
                else{
                    if(etat_exist[0]!="ok" || ($.inArray(cl_ex[0],CONGE_ARR)==-1) ){
                        if(etat_exist[1]!="ok" || ($.inArray(cl_ex[1],CONGE_ARR)==-1)){
                            return false;
                        }
                        else{
                            return true;
                        };
                    }
                    else{
                        return true;
                    };
                };
            }
            else if(class_dem[0]=="0"){ // Absence demandée l'après-midi
                if($.inArray(cl_ex[1],CONGE_ARR)==-1){
                    return false;
                }
                else{
                    if(etat_exist[1]!="ok") {
                        return false;
                    } 
                    else{
                        return true;
                    };
                };
            }
            else{ //Absence demandée le matin
                if($.inArray(cl_ex[0],CONGE_ARR)==-1){
                    return false;
                }
                else{
                    if(etat_exist[0]!="ok") {
                        return false;
                    }
                    else{
                        return true;
                    };
                };
            };
        };
     };

    function getCellClass(){
            isTempConge();
            var commentaire;
            var class_tab;
            var etat_conge;
            var ids_conge;
            var id_conge_pm;
            var id_conge_am;
            date_conge='';
            var i=0;
            $('#usrweek').on('click','.usr',function(){
                if(clickflag==0){
                    cell = $(this).children();
                    macase=cell;
                    if(cell.attr("class")!="ferie"){
                        clickflag=1;
                        class_tab = ['taf','taf'];
                        etat_conge = ['',''];
                        ids_conge = [0,0];  
                        cell_classes = cell.attr('class').split(' ');
                        ids_conge[0] = cell.attr('data-id-am');
                        ids_conge[1] = cell.attr('data-id-pm');
                        etat_conge[0]=cell.attr('data-etat-am');
                        etat_conge[1]=cell.attr('data-etat-pm');
                        date_conge = cell.attr("data-date");
                        tooltip = cell.html();
                        $("#about").dialog("open");
                        $("#about").on('click','div',function(e){
                            am = $(this).attr('data-am');
                            pm = $(this).attr('data-pm');
                            class_tab[0]=am;
                            class_tab[1]=pm;
                            commentaire = $("#commentaire").val();
                            $("#about").dialog("close");
                            if(!isValidated(class_tab,etat_conge,cell_classes)){
                                timestamp = $("#timestamp").val();
                                $.post(BASE_URL+'/ajax/temp/calculConge',{date : date_conge,
                                                    type : class_tab,
                                                    classe : cell_classes,
                                                    id : ids_conge,
                                                    com : commentaire,
                                                    tooltip : tooltip,
                                                    timestamp : timestamp},
                                        function(data){
                                            if(am != '0' && pm == '0'){
                                                cell_classes[0]="am_"+am;
                                            }
                                            else if(pm != '0' && am == '0'){
                                                cell_classes[1]="pm_"+pm;
                                            }
                                            else{
                                                cell_classes[0]="am_"+am;  
                                                cell_classes[1]="pm_"+pm;
                                            }
                                            $("#listing").fadeIn(); 
                                            // $("#liste").append(data.small_abs);
                                            $("#liste").empty();
                                            $("#liste").append(data.listing);
                                            cell.empty(); // On vide le code html de la div
                                            cell.append(data.tooltip);  // On ajout la balise <span> pour l'infobulle
                                            // cell.attr('data-id-am' , data.id_am);
                                            // cell.attr('data-id-pm' , data.id_pm);
                                            cell.removeClass(cell.attr('class'));
                                            cell.addClass(cell_classes[0]+' '+cell_classes[1]);
                                            clickflag=0;
                                },"json")   
                                .fail(function(){
                                    clickflag=0;
                                });
                            }
                            else{
                                $("#error").dialog("open");
                                clickflag=0;
                            }
                            e.stopImmediatePropagation(); // evite la propagation de l'Ã©vÃ¨nement click
                        });
                    }
                }
            });
        }