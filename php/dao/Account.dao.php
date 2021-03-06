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
     * 创建表
     * @param type $prefix
     * @param type $num
     */
    public function CreateTable( $prefix, $num=100){
        for($i=0;$i<$num;$i++){
            $sql = "DROP TABLE {$prefix}_{$i}";
            $this->db->Update($sql);
            $sql = "CREATE TABLE IF NOT EXISTS {$prefix}_{$i} LIKE accounts_template";
            $this->db->Update($sql);
        }
    }
    
    /**
     * 注册， 用事务，同时插入三张表中ß
     * 
     * @param type $data
     * @return boolean
     */
    public function DoRegister( $data ){
        try{
            $this->db->BeginTransaction();
            
            $data = $this->filterParameters($data);
            $cols = $this->getStrKey($data);
            $strval = $this->getStrVal($data);
            $sql = "insert ignore into {$this->tablename} (" . $cols . ") values (" . $strval . ")";
            $accountId = $this->db->Insert($sql);
            
            $data['account_id'] = $accountId;
            $cols = $this->getStrKey($data);
            $strval = $this->getStrVal($data);
            $suffix = $this->GetSuffix($data['email']);
            $sql = "insert ignore into account_email_{$suffix} (" . $cols . ") values (" . $strval . ")";
            $this->db->Insert($sql);
            
            $suffix = $this->GetSuffix($data['device_id']);
            $sql = "insert ignore into account_device_{$suffix} (" . $cols . ") values (" . $strval . ")";
            $this->db->Insert($sql);
            
            $this->db->Commit();
            return $accountId;
        } catch (Exception $ex) {
            $this->db->Rollback();
            return false;
        }
    }
    
    /**
     * 修改密码, 同时修改三张表中
     * 
     * @param type $data
     * @return boolean
     */
    public function UpdatePassword( $email, $deviceId, $params ){
        try{
            $this->db->BeginTransaction();
            
            $params = $this->filterParameters($params);
            $strval = $this->getStrKeyVal($params);
            
            $sql = "UPDATE {$this->tablename} SET " . $strval . " where email='{$email}'";
            $this->db->Update($sql);
            
            $suffix = $this->GetSuffix($email);
            $sql = "UPDATE account_email_{$suffix} SET " . $strval . " where email='{$email}'";
            $this->db->Update($sql);
            
            $suffix = $this->GetSuffix($deviceId);
            $sql = "UPDATE account_device_{$suffix} SET " . $strval . " where device_id='{$deviceId}' and email='{$email}'";
            $this->db->Update($sql);
            
            $this->db->Commit();
            return true;
        } catch (Exception $ex) {
            $this->db->Rollback();
            return false;
        }
    }
    
    /**
     * 修改密码, 同时修改三张表中
     * 
     * @param type $data
     * @return boolean
     */
    public function UpdateDeviceId( $email, $deviceId, $params ){
        try{
            $this->db->BeginTransaction();
            
            $params = $this->filterParameters($params);
            $strval = $this->getStrKeyVal($params);
            
            $sql = "UPDATE {$this->tablename} SET " . $strval . " WHERE email='{$email}'";
            $this->db->Update($sql);
            
            $suffix = $this->GetSuffix($email);
            $sql = "UPDATE account_email_{$suffix} SET " . $strval . " WHERE email='{$email}'";
            $this->db->Update($sql);
            
            // delete old record in account_device table
            $suffix = $this->GetSuffix($deviceId);
            $sql = "DELETE FROM account_device_{$suffix} WHERE device_id='{$deviceId}' and email='{$email}'";
            $this->db->Update($sql);
            
            // insert new one
            $suffix = $this->GetSuffix($params['device_id']);
            $cols = $this->getStrKey($params);
            $strval = $this->getStrVal($params);
            $sql = "insert ignore into account_device_{$suffix} (" . $cols . ") values (" . $strval . ")";
            $this->db->Insert($sql);
            
            $this->db->Commit();
            return true;
        } catch (Exception $ex) {
            $this->db->Rollback();
            return false;
        }
    }
}

