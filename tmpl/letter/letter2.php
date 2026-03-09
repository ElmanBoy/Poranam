<?php
$protocol = (substr_count(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') > 0) ? 'https://' : 'https://';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
    <title>Уведомление о записи</title>
    <!--[if !mso]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);
    </style>
    <style type="text/css">
        #outlook a {
            padding: 0;
        }
        
        .ReadMsgBody {
            width: 100%;
        }
        
        .ExternalClass {
            width: 100%;
        }
        
        .ExternalClass * {
            line-height: 100%;
        }
        
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: Ubuntu, Arial, 'Helvetica Neue', Helvetica, FreeSans, sans-serif;
        }
        h1{
            font-size: 1.5rem;;
        }
        
        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }
        
        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <!--[if !mso]><!-->
    <style type="text/css">
        @media only screen and (max-width:480px) {
            @-ms-viewport {
                width: 320px;
            }
            @viewport {
                width: 320px;
            }
        }
    </style>
    <!--<![endif]-->
    <!--[if mso]><xml>  <o:OfficeDocumentSettings>    <o:AllowPNG/>    <o:PixelsPerInch>96</o:PixelsPerInch>  </o:OfficeDocumentSettings></xml><![endif]-->
    <!--[if lte mso 11]><style type="text/css">  .outlook-group-fix {    width:100% !important;  }</style><![endif]-->
    <!--[if !mso]><!-->

    <!--<![endif]-->
    <style type="text/css">
        .hide_on_mobile {
            display: none !important;
        }
        
        @media only screen and (min-width: 480px) {
            .hide_on_mobile {
                display: table-row !important;
            }
        }
        
        [owa] .mj-column-per-100 {
            width: 100% !important;
        }
        
        [owa] .mj-column-per-50 {
            width: 50% !important;
        }
        
        [owa] .mj-column-per-33 {
            width: 33.333333333333336% !important;
        }
    </style>
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
            }
            .mj-column-per-50 {
                width: 50% !important;
            }
        }
    </style>
</head>

<body>
    <div class="mj-container">
        <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;mso-line-height-rule:exactly;">      <![endif]-->
        <div style="margin:0px auto;max-width:600px;background:#0032A0;">
            <table role="presentation" style="width:100%;background:#0032A0;" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr>
                        <td style="text-align:center;vertical-align:top;direction:ltr;padding:9px 0px 9px 0px;">
                            <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                            <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                <table role="presentation" style="vertical-align:top;" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="word-wrap:break-word;padding:0px 0px 0px 0px;" align="center">
                                                <table role="presentation" style="border-collapse:collapse;border-spacing:0px;" cellspacing="0" cellpadding="0" border="0" align="center">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width:600px;text-align: center"><img alt="" src="<?=$protocol.$_SERVER['SERVER_NAME']?>/images/logo_white.svg" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;height:50px; margin-left:10px;"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
        <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;mso-line-height-rule:exactly;">      <![endif]-->
        <div style="margin:0px auto;max-width:570px;background:#FFFFFF;word-wrap:break-word;padding:15px 15px 15px 15px;">
                <h1><?=$caption?></h1>
            <?=(strlen(trim($fio)) > 0) ? '<h3>Здравствуйте, '.$fio.'!</h3>' : ''?></h3>
            <?=$text?>

        </div>
        <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
        <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;mso-line-height-rule:exactly;">      <![endif]-->
        <div style="margin:0px auto;max-width:600px;background:#0032A0;">
            <table role="presentation" style="width:100%;background:#0032A0;" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                    <tr>
                        <td style="text-align:center;vertical-align:top;direction:ltr;padding:9px 0px 9px 0px;">
                            <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0">        <tr>          <td style="vertical-align:top;width:600px;">      <![endif]-->
                            <div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="word-wrap:break-word;padding:15px 15px 15px 15px;" align="center">
                                                <div style="cursor:auto;color:#FFFFFF;font-family: PT Sans, Trebuchet MS, sans-serif;font-size:11px;line-height:2;text-align:center;">
                                                    <p>Общественное движение &laquo;ПОРА&raquo;.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
        <!--[if mso | IE]>      <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">        <tr>          <td style="line-height:0px;mso-line-height-rule:exactly;">      <![endif]-->

        <!--[if mso | IE]>      </td></tr></table>      <![endif]-->
    </div>


</body></html>