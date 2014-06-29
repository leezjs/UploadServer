<?php
/**
 * 忘记密码
 *
 * @author Jensen
 */
class reset_password extends AbstractAction {

    public function run() {
        global $TPL;
        
        //common check
        $email = cleanInput( $this->params['email'] );
        $time = cleanInput( $this->params['time'] );
        $sig = cleanInput( $this->params['sig'] );

        $PageName = "重设密码";
        $TPL->assign( "PageName", $PageName );
        $TPL->assign( "email", $email );
        $TPL->assign( "time", $time );
        $TPL->assign( "sig", $sig );
        $TPL->assign( "errorMsg", $errorMsg );

        $data = array();
        $data['email'] = $email;
        $data['time'] = $time;
        // check sig
        if($sig != $this->assembleSig($data) ){
            $errorMsg = "请求签名不合法, 无法修改密码";
        }

        // check time
        $curTime = time();
        if( $curTime - $time > 3600 ){
            $errorMsg = "请求超时，请重新申请重置密码";
        }

        $newPwd = cleanInput( $this->params['newpwd'] );
        $confirmPwd = cleanInput( $this->params['confirmpwd'] );
        // do reset password
        if( isset($newPwd) && empty($errorMsg) ){
            $isSucceed = false;
            if( empty($newPwd) || empty($confirmPwd) ){
                $errorMsg = "新密码不能为空";
            }
            else if( $newPwd != $confirmPwd ){
                $errorMsg = "两次密码输入不一致";
            }
            else {
                $dao = $this->getDao("AccountEmailDAO");
                $userInfo = $dao->GetUserInfo($email);
                // check email exist
                if( $userInfo === false ){
                    $errorMsg = "用户信息不存在";
                }
                else{
                    //reset password
                    $params = array();
                    $params['password'] = $newPwd;
                    $accountDao = $this->getDao("AccountDAO");
                    $ret = $accountDao->UpdatePassword( $email, $userInfo['device_id'], $params );
                    if( $ret !== false ){
                        $errorMsg = "修改密码成功！";
                        $isSucceed = true;
                    }
                    else{
                        $errorMsg = "系统繁忙，请稍后重试！";
                    }
                }
            }
            
            $TPL->assign( "isSucceed", $isSucceed );
        }
        
        
        $TPL->assign( "errorMsg", $errorMsg );
        $TPL->display("resetpass.html");
    }
    
}
