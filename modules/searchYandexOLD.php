<? include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Р”РѕРєСѓРјРµРЅС‚ Р±РµР· РЅР°Р·РІР°РЅРёСЏ</title>
</head>

<body><pre>
<?
error_reporting(E_ALL);

//$ya=el_connect1('xmlsearch.yandex.ru', '/xmlsearch/?user=elman&key=03.217163:a00a8b615d3e56bc2bb1efaa21c3a8b5&query=%3CРёРіСЂС‹%3E&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D5.docs-in-group%3D3&maxpassages=10&page=0');
//echo $response=el_connect('xmlsearch.yandex.ru', 'xmlsearch', '?user=elman&key=03.217163:a00a8b615d3e56bc2bb1efaa21c3a8b5&query=%3CРёРіСЂС‹%3E&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D5.docs-in-group%3D3&maxpassages=10&page=0', 0, $method='GET');
$ya = file('http://xmlsearch.yandex.ru/xmlsearch/?user=elman&key=03.217163:a00a8b615d3e56bc2bb1efaa21c3a8b5&query=%3CРёРіСЂС‹%3E&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D5.docs-in-group%3D3&maxpassages=10&page=0');
//print_r($ya);

$p = xml_parser_create();
xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
xml_parse_into_struct($p, implode("\n", $ya), $vals, $index);
xml_parser_free($p);

echo "<hr>Index array\n";
print_r($index);
echo "\n<hr>РњР°СЃСЃРёРІ Vals\n";
print_r($vals);
?>
</body>
</html>