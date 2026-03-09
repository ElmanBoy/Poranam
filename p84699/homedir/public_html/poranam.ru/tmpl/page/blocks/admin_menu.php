<div class="wrap">
    <ul>
		<?
		//if(intval($_SESSION['user_level']) == 10){
		?>
			<li><a href="/lichnyy-kabinet/profile/"<?=($path == '/lichnyy-kabinet/profile') ? ' class="active"' : ''?>>
					<span class="material-icons">account_box</span>Профиль</a></li>
		<?
		//}
		if(intval($_SESSION['user_level']) != 10){
		?>
        <li><a href="/lichnyy-kabinet/polzovateli/"<?=($path == '/lichnyy-kabinet/polzovateli') ? ' class="active"' : ''?>>
                <span class="material-icons">account_box</span>Пользователи</a></li>
		<?
		}
		?>
        <li><a href="/lichnyy-kabinet/finansy/"<?=($path == '/lichnyy-kabinet/finansy') ? ' class="active"' : ''?>>
                <span class="material-icons">account_balance_wallet</span>Финансы</a></li>
		<?
		if(intval($_SESSION['user_level']) != 10){
		?>
        <li><a href="/meropriyatiya/"<?=($path == '/meropriyatiya') ? ' class="active"' : ''?>>
                <span class="material-icons">groups</span>Мероприятия</a></li>
		<?
		}
		?>
        <li><a href="/lichnyy-kabinet/statistika/"<?=($path == '/lichnyy-kabinet/statistika') ? ' class="active"' : ''?>>
                <span class="material-icons">assessment</span>Статистика</a></li>
		<?
		if(intval($_SESSION['user_level']) != 10){
		?>
        <li><a href="/lichnyy-kabinet/nastroyki/"<?=($path == '/lichnyy-kabinet/nastroyki') ? ' class="active"' : ''?>>
                <span class="material-icons">settings_applications</span>Настройки</a></li>
		<?
		}
		?>
    </ul>
</div>