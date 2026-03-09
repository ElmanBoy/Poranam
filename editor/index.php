<?PHP
session_start();
require_once('../Connections/dbconn.php');

$query_access1 = "SELECT * FROM userstatus";
$access1 = el_dbselect($query_access1, 0, $access1, 'result', true);
$row_access1 = el_dbfetch($access1);
$arreqlevel = array();
do {
    array_push($arreqlevel, $row_access1['level']);
} while ($row_access1 = el_dbfetch($access1));

$requiredUserLevel = $arreqlevel;//array(1, 2, 4);

include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

$query_user = "SELECT phpSP_users.fio AS fio, phpSP_users.userlevel AS userlevel, userstatus.name AS role FROM phpSP_users, userstatus 
WHERE phpSP_users.user='" . $_SESSION['login'] . "' AND phpSP_users.userlevel=userstatus.level";
$user = el_dbselect($query_user, 0, $user, 'result', true);
$row_user = el_dbfetch($user);

switch ($_GET['right']) {
    case 1:
        $urlRight = 'editor.php?cat=' . intval($_GET['cat']);
        break;
    case 2:
        $urlRight = 'infoblocks.php';
        break;
    case 3:
        $urlRight = 'upfile.php';
        break;
    case 4:
        $urlRight = 'modules/advert/index.php';
        setcookie('idshowtree[8]', "Y");
        break;
    case 5:
        $urlRight = 'modules/subscribe/index.php';
        setcookie('idshowtree[7]', "Y");
        break;
    case 6:
        $urlRight = 'e_modules/dbserv.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 7:
        $urlRight = 'e_modules/logging/log.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 8:
        $urlRight = 'modules.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 9:
        $urlRight = 'templates.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 10:
        $urlRight = 'modules/forms/catalogs.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 11:
        $urlRight = 'modules/catalog/catalogs.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 12:
        $urlRight = 'users.php';
        setcookie('idshowtree[6]', "Y");
        break;
    case 13:
        $urlRight = 'phpinfo.php';
        setcookie('idshowtree[6]', "Y");
        break;
    default:
        /*if($row_user['userlevel'] == 1 || $row_user['userlevel'] == 0) {
            $urlRight = 'sites.php';
        }elseif($row_user['userlevel'] == 4){
            $urlRight = 'queue.php';
        }else{*/
            $_SESSION['site_id'] = 1;
            $_SESSION['view_site_id'] = 1;
            $urlRight = 'menuadmin.php?site_is=1';
        //}
        break;
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Административный раздел <?= $_SERVER['SERVER_NAME'] ?></title>
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700&display=swap&subset=cyrillic" rel="stylesheet">
    <script language="javascript">

        //var docH=document.body.clientHeight;
        function show_hide() {
            var docW = document.body.clientWidth;
            if (document.getElementById("right").width != docW) {
                document.getElementById("left").width = 1;
                document.getElementById("right").width = docW;
                document.getElementById("open_left").src = "img/leftmenu_open.gif";
                document.cookie = "lpanel=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
            } else {
                document.getElementById("left").width = 230;
                document.getElementById("right").width = docW - 230;
                document.getElementById("open_left").src = "img/spacer.gif";
                document.cookie = "lpanel=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
            }
        }

        function resizeWindow() {
            var docW = document.body.clientWidth;
            var docH = document.body.clientHeight;
            var t = document.getElementById("mainTable");
            var c = document.getElementById("right");
            t.width = docW;
            t.height = docH;
            c.width = docW - 230;
            c.height = docH;
        }

        function MM_displayStatusMsg(msgStr) { //v1.0
            status = msgStr;
            document.MM_returnValue = true;
        }

        var pCurrWidth;
        pCurrWidth = 1;
        var interv = "";
        var pEnd = 0;
        var undraw1 = "";
        var opacity = 100;

        var speed = 50;

        function undrawProgress() {
            var pProgress_fon = document.getElementById("progress_fon");//СЃСЃС‹Р»РєР° РЅР° С„РѕРЅ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
            var pProgress_wrap = document.getElementById("progress_wrapper");//СЃСЃС‹Р»РєР° РЅР° РѕР±РѕР»РѕС‡РєСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
            var pProgress = document.getElementById("progress");//СЃСЃС‹Р»РєР° РЅР° РїРѕР»РѕСЃСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°

            opacity = opacity - 15;
            pProgress_wrap.style.filter = "Alpha(Opacity=" + opacity + ")";
            pProgress.style.filter = "Alpha(Opacity=" + opacity + ")";
            if (opacity < 1) {

                window.clearInterval(undraw1);
                undraw1 = "";
                pProgress_wrap.style.visibility = "hidden";
                pProgress.style.visibility = "hidden";
                pProgress_fon.style.visibility = "hidden";
            }
        }


        function grawProgress() {
            var pProgress_fon = document.getElementById("progress_fon");//СЃСЃС‹Р»РєР° РЅР° С„РѕРЅ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
            var pProgress_wrap = document.getElementById("progress_wrapper");//СЃСЃС‹Р»РєР° РЅР° РѕР±РѕР»РѕС‡РєСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
            var pProgress = document.getElementById("progress");//СЃСЃС‹Р»РєР° РЅР° РїРѕР»РѕСЃСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°

            pCurrWidth = pCurrWidth + 1;
            if (pCurrWidth < 400) {
                pProgress.style.width = pCurrWidth + "px";
            } else {
                window.clearInterval(interv);
                interv = "";
                if (undraw1 == "") {
                    undraw1 = window.setInterval("undrawProgress()", speed);
                }
            }
            if (pCurrWidth >= pEnd) {
                //pProgress_fon.style.visibility="hidden";
                window.clearInterval(interv);
                interv = "";
            }
        }

        var pMessage = "РџРѕР¶Р°Р»СѓР№СЃС‚Р°, РїРѕРґРѕР¶РґРёС‚Рµ...";

        function drawProgress(currpos, endpos) {
            if (pCurrWidth < 400) {
                var pProgress_fon = document.getElementById("progress_fon");//СЃСЃС‹Р»РєР° РЅР° С„РѕРЅ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
                var pProgress_wrap = document.getElementById("progress_wrapper");//СЃСЃС‹Р»РєР° РЅР° РѕР±РѕР»РѕС‡РєСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
                var pProgress = document.getElementById("progress");//СЃСЃС‹Р»РєР° РЅР° РїРѕР»РѕСЃСѓ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
                var pMess = document.getElementById("mess");//СЃСЃС‹Р»РєР° РЅР° С‚РµРєСЃС‚ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂР°
                pMess.innerHTML = pMessage;
                pProgress_fon.style.visibility = "visible";
                pProgress_wrap.style.visibility = "visible";
                pProgress.style.visibility = "visible";
                pProgress_wrap.style.filter = "Alpha(Opacity=100)";
                pProgress.style.filter = "Alpha(Opacity=100)";
                pCurrWidth = currpos;
                pEnd = endpos;
                if (interv == "") {
                    interv = window.setInterval("grawProgress()", speed);
                }
            }
        }

        function stopProgress() {
            //pCurrWidth=400;
            document.getElementById("progress_wrapper").style.display = "none";
            document.getElementById("progress_fon").style.visibility = "hidden";
        }

        function dragStart(ev) {
            ev.dataTransfer.effectAllowed='move';
            ev.dataTransfer.setData("Text", ev.target.getAttribute('id'));
            ev.dataTransfer.setDragImage(ev.target,100,100);
            return true;
        }

        function MM_openBrWindow(theURL, winName, features, myWidth, myHeight, isCenter) { //v3.0
            $("#preloader").show();

            if(myWidth.indexOf("%") !== -1){
                myWidth = ($(document).width() / 100) * parseInt(myWidth.replace("%", ""));
            }
            if(myHeight.indexOf("%") !== -1){
                myHeight = ($(window).height() / 100) * parseInt(myHeight.replace("%", ""));
            }
            myHeight = parseInt(myHeight) + 70;

            if (window.screen) if (isCenter) if (isCenter == "true") {
                var myLeft = ($(document).width() / 2) - myWidth / 2;
                var myTop = ($(window).height()) / 2 - myHeight / 2;
                if (myTop < 0) myTop = 20;
                features += (features != '') ? ',' : '';
                features += ',left=' + myLeft + ',top=' + myTop;
            }

            if (!$("#modal").is("div")) {
                $("<div id='modalWrap'></div><div id='modal' draggable='true' ondragstart='return dragStart(event)'><div class='close' id='modalheader'><i " +
                    "class='material-icons' title='Закрыть'>close</i></div><iframe></iframe></div>")
                    .insertBefore("body");

                $("#modal > .close > i").on("click", function () {
                    closeDialog();
                });
                $('#modal > .close > i').tooltip({showURL: false});
                dragElement(document.getElementById("modal"));
            }
            $("#modal").css({
                "top": myTop,
                "left": myLeft,
                "width": myWidth,
                "height": myHeight
            });
            $("#modal iframe").attr("src", theURL).css({
                "width": myWidth,
                "height": myHeight - 30
            }).css("visibility", "hidden")
                .on("load", function(){
                    $(this).css("visibility", "visible");
                    $("#preloader").hide();
                });
            $("#modal, #modalWrap").show();
        }

        function dragElement(elmnt) {
            var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            if (document.getElementById(elmnt.id + "header")) {
                // if present, the header is where you move the DIV from:
                document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
            } else {
                // otherwise, move the DIV from anywhere inside the DIV:
                elmnt.onmousedown = dragMouseDown;
            }

            function dragMouseDown(e) {
                e = e || window.event;
                e.preventDefault();
                // get the mouse cursor position at startup:
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                // call a function whenever the cursor moves:
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e = e || window.event;
                e.preventDefault();
                // calculate the new cursor position:
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                // stop moving when mouse button is released:
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }

        function closeDialog() {
            $("#modal, #modalWrap").hide();
        }

        function reloadFrame(){
            $("#MainFrame").attr("src", document.getElementById("MainFrame").contentDocument.location.href);
        }

        function reloadMenu(siteId){
            $("#leftMenu").attr("src", "leftmenu.php?siteId=" + siteId);
        }

        function hideMenuAdmin(){
            document.getElementById("leftMenu").contentDocument.getElementById("menuadmin").style.display="none";
        }

        function showMenuAdmin(site_id){
            var $ma = document.getElementById("leftMenu").contentDocument.getElementById("menuadmin");
            var $in = document.getElementById("leftMenu").contentDocument.getElementById("infoblocks");
            $ma.style.display="block";
            $in.style.display="block";
            $($ma).find("a").attr("href", "menuadmin.php?site_id=" + site_id);
            $($in).find("a").attr("href", "infoblocks.php?site_id=" + site_id);
        }

        $(document).ready(function(){
            $('*').tooltip({showURL: false});
        });

    </script>
    <link href="style.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        body {
            margin: 0;
        }

        body iframe, body table{
            height: 100%;
        }

        .style1 {
	/* [disabled]color: #336666; */
	/* [disabled]font-weight: bold; */
	/* [disabled]font-size: 1rem; */
        }

        .style2 {
	/* [disabled]font-size: 11px; */
        }

        .style3 {
	/* [disabled]font-size: 10px; */
	/* [disabled]color: #215253; */
        }

        .style4 {
	/* [disabled]color: #FFFFFF; */
        }

        #preloader {
            display: block;
            position: absolute;
            left: 48%;
            top: 48%;
            width: 54px;
            height: 54px;
            z-index: 1000000;
        }

        .sk-cube-grid {
            width: 4em;
            height: 4em;
            margin: auto;
        }

        .sk-cube-grid .sk-cube {
            width: 33%;
            height: 33%;
            background-color: #337ab7;
            float: left;
            -webkit-animation: sk-cube-grid-scale-delay 1.3s infinite ease-in-out;
            animation: sk-cube-grid-scale-delay 1.3s infinite ease-in-out;
        }

        .sk-cube-grid .sk-cube-1 {
            -webkit-animation-delay: 0.2s;
            animation-delay: 0.2s;
        }

        .sk-cube-grid .sk-cube-2 {
            -webkit-animation-delay: 0.3s;
            animation-delay: 0.3s;
        }

        .sk-cube-grid .sk-cube-3 {
            -webkit-animation-delay: 0.4s;
            animation-delay: 0.4s;
        }

        .sk-cube-grid .sk-cube-4 {
            -webkit-animation-delay: 0.1s;
            animation-delay: 0.1s;
        }

        .sk-cube-grid .sk-cube-5 {
            -webkit-animation-delay: 0.2s;
            animation-delay: 0.2s;
        }

        .sk-cube-grid .sk-cube-6 {
            -webkit-animation-delay: 0.3s;
            animation-delay: 0.3s;
        }

        .sk-cube-grid .sk-cube-7 {
            -webkit-animation-delay: 0s;
            animation-delay: 0s;
        }

        .sk-cube-grid .sk-cube-8 {
            -webkit-animation-delay: 0.1s;
            animation-delay: 0.1s;
        }

        .sk-cube-grid .sk-cube-9 {
            -webkit-animation-delay: 0.2s;
            animation-delay: 0.2s;
        }
		.main-header{
	height: 3rem;
	line-height: 3rem;
	background: #005c85;
	color: #FFF;
			}
