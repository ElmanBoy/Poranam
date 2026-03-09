<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
    //print_r($_POST);
    $meeting = null;
    $members = null;
    $users = null;
    $allUsers = [];
    $meeting_id = intval($_POST['params']);
    $meeting = el_dbselect("SELECT * FROM catalog_init_data WHERE id = $meeting_id", 0, $meeting, 'row', true);

    $queryArr = [];
    if(intval($meeting['field5']) > 0){
        $queryArr[] = 'field8 IN ('.$meeting['field5'].')';
    }
    if(intval($meeting['field6']) > 0){
        $queryArr[] = 'field9 IN ('.$meeting['field6'].')';
    }
    if(intval($meeting['field7']) > 0){
        $queryArr[] = 'field7 IN ('.$meeting['field7'].')';
    }
    if(strlen($meeting['field8']) > 0){
        $queryArr[] = 'field10 = \''.$meeting['field8'].'\'';
    }
    if(intval($meeting['field9']) > 0){
        $queryArr[] = 'field11 = \''.$meeting['field9'].'\'';
    }
    if(strlen($meeting['field12']) > 0 && intval($meeting['field12']) > 0){
        $queryArr[] = 'FIND_IN_SET('.intval($meeting['field12']).', field26) > 0';
    }
    if(strlen(trim($meeting['field17'])) > 0 && $meeting['field17'] != 0){
        $queryArr[] = '(field16 IN ('.$meeting['field17'].') OR field25 = '.$meeting['field17'].')';
    }
    if(intval($meeting['field13']) > 0){
        $queryArr[] = 'field6 = \''.$meeting['field13'].'\'';
    }
    $approved = false;
    if(intval($meeting['field14']) >= 14){
        $approved = true;
    }

    $members = el_dbselect("SELECT users FROM meeting_members WHERE meeting_id = $meeting_id", 0, $members, 'row', true);
?>
            <script src="/js/DataTables/datatables.min.js"></script>
        <link href="/js/DataTables/datatables.css?v=1" rel="stylesheet" />
<div class="pop_up">
    <div class="title">
        <h2>Список участников</h2>
        <div class="close" onclick="pop_up_meeting_list_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <form>
            <h3><?=$meeting['field1']?></h3>
            <div class="group">
                <div>
                    <?php
                    if($_SESSION['visual_user_id'] == $meeting['field4'] || $_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
                    ?>
                    <div class="control" id="control" style="position: relative;">
                        <? if(!$approved || $_SESSION['user_level'] == 11){ ?>
                        <button class="text icon" id="btn_save"><span class="material-icons">save</span>Сохранить</button>
                        <? }?>
                        <button class="text icon" id='btn_export'><span class="material-icons">file_download</span>Скачать</button>
                    </div>
                    <?php
                    }
                    ?>
                    <table class="table_data display" id="meeting_members" style="width:100%" data-page-length='15'>
                        <thead>
                        <tr>
                            <?php
                            if($_SESSION['visual_user_id'] == $meeting['field4'] || $_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
                                if(!$approved || $_SESSION['user_level'] == 11){
                                ?>
                            <th>
                                <div class="custom_checkbox">
                                    <label class="container"><input type="checkbox" id="check_all_members"><span class="checkmark"></span></label>
                                </div>
                            </th>
                                <?php
                                    }
                                }else{
                                ?>
                            <th></th>
                            <?php
                            }
                            ?>
                            <th>ID</th>
                            <?/*th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th*/?>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- row -->
                        <?/*
                        do{
                            $fio = explode(" ", $row_catalog['field1']);
                        */?><!--
                            <tr>
                                <td>
                                    <div class="custom_checkbox">
                                        <label class="container">
                                            <input type="checkbox" value="<?/*=$row_catalog['id']*/?>"><span class="checkmark"></span>
                                        </label>
                                    </div>
                                </td>
                                <td><?/*=$row_catalog['user_id']*/?></td>
                                <td><?/*=$fio[0]*/?></td>
                                <td><?/*=$fio[1]*/?></td>
                                <td><?/*=$fio[2]*/?></td>
                            </tr>
                                --><?/*
                        }while($row_catalog = el_dbfetch($catalog));*/

                        ?>

                        <!-- row -->
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </section>
    <iframe frameborder="0" width="0" height="0" id="export_frame"></iframe>
