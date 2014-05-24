<?php

class AbstractDAO {
    protected $tablename;
    protected $db;

    /**
     * 
     * @param type $dbType
     * 0 means formal db
     * 1 means crawler db
     */
    public function __construct( $dbType = 0 ) {
        $this->db = DB::GetInstance($dbType);
    }

    public function __destruct() {
        $this->db->Disconnect();
    }
    
    public function GetSuffix( $value, $tableNum = 100 ){
        // using the first charactor to do table sharding
        return ord($value)%$tableNum;
    }
    
    /**
     * if succeed, return last insert id
     * 
     * @param type $params
     * @return boolean
     */
    public function Add($params) {
        $params = $this->filterParameters($params);
        
        $cols = $this->getStrKey($params);
        $strval = $this->getStrVal($params);
        $sql = "insert ignore into {$this->tablename} (" . $cols . ") values (" . $strval . ")";
        $res = $this->db->Insert($sql);
        if ($res >= 0) {
            return $res;
        } else {
            return false;
        }
    }

    public function Update($id, $params, $primarykey = "id") {
        $params = $this->filterParameters($params);
        
        $strval = $this->getStrKeyVal($params);
        $sql = "UPDATE {$this->tablename} SET " . $strval . " where {$primarykey}='{$id}'";
        $res = $this->db->Update($sql);
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function Delete($id, $primarykey = "id") {
        $params = $this->filterParameters($params);
        
        $sql = "DELETE FROM {$this->tablename} where {$primarykey}={$id}";
        $res = $this->db->Update($sql);
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 执行事务
     * 
     * @param type $sqls
     */
    public function ExecTrans( $sqls ){
        try{
            $this->db->BeginTransaction();
            foreach( $sqls as $sql ){
                $this->db->Update($sql);
            }
            $this->db->Commit();
            return true;
        } catch (Exception $ex) {
            $this->db->Rollback();
            return false;
        }
    }
    
    protected function getStrKey($params) {
        $keys = array_keys($params);
        $strKeys = "";
        foreach ($keys as $key) {
            if ($strKeys == "") {
                $strKeys = "`" . $key . "`";
            } else {
                $strKeys = $strKeys . ", `" . $key . "`";
            }
        }
        return $strKeys;
    }

    protected function getStrVal($params) {
        $vals = array_values($params);
        $strVal = "";
        foreach ($vals as $value) {
            if ($strVal == "") {
                $strVal = "'" . $value . "'";
            } else {
                $strVal = $strVal . ", '" . $value . "'";
            }
        }
        return $strVal;
    }

    protected function getStrKeyVal($params) {
        $strVal = "";
        foreach ($params as $k => $value) {
            if ($strVal == "") {
                $strVal = $k . "='" . $value . "'";
            } else {
                $strVal = $strVal . "," . $k . "='" . $value . "'";
            }
        }
        return $strVal;
    }

    /**
     * get limited result set by page
     * @param string $sql
     * @param int $perpage
     * @param int $cpage current page
     * @return array
     */
    protected function getRows($sql, $perpage, $cpage = 1, $function = "GetPlan") {
        $start = ($cpage - 1) * $perpage;
        $end = $perpage;

        $sql .= " LIMIT $start,$end";

        //print $sql;
        //die();
        $result = $this->db->$function($sql);
        return $result;
    }

    protected function filterParameters($param) {
        if (is_array($param)) {
            foreach ($param as $k => $v) {
                $param[$k] = $this->filterParameters($v); //recursive
            }
        } elseif (is_string($param)) {
//            $trans = array(
//                '<' => '&lt;',
//                '>' => '&gt;'
//            );
//            $param = strtr($param,$trans);
//            $param = mysql_real_escape_string($param);
            $param = htmlspecialchars($param);
            // 过滤引号
            $trans = array(
                "'" => '&apos;'
            );
            $param = strtr($param, $trans);
            //$param = mysql_real_escape_string($param);
        }
        return $param;
    }
}