.main-title{
font-weight: 500;
/*font-size: 1rem;*/
color:#fff;
float: left;
line-height: 3rem;
margin-left: 2.5rem;
}

.main-title a{
	color:#fff;
	text-decoration: underline;}
.account {
	float: right;
line-height: 3rem;
margin-right: 2.5rem;
	}
        @-webkit-keyframes sk-cube-grid-scale-delay {
            0%, 70%, 100% {
                -webkit-transform: scale3D(1, 1, 1);
                transform: scale3D(1, 1, 1);
            }
            35% {
                -webkit-transform: scale3D(0, 0, 1);
                transform: scale3D(0, 0, 1);
            }
        }

        @keyframes sk-cube-grid-scale-delay {
            0%, 70%, 100% {
                -webkit-transform: scale3D(1, 1, 1);
                transform: scale3D(1, 1, 1);
            }
            35% {
                -webkit-transform: scale3D(0, 0, 1);
                transform: scale3D(0, 0, 1);
            }
        }

    </style>
</head>

<body>
<div id="progress_fon" style="filter:Alpha(Opacity=20); visibility:hidden; background-color:#D9E0E8; width:100%; height:100%; position:absolute"></div>
<div id="progress_wrapper"
     style="width:410px; padding:10px; background-color:#E9F5F8; border:1px solid #CCCCCC; cursor:wait; text-align:left; border:1px solid #CCCCCC; visibility:hidden; position:absolute; left:43%; top:30%;">
    <div id="mess"  style="margin-bottom:5px;"></div>
    <input type="image" src="img/close.gif" alt="РЎРєСЂС‹С‚СЊ РїСЂРѕРіСЂРµСЃСЃ-Р±Р°СЂ" onClick="stopProgress()"
           style="float:right; margin-right:-10px; margin-top:-25px">
    <div id="progress"
         STYLE="background-image:url(img/progress_fon.gif); border-right: 1px solid #AAAAAA; height:20px; width:1px; visibility:hidden; color:#FFFFFF; font-size:10px"></div>
