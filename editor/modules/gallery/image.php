
<li style="background-color:#EAEBEC;<?=($result_row['status']==0)?' border:3px solid red':''?>; width:250px; height:300px">
Номер №<?php echo $result_row['sort']; ?>,  ID<?=$result_row['id']?><br>
              <img src="http://<?php echo $_SERVER['SERVER_NAME'].$result_row['smallpath']; ?>" alt="<?php echo $_SERVER['SERVER_NAME'].$result_row['smallpath']; ?>" border="0"  style="cursor: hand;" onClick="flvFPW1('/editor/modules/gallery/galleryedit.php?id=<?php echo $result_row['id']; ?>&cat=<?=$_GET['cat']?>','edit','width=800,height=670, scrollbars=yes,resizable=yes',1,2,2);return document.MM_returnValue" <? $size=GetImageSize($_SERVER['DOCUMENT_ROOT'].$result_row['smallpath']); echo $size[3]; ?>><br>
  
  <?=($result_row['status']==0)?'<span style="color:red">Ждет одобрения модератора.</span><br>
  <input type=button value="Разрешить" class="but" onclick=allow('.$result_row['id'].')><br>':''?>
   <div id="textImg<?=$result_row['id']?>"><?=$result_row['text']?></div><br /><? /*/
   $co=''; 
   $nco=''; 
   $ncom=''; 
   $g="SELECT id FROM comments WHERE pagepath='".$path."/?id=".$result_row['id']."' AND (status IS NULL OR status=0)";
   $co=el_dbselect($g,0,$co);
  $nco=mysqli_num_rows($co); 
  if($nco>0){$ncom=' [новых '.$nco.']';}
   if($result_row['in_comments']==1){
   		echo '<input type=button value="Комментарии'.$ncom.'" class="but" onclick=comments('.$result_row['id'].')><br>';
   }else{ 
   		echo '';
	}
   mysqli_free_result($co);?>
  <br>
  <input name="Button" type="button" onClick="flvFPW1('/editor/modules/gallery/galleryedit.php?id=<?php echo $result_row['id']; ?>&cat=<?=$_GET['cat']?>','edit','width=800,height=670, scrollbars=yes,resizable=yes',1,2,2);return document.MM_returnValue" value="Редактировать" class="but"> <br*/?>
  <div id="commentImg<?=$result_row['id']?>" class="edit" onclick="editText(<?=$result_row['id']?>)">Править подпись</div>
  <div style="display:block; position:relative; z-index:1000; background-color:#EAEBEC" id="textarea<?=$result_row['id']?>"></div>
<label for="del<?=$result_row['id']?>"><input name="<?=$result_row['id']?>" type="checkbox" id="del<?=$result_row['id']?>" value="ON"> <b>Удалить</b></label>
 </li>