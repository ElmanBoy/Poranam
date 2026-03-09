<? if($row_menup['path']!=''){
      (substr_count($path, $row_menup['path'])>0)?$cl2='active':$cl2='active'; 
    }else{

    ?>
 	<li><a href="<?=$row_menupart['path']?>/"<?=($row_menupart['left']=='Y')?' target="_blank"':''?> <?=$clas?>><?=$row_menupart['name']?></a></li>
 <? }?>
