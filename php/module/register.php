<?php
/**
 * 用户注册
 *
 * @author Jensen
 */
class register extends AbstractAction {

    public function run() {
        $email = cleanInput($this->params['email']);
        $password = cleanInput($this->params['password']);
        $deviceid = cleanInput($this->params['deviceid']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['password'] = $password;
        $data['email'] = $email;
        $data['device_id'] = $deviceid;
        if( $this->validateParameter($data) ==  -1 ){
            return;
        }
        
        // check sig
        if($sig != $this->assembleSig($data) ){
            $this->output(1, "请求签名不合法");
            return;
        }
        
        $dao = $this->getDao("AccountDAO");
        // check email exist
        if($dao->IsUserExist($email)){
            $this->output(1, "用户{$email}已存在");
        }
        else{
            // add to db
            $data['account'] = $email;
            $accountId = $dao->Add( $data );
            if( $accountId !== false ){
                $this->output(0, "OK, accont id is {$accountId}");
            }
            else{
                $this->output(-1, "DB 操作失败");
            }
            
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
                'field' => 'email',
                'name' => 'email',
                'required' => true,
                'datatype' => VALIDATE_DATATYPE_EMAIL,
                'minlen' => 6
            ),
            array(
                'field' => 'password',
                'name' => 'password',
                'required' => true,
                'datatype' => VALIDATE_DATATYPE_STRING,
                'minlen' => 6
            ),
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
