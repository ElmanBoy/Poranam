<?php
$contact = array();
$i = 0;
function contactcmp($a, $b)
{
	return strnatcmp(strtolower($a["name"]), strtolower($b["name"]));
}
do {
	$contact[$i]['name'] = $row_catalog['field1'];
	$contact[$i]['address'] = $row_catalog['field2'];
	$contact[$i]['phone'] = $row_catalog['field3'];
	$contact[$i]['email'] = $row_catalog['field4'];
	$contact[$i]['requisit'] = $row_catalog['field5'];
	$contact[$i]['hours'] = $row_catalog['field6'];
	$contact[$i]['metro'] = $row_catalog['field7'];
	$i++;
} while ($row_catalog = el_dbfetch($catalog));
usort($contact, 'contactcmp');
?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script src="/js/ymap.js" type="text/javascript"></script>

		<ul id="addresses">
			<?php
		for($i = 0; $i < count($contact); $i++) {
			$address = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', strip_tags($contact[$i]['address']));
				?>
			<li>
				<strong><?=$contact[$i]['name']?></strong><br>
				<?=$contact[$i]['address']?>
				<span class="orangeDot">&nbsp;</span> м. <?=$contact[$i]['metro']?><br>
				<div class="addrEmail"><strong>E-mail:</strong>
				<?=$contact[$i]['email']?></div>
				<div class="addrPhone"><strong>Телефон:</strong>
					<a href="tel:<?=$contact[$i]['phone']?>"><?=$contact[$i]['phone']?></a></div>
				<div class="times">
					<?=$contact[$i]['hours']?>
				</div>
				<center>
					<button class="grayButton small" id="btnOfis"
							onclick="showMap('<?=$address?>', '<?=$contact[$i]['name']?>');">
						<?=$contact[$i]['name']?>
					</button>
				</center>
			</li>
			<?
		}
			?>

		</ul>
		<div id="map">
			<div class="hide_desktop"><strong>Перед приездом на склад просьба предупредить о Вашем визите заранее,
					позвонив по телефону: 8(495) 604 40 12</strong></div>
			<div id="ymap" style="width:567px; height:416px"></div>
		</div>
		<table border="0" id="contactTbl">
			<?php
			for($i = 0; $i < count($contact); $i++) {
				?>
				<tr>
					<td><strong><?=$contact[$i]['name']?> адрес:</strong><br>
						<?=$contact[$i]['address']?>
						<span class="orangeDot">&nbsp;</span> м. <?=$contact[$i]['metro']?>
					</td>
					<td><strong>Телефон:</strong><br>
						<a href="tel:<?=$contact[$i]['phone']?>"><?=$contact[$i]['phone']?></a>
					</td>
					<td><strong>E-mail:</strong><br>
						<?=$contact[$i]['email']?>
					</td>
					<td><strong>Реквизиты:</strong><br>
						<?=$contact[$i]['requisit']?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<strong class="hide_mobile">Перед приездом на склад просьба предупредить о Вашем визите заранее, позвонив по телефону: 8(495) 604 40
			12</strong>

<script>
	$(document).ready(function () {
		showMap("<?=str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', strip_tags($contact[0]['address']));?>", "<?=$contact[0]['name']?>");
	});
</script>