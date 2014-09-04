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
            <div class="legend_box">
                <table class="tdslegend monthlegend">
                    <tr>
                        <td><div class="off">Journée Off</div></td>
                        <td><div class="ca">Congés Payés</div></td>
                        <td><div class="rtt">RTT</div></td>
                        <td><div class="rcp">Récup</div></td>
                        <td><div class="fo">Formation</div></td>
                        <td><div class="mal">Maladie</div></td>
                        <td><div class="mi">Mission</div></td>
                        <td><div class="mo">MO</div></td>
                        <td><div class="oth">Autre</div></td>
                        <td><div class="taf">Présence</div></td>
                    </tr>
                </table>
            </div>
            <hr>
        <p>
            <a href="<?php echo Router::url('tds/edittds') ;?>" class="css3button">Retour</a>
            <a href="<?php echo Router::url($table['printline']) ;?>"  class="css3button" id="printline" target="_blank">Imprimer</a>
            <!-- <a href="<?php //echo $table['printline'];?>" class="css3button" id="printline" target="_blank">Imprimer</a> -->
        </p>

        </div>
<script type="text/javascript" src="<?php echo Router::webroot("/js/month.js")?>"></script>