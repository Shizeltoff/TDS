<div class="clearfix">
    <div id="contenu" class="content">
        <div id="section">
        	<h1><?php echo $nom_section; ?></h1>
        </div>
        <table id="tds" class="month">
                <thead>
                    <tr id="header" class="week_row">
                        <th colspan="32">
                            <span id="monthname"><?php echo $table['mois']; ?></span>
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
        </div>
</div>
<script type="text/javascript">
    window.print();
</script>