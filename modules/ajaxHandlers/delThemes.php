<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';
if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $answer = array();

    $del_theme = el_dbselect("DELETE FROM catalog_registryVote_data WHERE id=" . intval($_POST['id']),
        0, $del_theme, 'result', true);

    $scores = el_dbselect("SELECT * FROM catalog_registryVote_data WHERE active = 1 ORDER BY field1",
        0, $scores, 'result', true);
    $rs = el_dbfetch($scores);

    $answer['resultText'] = '<h2>Темы для голосования</h2>
                            <div class="group"><h3>Список</h3>';

    do {
        $answer['resultText'] .= '
            <div class="item">
                <div class="el_data">
                    <label for="metka_t' . $rs['id'] . '">Название</label>
                    <input class="el_input" id="metka_t' . $rs['id'] . '" type="text" value="' . $rs['field1'] . '">
                    <div class="button icon add delButton" data-id="' . $rs['id'] . '">
                    <span class="material-icons">remove_circle_outline</span></div>
                </div>
            </div>' . "\n";
    } while ($rs = el_dbfetch($scores));

    $answer['resultText'] .= '<div class="item">
                                    <div class="el_data">
                                        <label for="metka_newtype">Название</label>
                                        <input class="el_input" id="metka_newtype" name="new_theme" type="text" value="">
                                        <button class="button icon add"><span class="material-icons">add_circle_outline</span></button>
                                    </div>
                                </div>
                            </div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>';

    $answer['result'] = true;
    $answer['answerTarget'] = '#setThemes';

    echo json_encode($answer);
}
?>