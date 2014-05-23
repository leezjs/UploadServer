<?php

// defined for the parameter type in SQL statement
class SQLTypes {

    const INTEGER = 1;
    const STRING = 2;
    const FLOAT = 3;
    const DOUBLE = 4;
    const NATIVE = 5;

}

class PreparedStatement {

    private $sql;

    public function __construct($sql_) {
        $this->sql = $sql_;
    }

    public function GetPreparedSQL() {
        return $this->sql;
    }

    public function AddParameter($name, $type, $value) {
        switch ($type) {
            case SQLTypes::INTEGER:
                $value = intval($value);
                break;
            case SQLTypes::STRING:
                $value = "'" . mysql_real_escape_string($value) . "'";
                break;
            case SQLTypes::FLOAT:
                $value = floatval($value);
                break;
            case SQLTypes::DOUBLE:
                $value = doubleval($value);
                break;
            case SQLTypes::NATIVE:
                break;
            default:
                break;
        }

        $this->sql = preg_replace("/:$name/", $value, $this->sql);
    }

}

class DBException extends Exception {

    public function __construct($message, $code = 0, Exception $previous = null) {

        parent::__construct($message, $code);
    }

}

class DB {

    private $MYSQL_ERR_SELECT_DB = 101;
    private $MYSQL_ERR_CONNECT = 102;
    private $MYSQL_SUCCESS = 100;
    private $connection = null;
    private $schema = null;
    
    private $assoc = true;
    
    public static function GetInstance( $dbType = 0 ){
        if( $dbType == 0 ){
            static $instance = null;
            if( $instance === null ){
                $instance = new DB();
            }
            return $instance;
        }
        else if($dbType == 1){
            static $crawlerInstance = null;
            if( $crawlerInstance === null ){
                $crawlerInstance = new DB( CRAWLER_DB_SCHEMA, CRAWLER_DB_USERNAME, CRAWLER_DB_PASSWORD, CRAWLER_DB_HOST, CRAWLER_DB_PORT );
            }
            return $crawlerInstance;
            
        }
    }
    
    private function __construct( $db=DB_SCHEMA, $username=DB_USERNAME, $password=DB_PASSWORD, $host=DB_HOST, $port=DB_PORT ) {

        $re = $this->Connect( $db, $username, $password, $host, $port );

        if ($re != $this->MYSQL_SUCCESS) {
            echo 'Connection failed! ' . $this->GetErrMsg();
            die();
        }

        mysql_query("set names utf8");
    }

    function Connect($dbschema = '', $dbuser = '', $dbpass = '', $dbhost = 'localhost', $dbport = '3306') {

        $this->connection = mysql_connect($dbhost . ':' . $dbport, $dbuser, $dbpass, true);

        if ($this->connection != false) {

            if ($this->setCurrentSchema($dbschema) == $this->MYSQL_SUCCESS)
                return $this->MYSQL_SUCCESS;
            else {
                echo "select DB failed!  : " . $this->GetErrMsg();
                return $this->MYSQL_ERR_SELECT_DB;
            }
        } else
            return $this->MYSQL_ERR_CONNECT;
    }

    function SetCurrentSchema($schema) {

        $this->schema = $schema;
        if (mysql_select_db($this->schema, $this->connection)) {
            return $this->MYSQL_SUCCESS;
        } else {
            return $this->MYSQL_ERR_SELECT_DB;
        }
    }

    function GetCurrentSchema() {
        return $this->schema;
    }

    /**
     *
     * @param string $sql
     * @return string when query succeed, false when query failed
     */
    function GetValue($sql, $nameSpace = NULL, $key = NULL) {
        $this->assoc = false;
        $result = $this->Query($sql, $nameSpace, $key);

        if (count($result) == 0)
            return false;

        $this->assoc = true;
        return $result[0][0];
    }

    /**
     *
     * @param string $sql
     * @return array when query succeed, false when query failed
     */
    function GetRow($sql, $nameSpace = NULL, $key = NULL) {

        $result = $this->Query($sql, $nameSpace, $key);

        if (count($result) == 0)
            return false;

        return $result[0];
    }

