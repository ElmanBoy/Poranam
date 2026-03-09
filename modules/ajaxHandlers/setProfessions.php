<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $answer = array();

    $new_theme = array('active' => 1, 'cat' => 408, 'field1' => $_POST['new_profession']);
    el_dbinsert("catalog_proffesions_data", $new_theme);

    $scores = el_dbselect("SELECT * FROM catalog_proffesions_data WHERE active = 1 ORDER BY field1",
        0, $scores, 'result', true);
    $rs = el_dbfetch($scores);

    $answer['resultText'] = '<h2>Профессии</h2>
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
    $answer['answerTarget'] = '#setProfessions';

    echo json_encode($answer);
}
?>