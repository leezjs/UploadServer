<?php
/**
 * 根据设备号来置位用户信息
 * 不在列表中返回
 *
 * @author Jensen
 */
class delete_accounts extends AbstractAction {

    public function run() {
        $deviceid = cleanInput($this->params['deviceid']);
        $email = cleanInput($this->params['email']);
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
        if( $dao->DeleteAccountDeviceId($deviceid, $email) ){
            $this->output(0, "OK");
        }
        else{
            $this->output(0, "delete failed");
        }
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
