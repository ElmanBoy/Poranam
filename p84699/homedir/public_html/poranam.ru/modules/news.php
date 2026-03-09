<?php
$database_dbconn = el_getvar('database_dbconn');
$dbconn = el_getvar('dbconn');
$path = el_getvar('path');
$editFormAction = $_SERVER['REQUEST_URI'];
if (isset($_SERVER['QUERY_STRING'])) {
    $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_dbnewsall = "1";
if (isset($_GET['id'])) {
    $colname_dbnewsall = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
;
$query_dbnewsall = sprintf("SELECT * FROM news WHERE id = %s ORDER BY special DESC, year DESC, mont DESC, day DESC", $colname_dbnewsall);
$dbnewsall = @mysqli_query($dbconn);/* or die(mysqli_error()(), $query_dbnewsall);*/
$row_dbnewsall = el_dbfetch($dbnewsall);
$totalRows_dbnewsall = mysqli_num_rows($dbnewsall);

if (isset($_GET['id'])) { ?>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" class="normal_table">
        <tr>
            <td valign="top">
                <div class="news">
                    <h4><?php echo $row_dbnewsall['titleinside']; ?></h4>
                    <b><?php if ($row_dbnewsall['day'] < 10) {
                            echo '0' . $row_dbnewsall['day'];
                        } else {
                            echo $row_dbnewsall['day'];
                        } ?>.<?php if ($row_dbnewsall['mont'] < 10) {
                            echo '0' . $row_dbnewsall['mont'];
                        } else {
                            echo $row_dbnewsall['mont'];
                        } ?>
                        .<?php echo $row_dbnewsall['year']; ?> <?php echo $row_dbnewsall['city']; ?></b>
                    <p>
                        <? if (strlen($row_dbnewsall['bimage']) > 0) { ?>
                            <img src="<?php echo $row_dbnewsall['bimage']; ?>" alt="<?= $row_dbnews['title'] ?>" align="left" hspace="10" vspace="5"
                                 border="0"/>
                        <? } ?>
                        <?php echo $row_dbnewsall['text']; ?></p>
                </div>
            </td>
        </tr>
        <tr>
            <td><br><br><a href="<?= $path ?>/" class="style33"><b>� ������ ��������</b></a></td>
        </tr>
        <tr>
            <td><br>
                <p></p>
                <? //���� ���������� ���������
                if ($row_dbnewsall['comments'] == "Y") {
                    include $_SERVER['DOCUMENT_ROOT'] . '/modules/comments.php';
                } ?>
                <p></td>
        </tr>
    </table>
<? } ?>
<? //����� ������ ��������
if (!isset($_GET['id'])) {

    $maxRows_dbnews = 10;
    $pn = 0;
    if (isset($_GET['pn'])) {
        $pn = $_GET['pn'];
    }
    $startRow_dbnews = $pn * $maxRows_dbnews;
    if (isset($_GET['year'])) {
        if (strlen($_GET['day']) > 0) {
            $qw .= " AND day='" . $_GET['day'] . "' AND mont='" . $_GET['month'] . "' AND year='" . $_GET['year'] . "' ";
        } else {
            $qw .= " AND mont='" . $_GET['month'] . "' AND year='" . $_GET['year'] . "' ";
        }
    }
    ;
    $query_dbnews = "SELECT * FROM news WHERE cat='" . $row_dbcontent['cat'] . "' " . $qw . "ORDER BY special DESC, year DESC, mont DESC, day DESC";
    $query_limit_dbnews = sprintf("%s LIMIT %d, %d", $query_dbnews, $startRow_dbnews, $maxRows_dbnews);
    $dbnews = el_dbselect($query_limit_dbnews, 0, $dbnews, 'result', true);
    $row_dbnews = el_dbfetch($dbnews);
    $newscount = mysqli_num_rows($dbnews);

    if (isset($_GET['tr'])) {
        $tr = $_GET['tr'];
    } else {
        $all_dbnews = mysql_query($query_dbnews);
        $tr = mysqli_num_rows($all_dbnews);
    }
    $totalPages_dbnews = ceil($tr / $maxRows_dbnews) - 1;

    ?>
    <table border="0" width="80%" align="center">
        <tr>
            <td width="20%" align="center">
                <?php if ($pn > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, 0, $queryString_dbnews); ?>">� ������ </a>
                <?php } // Show if not first page ?>
            </td>
            <td width="20%" align="center">
                <?php if ($pn > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, max(0, $pn - 1), $queryString_dbnews); ?>">�����</a>
                <?php } // Show if not first page ?>
            </td>
            <td width="20%" align="center"><? $page = 1;
                $pagen = 0;
                $countpage = $tr / $maxRows_dbnews;
                if ($countpage > 1) {
                    do {
                        if ($pn != $pagen) {
                            echo "<a href=?pn=" . $pagen . "&tr=" . $tr . ">" . $page . "</a>&nbsp;&nbsp;";
                        } else {
                            echo "<b>" . $page . "</b>&nbsp;&nbsp;";
                        }
                        $page++;
                        $pagen++;
                        $countpage--;
                    } while ($countpage >= 0);
                }
                ?></td>
            <td width="20%" align="center">
                <?php if ($pn < $totalPages_dbnews) { // Show if not last page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, min($totalPages_dbnews, $pn + 1), $queryString_dbnews); ?>">������</a>
                <?php } // Show if not last page ?>
            </td>
            <td width="20%" align="center">
                <?php if ($pn < $totalPages_dbnews) { // Show if not last page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, $totalPages_dbnews, $queryString_dbnews); ?>">� ����� </a>
                <?php } // Show if not last page ?>
            </td>
        </tr>
    </table>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top">
                <?php
                if ($newscount > 0) {
                    do {
                        if (strlen($row_dbnews['title']) > 0) {
                            $id = $row_dbnews['id'];
                            ;
                            $query_comments = "SELECT * FROM news_comments WHERE news_id = '$id'";
                            $comments = el_dbselect($query_comments, 0, $comments, 'result', true);
                            $totalRows_comments = mysqli_num_rows($comments);
                            ?>

                            <table width="100%" border="0" cellpadding="0" cellspacing="7" class="normal_tbl" id="news_block">
                                <tr>
                                    <td width="1" rowspan="2" align="right" valign="top">
                                        <? if (strlen($row_dbnews['simage']) > 0) { ?><img src="<?php echo $row_dbnews['simage']; ?>"
                                                                                           alt="<?= $row_dbnews['title'] ?>" width="116" hspace="10" vspace="5"
                                                                                           border=0 align="left"/> <? } ?></td>
                                    <td valign="top" width="100%">
                                        <div class="news">
                                            <strong><?php echo $row_dbnews['title']; ?></strong>
                                            <br/>
                                            <strong><?php if ($row_dbnews['day'] < 10) {
                                                    echo '0' . $row_dbnews['day'];
                                                } else {
                                                    echo $row_dbnews['day'];
                                                } ?>.<?php if ($row_dbnews['mont'] < 10) {
                                                    echo '0' . $row_dbnews['mont'];
                                                } else {
                                                    echo $row_dbnews['mont'];
                                                } ?>.<?php echo $row_dbnews['year']; ?>  <?php echo $row_dbnews['city']; ?></strong>
                                            <p><?php echo $row_dbnews['anons']; ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top"><? /*?><a href="/news/?id=<?=$row_dbnews['id']?>#addcomm">
                <?=($totalRows_comments>0) ? "������������: $totalRows_comments" : "��������������"; ?></a> | </b><? */
                                        ?>
                                        <a href="<?= $path ?>/?id=<?php echo $row_dbnews['id']; ?>" class="style33"><strong>��������� &raquo;</strong></a></td>
                                </tr>
                            </table>
                        <? } ?>
                        <br>
                        <?php mysqli_free_result($comments);
                    } while ($row_dbnews = el_dbfetch($dbnews));
                } else {
                    echo "<h4 align=center>�������� �� ��������� ������ ���.</h4>";
                }
                ?>

            </td><? /*
<td valign="top"><? el_calendar('news') ?></td>
*/ ?>
        </tr>
    </table>

    <table border="0" width="80%" align="center">
        <tr>
            <td width="20%" align="center"><?php if ($pn > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, 0, $queryString_dbnews); ?>">� ������ </a>
                <?php } // Show if not first page ?>
            </td>
            <td width="20%" align="center"><?php if ($pn > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, max(0, $pn - 1), $queryString_dbnews); ?>">�����</a>
                <?php } // Show if not first page ?>
            </td>
            <td width="20%" align="center"><? $page = 1;
                $pagen = 0;
                $countpage = $tr / $maxRows_dbnews;
                if ($countpage > 1) {
                    do {
                        if ($pn != $pagen) {
                            echo "<a href=?pn=" . $pagen . "&tr=" . $tr . ">" . $page . "</a>&nbsp;&nbsp;";
                        } else {
                            echo "<b>" . $page . "</b>&nbsp;&nbsp;";
                        }
                        $page++;
                        $pagen++;
                        $countpage--;
                    } while ($countpage >= 0);
                }
                ?></td>
            <td width="20%" align="center"><?php if ($pn < $totalPages_dbnews) { // Show if not last page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, min($totalPages_dbnews, $pn + 1), $queryString_dbnews); ?>">������</a>
                <?php } // Show if not last page ?>
            </td>
            <td width="20%" align="center"><?php if ($pn < $totalPages_dbnews) { // Show if not last page ?>
                    <a href="<?php printf("%s?pn=%d%s", $currentPage, $totalPages_dbnews, $queryString_dbnews); ?>">� ����� </a>
                <?php } // Show if not last page ?>
            </td>
        </tr>
    </table>

    <? mysqli_free_result($dbnews);
} ?>

