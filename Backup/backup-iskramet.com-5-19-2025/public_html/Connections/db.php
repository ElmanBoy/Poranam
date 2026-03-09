<?

/*include_once $_SERVER['DOCUMENT_ROOT'].'/Connections/rb.php';
R::setup('mysql:host='.$hostname_dbconn.';dbname='.$database_dbconn, $username_dbconn, $password_dbconn);*/

function el_dbconnect ()
{
    global $hostname_dbconn, $username_dbconn, $password_dbconn, $database_dbconn;
    if (DB_TYPE == 'mysql') {
        $dbconn = mysqli_connect($hostname_dbconn, $username_dbconn, $password_dbconn, $database_dbconn);
        if (!$dbconn) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
    } elseif (DB_TYPE == 'oracle') {
        putenv("ORACLE_HOME=$home_dbconn");
        putenv("NLS_LANG=AMERICAN_AMERICA.CL8MSWIN1251");
        $dbconn = oci_pconnect($username_dbconn, $password_dbconn, $database_dbconn);
    }
    if (!$dbconn) {
        el_dberror($dbconn);
    } else {
        return $dbconn;
    }
}

function el_database ()
{
    global $database_dbconn;
    return $database_dbconn;
}

//prepare vars before inserting in db
function GetSQLValueString ( $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "" )
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
        case "time":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "defined":
            $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
    }
    return $theValue;
}

// add slashes for vars
function quote_smart ( $value )
{
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if (!is_numeric($value)) {
        $value = "'" . addslashes($value) . "'";
    }
    return $value;
}

//Execute any query
function el_sql ( $query, $result )
{
    global $database_dbconn, $dbconn;
    $query_result = "$query";
    $result = el_dbselect($query_result, 0, $result, 'result', true);
    $row_result = el_dbfetch($result);
    return $row_result;
}


//insert data in db
function el_dbinsert ( $table, $insertvars, $returnType = 'bool' )
{
    global $database_dbconn, $dbconn, $_POST;
    $insers = "";
    $inserv = "";
    $insertfield1 = "";

    $mysql_data_type_hash = array(
        1 => 'tinyint',
        2 => 'smallint',
        3 => 'int',
        4 => 'float',
        5 => 'double',
        7 => 'timestamp',
        8 => 'bigint',
        9 => 'mediumint',
        10 => 'date',
        11 => 'time',
        12 => 'datetime',
        13 => 'year',
        16 => 'bit',
        //252 is currently mapped to all text and blob types (MySQL 5.0.51a)
        253 => 'varchar',
        254 => 'char',
        246 => 'decimal'
    );

    $result0 = el_dbselect("SELECT * FROM " . $table, 0, $result0, 'result', true);
    $fields = mysqli_num_fields($result0);
    $c = 0;
    $err = 0;
    while (list($insertfield, $insertvar) = each($insertvars)) {
        if (substr_count($insertfield, ' *') > 0 && strlen($insertvar) < 1) {
            $err++;
        }
        $insertfield = str_replace(' *', '', $insertfield);
        if ($err == 0) {
            $c++;
            if ($c == count($insertvars)) {
                $end = '';
            } else {
                $end = ', ';
            }
            $num = 0.5;
            for ($i = 0; $i < $fields; $i++) {
                $finfo = mysqli_fetch_field_direct($result0, $i);
                if ($insertfield == $finfo->name) {
                    $num = $i;
                }

            }
            if ($num == 0.5) {
                echo '<div style="color:red">Поле ' . $finfo->name . ' в таблице ' . $table . ' не найдено.</div>';
            }
            $finfo = mysqli_fetch_field_direct($result0, $num);
            $type = ($finfo->type == 252) ? 'text' : $mysql_data_type_hash[$finfo->type];
            $inserv .= GetSQLValueString($insertvar, $type) . $end;
            $insertfield1 .= $insertfield . $end;
        } else {
            echo '<div style="color:red">Заполните поле ' . str_replace(' *', '', $insertfield) . '</div>';
        }
    }

    $insertSQL = "INSERT INTO " . $table . " (" . $insertfield1 . ") VALUES (" . $inserv . ")";
    $res = mysqli_query($dbconn, $insertSQL);
    if ($res != false) {
        return ($returnType == 'bool') ? true : $res;
    } else {
        echo mysqli_error($dbconn);
        return false;
    }
}

