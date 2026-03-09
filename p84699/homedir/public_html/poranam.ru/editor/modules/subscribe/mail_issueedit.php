<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {

$themes1=implode(';', $_POST['themes']);
;
$query_user = "SELECT fio, userlevel FROM phpSP_users WHERE user='$login'";
$user = el_dbselect($query_user, 0, $user, 'result', true);
$row_user = el_dbfetch($user);


  $updateSQL = sprintf("UPDATE mail_issues SET title=%s, body=%s, template=%s, themes=%s, date_create=%s, sender=%s WHERE id=%s",
                       GetSQLValueString($_POST['title'], "text"),
					   GetSQLValueString($_POST['body'], "text"),
					   GetSQLValueString($_POST['template'], "int"),
					   GetSQLValueString($themes1, "text"),
					   GetSQLValueString(date("Y-m-d H:i"), "date"),
					   GetSQLValueString($row_user['fio'], "text"),
					   GetSQLValueString($_GET['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
  echo "<script>alert('Изменения сохранены!')</script>";
}

;
$query_dbmail_issues = "SELECT * FROM mail_issues WHERE id='".$_GET['id']."'";
$dbmail_issues = el_dbselect($query_dbmail_issues, 0, $dbmail_issues, 'result', true);
$row_dbmail_issues = el_dbfetch($dbmail_issues);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Редактирование выпуска рассылки</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features, myWidth, myHeight, isCenter) { //v3.0
  if(window.screen)if(isCenter)if(isCenter=="true"){
    var myLeft = (screen.width-myWidth)/2;
    var myTop = (screen.height-myHeight)/2;
    features+=(features!='')?',':'';
    features+=',left='+myLeft+',top='+myTop;
  }
  window.open(theURL,winName,features+((features!='')?',':'')+'width='+myWidth+',height='+myHeight);
}

function fill(id, name){
var error=0;
var errmess="";
var id1= new Array;
var name1=new Array;
id1=id;
name1=name;
for (var i=0; i<id1.length; i++){
	if (document.getElementById(id1[i]).value==""){
		errmess+='Заполните поле "'+name1[i]+'"\n';
		error++;
	}
}
	if(error!=0)
	{	
		alert(errmess);
		return false;
	}else{
		return true;
	}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function checkdel(mail_issues){
var OK=confirm('Вы действительно хотите удалить подписчика "'+mail_issues+'" ?');
if (OK) {return true} else {return false}
}
</script>

</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['title', 'body'], ['Заголовок', 'Текст'])" enctype="multipart/form-data">
   <table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="right">Заголовок:</td>
    <td><input name="title" type="text" id="title" value="<?=$row_dbmail_issues['title']?>" size="50"></td>
  </tr>
  <tr>
    <td align="right">Текст: </td>
    <td><textarea name="body" cols="70" rows="15" id="body"><?=$row_dbmail_issues['body']?></textarea>
      <br>
      <input name="ButtonHTML" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=body&form=add','editor','','590','600','true')" value="Визуальный редактор">
</td>
  </tr>
    <?
  ;
$query_template = "SELECT * FROM mail_templates ORDER BY id DESC";
$template = el_dbselect($query_template, 0, $template, 'result', true);
$row_template = el_dbfetch($template);
  ?>
  <tr>
    <td align="right" valign="top">Шаблон: </td>
    <td>
	<select name="template" id="template">
	<? do{ ?>
      <option value="<?=$row_template['id']?>" <?=($row_dbmail_issues['template']==$row_template['id'])?"selected":""?>><?=$row_template['name']?></option>
	  <? }while($row_template = el_dbfetch($template)); ?>
    </select>    
	</td>
  </tr>
  <?
  ;
$query_themes = "SELECT * FROM mail_themes ORDER BY id DESC";
$themes = el_dbselect($query_themes, 0, $themes, 'result', true);
$row_themes = el_dbfetch($themes);
  ?>
  <tr>
    <td align="right" valign="top">Темы рассылок: </td>
    <td>
	<? 
	echo '
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
	';
	do{
		if(substr_count($row_dbmail_issues['themes'], ';')>0){
			$th=explode(';', $row_dbmail_issues['themes']);
			if(in_array($row_themes['id'], $th)){$ch="checked";}else{$ch="";}
		}else{
			if($row_themes['id']==$row_dbmail_issues['themes']){$ch="checked";}else{$ch="";}
		}
	echo '<tr><td valign=top><input type="checkbox" name="themes['.$row_themes['id'].']" value="'.$row_themes['id'].'" '.$ch.'> '.$row_themes['name'].'</td></tr>
	';
	}while($row_themes = el_dbfetch($themes));
	echo '
	</table>
	';
	?>	</td>
  </tr>
  <tr>
    <td><input name="Submit" type="submit" value="Сохранить" class="but"></td>
    <td align="right"><input name="close" type="button" id="close" value="Закрыть" onClick="window.close()" class="but">      
      &nbsp;&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="MM_update" value="update">
</form>
</body>
</html>
