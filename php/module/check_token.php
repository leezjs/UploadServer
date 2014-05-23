<?php
/**
 * 用户验证
 *
 * @author Jensen
 */
class check_token extends AbstractAction {

    public function run() {
        $email = cleanInput($this->params['email']);
        $token = cleanInput($this->params['token']);
        $sig = cleanInput($this->params['sig']);
        
        // parameter check
        $data = array();
        $data['token'] = $token;
        $data['email'] = $email;
        if( $this->validateParameter($data) ==  -1 ){
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
            $redis->connect('127.0.0.1', 6379);
            $cache = $redis->hMGet("account:".$email, array("token"));
            $redis->close();

            if( $token == $cache['token'] ){
                $this->output(0, "token is valid");
            }
            else{
                $this->output(0, "token is invalid");
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
                'required' => true,
                'datatype' => VALIDATE_DATATYPE_EMAIL,
                'minlen' => 6
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
