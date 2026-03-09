<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
    //print_r($_POST);
    $voting = null;
    $vote = null;
    $members = null;
    $users = null;
    $answers = null;
    $allUsers = [];
    $vote_id = intval($_POST['params']);

    //Информация о результатах голосования
    $voting = el_dbselect("SELECT * FROM `catalog_initresult_data` where field2 = $vote_id", 0, $voting, 'result', true);
    $vt = el_dbnumrows($voting);
    if($vt > 0){
        $rv = el_dbfetch($voting);

        //Информация о голосовании
        $vote = el_dbselect("SELECT field1 FROM catalog_init_data WHERE id = $vote_id", 0, $vote, 'row', true);

        //Справочник ответов
        $answers = el_dbselect("SELECT id, field1 FROM catalog_votesQuestions_data WHERE field2 = $vote_id", 0, $answers, 'result', true);
        $ra = el_dbfetch($answers);
        $ansArr = [];
        do{
            $ansArr[$ra['id']] = $ra['field1'];
        }while($ra = el_dbfetch($answers));

        //Справочник пользователей
        $members = el_dbselect("SELECT id, user_id FROM catalog_users_data WHERE active = 1", 0, $members, 'result', true);
        $rm = el_dbfetch($members);
        $usersArr = [];
        do{
            $usersArr[$rm['id']] = $rm['user_id'];
        }while($rm = el_dbfetch($members));
?>

<div class="pop_up">
    <div class="title">
        <h2>Результат голосования</h2>
        <div class="close" onclick="pop_up_meeting_list_close(); return false"><span class="material-icons">highlight_off</span></div>
    </div>
    <section>
        <form>
            <h3><?=$vote['field1']?></h3>
            <div class="group">
                <div>

                    <table class="table_data display" id="meeting_members" style="width:100%" data-page-length='15'>
                        <thead>
                        <tr>
                            <th>ID пользователя</th>
                            <th>Ответ</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- row -->
                        <?
                        do{
                        ?>
                            <tr>
                                <td><?=$usersArr[$rv['field1']]?></td>
                                <td><?=$ansArr[$rv['field4']]?></td>
                            </tr>
                               <?
                        }while($rv = el_dbfetch($voting));

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
        <?php
    }else{
        echo 'В этом голосовании ещё никто не проголосовал.';
    }

}
?>