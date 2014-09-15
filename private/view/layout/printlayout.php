<!DOCTYPE html>
<html lang='fr'>
    <head>
       <meta charset="utf-8">
        <title><?php if(isset($layoutTitle)){ echo $layoutTitle.' - ';}?>TDS</title>
        <!--[if lt ie 9]><script type="text/javascript">
            var e=["header","footer","section","article","hgroup","nav","aside","figure","figcaption"];
            for(var p in e){document.createElement(e[p])};
        </script><![endif]-->
        <!--[if lte ie 8]><script type="text/javascript">
        document.documentElement.className+=' ie7';
        </script><![endif]-->
        <!--[if lte IE 7]><script src="<?php echo Router::webroot("/js/lte-ie7.js")?>"</script><![endif]-->
        <link rel="stylesheet" type="text/css" href="<?php echo Router::webroot("css/global.css")?>">
        <link rel="stylesheet" type="text/css" href="<?php echo Router::webroot("css/tds.css") ?>" media="all">
        <link rel="stylesheet" type="text/css" href="<?php echo Router::webroot("css/month.css") ?>" media="all">
        <link rel="stylesheet" type="text/css" href="<?php echo Router::webroot("css/print.css")?>" media="print" >
        <link rel="shortcut icon" type="image/gif" href="<?php echo Router::webroot("img/TDS.gif")?>" /> 
    </head>
    <body>
        <div id="wrapper">
        <?php echo $layoutContent; ?>
        </div>
    </body>
</html>
