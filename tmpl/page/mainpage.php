<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/header.php';
?>
    <nav id="admin_menu">
        <div class="wrap">
			<?
			include $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/admin_menu.php';
			?>
        </div>
    </nav>

    <div class="content">
    <!-- изменяемая часть начало -->
    <div class="wrap">
        <main>
            <div class="text">
                <div class="box">
                    <h1><? el_text('el_pageprint', 'caption') ?></h1>
                </div>
                <div class="box">

                    <?
                    el_text('el_pageprint', 'text');
                    el_module('el_pagemodule', '');
                    ?>

                </div>
            </div>
        </main>
        <!-- изменяемая часть конец -->
    </div>


    <?php
    //include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/donate.php';
    ?>
    <?php
    //include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/news_list_short.php';
    ?>

<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/tmpl/page/blocks/footer.php';
?>