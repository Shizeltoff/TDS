<div id="contenu">
<p><a href="/" class="css3button">Retour à la vue</a></p>

    <!-- <input type="hidden" id="flag" value="<?php// echo $table['is_tmpconge'];?>"> -->
    <!-- <input type="hidden" id="week" value="<?php// echo $table['real_sem'];?>"> -->
    <!-- <input type="hidden" id="timestamp" value="<?php// echo $table['tmstp'];?>"> -->
    <table id="tds" class="month w960p">
            <thead>
                <tr id="header" class="week_row">
                    <!-- <th colspan="2" id="mois" onClick="selectDate();"><?php // echo $table['moisannee']; ?></th> -->
                    <th colspan="4">
                        <img src="<?php echo Router::url("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                        <span id="weeknum"><?php echo $table['mois']; ?></span>
                        <img src="<?php echo Router::url("img/next.png");?>" alt="next" id="next" class="weekbutton">
                    </th>
                </tr>
                <tr id="mois">
                    <?php echo $table['descr_mois']; ?>
                </tr>
            </thead>
            <tbody id="all">
    <!-- Affichage des congés de l'utilisateur logué -->
                <?php echo $table['user']; ?>
    <!-- Ligne vide pour marquer la séparation entre l'utilisateur logué et les autes membres du groupe -->
                <!-- <tr class="empty"><td colspan="32">&nbsp;</td></tr> -->
    <!-- Affichage des congés des autres membres du groupe -->
                <?php echo $table['others']; ?>
            </tbody>
        </table>
</div>