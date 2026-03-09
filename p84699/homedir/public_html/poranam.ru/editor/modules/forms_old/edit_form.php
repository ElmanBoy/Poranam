<? error_reporting(E_ALL);
include $_SERVER['DOCUMENT_ROOT'].'/Connections/dbconn.php';
$requiredUserLevel = array(1, 2); 
include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
$cat=intval($_GET['cat']);
$exist=el_dbselect("SELECT * FROM forms WHERE id='".intval($_GET['id'])."'", 0, $rexist);
$rexist=el_dbfetch($exist);
$exist_form=mysqli_num_rows($exist);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Редактирование структуры формы</title>
<head>
<link href="/editor/style.css" rel="stylesheet" type="text/css">
<script src="/editor/modules/forms/forms.js"></script>
</head>
<body>
<?
	if($_POST['formsUpdate']=='1'){
		$formId=el_dbselect("SELECT id FROM catalogs WHERE catalog_id ='".$_POST['catalog_id']."'", 0, $formId);
		$from_catalog=(mysqli_num_rows($formId)>0)?'1':'0';
		$_POST['catalog_id']=(strlen($_POST['catalog_id'])>0)?$_POST['catalog_id']:$_POST['tbl_name'];
		
		$new_table="form_".trim($_POST['catalog_id'])."_data";
		if(!el_dbselect("SELECT FROM $new_table", 0, $res)){
		$query_catdata = "CREATE TABLE $new_table (
			`id` int(11) NOT NULL auto_increment,
			`cat` INT NULL,
			`active` BOOL NOT NULL,
			`sort` int(11) NOT NULL default '0',
			`goodid` INT NULL,
			UNIQUE KEY `id` (`id`)
			) TYPE=MyISAM;";
			el_dbselect($query_catdata, 0, $res);
			
		$new_fields=el_dbselect("SELECT * FROM form_prop WHERE catalog_id='".$_POST['catalog_id']."'", 0, $new_fields);
		$option=el_dbfetch($new_fields);
		do{
			  $name="field".$option['field'];
			  switch ($option['type']){
				case "integer": $type="INTEGER";
				break;
				case "textarea":
				case "optionlist":
				case "option":
				case "select": $type="LONGTEXT";
				break;
				case "float":
				case "price": $type="DOUBLE";
				break;
				default: $type="TEXT";
				break;
			  }
			  
			$query_catdata = "ALTER TABLE $new_table ADD `$name` $type NULL";
			$catdata=el_dbselect($query_catdata, 0, $catdata);
			
		}while($option=el_dbfetch($new_fields));
	}			
		if($exist_form>0){
			el_dbselect("UPDATE forms SET 
			name=".GetSQLValueString($_POST['name'], 'text').", 
			title=".GetSQLValueString($_POST['title'], 'text').",  
			method=".GetSQLValueString($_POST['method'], 'text').",
			action=".GetSQLValueString($_POST['action'], 'text').",
			target=".GetSQLValueString($_POST['target'], 'text').",
			sorce_table=".GetSQLValueString($_POST['catalog_id'], 'text').",
			from_catalog=".GetSQLValueString($from_catalog, 'int').",
			catalog_id=".GetSQLValueString($_POST['catalog_id'], 'text').",
			type=".GetSQLValueString($_POST['type'], 'text').",  
			ajax=".GetSQLValueString(isset($_POST['ajax']) ? "true" : "", "defined","'1'","'0'").",  
			email=".GetSQLValueString($_POST['email'], 'text').",  
			email_charset=".GetSQLValueString($_POST['email_charset'], 'text').", 
			email_type=".GetSQLValueString($_POST['email_type'], 'text').", 
			answer=".GetSQLValueString($_POST['answer'], 'text').",  
			protect=".GetSQLValueString(isset($_POST['protect']) ? "true" : "", "defined","'1'","'0'").",
			prevalid=".GetSQLValueString(isset($_POST['prevalid']) ? "true" : "", "defined","'1'","'0'")." 
			WHERE id=".intval($_GET['id']), 0, $res);
		}else{
			el_dbselect("INSERT INTO forms (name, title, cat, method, action, target, sorce_table, catalog_id, from_catalog, type, ajax, email, email_charset, email_type, answer, protect, prevalid) VALUE (
			".GetSQLValueString($_POST['name'], 'text').",
			".GetSQLValueString($_POST['title'], 'text').",
			".GetSQLValueString($cat, 'int').",
			".GetSQLValueString($_POST['method'], 'text').",
			".GetSQLValueString($_POST['action'], 'text').",
			".GetSQLValueString($_POST['target'], 'text').",
			".GetSQLValueString($_POST['tbl_name'], 'text').",
			".GetSQLValueString($_POST['catalog_id'], 'text').",
			".GetSQLValueString($from_catalog, 'int').",
			".GetSQLValueString($_POST['type'], 'text').",  
			".GetSQLValueString(isset($_POST['ajax']) ? "true" : "", "defined","'1'","'0'").",
			".GetSQLValueString($_POST['email'], 'text').",
			".GetSQLValueString($_POST['email_charset'], 'text').",
			".GetSQLValueString($_POST['email_type'], 'text').",
			".GetSQLValueString($_POST['answer'], 'text').",
			".GetSQLValueString(isset($_POST['protect']) ? "true" : "", "defined","'1'","'0'").", 
			".GetSQLValueString(isset($_POST['prevalid']) ? "true" : "", "defined","'1'","'0'")." 
			)", 0, $res);
		}
		el_clearcache('forms', '');
		echo '<script>alert("Изменения сохранены!")</script>';
	}
	$exist=el_dbselect("SELECT * FROM forms WHERE id='".intval($_GET['id'])."'", 0, $rexist);
	$rexist=el_dbfetch($exist);
	$exist_form=mysqli_num_rows($exist);
