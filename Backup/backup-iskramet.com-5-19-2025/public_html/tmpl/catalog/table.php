<?php
$vendorArr = array();
$i = 0;
function vendorcmp($a, $b)
{
	return strnatcmp(strtolower($a["name"]), strtolower($b["name"]));
}

do {
	$vendorArr[$i]['id'] = $row_catalog['id'];
	$vendorArr[$i]['name'] = $row_catalog['field1'];
	$vendorArr[$i]['logo'] = $row_catalog['field3'];
	$vendorArr[$i]['desc'] = $row_catalog['field2'];
	$i++;
} while ($row_catalog = el_dbfetch($catalog));
usort($vendorArr, 'vendorcmp');
?>

<table border="0" id="vendorsNamesTbl" class="hide_mobile">
	<tr>
		<td width="25%">
			<?php
			$endCol = ceil($i / 4);
			$c = 1;
			for ($i = 0; $i < count($vendorArr); $i++) {
				echo '<a href="#' . $vendorArr[$i]['id'] . '">' . $vendorArr[$i]['name'] . '</a><br>' . "\n";
				if ($endCol == $c) {
					$c = 1;
					echo '</td><td width="25%">';
				} else {
					$c++;
				}

			}
			?>
		</td>
	</tr>
</table>
<center>
	<button class="redButton big hide_desktop" id="shooseVendors" data-source="vendorsNamesTbl">Выбрать производителя</button>
</center>


<table border="0" id="vendorsListTbl">
	<?php
	for ($i = 0; $i < count($vendorArr); $i++) {
		?>
		<tr>
			<td><a id="<?= $vendorArr[$i]['id'] ?>" name="<?= $i ?>"></a><strong><?= $vendorArr[$i]['name'] ?></strong></td>
			<td align="center"><?= (file_exists($_SERVER['DOCUMENT_ROOT'] . $vendorArr[$i]['logo'])) ?
					'<img src="' . $vendorArr[$i]['logo'] . '" alt="' . htmlspecialchars($vendorArr[$i]['name']) . '">' : '' ?></td>
			<td><?= $vendorArr[$i]['desc'] ?></td>
		</tr>
		<?php
	}
	?>
</table>