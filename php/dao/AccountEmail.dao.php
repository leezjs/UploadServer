<?php

/**
 * 用户DAO
 */
class AccountEmailDAO extends AbstractDAO {

    protected $tablename = "account_email";

    public function __construct() {
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }
    
    public function GetUserInfo($email){
        $suffix = $this->GetSuffix($email);
        $sql = "select * from {$this->tablename}_{$suffix} where email='$email'";
        
        return $this->db->GetRow($sql);
    }
    
    /**
     * 判断用户是否合法
     * 
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function DoUserAuth($email, $password){
        $suffix = $this->GetSuffix($email);
        $sql = "select account_id from {$this->tablename}_{$suffix} where email='$email' and password='$password'";
        $aid = $this->db->GetValue($sql);
        if( !empty($aid) ){
            return $aid;
        }
        else{
            return false;
        }
    }
}

