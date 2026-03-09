<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Connections/dbconn.php';

if (el_checkAjax()) {
    $reg = '';
    $where = '';

    if (is_array($_POST['subject'])) {
        $subject = implode(',', $_POST['subject']);
        $where = ' field2 IN (' . $subject . ')';
    } else {
        $subject = intval($_POST['subject']);
        $where = ' field2 = ' . $subject;
    }

    if ($subject > 0) {

        $reg = el_dbselect("SELECT id, field1 FROM catalog_regions_data WHERE $where AND active = 1 ORDER BY field1",
            0, $reg, 'result', true, true);
        if (el_dbnumrows($reg) > 0) {
            $r = el_dbfetch($reg);
            $regions = ['<option value="">Все</option>'];
            $sel = '';
            do {
                if(isset($_POST['values']) && count($_POST['values']) > 0){
                    $sel = in_array($r['id'], $_POST['values']) ? ' selected' : '';
                }
                $regions[] = '<option value="' . $r['id'] . '"'.$sel.'>' . $r['field1'] . '</option>' . "\n";
            } while ($r = el_dbfetch($reg));

            echo implode('', $regions);
        }
    }
}
?>