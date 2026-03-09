<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php'); 
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php"); 

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "update")) {
  $updateSQL = sprintf("UPDATE mail_templates SET name=%s, body=%s, type=%s, codepage=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
					   GetSQLValueString($_POST['body'], "text"),
					   GetSQLValueString($_POST['type'], "text"),
					   GetSQLValueString($_POST['codepage'], "text"),
                       GetSQLValueString($_GET['id'], "int"));

  ;
  $Result1 = el_dbselect($updateSQL, 0, $Result1, 'result', true);
  echo "<script>alert('Изменения сохранены!')</script>";
}


;
$query_dbmail_list = "SELECT * FROM mail_templates WHERE id='".$_GET['id']."' ORDER BY name ASC";
$dbmail_list = el_dbselect($query_dbmail_list, 0, $dbmail_list, 'result', true);
$row_dbmail_list = el_dbfetch($dbmail_list);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Редактирование шаблона рассылки</title>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
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
function checkdel(mail_list){
var OK=confirm('Вы действительно хотите удалить шаблон "'+mail_list+'" ?');
if (OK) {return true} else {return false}
}

function vis_button(){
var sel=document.getElementById("type");
var but=document.getElementById("ButtonHTML");
var trow=document.getElementById("coderow");;
	if(sel.options[sel.selectedIndex].value=="HTML"){
		but.style.display="block";
		trow.style.display="block";
	}else{
		but.style.display="none";
		trow.style.display="none";
	}
}

function show_help(){
var h=document.getElementById("help");
	if(h.style.display=="none"){
		h.style.display="block";
	}else{
		h.style.display="none";
	}
}
</script>

</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="add">
  <table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0" class="el_tbl">
  <tr>
    <td colspan="2"><a href="#" onClick="show_help()">Помощь</a>
      <div id="help" style="display:none"><? el_showalert("info","<b>Подсказка:</b><br>Чтобы система смогла вставить в шаблон ваши данные используйте следующие директивы:<ul style=margin:1px><li>%%subject%% - заголовок письма</li><li>%%name%% - имя получателя</li><li>%%theme%% - тема рассылки</li><li>%%body%% - содержимое письма</li><li>%%sitename%% - название сайта</li><li>%%siteurl%% - адрес сайта</li><li>%%date%% - дата рассылки</li></ul>") ?></div></td>
    </tr>
	<tr>
      <td align="right">Название:</td>
      <td><input name="name" type="text" id="name" value="<?=$row_dbmail_list['name']?>" size="50"></td>
    </tr>
    <tr>
      <td align="right">Шаблон: </td>
      <td><textarea name="body" cols="70" rows="15" id="body"><?=$row_dbmail_list['body']?></textarea>
          <div id="ButtonHTML" style="display:<?=($_GET['mode']=='html')?"block":"none"?>">
              <input name="ButtonHTML" type="button" class="but" onClick="MM_openBrWindow('/editor/newseditor.php?field=body&form=add','editor','','595','500','true')" value="Визуальный редактор">
        </div></td>
    </tr>
    <tr>
      <td align="right" valign="top">Тип рассылки: </td>
      <td><select name="type" id="type" onChange="vis_button()">
        <option value="HTML" <?=($row_dbmail_list['type']=="HTML")?"selected":""?>>HTML</option>
        <option value="TEXT" <?=($row_dbmail_list['type']=="TEXT")?"selected":""?>>TEXT</option>
      </select>      </td>
    </tr>
    <tr id="coderow" style="display:<?=($_GET['mode']=='html')?"block":"none"?>">
      <td align="right" valign="top">Кодировка: </td>
      <td><select name="codepage" id="codepage">
        <option value="KOI-8R" <?=($row_dbmail_list['codepage']=="KOI-8R")?"selected":""?>>KOI-8R</option>
        <option value="Windows-1251" <?=($row_dbmail_list['codepage']=="Windows-1251")?"selected":""?>>Windows-1251</option>
      </select></td>
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
