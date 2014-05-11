<?php
/**
 * 忘记密码
 *
 * @author Jensen
 */
class forgot_password extends AbstractAction {

    public function run() {
        $email = cleanInput($this->params['email']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['email'] = $email;
        if( $this->validateParameter($data) ==  -1 ){
            return;
        }
        
        // check sig
//        if($sig != $this->assembleSig($data) ){
//            $this->output(1, "请求签名不合法");
//            return;
//        }
        
        $dao = $this->getDao("AccountDAO");
        // check email exist
        if($dao->IsUserExist($email)){
            $newpassword = $this->randomString(6);
            // send email
            
            // reset password
            $params['password'] = $newpassword;
            $ret = $dao->Update( $email, $params, 'email' );
            if( $ret !== false ){
                $this->output(0, "OK, password reseted");
            }
            else{
                $this->output(-1, "DB 操作失败");
            }
        }
        else{
            $this->output(1, "用户{$email}不存在");
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

    private function randomString( $len )
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $randstring = '';
        for ($i = 0; $i < $len; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

}