</div>
        <script>
            var selRows = [<?=$members['users']?>];

            /*var oldExportAction = function (self, e, dt, button, config) {
                if (button[0].className.indexOf('buttons-excel') >= 0) {
                    if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                    }
                    else {
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                    }
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
            };

            var newExportAction = function (e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;

                dt.one('preXhr', function (e, s, data) {
                    // Just this once, load all data from the server...
                    data.start = 0;
                    data.length = 2147483647;

                    dt.one('preDraw', function (e, settings) {
                        // Call the original action function
                        oldExportAction(self, e, dt, button, config);

                        dt.one('preXhr', function (e, s, data) {
                            // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                            // Set the property to what it was before exporting.
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                        });

                        // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                        setTimeout(function(){dt.ajax.reload; $(".buttons-excel").removeClass("processing")}, 0);

                        // Prevent rendering of the full data to the DOM
                        return false;
                    });
                });

                // Requery the server with the new one-time export settings
                dt.ajax.reload();
            };*/

            var meetingTable = $('#meeting_members').DataTable({
                caseInsensitive: true,
                ajax: {
                    url: '/modules/ajaxHandlers/meeting_list.php',
                    type: 'POST',
                    length: 20,
                    data: {params: <?=json_encode($queryArr)?>}
                },
                /*layout: {
                    topStart: 'buttons',
                    info: null
                },
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Экспорт в Excel',
                        filename: 'Список учавствующих',
                        action: newExportAction,
                        exportOptions: {
                            <?php
                            //if($_SESSION['visual_user_id'] == $meeting['field4'] || $_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
                            ?>
                            columns: ':not(:first-child)',
                            rows: '.selected'
                            <?php
                            //}
                            ?>
                            /!*modifier: {
                                selected: true
                            },*!/
                            //'row-selector': 'tr.selected',
                            /!*rows: function ( idx, data, node ) {
                                return !!($('tr.selected', node).length);
                            },*!/
                            //rows: 'tr.selected'
                        },
                        exportData: {
                            //'row-selector': 'tr.selected'
                            /!*rows: function ( idx, data, node ) {
                                return !!($('tr.selected', node).length);
                            }*!/
                        }
                    }
                ],*/
                columnDefs: [

                    /*{
                        targets: 2,
                        //data: 'display',
                        type: 'html',
                        searchable: true,
                        render: function (data, type, row, meta) {
                            let fio = row[2].split(" ");
                            return fio[0] || "";
                        }
                    },
                    {
                        targets: 3,
                        //data: 'display',
                        type: 'html',
                        searchable: true,
                        render: function (data, type, row, meta) {
                            let fio = row[3].split(" ");
                            return fio[1] || "";
                        }
                    },
                    {
                        targets: 4,
                        //data: 'display',
                        type: 'html',
                        searchable: true,
                        render: function (data, type, row, meta) {
                            let fio = row[4].split(" ");
                            return fio[2] || "";
                        }
                    }*/
                    <?php
                    if($_SESSION['visual_user_id'] == $meeting['field4'] || $_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
                        if(!$approved || $_SESSION['user_level'] == 11){
                    ?>
                    {
                        orderable: false,
                        searchable: false,
                        render: DataTable.render.select(),
                        targets: 0
                    }
                    <?
                        }
                    }else{
                    ?>
                    {
                        targets: 0,
                        searchable: false,
                        visible: false,
                        render: function (data, type, row, meta) {
                            /*if (table.row(':eq(0)').selected()) {
                                return 'Участвовал';
                            }*/
                            //if(selRows.indexOf(data) != -1) {
                                //console.log(data, type, row, meta.row);
                                return '';
                            //}
                        }
                    }
                    <?php
                    }
                    ?>
                ],
                /*select: {
                    style: 'multi',
                    selector: 'td:first-child',
                    rowId: 0,
                    stateSave: true,
                    headerCheckbox: true,
                    info: false
                },*/
                //stateSave: true,
                //select: true,
                rowId: 0,
                lengthChange: false,
                pageLength: 20,
                processing: true,
                serverSide: true,
                order: [[1, 'asc']],
                search: {
                    return: true
                },
                language: {
                    url: '/js/DataTables/ru.json'
                }
            });



            meetingTable.on( 'draw', function ( e, settings ) {

                let storedCheckAll = localStorage.getItem('checkAllMembers' + <?=$meeting_id?>);
                if(storedCheckAll == '1'){
                    $('#check_all_members').prop('checked', true);
                }

                if(selRows.length > 0) {
                    for (let i = 0; i < selRows.length; i++) {
                        $('#meeting_members tr[id=' + selRows[i] + '] input').prop('checked', 'checked');
                        $('#meeting_members tr[id=' + selRows[i] + ']').addClass('selected');
                    }
                }

                $('#meeting_members input.dt-select-checkbox').on('change', function() {
                    let row = $(this).closest('tr');
                    let rowData = parseInt(row.attr("id"));

                    if (!$(this).prop("checked")) {
                        // Удалить строку из массива выбранных, если она была выбрана
                        let index = selRows.indexOf(rowData);
                        if (index !== -1) {
                            selRows.splice(index, 1);
                            row.removeClass('selected');
                        }
                    } else {
                        // Добавить строку в массив выбранных, если она была не выбрана
                        selRows.push(rowData);
                        row.addClass("selected");
                    }
                });


            });

            /*var tableData = meetingTable.buttons.exportData({
                //action: newExportAction,
                rows: 'tr.selected'
            }); console.log(tableData);*/

            $('#check_all_members').on('change', function () {
                if ($('#check_all_members').prop('checked')) {
                    $.post('/', {ajax: 1, action: 'getAllMembers', id: <?=$meeting_id?>}, function (data) {
                        selRows = JSON.parse(data);
                        for (let i = 0; i < selRows.length; i++) {
                            $('#meeting_members tr[id=' + selRows[i] + '] input').prop('checked', 'checked');
                            $('#meeting_members tr[id=' + selRows[i] + ']').addClass('selected');
                        }
                    });
                } else {
                    $('#meeting_members input').prop('checked', false);
                    $('#meeting_members tr').removeClass('selected');
                    selRows = [];
                }
            });
            <?php
            if($_SESSION['visual_user_id'] == $meeting['field4'] || $_SESSION['user_level'] == 11 || $_SESSION['user_level'] == 4){
            ?>
            $("#btn_save").on("click", function(e){
                e.preventDefault();
                $.post("/", {ajax: 1, action: "setMeetingMembers", id: <?=$meeting_id?>, members: selRows},
                    function(data){
                    alert(data);
                        if ($('#check_all_members').prop('checked')) {
                            localStorage.setItem('checkAllMembers' + <?=$meeting_id?>, '1');
                        }else{
                            localStorage.setItem('checkAllMembers' + <?=$meeting_id?>, '0');
                        }
                })
            });
            $("#btn_export").on("click", function(e){
                e.preventDefault();
                $("#export_frame").attr("src", "/modules/exportExcel.php?id=<?=$meeting_id?>");
            })
            <?php
            }
            ?>
        </script>
<?php
    /*}else{
        echo 'Пользователи с заданными параметрами не найдены.';
    }*/
}
?>