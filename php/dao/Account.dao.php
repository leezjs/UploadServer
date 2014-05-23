<?php

/**
 * 用户DAO
 */
class AccountDAO extends AbstractDAO {

    protected $tablename = "accounts";

    public function __construct() {
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }
    
    public function IsUserExist($email){
        $sql = "select account_id from {$this->tablename} where email='$email'";
        
        $aid = $this->db->GetValue($sql);
        if( !empty($aid) ){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * 最多只取十条
     * 
     * @param type $deviceId
     * @return type
     */
    public function GetUserListByDeviceId( $deviceId, $limit=10 ){
        $sql = "select account_id, email, password from {$this->tablename} where device_id='$deviceId' limit 0, $limit";
        
        $result = $this->db->GetPlan($sql);
        return $result;
    }
    
    /**
     * 判断用户是否合法
     * 
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function DoUserAuth($email, $password){
        $sql = "select account_id from {$this->tablename} where email='$email' and password='$password'";
        $aid = $this->db->GetValue($sql);
        if( !empty($aid) ){
            return $aid;
        }
        else{
            return false;
        }
    }
}

