<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php');
$requiredUserLevel = array(0, 1);
include($_SERVER['DOCUMENT_ROOT'] . "/editor/secure/secure.php");

if(isset($_POST['delId'])){
    $delId = intval($_POST['delId']);
    $ds = el_dbselect("DELETE FROM sites WHERE id = $delId", 0, $ds, 'result', true);
    $dc = el_dbselect("DELETE FROM cat WHERE site_id = $delId", 0, $dc, 'result', true);
    $dco = el_dbselect("DELETE FROM content WHERE site_id = $delId", 0, $dco, 'result', true);
    header('Location: sites.php');
}


$sites = el_dbselect("SELECT * FROM sites ORDER BY short_name", 0, $sites, 'result', true);

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Управление сайтами</title>
    <style type="text/css">
        /*@font-face {
            font-family: 'Roboto Condensed';
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            src: local('Roboto Condensed Light'), local('RobotoCondensed-Light'), url(RobotoCondensed-Light.ttf) format('truetype');
        }

        @font-face {
            font-family: 'Roboto Condensed';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local('Roboto Condensed'), local('RobotoCondensed-Regular'), url(RobotoCondensed-Regular.ttf) format('truetype');
        }

        @font-face {
            font-family: 'Roboto Condensed';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: local('Roboto Condensed Bold'), local('RobotoCondensed-Bold'), url(RobotoCondensed-Bold.ttf) format('truetype');
        }*/

        body,
        td,
        th {
            font: 0.8125rem 'Roboto Condensed', sans-serif;
            color: #333333;
        }

        body header, body footer {
            /* [disabled]background: #005c85; */
        }

        li, ul {
            margin: 0px;
            padding: 0px;
        }

        img {
            max-width: 100%;
            display: block;
            height: auto;
        }

        .span .content table tr td .logo {
            max-width: 3rem;
            float: left;
            /*! line-height: 3rem; */
            /*! margin-top: auto; */
            /*! margin-bottom: auto; */
            /*! vertical-align: middle; */
            /*! max-height: 2rem; */
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            height: 2rem;
            margin-left: 0.5rem;
        }
        .span .content table tr td .logo img{
            max-height: 2rem;
        }

        .span .content table tr td .name {
            margin-left: 1rem;
            /*! line-height: 3rem; */
            float: left;
        }

        .span .content table tr th {
            text-align: center;
            line-height: 2rem;
        }

        .span .content table tr th .link {
            vertical-align: middle;
            color: #0092D1;
        }

        .span .content table tr .icon {
            text-align: center;
            line-height: 1rem;
        }

        .box {
            padding: 1rem;
        }

        .center {
            text-align: center;
        }

        /*горизонтальная цетровка текста внутри флекса добавлением класса*/
        .span .content {
            display: flex;
            flex-flow: row wrap;

        }

        .link {
            /* [disabled]border-bottom: 1px dotted #0092d1; */
        }

        .span .content table tr th a .material-icons {
            text-decoration: none;
            vertical-align: middle;
            margin-right: 0.5rem;
            color: #0092D1;
        }

        button .material-icons,
        button .material-icons:hover{
            color: #fff;
            vertical-align: middle;
        }

        .span {
            max-width: 1280px;
            margin-right: auto;
            margin-left: auto;
        }

        .span .content table tr td .checkbox {
            text-align: center;
        }

        .span .content table tr .icon a .material-icons {
            color: #005C85;
            text-decoration: none;
            vertical-align: middle;
        }

        .span .content table tr td .name {
            color: #0092D1;
        }


        .span .content {
            margin-right: 2rem;
            margin-left: 2rem;
        }

        .span .content .link {
            /* [disabled]margin-left: 1rem; */
        }

        td {
            line-height: 3rem;
            height: 3rem;
            vertical-align: middle;
            text-align: center;
        }

        table tr:nth-child(2n) td {
            background: #F5F5F5;
        }
    </style>
    <!-- -->
    <link href="style.css" rel="stylesheet">
    <!--link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"-->
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/tooltip.js"></script>
    <script>
        $(document).ready(function () {
            $('*').tooltip({showURL: false});

            $("#add_site").on("click", function(){
                top.MM_openBrWindow('create_edit_site.php?id=0&action=new','metainfo','scrollbars=yes,resizable=yes','650','600','true')
            });

            $(".edit_site").on("click", function(e){
                e.preventDefault();
                var sid = $(this).data("value");
                top.MM_openBrWindow('create_edit_site.php?id=' + sid + '&action=edit','metainfo','scrollbars=yes,resizable=yes','650','600','true')
            });
            $(".clone_site").on("click", function(e){
                e.preventDefault();
                var sid = $(this).data("value");
                top.MM_openBrWindow('create_edit_site.php?id=' + sid + '&action=clone','metainfo','scrollbars=yes,resizable=yes','650','600','true')
            });
            $(".delete_site").on("click", function(e){
                e.preventDefault();
                var sid = $(this).data("value"),
                    sname = $(this).data("name");
                var yes = confirm("Уверены, что хотите удалить сайт \"" + sname + "\"?");
                if(yes) {
                    $("#delFrm #delId").val(sid);
                    $("#delFrm").trigger("submit");
                }
            });
            $("#sitesForm .checkbox").on("click", function(){
                var actionText = "",
                    status = "0",
                    state = $(this).prop("checked");
                if(state){
                    actionText = "опубликовать";
                    status = "1";
                    state = true;
                }else{
                    actionText = "заблокировать";
                    status = "0";
                    state = false;
                }
                var yes = confirm("Уверены, что хотите " + actionText + " сайт?");
                $(this).prop("checked", (yes) ? state : !state);
                if(yes) {
                    $.post("siteStatus.php", {id: $(this).val(), status: status}, function (data) {
                        var answer = JSON.parse(data);
                        alert((answer.result) ? "Сайт успешно " + ((status == "1") ? "опубликован" : "заблокирован") : "Ошибка: " + answer.errors);

                    });
                }
            });
        });
    </script>
