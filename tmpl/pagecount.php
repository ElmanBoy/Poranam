<div class="pagenavi">
<?php /*if ($pn > 0) { ?>
              <a href="<?php printf("%s?pn=%d%s", $currentPage, 0, $queryString_result); ?>" title="Первая страница" class="next">Предыдущая</a>
              <?php }*/ 
			  if ($pn > 0) { ?>
              <a href="<?php printf("%s?pn=%d%s", $currentPage, max(0, $pn - 1), $queryString_result); ?>" title="Предыдущая страница" class="next">Предыдущая</a>
         <?php } 
		 
		if ($tr>0) {
			
			if($_GET['pn']>9){
				$startcount=$_GET['pn'];
				$page=$startcount+1;
				$plus=0;
			}else{
				$startcount=0; 
				$page=1;
				$plus=2; 
			}
			
			$countpage=ceil($tr/$maxRows_result)-1;
			 if($_GET['pn']<$countpage && $_GET['pn']>9){
				echo ' <a href=?pn='.($startcount-10).$queryString_result.' class="nom">...</a>  ';
			}
			
			for($pagen=$startcount; $pagen<$startcount+10; $pagen++){
				if($countpage>=0 && $_GET['pn']<=$countpage+$plus){
					if($pn!=$pagen) {
						echo ' <a href=?pn='.$pagen.$queryString_result.' class="nom">'.$page.'</a> '; 
					}else{
						echo ' <a href="#" class="nom act">'.$page.'</a> ';
					}
					$page++;
					$countpage--;
				}
			}
			
			$endcount=$pagen++;
			if($countpage>=0 && $_GET['pn']<$countpage){
				echo ' <a href=?pn='.$endcount.'&tr='.$tr.$queryString_result.' class="nom">...</a> ';
			} 
		}
		
		if ($pn < $totalPages_result) { ?>
              <a href="<?php printf("%s?pn=%d%s", $currentPage, min($totalPages_result, $pn + 1), $queryString_result); ?>" title="Следующая страница" class="prev">Следующая</a>
              <?php } ?>
              <?php /*if ($pn < $totalPages_result) { ?>
              <a href="<?php printf("%s?pn=%d%s", $currentPage, $totalPages_result, $queryString_result); ?>" title="Последняя страница">&raquo;</a>
              <?php } */?>
</div>