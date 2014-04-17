<script type="text/javascript">
    $(document).ready(function(){
        var login = $("#login").val();
        $('#prev').click(function(){
            // prevWeek();
            alert('prev!');
        })
        $('#next').click(function(){
            // nextWeek();
            alert('next!');
        })
    });

</script>
<table id="tds" class="tab">
        <thead>
            <tr class="week_row">
                <th colspan="6">
                    <img src="<?php echo Router::webroot("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                    <span id="weeknum">Semaine <?php echo $table['sem']; ?></span>
                    <img src="<?php echo Router::webroot("img/next.png");?>" alt="next" id="next" class="weekbutton">
                </th>
            </tr>
            <tr id="semaine">
                <?php echo $table['semaine']; ?>
            </tr>
        </thead>
        <tbody>
        <tr id="usrweek">
            <?php echo $table['user']; ?>
        </tr>
<!-- Ligne vide pour marquer la séparation entre l'utilisateur logué et les autes membres du groupe -->
        <tr class="empty">            
            <td colspan="6"></td>
        </tr>
<!-- Affichage des semaines des autres membres du groupe -->
<?php foreach ($users as $k => $v) :?>
            <tr>
                <td><?php echo strtoupper($v->u_nom);?></td>
                <td><a href="#" id="<?php echo 'lu_'.$v->u_login;?>" data-date="<?php echo $table['lun'];?>"></a></td>
                <td><a href="#" id="<?php echo 'ma_'.$v->u_login;?>" data-date="<?php echo $table['mar'];?>"></a></td>
                <td><a href="#" id="<?php echo 'me_'.$v->u_login;?>" data-date="<?php echo $table['mer'];?>"></a></td>
                <td><a href="#" id="<?php echo 'je_'.$v->u_login;?>" data-date="<?php echo $table['jeu'];?>"></a></td>
                <td><a href="#" id="<?php echo 've_'.$v->u_login;?>" data-date="<?php echo $table['ven'];?>"></a></td>
            </tr>
<?php endforeach;?>
        </tbody>
    </table>
</div>