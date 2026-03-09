<?PHP
@session_start();

$entered_login = (strlen($_POST['entered_login']) > 0) ? $_POST['entered_login'] : $_SESSION['login'];
$entered_password = (strlen($_POST['entered_password']) > 0) ? $_POST['entered_password'] : $_SESSION['password'];

$getParams = '';
if (isset($_GET['right'])) {
    $getParams = '?right=' . intval($_GET['right']);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
// loading functions and libraries
function random($max)
{
    // create random number between 0 and $max
    srand((double)microtime() * 1000000);
    $r = round(rand(0, $max));
    if ($r != 0) $r = $r - 1;
    return $r;
}

function rotateBg()
{
    // rotate background login interface
    global $backgrounds, $bgImage, $i;
    $c = count($backgrounds);
    if ($c == 0) return;
    $r = random($c);
    if ($backgrounds[$r] == '' && $i < 10) {
        $i++;
        rotateBg();
    } elseif ($i >= 10) {
        if (!$bgImage || $bgImage == '') {
            $bgImage = 'bg_lock.gif';
        } else {
            $bgImage = $bgImage;
        }
    } else {
        $bgImage = $backgrounds[$r];
    }
    return $bgImage;
}

function in_array_php3($needle, $haystack)
{
    // check if the value of $needle exist in array $haystack
    // works for both php3 and php4
    if ($needle && $haystack) {
        if (phpversion() >= 4) {
            // phpversion = 4
            return (in_array($needle, $haystack));
        } else {
            // phpversion = 3
            for ($i = 0; $i <= count($haystack); $i++) {
                if ($haystack[$i] == $needle) {
                    return (true);
                }
            }
            return (false);
        }
    } else return (false);
}

if ($noDetailedMessages == true) {
    $strUserNotExist = $strUserNotAllowed = $strPwNotFound = $strPwFalse = $strNoPassword = $strNoAccess;
}
if ($bgRotate == true) {
    $i = 0;
    $bgImage = rotateBg();
}


// check if login is necesary
if (empty($_POST['entered_login']) && empty($_POST['entered_password'])) {
    // use data from session
    @session_start();
} else {
    // use entered data
    $login = $_POST['entered_login'];
    $password = $_POST['entered_password'];
    $_SESSION['login'] = $login;
    $_SESSION['password'] = $password;
}

if (empty($_SESSION['login'])) {
    // no login available
    include($cfgProgDir . "interface.php");
    exit;
}
if (empty($_SESSION['password'])) {
    // no password available
    $message = $strNoPassword;
    include($cfgProgDir . "interface.php");
    exit;
}


// use phpSecurePages with Database
if ($useDatabase == true) {
    // contact database
    if (empty($cfgServerPort)) {
        mysqli_connect($cfgServerHost, $cfgServerUser, $cfgServerPassword, $cfgDbDatabase)
        or die($strNoConnection);
    } else {
        mysqli_connect($cfgServerHost . ":" . $cfgServerPort, $cfgServerUser, $cfgServerPassword, $cfgDbDatabase)
        or die($strNoConnection);
    };
    $userQuery = mysqli_query($dbconn, "SELECT * FROM $cfgDbTableUsers WHERE $cfgDbLoginfield = '" . $_SESSION['login'] . "'");

    // check user and password
    if (mysqli_num_rows($userQuery) != 0) {
        // user exist --> continue
        $userArray = el_dbfetch($userQuery);

        if ($_SESSION['login'] != $userArray[$cfgDbLoginfield]) {
            // Case sensative user not present in database
            $message = $strUserNotExist;
//			include($cfgProgDir . "logout.php");
            include($cfgProgDir . "interface.php");
            exit;
        }
    } else {
        // user not present in database
        $message = $strUserNotExist;
//		include($cfgProgDir . "logout.php");
        include($cfgProgDir . "interface.php");
        exit;
    }
    if (!$userArray[$cfgDbPasswordfield]) {
        // password not present in database for this user
        $message = $strPwNotFound;
        include($cfgProgDir . "interface.php");
        exit;
    }


    //$userpass=$userArray["$cfgDbPasswordfield"];
    $userpass = crypt(md5($_SESSION['password']), "$1$" . $userArray["$cfgDbPasswordfield"]);

    if (stripslashes("$1$" . $userArray["$cfgDbPasswordfield"]) === $userpass) {
        $flag;
        $_SESSION['fio'] = $userArray["fio"];
        $_SESSION['user_id'] = $userArray["primary_key"];
        $_SESSION['user_level'] = $_SESSION['ulevel'] = $userArray['userlevel'];
        $_SESSION['user_group'] = $userArray['usergroup'];
        if($userArray['userlevel'] > 1) {
            $_SESSION['site_id'] = $userArray['usergroup'];
        }
    } else {
        // password is wrong
        $message = $strPwFalse;
//		include($cfgProgDir . "logout.php");
        include($cfgProgDir . "interface.php");
        exit;
    }
    if (isset($userArray["$cfgDbUserLevelfield"]) && !empty($cfgDbUserLevelfield)) {
        $userLevel = stripslashes($userArray["$cfgDbUserLevelfield"]);
    }
    if (($requiredUserLevel && !empty($requiredUserLevel[0])) || $minUserLevel) {
        // check for required user level and minimum user level
        if (!isset($userArray["$cfgDbUserLevelfield"])) {
            // check if column (as entered in the configuration file) exist in database
            $message = $strNoUserLevelColumn;
            include($cfgProgDir . "interface.php");
            exit;
        }
        if (empty($cfgDbUserLevelfield) || (!in_array($userLevel, $requiredUserLevel) && (!isset($minUserLevel) || empty($minUserLevel) || $userLevel < $minUserLevel))) {
            // this user does not have the required user level
            $message = $strUserNotAllowed;
            include($cfgProgDir . "interface.php");
            exit;
        }
    }
    if (isset($userArray["$cfgDbUserLevelfield"]) && !empty($cfgDbUserLevelfield)) {
        $ID = stripslashes($userArray["$cfgDbUserIDfield"]);
    }
} // use phpSecurePages with Data
elseif ($useData == true && $useDatabase != true) {
    $numLogin = count($cfgLogin);
    $userFound = false;
    // check all the data input
    for ($i = 1; $i <= $numLogin; $i++) {
        if ($cfgLogin[$i] != '' && $cfgLogin[$i] == $_SESSION['login']) {
            // user found --> check password
            if ($cfgPassword[$i] == '' || $cfgPassword[$i] != $_SESSION['password']) {
                // password is wrong
                $message = $strPwFalse;
                include($cfgProgDir . "logout.php");
                include($cfgProgDir . "interface.php");
                exit;
            }
            $userFound = true;
            $userNr = $i;
        }
    }
    if ($userFound == false) {
        // user is wrong
        $message = $strUserNotExist;
        include($cfgProgDir . "logout.php");
        include($cfgProgDir . "interface.php");
        exit;
    }
    $userLevel = $cfgUserLevel[$userNr];
    if (($requiredUserLevel && !empty($requiredUserLevel[0])) || $minUserLevel) {
        // check for required user level and minimum user level
        if (!in_array($userLevel, $requiredUserLevel) && (!isset($minUserLevel) || empty($minUserLevel) || $userLevel < $minUserLevel)) {
            // this user does not have the required user level
            $message = $strUserNotAllowed;
            include($cfgProgDir . "interface.php");
            exit;
        }
    }
    $ID = $cfgUserID[$userNr];
} // neither of the two data inputs was chosen
else {
    $message = $strNoDataMethod;
    include($cfgProgDir . "interface.php");
    exit;
}

// restore values
if (strlen($dbOld) > 0) $db = $dbOld;
if (strlen($messageOld) > 0) $message = $messageOld;
?>