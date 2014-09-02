<div class="content">
        <input type="hidden" id="timestamp" value="<?php echo $table['tmstp'];?>">
        <input type="hidden" id="groupe" value="<?php echo $grp;?>">
        <div class="choices">
            <select name="sections" id="sections">
<?php foreach ($sections as $key=>$value) :?>
    <?php if ($key == $grp) : ?>
            <option value="<?php echo $key; ?>" selected><?php echo $value; ?></option>
    <?php else: ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
    <?php endif; ?>
<?php endforeach; ?>        
            </select>
        </div>
        <table id="tds" class="month">
                <thead>
                    <tr id="header" class="week_row">
                        <th colspan="32">
                            <img src="<?php echo Router::url("img/prev.png");?>" alt="prev" id="prev" class="weekbutton">
                            <span id="monthname"><?php echo $table['mois']; ?></span>
                            <img src="<?php echo Router::url("img/next.png");?>" alt="next" id="next" class="weekbutton">
                        </th>
                    </tr>
                    <tr id="mois">
                        <?php echo $table['month_detail']; ?>
                    </tr>
                </thead>
                <tbody id="all">
                    <?php echo $table['allusers']; ?>
                </tbody>
            </table>
            <hr>
        <p>
            <a href="/" class="css3button">Retour</a>
            <a href="<?php echo $table['printline'];?>" class="css3button" id="printline" target="_blank">Imprimer</a>
        </p>

        </div>
<script type="text/javascript" src="<?php echo Router::webroot("/js/month.js")?>"></script>