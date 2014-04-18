<div id="contenu">
    <input type="hidden" id="flag" value="<?php echo $table['is_tmpconge'];?>">
    <input type="hidden" id="week" value="<?php echo $table['real_sem'];?>">
    <input type="hidden" id="timestamp" value="<?php echo $table['tmstp'];?>">
    <table id="tds" class="tab">
            <thead>
                <tr id="header" class="week_row">
                    <th colspan="2" id="mois" onClick="selectDate();"><?php echo $table['moisannee']; ?></th>
                    <th colspan="4">
                        <img src="<?php echo Router::url("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                        <span id="weeknum">Semaine <?php echo $table['sem']; ?></span>
                        <img src="<?php echo Router::url("img/next.png");?>" alt="next" id="next" class="weekbutton">
                    </th>
                </tr>
                <tr id="semaine">
                    <?php echo $table['semaine']; ?>
                </tr>
            </thead>
            <tbody id="all">
    <!-- Affichage des congés de l'utilisateur logué -->
                <?php echo $table['user']; ?>
    <!-- Ligne vide pour marquer la séparation entre l'utilisateur logué et les autes membres du groupe -->
                <tr class="empty"><td colspan="6">&nbsp;</td></tr>
    <!-- Affichage des congés des autres membres du groupe -->
                <?php echo $table['others']; ?>
            </tbody>
        </table>
</div>
<div>
    <table class="tdslegend">
        <tr>
            <td><div class="off"></div></td>
            <td>Journée Off</td>
            <td><div class="ca"></div></td>
            <td>Congés Payés</td>
            <td><div class="rtt"></div></td>
            <td>RTT</td>
            <td><div class="rcp"></div></td>
            <td>Récup</td>
            <td><div class="fo"></div></td>
            <td>Formation</td>
            <td><div class="mal"></div></td>
            <td>Maladie</td>
            <td><div class="mi"></div></td>
            <td>Mission</td>
            <td><div class="mo"></div></td>
            <td>MO</td>
            <td><div class="oth"></div></td>
            <td>Autre (Abs syndicale,...)</td>
            <td><div class="taf"></div></td>
            <td>Présence</td>
        </tr>
    </table>
</div>
<div id="about" class="about" title="Sélectionner le type d'absence désiré">
    <table class="tab">
        <thead>
            
            <tr>
                <th></th>
                <th>Journée complète</th>
                <th>Matinée</th>
                <th>Après-midi</th>
            </tr>
        </thead>
        <tr>
            <th scope="row" class="about">Journée Off</th>
            <td><div data-am="off" data-pm="off" class="am_off pm_off" ></div></td>
            <td><div data-am="off" data-pm="0" class="am_off pm_none"></div></td>
            <td><div data-am="0" data-pm="off" class="pm_off am_none"></div></td>
        </tr>
        <tr>      
            <th scope="row" class="about">Congés Payés</th>
            <td><div data-id-am="" data-id-pm="" data-am="ca" data-pm="ca" class="am_ca pm_ca"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="ca" data-pm="0" class="am_ca pm_none"></div></td>
            <td><div data-id-am="" data-id-pm=""  data-am="0" data-pm="ca" class="pm_ca am_none"></div></td>
        <tr>
            <th scope="row" class="about">RTT</th>
            <td><div data-id-am="" data-id-pm="" "RTT" data-am="rtt" data-pm="rtt"  class="am_rtt pm_rtt"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="rtt" data-pm="0" class="am_rtt pm_none"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="0" data-pm="rtt" class="pm_rtt am_none"></div></td>
        </tr>
        <tr>
            <th scope="row" class="about">Récup</th>
            <td><div data-id-am="" data-id-pm="" data-am="rcp" data-pm="rcp"  class="am_rcp pm_rcp"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="rcp" data-pm="0" class="am_rcp pm_none"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="0" data-pm="rcp" class="pm_rcp am_none"></div></td>
        </tr>        
        <tr>
            <th scope="row" class="about">Formation</th>
            <td><div data-id-am="" data-id-pm=""  data-am="fo" data-pm="fo" class="am_fo pm_fo"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="fo" data-pm="0" class="am_fo pm_none"></div></td>
            <td><div data-id-am="" data-id-pm=""  data-am="0" data-pm="fo"class="pm_fo am_none"></div></td>    
        </tr>
        <tr>
            <th scope="row" class="about">Maladie</th>
            <td><div data-id-am="" data-id-pm="" data-am="mal" data-pm="mal" class="am_mal pm_mal"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="mal" data-pm="0" class="am_mal pm_none"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="0" data-pm="mal" class="pm_mal am_none"></div></td>   
        </tr>
        <tr>
            <th scope="row" class="about">Mission</th>
            <td><div data-id-am="" data-id-pm="" data-am="mi" data-pm="mi" class="am_mi pm_mi"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="mi" data-pm="0" class="am_mi pm_none"></div></td>
            <td><div data-id-am="" data-id-pm="" data-am="0" data-pm="mi" class="pm_mi am_none"></div></td>   
        </tr>
        <tr>
            <th scope="row" class="about">MO</th>
            <td><div data-id-am="" data-id-pm="" data-am="mo" data-pm="mo" class="am_mo pm_mo"></div></td>
        </tr>
        <tr>
            <th scope="row" class="about">Présence</th>
            <td><div data-id-am="" data-id-pm="" data-am="taf" data-pm="taf" class="am_taf pm_taf"></div></td>
            <td class="presence"><div data-id-am="" data-id-pm="" data-am="taf" data-pm="0" class="am_taf pm_none"></div></td>
            <td class="presence"><div data-id-am="" data-id-pm="" data-am="0" data-pm="taf" class="pm_taf am_none"></div></td>
        </tr>
        <tr>
                <th scope="row" class="about">Commentaire</th>
                <td colspan="3"><textarea placeholder="Ajouter un commentaire" maxlenght=50 id="commentaire"></textarea></td>
            </tr>
    </table>
</div>
<div>
    <table class="tdsmenu">
        <tr>
            <td><a href="<?php echo Router::url('/tds/edittds');?>" class="css3button">Aujourd'hui</a></td>
            <td id="print"><?php echo $table['printline']; ?></td>
            <td id="month"><?php echo $table['monthview'];?></td> 
            <!--<td><a href="<?php// echo '/calendrier.php'; ?>" class="css3button" target="_blank">Vue mensuelle</a></td>-->
            <td><a href="<?php echo Router::url('/calendar/exportIcal');?>" class="css3button">Export Ical</a></td>
            <td></td>
        </tr>    
    </table>
</div>
<div id="listing">
    <fieldset class="liste">
        <legend>Liste des congés a demander :</legend>
        <p>
            <a href="<?php echo Router::url('/validation/erase/tds'); ?>" class="right css3button" id="erase-button">Effacer vos modifications</a>
        </p>
            <p>Affichage de toutes les demandes de congés faites en cliquant dans le tableau </p>
        <ul id="liste">
            <?php echo $table['listing']; ?>
        </ul>
        <p>
            <a href="<?php echo Router::url('/validation/validate/tds'); ?>" class="left css3button" id="validate-button">Envoyer toutes vos demandes</a>
        </p>
    </fieldset>
</div>

<div id="error" title="Erreur">
    <p class="error">Vous ne pouvez pas modifier/supprimer ce congé car il a déjà été validé par votre supérieur.</p>
</div>

<div id="selecteur">
<p>PIT ETRE UN DATE PICKER UN JOUR</p>
</div>


<script type="text/javascript" src="<?php echo Router::webroot("/js/tds.js")?>"></script>