<?php
session_start();
//ini_set('session.gc_maxlifetime', 60*60*24*7);
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php');
//@session_start();
$entered_login=(strlen($_POST['entered_login'])>0)?$_POST['entered_login']:$_SESSION['login'];
$entered_password=(strlen($_POST['entered_password'])>0)?$_POST['entered_password']:$_SESSION['password'];

@session_set_cookie_params(60*60*24*7);
//session_save_path(getcwd().DIRECTORY_SEPARATOR . "temp");

/**************************************************************/
/*              phpSecurePages version 0.20 beta              */
/*          Written by Paul Kruyt - kruyt@email.com           */
/*           Start of phpSecurePages Configuration            */
/**************************************************************/

/****** Installation ******/
$cfgProgDir =  '/editor/secure/';
  // location of phpSecurePages calculated from the root of the server
  // Example: if you installed phpSecurePages on http://www.mydomain.com/phpSecurePages/
  // the value would be $cfgProgDir = '/phpSecurePages/'
$cfgIndexpage = '';
  // page to go to, if login is cancelled
  // Example: if your main page is http://www.mydomain.com/index.php
  // the value would be $cfgIndexpage = '/index.php'
$admEmail = 'info@'.str_replace("www.","",$_SERVER['SERVER_NAME']);
  // E-mail adres of the site administrator
  // (This is being showed to the users on an error, so you can be notified by the users)
$noDetailedMessages = false;
  // Show detailed error messages (false) or give one single message for all errors (true).
  // If set to 'false', the error messages shown to the user describe what went wrong.
  // This is more user-friendly, but less secure, because it could allow someone to probe
  // the system for existing users.
$languageFile = 'lng_russian-cp1251.php';        // Choose the language file
$bgImage = 'bg_lock.gif';                 // Choose the background image
$bgRotate = true;                         // Rotate the background image from list
                                          // (This overrides the $bgImage setting)


/****** Lists ******/
// List of backgrounds to rotate through
$backgrounds[] = 'bg_lock.gif';
$backgrounds[] = 'bg_gun.gif';


/****** Database ******/
$useDatabase = true;                     // choose between using a database or data as input

/* this data is necessary if a database is used */
$cfgServerHost = $hostname_dbconn;             // MySQL hostname
$cfgServerPort = '';                      // MySQL port - leave blank for default port
$cfgServerUser = $username_dbconn;                  // MySQL user
$cfgServerPassword = $password_dbconn;                  // MySQL password

$cfgDbDatabase = $database_dbconn;        // MySQL database name containing phpSecurePages table
$cfgDbTableUsers = 'phpSP_users';         // MySQL table name containing phpSecurePages user fields
$cfgDbLoginfield = 'user';                // MySQL field name containing login word
$cfgDbPasswordfield = 'password';         // MySQL field name containing password
$cfgDbUserLevelfield = 'userlevel';       // MySQL field name containing user level
  // Choose a number which represents the category of this users authorization level.
  // Leave empty if authorization levels are not used.
  // See readme.txt for more info.
$cfgDbUserIDfield = 'primary_key';        // MySQL field name containing user identification
  // enter a distinct ID if you want to be able to identify the current user
  // Leave empty if no ID is necessary.
  // See readme.txt for more info.


/****** Database - PHP3 ******/
/* information below is only necessary for servers with PHP3 */
$cfgDbTableSessions = 'phpSP_sessions';
  // MySQL table name containing phpSecurePages sessions fields
$cfgDbTableSessionVars = 'phpSP_sessionVars';
  // MySQL table name containing phpSecurePages session variables fields


/****** Data ******/
$useData = true;                          // choose between using a database or data as input

/* this data is necessary if no database is used */
$cfgLogin[1] = '';                        // login word
$cfgPassword[1] = '';                     // password
$cfgUserLevel[1] = '';                    // user level
  // Choose a number which represents the category of this users authorization level.
  // Leave empty if authorization levels are not used.
  // See readme.txt for more info.
$cfgUserID[1] = '';                       // user identification
  // enter a distinct ID if you want to be able to identify the current user
  // Leave empty if no ID is necessary.
  // See readme.txt for more info.

$cfgLogin[2] = '';
$cfgPassword[2] = '';
$cfgUserLevel[2] = '';
$cfgUserID[2] = '';

$cfgLogin[3] = '';
$cfgPassword[3] = '';
$cfgUserLevel[3] = '';
$cfgUserID[3] = '';


/**************************************************************/
/*             End of phpSecurePages Configuration            */
/**************************************************************/


// getting other login variables
$cfgHtmlDir = "http://" . getenv("HTTP_HOST") . $cfgProgDir;
$cfgProgDir = getenv("DOCUMENT_ROOT") . $cfgProgDir;
//if ($message) $messageOld = $message;
$message = false;


// include functions and variables
function admEmail() {
	// create administrators email link
	global $admEmail;
	return("<A HREF='mailto:$admEmail'>$admEmail</A>");
}
include($cfgProgDir . $languageFile);
//include($cfgProgDir . "session.php");


// choose between login or logout
if (isset($_GET['logout']) || $logout) {
	// logout
	include($cfgProgDir."logout.php");
} else {
	// loading login check
	include($cfgProgDir."checklogin.php");
}

@setcookie(session_name(), session_id(), time()+60*60*24*7, "/"); 
?>