    /**
     *
     * @param string $sql
     * @return array when query succeed, false when query failed
     */
    function GetCol($sql, $nameSpace = NULL, $key = NULL) {

        $result = $this->Query($sql, $nameSpace, $key);

        if (count($result) == 0)
            return false;

        $data = array();

        foreach ($result as $key => $value)
            $data[] = $value[0];

        return $data;
    }

    /**
     *
     * @param string $sql
     * @return array when query succeed, false when query failed
     */
    function GetPlan($sql, $nameSpace = NULL, $key = NULL) {

        $result = $this->Query($sql, $nameSpace, $key);
        if ($result == false || count($result) == 0)
            return array();
        return $result;
    }

    function Update($sql) {

        mysql_query($sql, $this->connection);

        if ($this->HasError() == true) {
            echo "Update: $sql : " . $this->GetErrMsg();
            throw new DBException("fail in update: $sql");
        }

        return true;
    }

    function Insert($sql) {

        mysql_query($sql, $this->connection);
        if ($this->HasError() == true) {
            //echo "Insert: $sql : " . $this->GetErrMsg() ;
            //throw new DBException("fail in insert: $sql");
            return false;
        }

        return $this->LastID();
    }

    function LastID() {
        return mysql_insert_id($this->connection);
    }

    function CountResultRows($rs) {
        return mysql_num_rows($rs);
    }

    function CountAffectedRows() {
        return mysql_affected_rows($this->connection);
    }

    function Disconnect() {
        return mysql_close($this->connection);
    }

    function Query($sql, $nameSpace = NULL, $key = NULL) {

        if ($sql instanceof PreparedStatement)
            $sql = $sql->GetPreparedSQL();

        $result = mysql_query($sql, $this->connection);


        if ($this->HasError() == true) {
            echo "Query: $sql : " . $this->GetErrMsg();
            throw new DBException("fail in query: $sql");
            return false;
        }

        if (!$result)
            return false;

        $data = array();

        while (($row = $this->fa($result)) != false)
            $data[] = $row;

        @mysql_free_result($result);

        return $data;
    }

    function fa($rs) {
        if ($this->assoc==false) {
            return @mysql_fetch_array($rs);
        } else {
            return @mysql_fetch_assoc($rs);
        }
    }

    function FormatValue($theValue, $theType = null, $slashes = 'gpc') {

        if ($slashes == 'gpc') {
            $theValue = get_magic_quotes_gpc() ? $theValue : addslashes($theValue);
        } elseif ($slashes == 'rt') {
            $theValue = get_magic_quotes_runtime() ? $theValue : addslashes($theValue);
        }

        if (empty($theType))
            $theType = gettype($theValue);

        switch ($theType) {
            case "integer":
                $theValue = ($theValue === '') ? "NULL" : intval($theValue);
                break;
            case "double":
                $theValue = ($theValue != '') ? "'" . doubleval($theValue) . "'" : "NULL";
                break;
            case "string":
                if ($theValue != "NOW()") {
                    $theValue = "'" . $theValue . "'";
                }
                break;
            default :
                $theValue = "NULL";
                break;
        }
        Return $theValue;
    }

    function FormatField($theField) {
        return '`' . $theField . '`';
    }

    /*     * ********************************************************
     * 	The following functions are supporting transaction
     * 	Seal Created by 2010-1-25
     * ******************************************************** */

    function BeginTransaction() {
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("BEGIN");
    }

    function Rollback() {
        mysql_query("ROLLBACK");
    }

    function Commit() {
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
    }

    function HasError() {
        return mysql_errno($this->connection) == 0 ? false : true;
    }

    function GetErrMsg() {
        return mysql_error($this->connection);
    }

    /*     * ********************************************************
     * 	this method is for create the prepared sql statement
     * 	Seal Created by 2010-3-2
     * ******************************************************** */

    function CreatePreparedStatement($sql) {
        return new PreparedStatement($sql);
    }

}