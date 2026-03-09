<?php
session_start();
error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$res = '';
$user_exist = '';
$exOrder = '';
$err = 0;
$errStr = array();
$regMessage = '';
$user_ident = (intval($_SESSION['user_id']) > 0) ? "field2 = '".intval($_SESSION['user_id'])."'" : "field9 = '".session_id()."'";

if (is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0) {

	//TODO: Добавить проверки всех обязательных полей здесь

	if ($err == 0) {

		$user_id = el_autoregistration();

		$ids = array();
		$logist = '';
		$g = '';
		$c = 0;
		$form_cart = array();
		foreach ($_SESSION['cart'] as $id => $count) {
			if ($id != '')
				$ids[] = $id;
		}
		//Получаем информацию о выбранном типе доставке
		$logist = array();
		if(!isset($_POST['logistName'])) {
			$logist = el_dbselect("SELECT * FROM catalog_delivery_data WHERE active=1 AND id=" . intval($_POST['logist']), 0,
				$logist, 'row',
				true);
		}else{
			$logist['field1'] = $_POST['logistName'];
			$logist['field3'] = $_POST['logistPrice'];
			//Если доставка не курьером, то адрес доставки - это адрес пвз или постомата
			if(intval($_POST['logistType']) == 1 || intval($_POST['logistType']) == 3){
				$address = $_POST['logistAddress'];
			}else{
				$addressFields = array('region' => 'Регион', 'city' => 'Город', 'index' => 'Индекс', 'street' => 'улица',
					'house' =>	'дом',	'flat' => 'квартира', 'entrance' => 'подъезд', 'floor' => 'этаж', 'domofon' => 'домофон');
				$addressArr = array();
				foreach ($addressFields as $field => $label){
					if(strlen(trim($_POST[$field])) > 0){
						$addressArr[] = $label.': '.$_POST[$field];
					}
				}
				if(count($addressArr) > 0){
					$address = implode('<br>', $addressArr);
				}
			}
		}

		//Получаем информацию о заказанных товарах по их id
		$g = el_dbselect("SELECT * FROM catalog_goods_data WHERE id IN (" . implode(',', $ids) . ")", 0, $g, 'result', true);
		if (el_dbnumrows($g) > 0) {
			$row_catalog = el_dbfetch($g);
			$totalSumm = 0;
			$totalCount = 0;
			do {
				switch ($row_catalog['field52']) {
					case 'Хит продаж':
						$old_price = '';
						$price = $row_catalog['field26'];
						break;
					case 'Скидка':
						$price = $row_catalog['field25'];
						break;
					default:
						$price = $row_catalog['field26'];
						break;
				}
				$count = $_SESSION['cart'][$row_catalog['id']];
				$c++;
				$summ = $price * $count;
				$totalSumm += $summ;
				$totalCount += $count;
				$form_cart[] = '
	<input type="hidden" name="itransfer_item'.$c.'_name" value="'.htmlspecialchars($row_catalog['field1']).'" />
    <input type="hidden" name="itransfer_item'.$c.'_quantity" value="'.$count.'" />
    <input type="hidden" name="itransfer_item'.$c.'_measure" value="шт." />
    <input type="hidden" name="itransfer_item'.$c.'_price" value="'.$price.'.00" />
    <input type="hidden" name="itransfer_item'.$c.'_vatrate" value="0" />';
			} while ($row_catalog = el_dbfetch($g));

			//Добавляем строку с доставкой
			if(floatval($_POST['logistPrice']) > 0){
				$c++;
				$form_cart[] = '
	<input type="hidden" name="itransfer_item'.$c.'_name" value="Доставка" />
    <input type="hidden" name="itransfer_item'.$c.'_quantity" value="1" />
    <input type="hidden" name="itransfer_item'.$c.'_measure" value="шт." />
    <input type="hidden" name="itransfer_item'.$c.'_price" value="'.floatval($_POST['logistPrice']).'.00" />
    <input type="hidden" name="itransfer_item'.$c.'_vatrate" value="0" />';

				//Стоимость заказа с учетом доставки
				$totalSumm += floatval($_POST['logistPrice']);
			}

			//Определяем тип пользователя
			$clientType = (strlen(trim($_POST['orgName'])) > 0) ? 'LEGAL' : 'PRIVATE';

			$newIdOrder = el_getUpdateOrder($user_id, $ids, $totalSumm, intval($_POST['payment']), $logist, $address);

			$_POST['newIdOrder'] = $newIdOrder;
			$_SESSION['orderInfo'] = $_POST;

			if(intval($newIdOrder) > 0){
				$goodsList = implode("\n", $form_cart);
				ob_start();
				include $_SERVER['DOCUMENT_ROOT'] . '/modules/invoiceBoxFrm.php';
				$cacheStr = ob_get_contents();
				ob_end_clean();
				echo json_encode(array(
					'container' => 'message_login',
					'orderNumber' => date('Ymd') . $newIdOrder,
					'goodCount' => $totalCount,
					'orderSumm' => number_format($totalSumm, 0, ', ', ' '),
					'deliveryPrice' => intval($logist['field3']),
					'deliveryType' => $logist['field1'],
					'totalSumm' => number_format($totalSumm, 0, ', ', ' '),
					'result' => true,
					'resultText' => $cacheStr,
						//'session' => $_SESSION
					)
				);
			}
		}
	}
}
?>
