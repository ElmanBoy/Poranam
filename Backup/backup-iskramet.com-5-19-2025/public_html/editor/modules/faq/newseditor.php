<html>
<head>
<title>HTML-Редактор________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________________</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
var imagePath;
</script>
<script src="/editor/editor.js" language="JavaScript"></script>
<script src="/editor/colors.js" language="JavaScript"></script>
<script src="/editor/link.js" language="JavaScript"></script>
<script src="/editor/table.js" language="JavaScript"></script>
<style>
input, select { FONT-FAMILY: MS Sans Serif; FONT-SIZE: 12px; }
body, td { FONT-FAMILY: Tahoma; FONT-SIZE: 12px }
a:hover { color: #86869B }
a:visited { color: navy }
a { color: navy }
a:active { color: #ff0000 }
.st { FONT-FAMILY: MS Sans Serif; FONT-SIZE: 12px; }
.MenuFile { position:absolute; top:27; }
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	border:0px;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
</style>
<script language="JavaScript">
var imagePath;
var datatxt='<?  $str = preg_replace("/(\n|\r)/","",$HTTP_POST_VARS['txt']);  echo $str; ?>';

function sendtext() {
document.Add.text.value=document.Add.NMH.value;
window.opener.document.Add.<?php echo $field; ?>.value=message.document.body.innerHTML;
window.close();
 }
</script>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="window.resizeTo(785,625); window.status='HTML-редактор';">
  <form method="post" name="Add">
    <table width="100" cellpadding="0" cellspacing="0">
      <tr height="1" bgcolor="silver">
        <td colspan="26"></td>
      </tr>
      <tr height="28">
        <td width="22" nowrap align="center"> <img src="/editor/img/save.gif" onClick="Save()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Сохранить файл"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/print.gif" onClick="PrintPage()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Печать"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/cut.gif"   onClick="FormatText('cut')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Вырезать"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/copy.gif"  onClick="FormatText('copy')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Копировать"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/paste.gif" onClick="FormatText('paste')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Вставить"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/preview.gif"  onClick="Preview()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Предпросмотр"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/undo.gif"  onClick="FormatText('Undo', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Назад"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/redo.gif"  onClick="FormatText('Redo', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Вперед"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/wlink.gif"  onClick="OpenLink()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Вставить ссылку"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/paragraf.gif"  onClick="FormatText('InsertParagraph', 'false')" style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Новый абзац"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/br.gif"  onClick="AddHTML('<BR>')" style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Новая строка"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/hr.gif"  onClick="FormatText('InsertHorizontalRule', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Горизонтальная полоса"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/image.gif" alt="Вставить картинку" 
style="cursor: hand;"  onClick="MM_openBrWindow('/editor/addimage.php','addimage','scrollbars=yes,resizable=yes','650','570','true')" onMouseOver="b(this)" onMouseOut="a(this)"> </td>
        <td width="22" nowrap align="center"> <img src="/editor/img/table.gif"  onClick="InsertTable()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Вставить таблицу"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td width="23" nowrap align="center"> <img src="/editor/img/code.gif"  onClick="Code = prompt('Введите HTML-код', ''); 	if ((Code != null) && (Code != '')){ AddHTML(Code); }" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" class="Im" alt="Вставить HTML код"> </td>
        <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
        <td><table cellpadding="0" cellspacing="0">
            <tr height=28>
              <td><img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
              <td>
                <select name="selectSize" title="Размер шрифта" onChange="FormatText('fontsize', selectSize.options[selectSize.selectedIndex].value);document.Add.selectSize.options[0].selected = true;" >
                  <option selected>-- Размер --</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                </select>
              </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/bold.gif"  onClick="FormatText('bold', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Жирный шрифт"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/italic.gif"  onClick="FormatText('italic', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Наклонный шрифт"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/under.gif"  onClick="FormatText('underline', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Подчеркнутый шрифт"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/strike.gif"  onClick="FormatText('StrikeThrough', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Перечеркнутый шрифт"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/fcolor.gif"  onClick="OpenColors()" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Цвет шрифта"> </td>
              <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/aleft.gif"  onClick="FormatText('JustifyLeft', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Выравнивание по левому краю"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/center.gif"  onClick="FormatText('JustifyCenter', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Выравнивание по центру"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/aright.gif"  onClick="FormatText('JustifyRight', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Выравнивание по правому краю"> </td>
              <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/blist.gif" onMouseOver="b(this)" onMouseOut="a(this)"  onClick="FormatText('InsertUnorderedList', '')" 
style="cursor: hand;" alt="Ненумерованный список"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/nlist.gif"  onClick="FormatText('InsertOrderedList', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Нумерованный список"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/ileft.gif"  onClick="FormatText('Outdent', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Уменьшить отступ"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/iright.gif"  onClick="FormatText('Indent', '')" 
style="cursor: hand;" onMouseOver="b(this)" onMouseOut="a(this)" alt="Увеличить отступ"> </td>
              <td> <img src="/editor/img/I.gif" alt="I.gif" border="0"> </td>
              <td width="22" nowrap align="center"> <img src="/editor/img/help.gif" alt="Помощь" 
style="cursor: hand;"  onClick="MM_openBrWindow('/editor/help.htm','help','scrollbars=yes,resizable=yes','600','600','true')" onMouseOver="b(this)" onMouseOut="a(this)"> </td>
            </tr>
        </table></td>
      </tr>
      <tr height="1" bgcolor="silver">
        <td colspan="30"></td>
      </tr>
      <tr height="1" bgcolor="silver">
        <td colspan="30"></td>
      </tr>
      <tr>
        <td colspan="20"> </td>
      </tr>
    </table>
    <input name="text" type="hidden" id="text" > <input name="data" type="hidden" id="data" value="">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td> 
        <script language="javascript">
w=document.body.clientWidth; 
h=document.body.clientHeight-100;
document.write ('<div id="Frm"><iframe src="blank.html" id="message" width='+w+' height='+h+' style="bg" onLoad="JavaScript: message.document.body.innerHTML = datatxt;"></iframe></div><textarea name="NMH" style="width:'+w+'px;height:'+h+'px;display:none"></textarea>')
frames.message.document.designMode = "On";
</script> 
        <div id="im1"> <img src="/editor/img/Normal.gif" alt="Режим дизайна" name="Normal" width="108" height="17" border="0" usemap="#m_Normal"> 
                    
          <map name="m_Normal">
            <area shape="poly" coords="59,1,56,8,59,15,99,15,105,3,104,1,59,1" href="javascript:ShowHTML();">
          </map>
        </div>
        <div id="im2" style="display:none"> <img src="/editor/img/HTML.gif" alt="Просмотр HTML кода" name="HTML" width="108" height="17" border="0" usemap="#m_HTML"> 
          <map name="m_HTML">
            <area shape="poly" coords="1,1,51,0,55,9,53,15,8,15,2,3,1,1" href="javascript:ShowNormal();">
          </map>
        </div>
        <div align="center">
          <input type="button" name="Button" value="Вставить" style="background-color:#CBEDCD;" onClick="ShowHTML(); sendtext()">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            
<input type="button" style="background-color:#FBD0C6;" onClick="window.close()" name="Button" value="Закрыть">
        </div></td>
      </tr>
  </table>
  
</form></center>
</body>
</html>
