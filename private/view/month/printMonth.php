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
            <table class="printlegend">
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
</div>
<script type="text/javascript">
    window.print();
    window.close();
</script>