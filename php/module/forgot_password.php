<?php
/**
 * 忘记密码
 *
 * @author Jensen
 */
class forgot_password extends AbstractAction {

    public function run() {
        global $MAIL_SUBJECT;
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
        
        $dao = $this->getDao("AccountEmailDAO");
        $userInfo = $dao->GetUserInfo($email);
        // check email exist
        if( $userInfo !== false ){
            $requestTime = time();
            $data = array();
            $data['time'] = $requestTime;
            $data['email'] = $email;
            $sig = $this->assembleSig($data);
//            $url = HOST."index.php?action=reset_password&email={$email}&time={$requestTime}&sig={$sig}";
            
            // for test
//            echo $url;
            // generate check some
//            $newpassword = $this->randomString(6);
            // send email
//            $message = "您刚申请了《天天爱唱歌》的密码重置，点击<a href=\"{$url}\">这里</a>进行密码重置<br />";
//            $message .= "<a href=\"{$url}\">密码重置</a>";
            $message = "您刚申请了《天天爱唱歌》的密码重置，点击进行密码重置";
            
            // Send
            $ret = mail($email, $MAIL_SUBJECT, $message);
            
            if($ret){
                $this->output(0, "OK, mail sent");
            }
            else{
                $this->output(1, "mail send failed");
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
