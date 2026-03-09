<div id="block-performed" class="block-alter">
	<div class="h-25"></div>
	<div id="bp-carousel">
		<h2 class="tac">Выполненные проекты</h2>
		<div class="cycle-slideshow" 
			data-cycle-fx=carousel
			data-cycle-slides='.item-carousel'
			data-cycle-timeout=0
			data-cycle-carousel-visible=4
			data-cycle-next="#bp-next"
			data-cycle-prev="#bp-prev"
			data-cycle-pager="#bp-pager"
			>
			<?
            $pr = el_dbselect("SELECT id, name, path, 
 (SELECT field4 FROM catalog_gallery_data WHERE cat = cat.id ORDER BY sort LIMIT 0, 1) AS cover
 FROM cat WHERE parent = 47 AND nourl IS NULL AND (id < 388 OR id > 393) ORDER BY sort", 0, $pr, 'result', true);
            if(el_dbnumrows($pr) > 0) {
                $rpr = el_dbfetch($pr);
                do {
                    echo '<div class="item-carousel">
                        <div class="item-project">
                        <a href="'.$rpr['path'].'" class="item-project-thumb" style="background-image:url('.$rpr['cover'].')"></a>
                        <div class="item-project-title">
                        <a href="'.$rpr['path'].'">'.$rpr['name'].'</a>
                        </div>
                        <div class="item-project-excerpt">&nbsp;</div>
                        </div>
                      </div>';
                } while ($rpr = el_dbfetch($pr));
            }
            ?>
		</div>
		<a href="#" id="bp-prev">
			<svg width="40" height="40">
				<polyline style="fill:none;stroke-width:3;stroke:#fff" points="25,5 10,20 25,35" />
			</svg>
		</a>
		<a href="#" id="bp-next">
			<svg width="40" height="40">
				<polyline style="fill:none;stroke-width:3;stroke:#fff" points="15,5 30,20 15,35" />
			</svg>
		</a>
	</div>
	<div class="tac">
		<span id="bp-pager" class="cycle-pager"></span>
		<div class="h-25"></div>
		<a class="button" href="/fotogalereya"><span>Все проекты</span></a>
	</div>
	<div class="h-25"></div>
</div>