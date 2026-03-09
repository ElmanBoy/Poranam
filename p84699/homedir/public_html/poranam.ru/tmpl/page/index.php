<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= $xxicms_meta['title']; ?></title>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="description" content="<?= $xxicms_meta['description']; ?>">
    <meta name="keywords" content="<?= $xxicms_meta['keywords']; ?>">
    <meta name='yandex-verification' content='7bbe33c8b2e19f3b' />
    <link rel="shortcut icon" type="image/x-icon" href="<?= $skin_path ?>img/favicon.ico">
	<style>
		<?php 
			include($skin_root.'style.css');
		?>
	</style>
	<script>
		var ajaxUrl = '<?= $home_url ?>/ajax';
		var homeUrl = '<?= $home_url ?>';
	</script>
</head>
<body>
<div id="container">
	<div id="header">
		<div class="block-inside">
			<div>
				<a href="<?= $home_url ?>" class="logo">
					<span>
						<strong>Технологии</strong>
						Микроклимата
					</span>
					<?= xxicms_get_user_var('slogan') ?>
				</a>
			</div>
			<div id="header-callback">
				<a class="button callback-link" href="#">
					<span>Обратный звонок</span>
				</a>
				<div class="h-10"></div>
				<a class="button tender-link" href="#">
					<span>Пригласить в тендер</span>
				</a>
			</div>
			<div id="header-contacts">
				<div id="header-phone"><?= xxicms_get_user_var('phone') ?></div>
				<div id="header-email"><a href="mailto:<?= xxicms_get_user_var('info-email') ?>"><?= xxicms_get_user_var('info-email') ?></a></div>
			</div>
			<div id="header-address">
				<span id="header-address-title">Москва</span>
				<span id="header-address-subtitle">ул. Шаболовка, дом 34, стр. 3</span>
			</div>
		</div>
	</div>
	<div id="top-menu">
		<div id="tm-01">
			<div class="tm-title">Специализация</div>
			<?= xxicms_get_nav('top-menu-01') ?>
		</div>
		<div id="tm-02">
			<div class="tm-title">Услуги</div>
			<?= xxicms_get_nav('top-menu-02') ?>
		</div>
		<div id="tm-03">
			<div class="tm-title">Компания</div>
			<?= xxicms_get_nav('top-menu-03') ?>
		</div>
		<a id="tm-calc-link" href="<?= $home_url ?>onlayn-kalkulyator"><span><em>Он-лайн калькуляторы стоимости работ</em></span></a>
	</div>
	<div id="main">
		<?php if(xxicms_is_frontpage()): ?>
			<?php include('homepage.php') ?>
		<?php elseif(xxicms_get_page() == 'onlayn-kalkulyator'): ?>
			<div class="block-inside">
				<?= $xxicms_content ?>
				<div class="h-50"></div>
			</div>
		<?php else: ?>
			<div class="h-one"></div>
			<div class="sidebar">
				<?php if(isset($xxicms_page_params['nav']) && trim($xxicms_page_params['nav']) != ''): ?>
					<div class="sidebar-title st-<?= $xxicms_page_params['nav'] ?>">
						<?php
							$arr_chapters = array(
								'company' => 'КОМПАНИЯ',
								'vent' => 'ВЕНТИЛЯЦИЯ',
								'cond' => 'КОНДИЦИОНИРОВАНИЕ',
								'heat' => 'ОТОПЛЕНИЕ',
								'services' => 'УСЛУГИ',
								'calc' => 'ОНЛАЙН КАЛЬКУЛЯТОР'
							);
						?>
						<h3><?= $arr_chapters[$xxicms_page_params['nav']] ?></h3>
					</div>
				<?php else: ?>
					<div class="sidebar-title st-company">
						<h3>КОМПАНИЯ</h3>
					</div>
				<?php endif; ?>
				<div class="sidebar-menu">
					<?php if(isset($xxicms_page_params['nav']) && trim($xxicms_page_params['nav']) != ''): ?>
						<?= xxicms_get_nav($xxicms_page_params['nav']) ?>
					<?php else: ?>
						<?= xxicms_get_nav('company') ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="content">
				<div id="breadcrumbs">
					<?= xxicms_get_breadcrumbs() ?>
				</div>
				<?= $xxicms_content ?>
				<div class="h-50 mobile-hide"></div>
			</div>
		<?php endif; ?>
	</div> <!-- END OF #main -->
	
	
	<?php if(!xxicms_get_page() != 'objects'): ?>
		<?php include('bottom-projects.php') ?>
	<?php endif; ?>
	
	<?php if( (!xxicms_is_frontpage()) && (xxicms_get_page() != 'specialist-call') ): ?>
		<?php include('bottom-form.php') ?>
	<?php endif; ?>
	
	<div id="footer">
		<div id="footer-top">
			<div class="row">
				<div class="col quarter">
					<div class="h-10"></div>
					<a href="<?= $home_url ?>" class="logo">
						<span>
							<strong>Технологии</strong>
							Микроклимата
						</span>
						<?= xxicms_get_user_var('slogan') ?>
					</a>
				</div>
				<div class="col three-quarters">
					<div class="row">
						<div class="col quarter">
							<h5>Специализация</h5>
							<?= xxicms_get_nav('top-menu-01') ?>
						</div>
						<div class="col quarter">
							<h5>Услуги</h5>
							<?= xxicms_get_nav('top-menu-02') ?>
						</div>
						<div class="col quarter">
							<h5>Компания</h5>
							<?= xxicms_get_nav('top-menu-03') ?>
						</div>
						<div class="col quarter">
							<h5 class="mobile-hide">&nbsp;</h5>
							<div class="mobile-show">
								<ul>
									<li><a href="<?= $home_url ?>onlayn-kalkulyator">Онлайн-калькуляторы</a></li>
									<li><a class="callback-link-inline" href="#">Обратный звонок</a></li>
									<li><a href="<?= $home_url ?>sitemap">Карта сайта</a></li>
								</ul>
							</div>
							<div class="h-25 mobile-hide"></div>
