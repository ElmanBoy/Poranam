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
            0, $reg, 'result', true);
        if (el_dbnumrows($reg) > 0) {
            $r = el_dbfetch($reg);
            $regions = array();
            do {
                $regions[] = '<option value="' . $r['id'] . '">' . $r['field1'] . '</option>' . "\n";
            } while ($r = el_dbfetch($reg));

            echo implode('', $regions);
        }
    }
}
?>