<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$cartInfo = el_buildCart();
?>
<div class="title">О заказе</div>
<div class="row"><span>Товаров:</span><?=$cartInfo[1]?></div>
<div class="row"><span>На сумму:</span>
	<div class="price" id="cartSumm"><?=number_format($cartInfo[2], 0, ', ', ' ')?></div>
</div>
<div class="row"><span>Доставка:</span>
	<div class="price" id="deliveryPrice"><?=($cartInfo[2] >= 2000) ? 0 : ''?></div>
	<p id="deliveryType"></p>
</div>
<div class="row"><span>К оплате:</span>
	<div class="price" id="totalSumm"></div>
	<p id="paymentType">Картой или наличными в пунктах выдачи заказов или постаматах</p>
</div>
<?
if(isset($_SESSION['login'])){
	?>
	<div class="row"><span>Контакты:</span>
		<p><?=$_SESSION['user_fio']?>,
			<br /><?=$_SESSION['user_phone']?>,
			<br /><?=$_SESSION['user_mail']?></p>
	</div>
	<?
}
?>
</div>