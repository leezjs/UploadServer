<?php

/**
 * 用户DAO
 */
class AccountDeviceDAO extends AbstractDAO {

    protected $tablename = "account_device";

    public function __construct() {
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }
    
    /**
     * 最多只取十条
     * 
     * @param type $deviceId
     * @return type
     */
    public function GetUserListByDeviceId( $deviceId, $limit=10 ){
        $suffix = $this->GetSuffix($deviceId);
        $sql = "select account_id, email, password from {$this->tablename}_{$suffix} where device_id='$deviceId' limit 0, $limit";
        
        $result = $this->db->GetPlan($sql);
        return $result;
    }
}

