<?php 
//Initialize logging clases
include_once($_SERVER['DOCUMENT_ROOT'].'/editor/e_modules/logging/logInit.php');
if(isset($_POST['subDelete'])){ //Deletion form submitted:
	$pass = $database->clean($_POST['pass']);
	$log->emptyLog($pass);
	$_SESSION['delete_msg'] = 'Сделана новая запись';
}

//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Журнал событий в Административном разделе</title>
<link href="styles.css" rel="stylesheet" type="text/css" />
<link href="/editor/style.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
#topH1{
	border-bottom-color:#C7D082;
	border-bottom-style:inset;
	border-bottom-width:5px;
}
p{
	font-size:1.2em;
}
 
br {
clear: left;
}
-->
</style>
</head>

<body>
<table border="0" width="98%" align="center" cellpadding="0" cellspacing="0" id="page">
  <tr>
    <td><div class="entry">
    	<h4>Журнал событий в Административном разделе</h4>
    	<!--div id="log_header">Activity log for file "<a href="test.php">test.php</a>"</div-->
        <?php $log->displayMsg('delete'); ?>
    	<form id="deleteForm" name="deleteForm" method="post">
          <input name="subDelete" type="hidden" value="true" />
    	  <label for="pass">Пароль:</label>
    	  <input type="password" name="pass" id="textfield" />
          
          <input name="deleteBtn" type="submit" class="but" value="Очистить" />
    	  
        </form>
        <div id="log_num">Показаны все записи</div>
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
          <tr>
            <td width="16" class="log_top">&nbsp;</td>
            <td width="169" class="log_top"><div align="center">Действие/Событие</div></td>
            <td width="190" class="log_top"><div align="center">Пользователь</div></td>
            <td width="140" class="log_top"><div align="center">Время/Дата</div></td>
            <td width="104" class="log_top"><div align="center">IP</div></td>
            <td width="139" class="log_top"><div align="center">Приоритет</div></td>
          </tr>
          <?php
            $log->displayLogs();
          ?>
          <tr>
            <td colspan="6">&nbsp;</td>
          </tr>
        </table>
        <div class="log_nav"><a href="#topH1">Наверх</a></div>
    </div></td>
  </tr>
  <tr>
  <td></td>
  </tr>
 </table>
</body>
</html>
