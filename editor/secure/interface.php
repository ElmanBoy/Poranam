<?PHP
//  ------ create table variable ------
// variables for Netscape Navigator 3 & 4 are +4 for compensation of render errors
$Browser_Type = strtok($_SERVER['HTTP_USER_AGENT'], "/");
if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/Mozilla\/5\.0/", $_SERVER['HTTP_USER_AGENT'])) {
    $theTable = 'WIDTH="400" HEIGHT="245"';
} else {
    $theTable = 'WIDTH="404" HEIGHT="249"';
}

// ------ create document-location variable ------
if (preg_match("/php\.exe/", $_SERVER['PHP_SELF']) || preg_match("/php3\.cgi/", $_SERVER['PHP_SELF'])) {
    // $documentLocation = $HTTP_ENV_VARS["PATH_INFO"];
    $documentLocation = getenv("PATH_INFO") . $getParams;
} else {
    $documentLocation = $_SERVER['PHP_SELF'] . $getParams;
}

?>
<html>
<head>
    <meta name="description" content="<?PHP echo $strLoginInterface; ?>">
    <meta name="keywords" content="<?PHP echo $strLogin; ?>">
    <title><?PHP echo $strLoginInterface; ?></title>

    <SCRIPT LANGUAGE="JavaScript">
        <!--
        //  ------ check form ------
        function checkData() {
            var f1 = document.forms[0];
            var wm = "<?PHP echo $strJSHello; ?>\n\r\n";
            var noerror = 1;

            // --- entered_login ---
            var t1 = f1.entered_login;
            if (t1.value == "" || t1.value == " ") {
                wm += "<?PHP echo $strLogin; ?>\r\n";
                noerror = 0;
            }

            // --- entered_password ---
            var t1 = f1.entered_password;
            if (t1.value == "" || t1.value == " ") {
                wm += "<?PHP echo $strPassword; ?>\r\n";
                noerror = 0;
            }

            // --- check if errors occurred ---
            if (noerror == 0) {
                alert(wm);
                return false;
            } else return true;
        }

        function MM_goToURL() { //v3.0
            var i, args = MM_goToURL.arguments;
            document.MM_returnValue = false;
            for (i = 0; i < (args.length - 1); i += 2) eval(args[i] + ".location='" + args[i + 1] + "'");
        }

        //-->
    </SCRIPT>

    <style type="text/css">
        <!--
        A:hover.link {
            background-color: #E9E9E9;
        }

        table {
            width: 260px;
        }

        table tr td {
            padding: 5px;
            margin: 5px;
        }

        h4 {
            padding: 5px;
            font-weight: normal;
        }

        /
        /
        -->
    </style>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<center>
    <form action='<?PHP echo $documentLocation; ?>' METHOD="post" onSubmit="return checkData()">
        <TABLE WIDTH="100%" HEIGHT="100%" CELLPADDING="0" CELLSPACING="0">
            <TR>
                <TD ALIGN="center" VALIGN="middle">

                    <!-- Place your logo here -->

                    <TABLE border="1" CELLPADDING="0" CELLSPACING="0" bordercolor="#FFFFFF" bgcolor="#F0F0F0">
                        <TR>
                            <TD ALIGN="center" VALIGN="middle">
                                <TABLE CELLPADDING="4" WIDTH="100%" HEIGHT="100%" BACKGROUND="" class="el_tbl">
                                    <TR>
                                        <TD ALIGN="center" COLSPAN="2"><h4><?PHP echo $strLoginInterface; ?></h4></TD>
                                    </TR>
                                    <?PHP
                                    // check for error messages
                                    if ($message) {
                                        ?>
                                        <TR>
                                            <TD ALIGN="center" COLSPAN="2">
                                                <B><I>
                                                        <NOBR><? echo $message; ?> </NOBR>
                                                    </I></B>
                                            </TD>
                                        </TR>
                                    <? } ?>
                                    <tr>
                                        <td style="border:0px"><B><FONT FACE="Arial,Helvetica,sans-serif" SIZE="-1"><?PHP echo $strLogin; ?>
                                                    : </FONT></B></td>
                                        <td style="border:0px"><INPUT TYPE="text" NAME="entered_login" STYLE="font-size: 9pt;" TABINDEX="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:0px"><B><FONT FACE="Arial,Helvetica,sans-serif" SIZE="-1"><?PHP echo $strPassword; ?>
                                                    : </FONT></B></td>
                                        <td style="border:0px"><INPUT TYPE="password" NAME="entered_password" STYLE="font-size: 9pt;" TABINDEX="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <TD VALIGN="bottom">
                                                <input type="button" class="but close"
                                                       onClick="MM_goToURL('parent','<?PHP echo "http://" . getenv("HTTP_HOST") . $cfgIndexpage; ?>')" value="Отмена">
                                        </TD>
                                        <td ALIGN="right" VALIGN="bottom">
                                            <input name="submit" type=submit class="but agree" tabindex="1" value="Вход" style="float: none">
                                        </td>
                                    </tr>
                                </table>
                            </TD>
                        </TR>
                    </TABLE>

                </TD>
            </TR>
        </TABLE>
    </form>
</center>

<SCRIPT LANGUAGE="JavaScript">
    <!--
    document.forms[0].entered_login.select();
    document.forms[0].entered_login.focus();
    //-->
</SCRIPT>
</body>
</html>
