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
}

