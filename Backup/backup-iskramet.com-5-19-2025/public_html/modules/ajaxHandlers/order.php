<?php
session_start();
error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$res = '';
$user_exist = '';
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
		$cartString = '';
		$row_cart = array();
		$show_cart = array();
		$sj_cart = array();
		//Создаем справочник путей
		$catsPath = el_getGoodPath();
		foreach ($_SESSION['cart'] as $id => $count) {
			if ($id != '')
				$ids[] = $id;
		}
		//Справочник способов оплаты
		$payTypes = getRegistry("payment");

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
			$totalCount = 0;
			$totalSumm = 0;
			$totalBonus = 0;

			do {
				if (strlen($row_catalog['field3']) > 0) {
					$imgArr = explode(' , ', $row_catalog['field3']);
					$img = str_replace('big_', 'small_', $imgArr[0]);
					$imArr = explode('.', $img);
					$ext = strtolower(end($imArr));
				}
				switch ($row_catalog['field52']) {
					case 'Хит продаж':
						$class = ' pic hit';
						$old_price = '';
						$price = $row_catalog['field26'];
						$priceField = 'field26';
						$bonuses = true;
						break;
					case 'Скидка':
						$class = ' pic discount';
						$discount = ($row_catalog['field26'] / 100) * 5;
						$price = $row_catalog['field25'];//round($row_catalog['field26'] - $discount);
						$priceField = 'field25';
						$old_price = '<div class="price_old">'.number_format($row_catalog['field26'], 0, ', ', ' ').'</div>';
						$bonuses = false;
						break;
					default:
						$class = '';
						$old_price = '';
						$price = $row_catalog['field26'];
						$priceField = 'field26';
						$bonuses = true;
						break;
				}
				$link = 'https://toptoy.ru'.$catsPath[intval($row_catalog['cat'])] . '/' . $row_catalog['path'] . '.html';
				$count = $_SESSION['cart'][$row_catalog['id']];
				$summ = $price * $count;
				$totalCount += $count;
				$totalSumm += $summ;
				$bonus = el_calcBonus($price);
				if($bonuses)
					$totalBonus += $bonus;
				$c++;

				$row_cart[] = '<tr>
			          <td align="center">' . $c . '</td>
			          <td><a href="' . $link . '"><img src="https://toptoy.ru' . $img . '" hspace="10" align="left" style="max-width: 12%;"/>' .
					$row_catalog['field1'] .	'</a></td>
			          <td align="center">' . $count . '</td>
			          <td align="right" style="white-space: nowrap;">' . number_format($price, 0, ', ', ' ') . 'р.</td>
			          <td align="right" style="white-space: nowrap;">' . number_format($summ, 0, ', ', ' ') . 'р.</td>
			        </tr>';
				$show_cart[] = '<tr>
			          <td>' . $c . '</td>
			          <td><a href="' . $link . '"><img src="https://toptoy.ru' . $img . '"/>' .
					$row_catalog['field1'] .	'</a></td>
			          <td>' . $count . '</td>
			          <td class="nobr">' . number_format($price, 0, ', ', ' ') . 'р.</td>
			          <td class="nobr">' . number_format($summ, 0, ', ', ' ') . 'р.</td>
			        </tr>';

				$sj_cart[] = '{
                        "id": "'.$row_catalog['id'].'",
                        "name": "'.$row_catalog['field1'].'",
                        "price": '.$price.',
                        "quantity": '.$count.'
                    }';
			} while ($row_catalog = el_dbfetch($g));

			$row_cart[] = '<tr>
		          <td colspan="4" align="right">Стоимость доставки:</td>
		          <td align="right">' . number_format($logist['field3'], 0, ', ', ' ') . 'р.</td>
		        </tr>
		        <tr>
		          <td colspan="4" align="right"><strong>К оплате:</strong></td>
		          <td align="right" style="white-space: nowrap;"><strong>' . number_format(($totalSumm + intval($logist['field3'])), 0, ', ', ' ') . 'р.</strong></td>
		        </tr>
		      </tbody>
		    </table>';

			//Вносим бонусы на баланс
			if($totalBonus > 0){
				$res = el_dbselect("INSERT INTO catalog_bonuses_data 
				(active, field1, field2, field3, field4)
				VALUES (1, '".date('Y-m-d H:i:s')."', '".$_SESSION['user_id']."', '$newIdOrder', '$totalBonus')", 0, $res,
					'result', true);
			}

			//Если это не предоплаченный заказ, получаем номер нового заказа
			if(intval($_POST['payment']) != 2) {
				$newIdOrder = el_getUpdateOrder($user_id, $ids, $totalSumm, intval($_POST['payment']), $logist, $address);
			}else{
				$newIdOrder = $_POST['newIdOrder'];
				$res = el_dbselect("UPDATE catalog_cart_data SET field8 = $newIdOrder, field2 = '$user_id' WHERE 
				field9 = '".session_id()."' AND (field8 IS NULL OR field8 = '') 
				AND field3 IN (" . implode(',', $ids) . ")", 0, $res, 'result', true);
			}

			//Начало письма
			$cartString = '<h2 style="font-family:Verdana, Geneva, sans-serif">Заказ
		      №' . date('Ymd') . $newIdOrder . '</h2>
		    <h3 style="font-family:Verdana, Geneva, sans-serif">Контакты:</h3>
		    <p style="font-family:Verdana, Geneva, sans-serif">' . $_POST['name'] . ',</p>
		    <p style="font-family:Verdana, Geneva, sans-serif">Телефон: <b>' . $_POST['phone'] . '</b></p>
		    <p style="font-family:Verdana, Geneva, sans-serif">Почта: <a href="mailto:' . $_POST['mail'] . '">' . $_POST['mail'] . '</a></p>
		    <p style="font-family:Verdana, Geneva, sans-serif">Оплата: <b>' . $payTypes[$_POST['payment']] . '</b></p>
		    <h3 style="font-family:Verdana, Geneva, sans-serif">Доставка:</h3>
		    <p style="font-family:Verdana, Geneva, sans-serif">' . $logist['field1'] . '<br>
		    ' . $address . '</p>' . ((strlen(trim($_POST['comment'])) > 0) ? '
		    <h3 style="font-family:Verdana, Geneva, sans-serif">Комментарии:</h3>
		    <p style="font-family:Verdana, Geneva, sans-serif">' . $_POST['comment'] . '</p>' : '') . '
		    <h3 style="font-family:Verdana, Geneva, sans-serif">Состав заказа:</h3>
		    <table style="font-family:Verdana, Geneva, sans-serif" width="100%"
		      cellspacing="0" cellpadding="8" border="1">
		      <tbody>
		        <tr>
		          <th scope="col">#</th>
		          <th scope="col">Наименование</th>
		          <th scope="col">Кол-во</th>
		          <th scope="col">Цена</th>
		          <th scope="col">Итого</th>
		        </tr>' . implode("\n", $row_cart).$regMessage;

			if($totalBonus > 0){
				$cartString .= '<p>Начислено '.$totalBonus.' бонусн'.el_postfix($totalBonus, 'ый', 'ых', 'ых').' 
				балл'.el_postfix($totalBonus, '', 'а', 'ов').'</p>';
			}


			if(intval($_POST['payment']) != 2) {
				//Отправка уведомления с новым паролем пользователю
				$letter_body = el_render('/tmpl/letter/letter1.php',
					['caption' => 'Уважаемый/ая ' . $_POST['name'] . '!',
						'orderNumber' => $newIdOrder,
						'text' => $cartString,
						'phone' => '+7 (499) 40-40-615',
						'cznName' => 'TOPTOY.RU Для детей и родителей',
						'buttonText' => 'Перейти в личный кабинет',
						'buttonUrl' => 'http://' . $_SERVER['SERVER_NAME'] . '/lk/'
					]
				);
				$mailResult = el_mail($_POST['mail'], 'Заказ №' . date('Ymd') . $newIdOrder . ' на сайте ' . $_SERVER['SERVER_NAME'],
					$letter_body, 'order@toptoy.ru', 'html', 'smtp', '', 'order@toptoy.ru');
			}else{
				$mailResult = true;
			}

			//Теперь нужно сообщить Saferoute, что нужно перевести заказ в Личный кабинет
			$srOrderId = $_POST['logistOrderId'];
			include_once $_SERVER['DOCUMENT_ROOT']."/modules/saferoute/SafeRouteWidgetApi.php";
			$widgetApi = new SafeRouteWidgetApi();
			$widgetApi->setToken('NE5Dz5q94DpCop4If-wBtppOAyIY32tO');
			$widgetApi->setShopId('76986');

			$widgetApi->setMethod('POST');
			$widgetApi->setData(array('id' => $srOrderId, 'cmsId' => date('Ymd').$newIdOrder,
				'payment' => false, 'paymentMethod' => 'payment_'.$_POST['payment'],
				'status' => 'status_1', 'nppOption' => true));

			$srResult = $widgetApi->submit('https://api.saferoute.ru/v2/widgets/update-order');

			if($mailResult){
				ob_start();
				include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/orderComplete.php';
				$cacheStr = ob_get_contents();
				ob_end_clean();
				$_SESSION['cart'] = array();
				$_SESSION['orderInfo'] = array();
				setcookie('cart', '', time() - 86600);
				unset($_COOKIE['cart']);
				unset($_SESSION['cart']);
				unset($_SESSION['orderInfo']);
				echo json_encode(array(
					'container' => 'message_login',
					'orderNumber' => date('Ymd') . $newIdOrder,
					'goodCount' => $totalCount,
					'orderSumm' => number_format($totalSumm, 0, ', ', ' '),
					'deliveryPrice' => intval($logist['field3']),
					'deliveryType' => $logist['field1'],
					'totalSumm' => number_format(($totalSumm + intval($logist['field3'])), 0, ', ', ' '),
					'result' => true,
					'resultText' => $cacheStr));
			}
		}
	}
}
?>