?>

<br><center>
 <a href="params.php">Список полей</a>
| <a href="catalogs.php?mode=new">Создать форму</a>
| <a href="catalogs.php?mode=list">Список форм</a>
<?
if(!empty($_GET['cat'])){?>
|
<a href="'/editor/editor.php?cat=<?=$_GET['cat']?>"> &laquo; Вернуться к редактированию раздела</a>
<? } if(!empty($_GET['id'])){?>
|
<a href="/editor/modules/forms/catalogs.php?id=<?=$_GET['id']?><?=(!empty($_GET['cat']))?'&cat='.$_GET['cat']:''?>">Редактировать структуру формы &raquo;</a>
<? }?>
<br>
<h4>Настройки формы &laquo;<?=$rexist['name']?>&raquo;</h4>
</center>
<form name="formsParam" method="post" action="">
<table border="0" cellspacing="0" cellpadding="4" align="center" class="el_tbl">
  <tr>
    <td>Имя формы</td>
    <td>
      <input name="name" type="text" id="name" size="40" value="<?=$rexist['name']?>">    </td>
  </tr>
  <tr>
    <td>Заголовок над формой</td>
    <td><input name="title" type="text" id="title" size="40" value="<?=$rexist['title']?>"></td>
  </tr>
  <tr>
    <td>Отправлять данные по адресу<br>
	<small>(по умолчанию - пустое значение, форма отправляет данные своему обработчику)</small></td>
    <td><input name="action" type="text" id="action" size="40" value="<?=$rexist['action']?>"></td>
  </tr>
    <tr>
      <td valign="top">Метод отправки </td>
      <td><label for="RG0">
        <input type="radio" name="method" value="POST" id="RG0"<?=($rexist['method']=='POST')?' checked':''?>>
        POST</label>
        <br>
        <label for="RG1">
        <input type="radio" name="method" value="GET" id="RG1"<?=($rexist['method']=='GET')?' checked':''?>>
        GET</label>
      </td>
    </tr>
  <tr>
    <td valign="top">Отправлять </td>
    <td>
     <label for="RG1_0">
        <input type="radio" name="target" value="_blank" id="RG1_0"<?=($rexist['target']=='_blank')?' checked':''?>>
        В новое окно</label>
      <br>
      <label for="RG1_1">
        <input type="radio" name="target" value="_self" id="RG1_1"<?=($rexist['target']=='_self' || strlen($rexist['target'])==0)?' checked':''?>>
        В текущее окно</label>
    </td>
  </tr>
  <tr>
    <td>Создать по каталогу или создать новую</td>
    <td><select name="catalog_id" id="catalog_id">
    <?
	$catalogs=el_dbselect("SELECT name, catalog_id FROM catalogs", 0, $catalogs);
	$row_cats=el_dbfetch($catalogs);
	do{
		$sel=($rexist['catalog_id']==$row_cats['catalog_id'])?' selected':'';
		echo '<option value="'.$row_cats['catalog_id'].'"'.$sel.'>'.$row_cats['name'].'</option>'."\n";
	}while($row_cats=el_dbfetch($catalogs));
	mysqli_free_result($catalogs);
	$catalogs=el_dbselect("SELECT name, sorce_table FROM forms", 0, $catalogs);
	$row_cats=el_dbfetch($catalogs);
	do{
		$sel=($rexist['sorce_table']==$row_cats['sorce_table'])?' selected':'';
		echo '<option value="'.$row_cats['sorce_table'].'"'.$sel.'>'.$row_cats['name'].'</option>'."\n";
	}while($row_cats=el_dbfetch($catalogs));
	?>
    <option <?=(substr_count($rexist['catalog_id'], 'adventor_new_form')>0)?' value="'.$rexist['catalog_id'].'" selected':' value="adventor_new_form"'?>>Создать новую</option>
    </select>    </td>
  </tr>
  <tr>
    <td valign="top">Куда помещать данные</td>
    <td><p>
      <label for="RadioGroup1_0">
        <input type="radio" name="type" value="db" id="RadioGroup1_0"<?=($rexist['type']=='db')?' checked':''?>>
        В базу данных</label>
      <br>
      <label for="RadioGroup1_1">
        <input type="radio" name="type" value="email" id="RadioGroup1_1"<?=($rexist['type']=='email')?' checked':''?>>
        На e-mail</label>
      <br>
      <label for="RadioGroup1_2">
        <input name="type" type="radio" id="RadioGroup1_2" value="both"<?=($rexist['type']=='both')?' checked':''?>>
        В базу данных и на e-mail</label>
      <br>
    </p></td>
  </tr>
  <tr>
    <td>Использовать AJAX</td>
    <td><input type="checkbox" name="ajax" id="ajax"<?=($rexist['ajax']=='1')?' checked':''?>></td>
  </tr>
  <tr>
    <td>Защитить от автоматичекого заполнения</td>
    <td><input type="checkbox" name="protect" id="protect"<?=($rexist['protect']=='1')?' checked':''?>></td>
  </tr>
  <tr>
    <td>Показать введенные данные перед отправкой для проверки</td>
    <td><input type="checkbox" name="prevalid" id="prevalid"<?=($rexist['prevalid']=='1')?' checked':''?>></td>
  </tr>
  <tr>
    <td>E-mail получателя <br><small>(можно несколько через запятую)</small></td>
    <td><input name="email" type="text" id="email" size="40" value="<?=$rexist['email']?>"></td>
  </tr>
    <tr>
      <td valign="top">Формат письма</td>
      <td><p>
          <label for="type1_0">
          <input type="radio" name="email_type" value="HTML" id="type1_0"<?=($rexist['email_type']=='HTML' || strlen($rexist['email_type'])==0)?' checked':''?>>
          HTML</label>
          <br>
          <label for="type1_1">
          <input type="radio" name="email_type" value="TEXT" id="type1_1"<?=($rexist['email_type']=='TEXT' )?' checked':''?>>
          Текст</label>
        </p></td>
    </tr>
    <tr>
      <td valign="top">Кодировка письма</td>
      <td>
      <select name="email_charset" id="email_charset">
            <option value="windows-1251"<?=($rexist['email_charset']=='windows-1251' || strlen($rexist['email_charset'])==0)?' selected':''?>>Windows-1251</option>
            <option value="KOI-8R"<?=($rexist['email_charset']=='KOI-8R')?' selected':''?>>KOI-8R</option>
        </select>
      </td>
    </tr>
  <tr>
    <td valign="top">Текст ответа после <br>
      отправки данных</td>
    <td><textarea name="answer" id="answer" cols="40" rows="5"><?=$rexist['answer']?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input name="formsUpdate" type="hidden" id="formsUpdate" value="1">
      <input type="submit" name="Submit" id="Submit" value="Сохранить" class="but"></td>
    </tr>
</table>
</form>
</body>
</html>