<!--							<form role="search" method="post" id="searchform" class="searchform" action="<?= $home_url ?>search">
								<div>
									<input type="text" value="" name="s" id="s" placeholder="Поиск по сайту">
									<input type="submit" id="searchsubmit" value="">
								</div>
							</form>-->
						</div>
					</div>
				</div>
			</div>
			<div class="h-25 mobile-hide"></div>
			<div class="row" id="footer-address-row">
				<div class="col quarter" id="footer-phone">
					+7 (495) 921-57-03
				</div>
				<div class="col quarter" id="footer-address">г. Москва, ул. Шаболовка, дом 34, стр. 3</div>
				<div class="col quarter" id="footer-working-hours">Пн-Сб с 9.00 до 19.00, Вс - выходной</div>
				<div class="col quarter" id="footer-email"><a href="mailto:info@climate-technology.ru">info@climate-technology.ru</a></div>
			</div>
		</div>
		<div id="footer-bottom">
			Все права защищены и принадлежат компании Технологии микроклимата 2016
		</div>
	</div>
</div> <!-- END OF #container -->

<div id="nav">
	<div id="nav-inside">
		<?= xxicms_get_nav('mobile') ?>
		<ul>
			<li><a class="callback-link-inline" href="#">Заказать звонок</a></li>
			<li><a class="tender-link-inline" href="#">Пригласить в тендер</a></li>
		</ul>
	</div>
</div>

<div id="nav-link">
	<svg viewBox="0 0 40 40">
		<g id="nav-link-idle">
			<line x1="8" y1="12" x2="32" y2="12"></line>
			<line x1="8" y1="20" x2="32" y2="20"></line>
			<line x1="8" y1="28" x2="32" y2="28"></line>
		</g>
		<g id="nav-link-active">
			<line x1="10" y1="30" x2="30" y2="10"></line>
			<line x1="10" y1="10" x2="30" y2="30"></line>
		</g>
	</svg>
</div>

<div id="to-top">
	<svg viewBox="0 0 40 40">
		<polyline fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" points="10,25 20,14 30,25" />
	</svg>
</div>

<div class="hidden-popups">
	<div class="popup-overlay">
		<div class="popup-general" id="popup-callback">
			<div class="popup-close"><svg width='20' height='20'><line x1='3' y1='3' x2='17' y2='17' stroke='#fff' stroke-width='2' /><line x1='17' y1='3' x2='3' y2='17' stroke='#fff' stroke-width='2' /></svg></div>
			<div class="popup-title popup-title-callback">ЗАКАЗ ОБРАТНОГО ЗВОНКА</div>
			<?= xxicms_do_shortcode('[form callback]'); ?>
		</div>
		<div class="popup-general" id="popup-tender">
			<div class="popup-close"><svg width='20' height='20'><line x1='3' y1='3' x2='17' y2='17' stroke='#fff' stroke-width='2' /><line x1='17' y1='3' x2='3' y2='17' stroke='#fff' stroke-width='2' /></svg></div>
			<div class="popup-title popup-title-tender">ПРИГЛАСИТЬ В ТЕНДЕР</div>
			<?= xxicms_do_shortcode('[form tender]'); ?>
		</div>
	</div>
</div>

<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,700&amp;subset=cyrillic" rel="stylesheet">

<!--
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
<script>
  WebFont.load({
    google: {
      families: [ 'Open+Sans:400,300,400italic,700&amp;subset=latin,cyrillic' ]
    }
  });
</script>-->

<?= $xxicms_template_tail; ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript"> 
	if(navigator.userAgent.indexOf("Speed Insights") == -1) {
	(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter17880562 = new Ya.Metrika({ id:17880562, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); 
	}
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/17880562" style="position:absolute; left:-9999px;" alt="yandex metrika" /></div></noscript> 
<!-- /Yandex.Metrika counter -->

<!-- Google Analytics -->
<script>
	function include(filename)
	{
	var head = document.getElementsByTagName('head')[0];

	var script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';

	head.appendChild(script)
	}   
	if(navigator.userAgent.indexOf("Speed Insights") == -1) {
		window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
		ga('create', 'UA-35817590-1', 'auto');
		ga('send', 'pageview');
		include('https://www.google-analytics.com/analytics.js');
	}
</script>
<!-- End Google Analytics -->

</body>
</html>	