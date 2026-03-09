<?
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/site_props.php';
$requiredUserLevel = array(1);
//include($_SERVER['DOCUMENT_ROOT']."/editor/secure/secure.php");
if (!isset($_REQUEST['ajax'])) {
    ?>
    <script type="text/javascript">
        function refresh_settings(cat_id, params) {
            preloader('show');
            $.post('/modules/settingTable.php', {'catalog_id': cat_id, 'cat': '<?=$cat?>', 'params': params},
                function (data) {
                    $("table." + cat_id).html(data);
                    preloader('hide');
                    $('*').tooltip();
                });
            return true;
        }

        function delSetting(id, cat_id) {
            var OK = confirm("Уверены, что хотите удалить эту запись?");
            if (OK) {
                preloader('show');
                $.get("/modules/delSetting.php", {'id': id, 'cat_id': cat_id, 'ajax': 1}, function (data) {
                    eval(data);
                    $('*').tooltip();
                });
            }
        }

        function editSetting(id, cat_id) {
            $.post("/tmpl/catalog/settings.php", {'id': id, 'ajax': 1, 'action': cat_id}, function (data) {
                $("#" + cat_id + id).html(data);
                $('*').tooltip();
            })
        }

        function saveRow(cat_id) {
            $.post("/tmpl/catalog/settings.php", $('#' + cat_id + "FrmEdit").serialize(), function (data) {
                eval(data);
                $('*').tooltip();
            })
        }

        function newRow(cat_id) {
            $.post("/tmpl/catalog/settings.php", $('#' + cat_id + "Frm").serialize(), function (data) {
                eval(data);
                $('*').tooltip();
            })
        }

        function saveHistory() {
            $.post("/tmpl/catalog/settings.php", {'ajax': 1, 'dhistory': $("#dhistory").val()}, function (data) {
                eval(data);
            })
        }
    </script>
    <style>
        .tab-content {
            background-color: #fff
        }
    </style>
    <fieldset class="well">
        <legend>Справочники</legend>
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab">Менеджеры</a></li>
                <li><a href="#tab2" data-toggle="tab">Реклама</a></li>
                <li><a href="#tab3" data-toggle="tab">Категории клиентов</a></li>
                <li><a href="#tab4" data-toggle="tab">Регионы</a></li>
                <li><a href="#tab5" data-toggle="tab">Результаты звонков</a></li>
                <li><a href="#tab6" data-toggle="tab">Результаты встреч</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab1">
                    <p>
                    <table id="setTable" class="table table-striped table-hover smallFont managers" style="width:auto !important; float:left">
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_managers_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'managers'));
                        ?>
                    </table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового менеджера</p>
                        <form id="managersFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Фамилия, Имя, Отчество"><br>
                            <input type="text" name="user" class="input-small" placeholder="Логин">
                            <input type="password" name="password" class="input-small" placeholder="Пароль"><br>
                            <input type="hidden" name="catalog_id" value="managers">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('managers')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
                <div class="tab-pane" id="tab2">
                    <p>
                    <table id="setTable2" class="table table-striped table-hover smallFont sources" style="width:auto !important; float:left">
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_sources_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'sources'));
                        ?></table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового вида рекламы</p>
                        <form id="sourcesFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Название типа рекламы"><br>
                            <input type="hidden" name="catalog_id" value="sources">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('sources')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
                <div class="tab-pane" id="tab3">
                    <p>
                    <table id="setTable2" class="table table-striped table-hover smallFont clieintscat" style="width:auto !important; float:left">
                        <tbody>
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_clieintscat_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'clieintscat'));
                        ?></tbody>
                    </table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового типа клиента</p>
                        <form id="clieintscatFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Название типа клиента"><br>
                            <input type="hidden" name="catalog_id" value="clieintscat">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('clieintscat')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
                <div class="tab-pane" id="tab4" style="height:300px !important; overflow:auto">
                    <p>
                    <table id="setTable2" class="table table-striped table-hover smallFont regions" style="width:auto !important; float:left">
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_regions_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'regions'));
                        ?></tbody></table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового региона</p>
                        <form id="regionsFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Название региона с кодом"><br>
                            <input type="hidden" name="catalog_id" value="regions">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('regions')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
                <div class="tab-pane" id="tab5">
                    <p>
                    <table id="setTable2" class="table table-striped table-hover smallFont telresults" style="width:auto !important; float:left">
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_telresults_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'telresults'));
                        ?></table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового результата звонка</p>
                        <form id="telresultsFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Название результат звонка"><br>
                            <input type="hidden" name="catalog_id" value="telresults">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('telresults')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
                <div class="tab-pane" id="tab6">
                    <p>
                    <table id="setTable2" class="table table-striped table-hover smallFont fullresults" style="width:auto !important; float:left">
                        <?
                        $men = el_dbselect("SELECT id, field1 FROM catalog_fullresults_data WHERE active=1 ORDER BY field1 ASC", 0, $men);
                        el_dbrowprint($men, '/tmpl/catalog/settings.php', 'В этом справочнике пока пусто.', array('catalog_id' => 'fullresults'));
                        ?></table>
                    <div style="float:left; margin-left:20px;">
                        <p class="text-info">Добавление нового результата встречи</p>
                        <form id="fullresultsFrm" onSubmit="return false">
                            <input type="text" name="field1" placeholder="Название результат встречи"><br>
                            <input type="hidden" name="catalog_id" value="fullresults">
                            <input type="hidden" name="ajax" value="1">
                            <input type="hidden" name="action" value="newRow">
                            <button onClick="newRow('fullresults')" class="btn btn-primary">Добавить</button>
                        </form>
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="well">
        <legend>Общие настройки</legend>
        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab7" data-toggle="tab">Время хранения данных</a></li>
                <!--li><a href="#tab8" data-toggle="tab">Section 2</a></li-->
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab7">
                    <p class="text-info">Сколько дней хранить информацию о клиентах</p>
                    <input type="text" name="dhistory" id="dhistory" value="<?= $site_property['dhistory'] ?>"><br>
                    <button class="btn btn-primary" onClick="saveHistory()">Сохранить</button>
                </div>
                <!--div class="tab-pane" id="tab8">
                  <p>Howdy, I'm in Section 2.</p>
                </div-->
            </div>
        </div>
    </fieldset>
<? } ?>