</head>

<body>

<h5>Управление сайтами</h5>
<br>
<button id="add_site"><i class="material-icons">add_circle</i> Создать новый сайт</button>
<br><br>
<main>
    <div class="span">
        <form method="post" id="delFrm"><input type="hidden" name="delId" id="delId"></form>
        <form method="post" name="sitesForm" id="sitesForm">
        <div class="content">
            <table width="100%" border="0" cellspacing="0" cellpadding="0.5rem">
                <tr>
                    <th scope="col">Название</th>
                    <th scope="col">Опубликован</th>
                    <th scope="col">Настройка</th>
                    <th scope="col">Клонировать</th>
                    <?/*th scope="col">Шаблон</th*/?>
                    <th scope="col">Содержание</th>
                    <th scope="col">Удалить</th>
                </tr>
                <?php
                if (el_dbnumrows($sites) > 0) {
                    $rs = el_dbfetch($sites);
                    do {
                        ?>
                        <tr>

                            <td>
                                <div class="logo"><img src="<?= $rs['logo'] ?>" alt="<?= $rs['short_name'] ?>"></div>
                                <a class="name" href="https://<?= $rs['domain'] ?>.<?=$GLOBALS['main_domain']?>" target="_blank" title="Перейти на сайт"><?= $rs['short_name'] ?></a>
                            </td>
                            <td><input type="checkbox" class="checkbox"<?=(intval($rs['active']) == 1) ? ' checked' : ''?> value="<?= $rs['id'] ?>"></td>
                            <td class="icon"><i class="material-icons"><a href="#" title="Настройка сайта" class="edit_site" data-value="<?= $rs['id'] ?>">&#xe8b9;</a></i></td>
                            <td class="icon"><i class="material-icons"><a href="#" title="Сделать копию сайта" class="clone_site" data-value="<?= $rs['id'] ?>">control_point_duplicate</a></i></td>
                            <?/*td class="icon"><i class="material-icons"><a href="#" title="Редактировать шаблон">&#xe42b;</a></i></td*/?>
                            <td class="icon"><i class="material-icons"><a href="menuadmin.php?site_id=<?= $rs['id'] ?>" onclick="top.showMenuAdmin(<?= $rs['id'] ?>)" title="Редактировать контент">edit</a></i></td>
                            <td class="icon"><i class="material-icons"><a href="#" title="Удалить сайт" class="delete_site" data-value="<?= $rs['id'] ?>"
                             data-name="<?= $rs['short_name'] ?>">delete_forever</a></i></td>
                        </tr>
                        <?php
                    } while ($rs = el_dbfetch($sites));
                } else {
                    echo '<tr><td colspan=7>Пока не создано ни одного сайта</td></tr>';
                }
                ?>
            </table>
        </div>
        </form>
    </div>
</main>
<footer>

</footer>
</body>

</html>