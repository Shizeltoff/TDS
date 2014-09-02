<div class="clearfix">
    <div id="contenu" class="content">
<div id="choices">
    <select name="sections" id="">
<?php foreach ($sections as $key=>$value) :?>
    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php endforeach; ?>        
    </select>
</div>
    <!-- <input type="hidden" id="flag" value="<?php// echo $table['is_tmpconge'];?>"> -->
    <!-- <input type="hidden" id="week" value="<?php// echo $table['real_sem'];?>"> -->
    <!-- <input type="hidden" id="timestamp" value="<?php// echo $table['tmstp'];?>"> -->
    <table id="tds" class="month">
            <thead>
                <tr id="header" class="week_row">
                    <th colspan="32">
                        <img src="<?php echo Router::url("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                        <span id="weeknum"><?php echo $table['mois']; ?></span>
                        <img src="<?php echo Router::url("img/next.png");?>" alt="next" id="next" class="weekbutton">
                    </th>
                </tr>
                <tr id="mois">
                    <?php echo $table['month_detail']; ?>
                </tr>
            </thead>
            <tbody id="all">
                <?php echo $table['all']; ?>
            </tbody>
        </table>
        <hr>
    <p>
        <a href="<?php echo Router::url('membres/login') ?>" class="css3button">Se connecter</a>
        <a href="/" class="css3button" onclick="return false">Imprimer</a>
        <!-- <a href="<?php //echo Router::url('month/print/'.$table['tmstp']);  ?>" class="css3button" onclick="return false">Imprimer</a> -->
    </p>
    <!--<script type="text/javascript" src="<?php //echo Router::webroot("/js/month.js")?>"></script>-->

    </div>
</div>