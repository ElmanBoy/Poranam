<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/header.php';
?>
<nav id="admin_menu">
    <div class="wrap">

    </div>
</nav>

<div class="content">
    <!-- изменяемая часть начало -->
    <div class="slide_welcome">
        <div class="wrap">
            <div class="text_welcome">
                <div class="box">Многофункциональная платформа для работы в масштабах дома, улицы …
                    страны по объединению граждан РФ, выработки общих интересов (путем рассмотрения инициатив участников и голосование по ним)
                    и представления этих интересов как в органах власти посредством выборов, так и в проведении других установленных законом мероприятий.</div>

            </div>
        </div>
    </div>
    <main>
        <div class="wrap">

            <div class="widgets">
                <div class="box">
                    <div class="item">
                        <a href="/deyatelnost/">
                            <div class="title">Участвуй</div>
                            <div class="info_value">25</div>
                            <div class="info_desc">Региональных и профессональных групп</div>
                        </a>
                    </div>
                </div>
                <div class="box">
                    <div class="item">
                        <a href="/initsiativy/">
                            <div class="title">Предлагай</div>
                            <div class="info_value">118</div>
                            <div class="info_desc">Региональных и профессиональных инициатив выдвинуто</div>
                        </a>
                    </div>
                </div>
                <div class="box">
                    <div class="item">
                        <a href="/golosovanie/">
                            <div class="title">Голосуй</div>
                            <div class="info_value">64</div>
                            <div class="info_desc">Региональных и профессиональных голосований проведено</div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="promo">

                <div class="pic">
                    <div class="box"><img src="/images/rupor.jpg"></div>
                </div>


                <div class="intro">
                    <div class="box">
                        <?/*div class="title">Создавай</div*/?>
                        <p><?=$row_dbcontent['text']?></p>
                        <a href="/prisoedinitsya/">
                            <div class="party_name">Присоединиться к движению "Пора"</div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <!-- изменяемая часть конец -->
</div>


<?php /*
$news = el_dbselect("SELECT * FROM catalog_news_data WHERE active = 1",
    3, $news, 'result', true);
if(el_dbnumrows($news) > 0){
$rn = el_dbfetch($news);
?>
<div class="news_list">
    <div class="wrap">
        <?
        do{
        ?>
        <div class="item">
            <div class="pic">
                <div class="box">
                    <img src="<?=$rn['field4']?>" />
                </div>
            </div>
            <div class="anons">
                <div class="box">
                    <div class="date"><?=$rn['field2']?></div>
                    <div class="title"><?=$rn['field1']?></div>
                    <div class="disc">
                        <p><?=$rn['field11']?></p>
                    </div>
                    <button class="button text">Подробнее</button>
                </div>
            </div>

        </div>
            <?
        }while($rn = el_dbfetch($news));
          */  ?>
    </div>
</div>
<?php
//}
include_once $_SERVER['DOCUMENT_ROOT'].'/tmpl/page/blocks/footer.php';
?>