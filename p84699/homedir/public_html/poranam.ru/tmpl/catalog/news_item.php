<div class="shortcode shortcode_news_list_block clearfix">
	<figure>
		<a href="/novosti/<?=urlencode($an['path'])?>.html"><img src="<?=$an['field4']?>" alt="<?=htmlspecialchars($an['field1'])?>" title="<?=htmlspecialchars($an['field1'])?>"></a>
	</figure>

	<div class="text_block">
		<h3><a href="/novosti/<?=urlencode($an['path'])?>.html"><?=$an['field1']?></a></h3>

		<p><?=$an['field3']?></p>

		<div class="text_block_footer">
			<div class="tag_info">
				<?php
				if(strlen(trim($an['field8'])) > 0) {
					$tags = explode(';', $an['field8']);
					for ($i = 0; $i < count($tags); $i++) {
						?>
						<span><a href="/novosti/?tag=<?= $tags[$i] ?>">#<?= $tags[$i] ?></a></span>
						<?php
					}
				}
				?>
			</div>
			<div class="date_info"><?=el_date_string($an['field2'])?></div>
			<div class="meta_info">
				<span><a class="icon-eye-outline"><span><?=intval($an['field15'])?></span></a></span>
				<span><a class="icon-comment"><span><?=intval($an['field18'])?></span></a></span>
				<span><a href="#" class="icon-heart" data-balloon-pos="up" id="n<?=$an['id']?>"><span><?=intval($an['field17'])?></span></a></span>
			</div>
		</div>
	</div>
</div>