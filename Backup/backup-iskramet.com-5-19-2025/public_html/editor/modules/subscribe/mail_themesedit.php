<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {

;
$query_check = "SELECT * FROM mail_themes WHERE name='".$_POST['name']."'";
$check = el_dbselect($query_check, 0, $check, 'result', true);
$row_check = el_dbfetch($check);

if(mysqli_num_rows($check)>0 && $row_check['id']!=$_GET['id']){echo "<script language=javascript>alert('Тема с таким названием уже есть!')</script>";}else{

  $updateSQL = sprintf("UPDATE mail_themes SET name=%s, description=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['description'], "text"),
					   GetSQLValueString($_GET['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
  echo "<script>alert('Изменения сохранены!')</script>";
}
}

;
$query_dbmail_themes = "SELECT * FROM mail_themes WHERE id='".$_GET['id']."'";
$dbmail_themes = el_dbselect($query_dbmail_themes, 0, $dbmail_themes, 'result', true);
$row_dbmail_themes = el_dbfetch($dbmail_themes);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Редактирование темы подписки</title>
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
</script>

</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="add" onSubmit="return fill(['name'], ['Название'])" enctype="multipart/form-data">
   <table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td align="right">Название:</td>
    <td><input name="name" type="text" id="name" value="<?=$row_dbmail_themes['name']?>" size="50"></td>
  </tr>
  <tr>
    <td align="right">Описание: </td>
    <td><textarea name="description" cols="40" rows="5" id="description"><?=$row_dbmail_themes['description']?></textarea></td>
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
