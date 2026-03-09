
<?
if($_GET['cat']!=$result_row['cat']){
	$aStart='<a href="editor.php?cat='.$result_row['cat'].'">';
	$aEnd='</a>';
	$iTitle='Кликните для просмотра картинок в этом альбоме';
}else{
	$aStart=$aEnd='';
	$iTitle='Это текущий альбом" style="border:3px solid #ccdce6';
}
echo $aStart;
?>
<img src="<?=(strlen($result_row['cover'])>0)?$result_row['cover']:'/images/empty.gif'?>" border="0" title="<?=$iTitle?>">
<?=$aEnd?><br>
<?=$result_row['name']?><br> 
<?
$img=el_dbselect("SELECT id FROM photo WHERE caption=".$result_row['cat'], 0, $img);
$timg=mysqli_num_rows($img);
echo '
<input type=image src="/editor/img/menu_delete.gif" title="Удалить альбом" onClick="delAlbum('.$result_row['cat'].', \''.str_replace("'", '`', str_replace('"', '``', $result_row['name'])).'\')" align=middle>
<small>'.$timg.' фотографи'.el_postfix($timg, 'я', 'и', 'й').'</small>
<input type=image src="/editor/img/icon_property.gif" title="Нстройки альбома" onClick="flvFPW1(\'/editor/modules/gallery/albumedit.php?cat='.$result_row['cat'].'\',\'edit\',\'width=700,height=620, scrollbars=yes,resizable=yes\',1,2,2);return document.MM_returnValue" align=middle>
';
?>
<br><hr>