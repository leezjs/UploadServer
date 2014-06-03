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
     * 最多只取十条, list_status 为0的记录
     * 
     * @param type $deviceId
     * @return type
     */
    public function GetUserListByDeviceId( $deviceId, $limit=10 ){
        $suffix = $this->GetSuffix($deviceId);
        $sql = "select account_id, email, password from {$this->tablename}_{$suffix} where device_id='$deviceId' and list_status=0 order by account_id desc limit 0, $limit";
        
        $result = $this->db->GetPlan($sql);
        return $result;
    }
    
    /**
     * 更新device 状态位
     * 
     * @param type $deviceId
     * @param type $email
     * @return boolean
     */
    public function DeleteAccountDeviceId( $deviceId, $email ){
        $suffix = $this->GetSuffix($deviceId);
        $sql = "update {$this->tablename}_{$suffix} set list_status=1 where device_id='{$deviceId}' and email='{$email}'";
        
        try{
            $result = $this->db->Update($sql);
            return $result;
        } catch (Exception $ex) {
            return false;
        }
    }
    
}

