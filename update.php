<?php
$res = null;
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
el_dbselect("UPDATE catalog_init_data SET field14 = 15 WHERE field14 = 14 AND field3 < '".date('Y-m-d H:i:s')."'", 0, $res, 'result', true, true);