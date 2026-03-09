<?php


namespace DB;


class DataBase
{
    private $dbconn;

    public function __construct ( $dbconn )
    {
        $this->dbconn;
    }

    public function query ( $query, $limit, $mode = 'result', $debug = false, $showQuery = false, $pageStart = 0 )
    {
        $maxRows_result = $limit;
        $pageNum_result = 0;
        if (intval($pageStart) > 0) {
            $pageNum_result = $pageStart;
        }
        $startRow_result = $pageNum_result * $maxRows_result;
        $query_limit_result = sprintf("%s LIMIT %d, %d", $query, $startRow_result, $maxRows_result);
        $result_query = ($limit > 0) ? $query_limit_result : $query;
        if ($showQuery) echo '<pre>' . $result_query . '</pre><br>';
        $result = mysqli_query($this->dbconn, $result_query);

        if ($result != FALSE) {
            if ($mode == 'result') {
                return $result;
            } elseif ($mode == 'row') {
                return $row_result = $this->fetch($result);
            }
        } else {
            if ($debug == true) el_dberror($result, $query);
            return FALSE;
        }
        return true;
    }

    public function prepare($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

        switch ($theType) {
            case "text":
            case "blob":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
                break;
            case "date":
            case "datetime":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }

    public function fetch ( $result )
    {
        return mysqli_fetch_assoc($result);
    }

    public function lastId ( )
    {
        return mysqli_insert_id($this->dbconn);
    }

    public function numrows ( $result )
    {
        return mysqli_num_rows($result);
    }

    public function seek($result, $position)
    {
        $out = mysqli_data_seek($result, $position);
        if (!$out) echo $this->error();
    }

    public function error($query = '')
    {
        $errStr =  '<pre>';
        $errStr .= mysqli_errno($this->dbconn) . ": " . mysqli_error($this->dbconn);
        $errStr .= (strlen($query) > 0) ? ' in query: ' . $query : '';
        $errStr .= '</pre>';
        return $errStr;
    }

    public function printRows ( $resultout, $template, $emptymess )
    {
        if ($this->numrows($resultout) > 0) {
            $result_row = $this->fetch($resultout);
            do {
                if(is_file($_SERVER['DOCUMENT_ROOT'] . $template)) {
                    include $_SERVER['DOCUMENT_ROOT'] . $template;
                }else{
                    echo $template;
                }
            } while ($result_row = $this->fetch($resultout));
        } else {
            echo $emptymess;
        }
    }
}