<script language="JavaScript" type="text/JavaScript">
<!--

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


function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function checkdel(news){
var OK=confirm('Вы действительно хотите удалить вакансию "'+news+'" ?');
if (OK) {return true} else {return false}
}
function checkdelc(news){
var OK=confirm('Вы действительно хотите удалить заявку "'+news+'" ?');
if (OK) {return true} else {return false}
}

function opclose(id){
if (document.getElementById(id).style.display=="none"){
document.cookie = "vac["+id+"]=Y; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
document.getElementById(id).style.display="block";
}else{
document.cookie = "vac["+id+"]=N; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/editor/;";
document.getElementById(id).style.display="none";
};
}
//-->
</script>
<?php if($_GET['mode']=='list' || !isset($_GET['mode'])){
	if(isset($_POST['id'])){
		$del=el_dbselect("DELETE FROM orders WHERE id=".$_POST['id'],0,$del);
	}
		$compet=el_dbselect("SELECT * FROM orders WHERE cat=".$cat." ORDER BY id DESC",20,$compet);
		$row_compet=el_dbfetch($compet);
		$vacan=el_dbselect("SELECT * FROM orders WHERE cat=".$cat,0,$vacan);
		$row_vacan=el_dbfetch($vacan);
		$tr=mysqli_num_rows($vacan);
	?>
	
	<h4>Список заявок</h4>
<button class="but" onClick="location.href='?cat=<?=$cat?>&mode=par'">Параметры</button><br>
	<? 
if(mysqli_num_rows($compet)>0){
	el_dbpagecount($compet, '', 20, $tr, '/tmpl/pagecount.php') ?>
<table width="80%" border="0" align="center" cellpadding="3" cellspacing="0" class="el_tbl">
<? do{ ?><form method="post" onSubmit="return checkdelc('W-00<?=$row_compet['id']?>')">
  <tr>
    <td>Заявка W-00<?=$row_compet['id'].' от '.$row_compet['date_send']?><input type="hidden" name="id" value="<?=$row_compet['id']?>"></td>
    <td><input name="detail" type="button" value="Подробнее" onClick="opclose(<?=$row_compet['id']?>)" class="but">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Submit" type="submit" value="Удалить" class="but"></td>
  </tr>
  <tr>
    <td colspan="2">
	<div id="<?=$row_compet['id']?>" style="display:<? if($_COOKIE['vac'][$row_compet['id']]=="Y"){echo "block";}else{echo "none";}; ?>">
	<table width="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#E0DFE3">
      <tr>
        <td width="24%" align="right" valign="top"><strong>Заказ отправлен с IP</strong> :</td>
        <td width="76%" valign="top"><?=$row_compet['ip']?></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Состав заявки</strong> :</td>
        <td valign="top"><?=$row_compet['text']?></td>
      </tr>
    </table>
	</div>
	</td>
    </tr>
  </form><? }while($row_compet=el_dbfetch($compet));?>
</table>
<br>
<? el_dbpagecount($compet, '', 20, $tr, '/tmpl/pagecount.php') ?>
<br><br><center>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</center>

  <? }else{ echo '<h4 align=center>Заявок пока нет.</h4>';} 
}elseif($_GET['mode']=='par'){
if(isset($_POST['save']) && $_POST['save']=='prop'){
	$expar=el_dbselect("SELECT id FROM order_props WHERE cat='".$cat."'",0,$expar);
	if(mysqli_num_rows($expar)>0){
		if(!empty($_POST['email'])){
			el_dbselect("UPDATE order_props SET email='".$_POST['email']."', count='".$_POST['count']."', hotel='".$_POST['hotel']."', hosting='".$_POST['hosting']."', type='".$_POST['type']."'",0,$result);
			echo '<script language=javascript>alert("Изменения сохранены!")</script>';
		}else{
			echo '<script language=javascript>alert("Заполните поле email!")</script>';
		}
	}else{
		$insertvars=array('id'=>'\'\'', 'cat'=>$cat, 'email *'=>$_POST['email'], 'count'=>$_POST['count'], 'hotel'=>$_POST['hotel'], 'hosting'=>$_POST['hosting'], 'type'=>$_POST['type']);
		if(el_dbinsert("order_props", $insertvars)){
			echo '<script language=javascript>alert("Изменения сохранены!")</script>';
		}else{
			echo '<script language=javascript>alert("Ошибка сохранения данных!")</script>';
		}
	}
}
$p=el_dbselect("SELECT * FROM order_props WHERE cat=".$cat, 0, $p);
$par=el_dbfetch($p);
?><h4>Параметры формы заявки</h4>
<button class="but" onClick="location.href='?cat=<?=$cat?>&mode=list'">Список заявок</button><br><br>
<form method="post">
<table align="center" cellpadding="5" cellspacing="0" class="el_tbl">
<tr>
  <td>На какой адрес отправлять заявку </td>
  <td colspan="4"><input name="email" type="text" id="email" value="<?=$par['email']?>" size="40" />
    <input name="save" type="hidden" id="save" value="prop" /></td>
  </tr>
  <!--
  <tr>
    <td colspan="5">
	Вводите каждое новое значение с новой строки
	<table align="center" cellpadding="5" cellspacing="0" class="el_tbl">

      <tr>
        <td>Число участников:<br><textarea name="count" cols="30" rows="5" id="count"><?=$par['count']?></textarea></td>
        <td>Категория отеля: <br><textarea name="hotel" cols="30" rows="5" id="hotel"><?=$par['hotel']?></textarea></td>
        </tr>
      <tr>
        <td>Размещение:<br />
          <textarea name="hosting" cols="30" rows="5" id="hosting"><?=$par['hosting']?></textarea></td>
        <td>Тип тура:<br />
          <textarea name="type" cols="30" rows="5" id="type"><?=$par['type']?></textarea></td>
        </tr>
		
    </table>    </td>
  </tr>-->
  <tr>
    <td colspan="5" align="center"><input type="submit" name="Submit" value="Сохранить" class="but" /></td>
  </tr>
</table>
</form>
<? }?>