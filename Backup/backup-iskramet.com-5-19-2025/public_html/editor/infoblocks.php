<?php 
require_once('../Connections/dbconn.php');
 
$requiredUserLevel = array(0, 1, 2);
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");

$site_id = (intval($_GET['site_id']) > 0) ? intval($_GET['site_id']) : intval($_SESSION['site_id']);

if($_POST['action']=='del'){
	$res=el_dbselect("DELETE FROM infoblocks WHERE id='".$_POST['id']."'", 0, $res);
	echo "<script language=javascript>alert('Инфоблок №".$_POST['id']." удален!')</script>";
}

$li= el_dbselect("SELECT id, name, ctime, author, edit FROM infoblocks ORDER BY id ASC", 0, $li);
$linf=el_dbfetch($li);

$site = el_dbselect("SELECT * FROM sites WHERE id=".$site_id, 0, $sites, 'row', true);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Список инфоблоков</title>
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
<script language="javascript">
function act1(mode, row){
	if (mode=="edit"){
		location.href="infoblocksedit.php?id="+row;
	}
	if (mode=="del"){
		var OK=confirm("Вы уверены, что хотите удалить инфоблок №"+row+" ?"); 
		if(OK){
			document.act.action.value=mode; document.act.id.value=row; document.act.submit();
		} 
	}
	if (mode=="link"){
		location.href="infoblockslink.php?id="+row;
	}
}

$(document).ready(function(){
    $('*').tooltip();
});
</script>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<h1>Список инфоблоков сайта &laquo;<?=$site['short_name']?>&raquo;</h1>

<input type="button" value="Создать новый инфоблок" onClick="location.href='infoblocksedit.php?new'" class="but">
<? if(el_dbnumrows($li)>0){ ?>

<form method="post" name="act"><input type="hidden" name="action"><input type="hidden" name="id"></form>

<? do{ ?>
<div id="<?=$linf['id']?>" class="row"><div id="left">ID<?=$linf['id']?>&nbsp;<strong><?=$linf['name']?></strong> <small>[автор: <?=$linf['author']?>, дата создания: <?=$linf['ctime']?>]</small></div>  <div id="right">
        <i class="material-icons" title="Выбор разделов, где будет работать инфоблок" onClick="act1('link', <?=$linf['id']?>)">link</i>&nbsp;
        <i class="material-icons" title="Редактировать содержимое инфоблока" onClick="act1('edit', <?=$linf['id']?>)">edit</i>&nbsp;
        <i class="material-icons" onClick="act1('del', <?=$linf['id']?>)" title="Удалить инфоблок">delete</i>
    </div>
</div>
<? }while($linf=el_dbfetch($li));

}else{ ?>
<h4 align="center">Ни один инфоблок еще не создан.</h4>
<? }?>
</body>
</html>