</div>
<iframe frameborder="0" width="1" height="1" src="longsession.php" style="display:none"></iframe>
<?/*script>
    window.statusbar = "";
    var docH = document.body.clientHeight; //-29
    document.write('<table id=mainTable width="100%" border="0" cellspacing="0" cellpadding="0" height="' + docH + '">');
</script*/?>
<table id=mainTable width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td colspan="3" class="main-header">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>

                <td>
                    <div id="ttop" class="style1">
                        <div class="main-title">Административный раздел сайта <a href="/" target="_blank"><?=$_SERVER['SERVER_NAME']?></a>
<!--<a href="http://<?= strtoupper($_SERVER['SERVER_NAME']) ?>" target="_blank"><?= strtoupper($_SERVER['SERVER_NAME']) ?></a>-->
                        </div>
                        
                        <div class="account">
    <i class="material-icons">account_box</i>
    <? echo "<font style='margin-right: 1rem;''>" . $row_user['role'] . "</font><b>" . $row_user['fio'] . "</b>" ?>
    </div>
                        
                  </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td width="<?= ($_COOKIE['lpanel'] == 'N') ? "1" : "250"; ?>" id="left">
        <iframe src="leftmenu.php" name="topmenu" id="leftMenu" width="245" height="100%" align="left" scrolling="auto" frameborder="0"
                onMouseOver="MM_displayStatusMsg('');return document.MM_returnValue"></iframe>
    </td>

    <td width="1"><img src="<?= ($_COOKIE['lpanel'] == 'N') ? "img/leftmenu_open.gif" : "img/spacer.gif"; ?> " name="open" id="open_left" onClick="show_hide()"
                       title="РџРѕРєР°Р·Р°С‚СЊ РјРµРЅСЋ" style="cursor:e-resize"></td>

    <script language=javascript> var dw = document.body.clientWidth - 230;
        document.write('<td width="<?=($_COOKIE['lpanel'] == 'N') ? "100%" : "'+dw+'";?>" id="right">')</script>
    <iframe align="left" frameborder="0" height="100%" name="Main" id="MainFrame" scrolling="auto" width="100%"
            src="<?= $urlRight ?>"></iframe>
    </td> <!-- <---- </td> бездомный тэг ********************************************* -->
</tr>
</table>
<div id="preloader">
    <div class="sk-cube-grid">
        <div class="sk-cube sk-cube-1"></div>
        <div class="sk-cube sk-cube-2"></div>
        <div class="sk-cube sk-cube-3"></div>
        <div class="sk-cube sk-cube-4"></div>
        <div class="sk-cube sk-cube-5"></div>
        <div class="sk-cube sk-cube-6"></div>
        <div class="sk-cube sk-cube-7"></div>
        <div class="sk-cube sk-cube-8"></div>
        <div class="sk-cube sk-cube-9"></div>
    </div>
</div>
</body>
<script>
    var mf = document.getElementById("MainFrame"),
        pr = document.getElementById("preloader");
    mf.addEventListener("load", function () {
        mf.contentWindow.addEventListener("unload", function () {
            mf.style.visibility = "hidden";
            pr.style.display = "block";
        });
        pr.style.display = "none";
        mf.style.visibility = "visible";
    });
</script>
</html>