function el_dberror ( $conn, $query )
{
    //var_dump($err);
    global $dbconn;
    if (DB_TYPE == 'mysql') {
        echo '<pre>';
        print mysqli_errno($dbconn) . ": " . mysqli_sqlstate($dbconn).' '.mysqli_error($dbconn);
        echo ' in query: ' . $query . '</pre>';
    } elseif (DB_TYPE == 'oracle') {
        $err = oci_error($conn);
        if (strlen($err['message']) > 0 || strlen($err['code']) > 0) {
            print "Error code = " . $err['code'];
            print "<br>Error message = " . htmlentities($err['message']);
            print "<br>Error position = " . $err['offset'];
            print "<br>SQL Statement = " . htmlentities($err['sqltext']);
        }
    }
}

function el_dbseek ( $result, $position )
{
    global $dbconn; //error_reporting(E_WARNING);
    if (DB_TYPE == 'mysql') {
        $out = mysqli_data_seek($result, $position);
    } elseif (DB_TYPE == 'oracle') {
        oci_fetch($result);
        $lob = oci_new_descriptor($dbconn);//OCIResult($result, 1);
        $out = $lob->seek($position, OCI_SEEK_SET);
    } //echo $out;
    if (!$out) el_dberror($result);
}

function el_dbinsertid ()
{
    //
}

function el_dbcommit ()
{
    global $dbconn;
    if (DB_TYPE == 'mysql') {
        //
    } elseif (DB_TYPE == 'oracle') {
        $committed = oci_commit($dbconn);
        if (!$committed) el_dberror($dbconn);
    }
}

function el_dblongtext ( $result, $fieldSet, $fieldName )
{
    global $dbconn;
    $fieldName = strtoupper($fieldName);
    if (DB_TYPE == 'mysql') {
        return $fieldSet[$fieldName];
    } elseif (DB_TYPE == 'oracle') {
        $column_type = oci_field_type($result, $fieldName);
        if ($column_type == 'CLOB' && $fieldSet[$fieldName] != NULL) {
            $lob = oci_result($result, $fieldName);//oci_new_descriptor($dbconn);
            return $lob->load();
        } else {
            return $fieldSet[$fieldName];
        }
    }
}

//select from db
function el_dbselect ( $query, $limit, $resultout, $mode = 'result', $debug = false, $showQuery = false, $noPn = false )
{
    global $database_dbconn, $dbconn, $_GET;
    $maxRows_result = $limit;
    $pageNum_result = 0;
    if (isset($_GET['pn']) && !$noPn) {
        $pageNum_result = $_GET['pn'];
    }
    $startRow_result = $pageNum_result * $maxRows_result;
    $query_limit_result = sprintf("%s LIMIT %d, %d", $query, $startRow_result, $maxRows_result);
    $result_query = ($limit > 0) ? $query_limit_result : $query;
    if (DB_TYPE == 'mysql') {
        if ($showQuery) echo '<pre>' . $result_query . '</pre><br>';
        $result = mysqli_query($dbconn, $result_query);

    } elseif (DB_TYPE == 'oracle') {
        $stmt = oci_parse($dbconn, $result_query);
        oci_execute($stmt);
        $result = $stmt;
    }
    if ($result != FALSE) {
        if ($mode == 'result') {
            return $result;
        } elseif ($mode == 'row') {
            return $row_result = el_dbfetch($result);
        }
    } else {
        if ($debug == true) el_dberror($result, $query);
        return FALSE;
    }
}

function el_dbfetch ( $result, $type = 'ASSOC', $debug = false )
{
    global $dbconn;
    $errStr = '';
    if (DB_TYPE == 'mysql') {
        switch ($type) {
            case 'BOTH':
            case 'NUM':
                $out = mysqli_fetch_array($result);
                break;
            case 'ASSOC':
            case 'LOBS':
            default:
                $out = mysqli_fetch_assoc($result);
                break;
        }
    } elseif (DB_TYPE == 'oracle') {
        switch ($type) {
            case 'BOTH':
                $out = oci_fetch_array($result, OCI_BOTH);
                break;
            case 'NUM':
                $out = oci_fetch_array($result, OCI_NUM);
                break;
            case 'LOBS':
                $out = oci_fetch_array($result, OCI_RETURN_LOBS);
                break;
            case 'ASSOC':
            default:
                $out = oci_fetch_array($result, OCI_ASSOC);
                break;
        }
    }
    if ($out != FALSE) {
        return $out;
    } else {
        if ($debug == true) el_dberror($result, '');
        return FALSE;
    }
}

