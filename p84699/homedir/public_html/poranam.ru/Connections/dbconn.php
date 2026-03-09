<?php
@session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$hostname_dbconn = "localhost";
$database_dbconn = "p84699_db";
$username_dbconn = "p84699_dbuser";
$password_dbconn = "v*#zIGKvAvcPGZUh";

$pop_mail_server = "localhost";
$smtp_mail_server = "localhost";

$GLOBALS['main_domain'] = 'climate.3net.ru';

define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('DB_TYPE', 'mysql');
define('NOCLEAN', 'NO');
ini_set('date.timezone', 'Europe/Moscow');

if (DB_TYPE == 'mysql') {
    $dbconn = mysqli_init ();
    mysqli_real_connect($dbconn, $hostname_dbconn, $username_dbconn, $password_dbconn, $database_dbconn);
    //mysqli_options($dbconn, MYSQLI_OPT_LOCAL_INFILE, 'on');
    if (!$dbconn) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }
    $GLOBALS['dbconn'] = $dbconn;
} elseif (DB_TYPE == 'oracle') {
    putenv("ORACLE_HOME=$home_dbconn");
    //putenv("NLS_LANG=AMERICAN_AMERICA.CL8MSWIN1251");
    $dbconn = oci_pconnect($username_dbconn, $password_dbconn, $database_dbconn);
    if (!$dbconn) {
        $err = oci_error();
        print "Error code = " . $err['code'];
        print "<br>Error message = " . htmlentities($err['message']);
        print "<br>Error position = " . $err['offset'];
        print "<br>SQL Statement = " . htmlentities($err['sqltext']);
    }
}

$_SESSION['user_lang'] = ($_SESSION['user_lang'] == '') ? 'ru' : $_SESSION['user_lang'];

//$dbconn = mysql_pconnect($hostname_dbconn, $username_dbconn, $password_dbconn) or trigger_error(mysqli_error()(),E_USER_ERROR);
require_once('functions.php');
$url_arr = explode('/', $_SERVER['SCRIPT_NAME']);
if ($url_arr[1] == 'editor') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/editor/e_modules/logging/logInit.php';
}
include "site_props.php";
el_dbselect("SET NAMES 'utf8'", 0, $res, 'result');
el_dbselect("SET character_set_server='utf8'", 0, $res, 'result');
$debug = true;
//error_reporting(E_ALL);
?>