<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
//error_reporting(E_ALL);
$requiredUserLevel = array(0, 1, 2, 3, 4);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$site_id = intval($_SESSION['site_id']);
$user_id = intval($_SESSION['user_id']);

$q = el_dbselect("SELECT * FROM catalog_support_data WHERE 
id = ".intval($_GET['id'])." ORDER BY field2 DESC, field4 DESC", 20, $q, 'result', true);
$rq = el_dbfetch($q);
?>
<html>
<head>
    <title>Техническая поддержка</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="style.css?v=<?= el_genpass() ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/css/flatpickr.min.css">
    <script src="/js/jquery-1.11.0.min.js"></script>
</head>

<body>
<?php
if(el_dbnumrows($q) > 0){
    do{
        ?>
     <div class="message">
         <h4><?=$rq['field1']?></h4>
         <span class="date"><?=$rq['field2']?> <?=$rq['field4']?></span>
         <p><?=$rq['field6']?></p>
     </div>
<?php
    }while($rq = el_dbfetch($q));
}
?>
</body>
</html>