function el_dbnumrows ( $result )
{
    if (DB_TYPE == 'mysql') {
        return @mysqli_num_rows($result);
    } elseif (DB_TYPE == 'oracle') {
        //$rows=el_dbfetch($result);
        return oci_num_rows($result);
    }
}

function el_dbresultfree ( $result )
{
    if (DB_TYPE == 'mysql') {
        mysqli_free_result($result);
    } elseif (DB_TYPE == 'oracle') {
        oci_free_statement($result);
    }
}

//Parsing dump for db
function el_mysqldump ( $url, $ignoreerrors = false )
{
    $file_content = file($url);
    //print_r($file_content);
    $query = "";
    foreach ($file_content as $sql_line) {
        $tsl = trim($sql_line);
        if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
            $query .= $sql_line;
            if (preg_match("/;\s*$/", $sql_line)) {
                $query = str_replace(";", "", "$query");
                $result = mysql_query($query);
                if (!$result && !$ignoreerrors) die(mysqli_error()());
                $query = "";
            }
        }
    }
}


//paging navigation
function el_dbpagecount ( $result, $pageurl, $maxRows_result, $totalRows_result, $template )
{
    global $_GET, $_SERVER;

    $pn = 0;
    if (isset($_GET['pn'])) {
        $pn = $_GET['pn'];
    }

    if (isset($_GET['tr'])) {
        $tr = $_GET['tr'];
    } else {
        $tr = $totalRows_result;
    }

    if ($tr > 0) {
        $totalPages_result = ceil($tr / $maxRows_result) - 1;

        $queryString_result = "";
        if (!empty($_SERVER['QUERY_STRING'])) {
            $params = explode("&", $_SERVER['QUERY_STRING']);
            $newParams = array();
            foreach ($params as $param) {
                if (stristr($param, "pn") == false && stristr($param, "tr") == false) {
                    array_push($newParams, $param);
                }
            }
            if (count($newParams) != 0) {
                $queryString_result = "&" . htmlentities(implode("&", $newParams));
            }
        }
        $queryString_result = sprintf("&tr=%d%s", $tr, $queryString_result);


        if (isset($pageurl)) {
            $pageurl = "&" . $pageurl;
        } else {
            $pageurl = "";
        }
        if ($tr > 0) {
            include $_SERVER['DOCUMENT_ROOT'] . $template;
        }
    }
}

//output data in cicle on page from db
function el_dbrowprint ( $resultout, $template, $emptymess )
{
    if (mysqli_num_rows($resultout) > 0) {
        $result_row = el_dbfetch($resultout);
        do {
            include $_SERVER['DOCUMENT_ROOT'] . $template;
        } while ($result_row = el_dbfetch($resultout));
    } else {
        echo $emptymess;
    }
}

//Recreate index(FULLTEXT) for specified table
function el_reindex ( $table )
{
    if (substr_count($table, 'catalog_') > 0) {
        $catalog_id = preg_replace('/catalog_(.*)_data/', '$1', $table);
        $showIndex = el_dbselect("SELECT field FROM catalog_prop WHERE search=1 
							   AND (type='text' OR type='textarea') AND catalog_id='" . $catalog_id . "'", 0, $showIndex);
        $tf = el_dbfetch($showIndex);
        $fields = array();
        $keyNames = array();
        do {
            $fields[] = 'field' . $tf['field'];
        } while ($tf = el_dbfetch($showIndex));
        echo $fList = implode(',', $fields);
    } else {
        $showIndex = el_dbselect("SHOW COLUMNS FROM " . $table, 0, $showIndex);
        $tf = el_dbfetch($showIndex);
        $fields = array();
        $keyNames = array();
        do {
            if ($tf['Type'] == 'text' || $tf['Type'] == 'longtext') {
                $fields[] = $tf['Field'];
            }
        } while ($tf = el_dbfetch($showIndex));
        $fList = implode(',', $fields);
    }

    $textFields = el_dbselect("SHOW INDEX FROM " . $table, 0, $textFields);
    $in = el_dbfetch($textFields);
    do {
        if ($in['Index_type'] == 'FULLTEXT') {
            $dropIndex = $in['Key_name'];
        }
    } while ($in = el_dbfetch($textFields));
    el_dbselect("ALTER TABLE " . $table . " DROP INDEX `" . $dropIndex . "`", 0, $res);
    el_dbselect("ALTER TABLE " . $table . " ADD FULLTEXT " . $in['Key_name'] . " (" . $fList . ")", 0, $res);
    el_dbselect("OPTIMIZE TABLE " . $table, 0, $res);
}

?>