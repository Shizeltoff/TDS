<div id="contenu">
    <table id="monthTable" class="month">
            <thead>
                <tr id="header" class="week_row">
                    <th colspan="6">
                        <img src="<?php echo Router::url("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                        <span id="mois">Décembre</span>
                        <img src="<?php echo Router::url("img/next.png");?>" alt="next" id="next" class="weekbutton">
                    </th>
                </tr>
                <tr id="semaine">
                    <th></th>
                    <?php for ($i=1; $i <32 ; $i++) { 
                        echo "<th>".$i."</th>";
                    }?>
                </tr>
            </thead>
            <tbody>
                <?php echo $table['user']; ?>
            </tbody>
        </table>
</div>