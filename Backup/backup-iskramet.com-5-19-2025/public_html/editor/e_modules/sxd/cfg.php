<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
$CFG = array (
  'charsets' => 'cp1251 utf8 latin1',
  'lang' => 'auto',
  'time_web' => '600',
  'time_cron' => '600',
  'backup_path' => 'backup/',
  'backup_url' => 'backup/',
  'only_create' => 'MRG_MyISAM MERGE HEAP MEMORY',
  'globstat' => 0,
  'my_host' => $hostname_dbconn,
  'my_port' => 3306,
  'my_user' => $username_dbconn,
  'my_pass' => $password_dbconn,
  'my_comp' => 0,
  'my_db' => $database_dbconn,
  'auth' => 'mysql cfg',
  'user' => '',
  'pass' => '',
  'confirm' => '6',
  'exitURL' => './',
);
?>