<?php
/**
 * 用户验证
 *
 * @author Jensen
 */
class check_token extends AbstractAction {

    public function run() {
        $email = cleanInput($this->params['email']);
        $accountId = intval($this->params['account_id']);
        $token = cleanInput($this->params['token']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['token'] = $token;
        $data['email'] = $email;
        $data['account_id'] = $accountId;
        
        if( $this->validateParameter($data) ==  -1 ){
            return;
        }
        
        // check if email is empty
        if (empty($email) && $accountId == 0){
            $this->output(-2, "account id and email cannot be empty at the same time!");
            return;
        }
        
        // check sig
//        if($sig != $this->assembleSig($data) ){
//            $this->output(1, "请求签名不合法");
//            return;
//        }
        
            
        // get from redis
        try{
            $redis = new Redis();
            $redis->connect(REDIS_SERVER, REDIS_PORT);
            if ($accountId != 0){
                $cache = $redis->hMGet("account_id:".$accountId, array("token"));
            }
            else{
                $cache = $redis->hMGet("account:".$email, array("token"));
                
            }
            
            $redis->close();

            if( $token == $cache['token'] ){
                $this->output(0, "token is valid");
            }
            else{
                $this->output(1, "token is invalid");
            }
        } catch (Exception $ex) {
            $this->output(2, "user auth failed, failed to get data from cache");
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
                'required' => false,
                'datatype' => VALIDATE_DATATYPE_EMAIL,
                'minlen' => 6
            ),
            array(
                'field' => 'account_id',
                'name' => 'account_id',
                'required' => false,
                'datatype' => VALIDATE_DATATYPE_INT,
            ),
            array(
                'field' => 'token',
                'name' => 'token',
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
}
