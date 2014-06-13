<?php
/**
 * 用户验证
 *
 * @author Jensen
 */
class user_auth extends AbstractAction {

    public function run() {
        $email = cleanInput($this->params['email']);
        $password = cleanInput($this->params['password']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['password'] = $password;
        $data['email'] = $email;
        if( $this->validateParameter($data) ==  -1 ){
            return;
        }
        
        // check sig
//        if($sig != $this->assembleSig($data) ){
//            $this->output(1, "请求签名不合法");
//            return;
//        }
        
        $dao = $this->getDao("AccountEmailDAO");
        $accountId = $dao->DoUserAuth($email, $password);
        // check email exist
        if( $accountId !== false )
        {
            // generate random token
            $token = $this->randomToken(32);
            $detail = array( 
                "account_id" => $accountId,
                "token" => $token, 
            );
            
            // put into redis
            try{
                $redis = new Redis();
                $redis->connect(REDIS_SERVER, REDIS_PORT);
                // duplicate user info saved in redis
                $redis->hMSet("account:".$email, $detail);
                $redis->hMSet("account_id:".$accountId, $detail);
                $redis->close();
                
                $this->output(0, "OK", $detail);
            } catch (Exception $ex) {
                $this->output(2, "user auth failed, failed to store to cache");
            }
        }
        else{
            $this->output(1, "User Not valid");
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
                'datatype' => VALIDATE_DATATYPE_STRING
            ),
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
    
    private function randomToken( $len )
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVXYZ";
        $randstring = '';
        for ($i = 0; $i < $len; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
}
