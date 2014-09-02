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
            <hr>

        </div>
</div>
<script type="text/javascript">
    window.print();
</script>