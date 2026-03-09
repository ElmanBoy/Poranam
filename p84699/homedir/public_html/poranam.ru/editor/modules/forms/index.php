<script src="/editor/modules/forms/forms.js"></script>
<form name="delFrm" method="post"><input type="hidden" name="delId" /></form>
<? 
$requiredUserLevel = array(1, 2); 
include_once($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
$maxRows=20;

$exist=el_dbselect("SELECT * FROM forms WHERE cat=$cat", 0, $formsParam);
$rexist=el_dbfetch($exist);
$exist_form=mysqli_num_rows($exist);
if($rexist['from_catalog']=='0'){
	$src_table=$rexist['sorce_table'];
	$prop_table='form_prop';
}else{
	$src_table='catalog_'.$rexist['catalog_id'].'_data';
	$prop_table='catalog_prop';
}

if(!empty($_POST['delId'])){
	el_dbselect("DELETE from `$src_table` WHERE id='".intval($_POST['delId'])."'", 0, $res);
}

if(isset($_POST['saveRecords']) && $_POST['saveRecords']=='1'){
	$data=el_dbselect("SELECT * FROM `$src_table` WHERE cat=$cat ORDER BY id DESC", $maxRows, $data);
	$sdata=el_dbfetch($data);
	do{
		el_dbselect("UPDATE `$src_table` SET active='".((isset($_POST['active'.$sdata['id']]))?'1':'0')."' WHERE id='".$sdata['id']."'", 0, $res);
	}while($sdata=el_dbfetch($data));
	echo "<script>alert('Изменения сохранены!')</script>";
}

$data=el_dbselect("SELECT * FROM `$src_table` WHERE cat=$cat ORDER BY id DESC", $maxRows, $data);
$title=el_dbselect("SELECT field FROM `$prop_table` WHERE title=1", 0, $title, 'row');
$rdata=el_dbfetch($data);
$tot=el_dbselect("SELECT * FROM `$src_table` WHERE cat=$cat", 0, $tot);
$total=mysqli_num_rows($tot);

if($exist_form>0){
	echo '<br><br><input type=button onclick="location.href=\'/editor/modules/forms/edit_form.php?cat='.$cat.'&id='.$rexist['id'].'\'" class=but value="Настройки формы &laquo;'.$rexist['name'].'&raquo;"><br><br>';
	
	if($total>0){
		el_dbpagecount($data, '/editor/editor.php?cat='.$cat, $maxRows, $total, '/tmpl/paging/pagecount.php');
		echo '
		<form method=post action="editor.php?'.$_SERVER['QUERY_STRING'].'">
		<table class=el_tbl border=0 cellpadding=4 cellspacing=0 width=80%>
		<tr>
			<th> Номер </th>
			<th> Заголовок </th>
			<th colspan=3> Действия </th>
			</tr>
		';
		do{
			echo '
			<tr onmouseover="line_over('.$rdata['id'].')" onmouseout="line_out('.$rdata['id'].')" id="string'.$rdata['id'].'">
			<td><img id="img'.$rdata['id'].'" src="/editor/img/spacer.gif" width=19 height=17 align=left> №'.$rdata['id'].'</td>
			<td width=70%><b>'.$rdata['field'.$title['field']].'</b></td>
			<td>
			<label for="active'.$rdata['id'].'">
			<input type=checkbox id="active'.$rdata['id'].'" name="active'.$rdata['id'].'" value="1" '.(($rdata['active']=='1')?'checked':'').'> 
			Активная</label>
			</td>
			<td><img src="/editor/img/open.gif" title="Открыть" style="cursor:pointer" 
			onclick="readMessage('.$rdata['id'].', '.$cat.')"></td>
			<td><img src="/editor/img/menu_delete.gif" style="cursor:pointer" title="Удалить" onclick="deleteMessage('.$rdata['id'].')"></td>
			</tr>
			<tr style="display:none" id="tr'.$rdata['id'].'"><td colspan=5><div id="div'.$rdata['id'].'"></div></td></tr>
			';
		}while($rdata=el_dbfetch($data));
		echo '</table><br><br><input type=hidden name=saveRecords value=1>
		<center><input type=submit name=Submit value="Сохранить изменения" class=but></center></form>
		';
		el_dbpagecount($data, '/editor/editor.php?cat='.$cat, $maxRows, $total, '/tmpl/paging/pagecount.php');
	}else{
		echo '<h4 align=center>Пока нет ни одной записи.</h4>';
	}
}else{
	echo '
	
	<br><br><input type=button onclick="location.href=\'/editor/modules/forms/catalogs.php?mode=new\'" class=but value="Создать новую форму"><br><br>';
}


?>

