<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';

if (el_checkAjax()) {
    $err = 0;
    $errStr = array();
    $answer = array();

    foreach ($_POST as $name => $value) {
        if (substr_count($name, 'score') > 0) {
            $id = intval(str_replace('score', '', $name));
            $value = intval($value);
            el_dbselect("UPDATE catalog_scores_data SET field3=" . intval($value) . " WHERE id=$id",
                0, $res, 'result', true);
        }
    }

    $scores = el_dbselect("SELECT * FROM catalog_scores_data WHERE field2 = 'За действия' 
                                AND active = 1", 0, $scores, 'result', true);
    $rs = el_dbfetch($scores);

    $answer['resultText'] = '<h2>Баллы</h2>
                            <div class="group">
                            <h3>За действия</h3>';

    do {
        $answer['resultText'] .= '<div class="item">
            <div class="el_data input_number">
                <label for="metka' . $rs['id'] . '">' . $rs['field1'] . '</label>
                <div class="number-minus button icon" onclick="this.nextElementSibling.stepDown(); 
                this.nextElementSibling.onchange();"><span class="material-icons">keyboard_arrow_down</span></div>
                <input class="el_input" id="metka' . $rs['id'] . '" name="score' . $rs['id'] . '" value="' . $rs['field3'] . '" type="number" min="0">
                <div class="number-plus button icon" onclick="this.previousElementSibling.stepUp(); 
                this.previousElementSibling.onchange();"><span class="material-icons">keyboard_arrow_up</span></div>
            </div>
        </div>' . "\n";
    } while ($rs = el_dbfetch($scores));

    $answer['resultText'] .= '<h3>За взносы</h3>';

    $scores = el_dbselect("SELECT * FROM catalog_scores_data WHERE field2 = 'За взносы' 
                                AND active = 1", 0, $scores, 'result', true);
    $rs = el_dbfetch($scores);
    $scArr = array();
    do {
        $answer['resultText'] .= '<div class="item">
                <div class="el_data input_number">
                    <label for="metka' . $rs['id'] . '">' . $rs['field1'] . '</label>
                    <div class="number-minus button icon" onclick="this.nextElementSibling.stepDown(); 
                    this.nextElementSibling.onchange();"><span class="material-icons">keyboard_arrow_down</span></div>
                    <input class="el_input" id="metka' . $rs['id'] . '" name="score' . $rs['id'] . '" value="' . $rs['field3'] . '" type="number" min="0">
                    <div class="number-plus button icon" onclick="this.previousElementSibling.stepUp(); 
                    this.previousElementSibling.onchange();"><span class="material-icons">keyboard_arrow_up</span></div>
                </div>
            </div>' . "\n";
    } while ($rs = el_dbfetch($scores));

    $answer['resultText'] .= '</div>
                            <button class="button icon text"><span class="material-icons">save</span>Сохранить</button>';

    $answer['result'] = true;
    $answer['answerTarget'] = '#setScores';

    echo json_encode($answer);
}
?>