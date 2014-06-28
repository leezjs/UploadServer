<?php
/**
 * 忘记密码
 *
 * @author Jensen
 */
class reset_password extends AbstractAction {

    public function run() {
        $newPwd = cleanInput( $this->params['newpwd'] );
        $confirmPwd = cleanInput( $this->params['confirmPwd'] );
        
        // do reset password
        if( isset($newPwd) ){
            
        }
        // display reset password page
        else{
            global $TPL;
            
            // check parameter
            // pass email and timestamp
            
            $PageName = "重设密码";
            $TPL->assign( "PageName", $PageName );
            $TPL->display("resetpass.html");
        }
    }
    
}
