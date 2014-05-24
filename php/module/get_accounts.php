<?php
/**
 * 根据设备号来获取该设备注册的用户名和密码
 *
 * @author Jensen
 */
class get_accounts extends AbstractAction {

    public function run() {
        $deviceid = cleanInput($this->params['deviceid']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['device_id'] = $deviceid;
        if( $this->validateParameter($data) ==  -1 ){
            return;
        }
        
        // check sig
//        if($sig != $this->assembleSig($data) ){
//            $this->output(1, "请求签名不合法");
//            return;
//        }
        
        $dao = $this->getDao("AccountDeviceDAO");
        // check email exist
        $userList = $dao->GetUserListByDeviceId($deviceid);
        $this->output(0, "OK", $userList);
    }

    /**
     * param validation
     * 
     * @param type $param
     * @return int
     */
    protected function validateParameter( $param ) {
        $validate_config = array(
            array(
                'field' => 'device_id',
                'name' => 'device_id',
                'required' => true,
                'datatype' => VALIDATE_DATATYPE_STRING
            )
        );

        $validator = new Validator($validate_config);
        try {
            $validator->Validate( $param );
            return 0;
        } catch (ValidateException $e) {
            $this->output( -2, $e->getMessage() );
            return -1;
        }
    }
}
