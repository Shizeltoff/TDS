<div>
    <h1 style="text-align:center"><?php echo $section; ?></h1>
</div>
<div id="contenu">
    <table id="tds" class="tab">
            <thead>
                <tr id="header" class="week_row">
                    <th colspan="2" id="mois"><?php echo $table['moisannee']; ?></th>
                    <th colspan="4">
                        <span id="weeknum">Semaine <?php echo $table['sem']; ?></span>
                    </th>
                </tr>
                <tr id="semaine">
                    <?php echo $table['semaine']; ?>
                </tr>
            </thead>
            <tbody id="all">
                <!-- <tr class="empty"><td colspan="6">&nbsp;</td></tr> -->
                <?php echo $table['all']; ?>
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
<script type="text/javascript">
    window.print();
</script>