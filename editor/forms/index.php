<? 
if($_POST['formsUpdate']=='1'){
	$exist=el_dbselect("SELECT * FROM forms WHERE cat=$cat", 0, $formsParam);
	if(mysqli_num_rows($exist)>0){
		el_dbselect("UPDATE forms SET 
		name=".GetSQLValueString($_POST['name'], 'text').", 
		title=".GetSQLValueString($_POST['title'], 'text').",  
		cat=".$cat.",  
		catalog_id=".GetSQLValueString($_POST['catalog_id'], 'text').",
		type=".GetSQLValueString($_POST['type'], 'text').",  
		ajax=".GetSQLValueString(isset($_POST['ajax']) ? "true" : "", "defined","'1'","'0'").",  
		email=".GetSQLValueString($_POST['email'], 'text').",  
		answer=".GetSQLValueString($_POST['answer'], 'text').",  
		protect=".GetSQLValueString(isset($_POST['protect']) ? "true" : "", "defined","'1'","'0'")." 
		WHERE cat=$cat", 0, $res);
	}else{
		el_dbselect("INSERT INTO forms (name, title, cat, catalog_id, type, ajax, email, answer, protect) VALUE (
		".GetSQLValueString($_POST['name'], 'text').",
		".GetSQLValueString($_POST['title'], 'text').",
		".GetSQLValueString($cat, 'int').",
		".GetSQLValueString($_POST['catalog_id'], 'text').",
		".GetSQLValueString($_POST['type'], 'text').",  
		".GetSQLValueString(isset($_POST['ajax']) ? "true" : "", "defined","'1'","'0'").",
		".GetSQLValueString($_POST['email'], 'text').",
		".GetSQLValueString($_POST['answer'], 'text').",
		".GetSQLValueString(isset($_POST['protect']) ? "true" : "", "defined","'1'","'0'")." 
		)", 0, $res);
	}
	echo '<script>alert("Изменения сохранены!")</script>';
}

$formsParam=el_dbselect("SELECT * FROM forms WHERE cat=$cat", 0, $formsParam, 'row');
?>

<form name="formsParam" method="post" action="">
<table border="0" cellspacing="0" cellpadding="4" align="center" class="el_tbl">
  <tr>
    <td>Имя формы</td>
    <td>
      <input name="name" type="text" id="name" size="40" value="<?=$formsParam['name']?>">    </td>
  </tr>
  <tr>
    <td>Заголовок над формой</td>
    <td><input name="title" type="text" id="title" size="40" value="<?=$formsParam['title']?>"></td>
  </tr>
  <tr>
    <td>Создать по каталогу</td>
    <td><select name="catalog_id" id="catalog_id">
    <?
	$catalogs=el_dbselect("SELECT name, catalog_id FROM catalogs", 0, $catalogs);
	$row_cats=el_dbfetch($catalogs);
	do{
		$sel=($formsParam['catalog_id']==$row_cats['catalog_id'])?' selected':'';
		echo '<option value="'.$row_cats['catalog_id'].'"'.$sel.'>'.$row_cats['name'].'</option>'."\n";
	}while($row_cats=el_dbfetch($catalogs));
	?>
    </select>    </td>
  </tr>
  <tr>
    <td valign="top">Куда помещать данные</td>
    <td><p>
      <label for="RadioGroup1_0">
        <input type="radio" name="type" value="db" id="RadioGroup1_0"<?=($formsParam['type']=='db')?' checked':''?>>
        В базу данных</label>
      <br>
      <label for="RadioGroup1_1">
        <input type="radio" name="type" value="email" id="RadioGroup1_1"<?=($formsParam['type']=='email')?' checked':''?>>
        На e-mail</label>
      <br>
      <label for="RadioGroup1_2">
        <input name="type" type="radio" id="RadioGroup1_2" value="both"<?=($formsParam['type']=='both')?' checked':''?>>
        В базу данных и на e-mail</label>
      <br>
    </p></td>
  </tr>
  <tr>
    <td>Использовать AJAX</td>
    <td><input type="checkbox" name="ajax" id="ajax"<?=($formsParam['ajax']=='1')?' checked':''?>></td>
  </tr>
  <tr>
    <td>Защитить от автоматичекого заполнения</td>
    <td><input type="checkbox" name="protect" id="protect"<?=($formsParam['protect']=='1')?' checked':''?>></td>
  </tr>
  <tr>
    <td>E-mail получателя <br><small>(можно несколько через запятую)</small></td>
    <td><input name="email" type="text" id="email" size="40" value="<?=$formsParam['email']?>"></td>
  </tr>
  <tr>
    <td valign="top">Текст ответа после <br>
      отправки данных</td>
    <td><textarea name="answer" id="answer" cols="40" rows="5"><?=$formsParam['answer']?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input name="formsUpdate" type="hidden" id="formsUpdate" value="1">
      <input type="submit" name="Submit" id="Submit" value="Сохранить" class="but"></td>
    </tr>
</table>
</form>