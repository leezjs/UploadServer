<?php
/**
 * 用户注册
 *
 * @author Jensen
 */
class setup extends AbstractAction {

    public function run() {
        // create tables in database
        $dao = $this->getDao("AccountDAO");
        $dao->CreateTable("account_email");
        $dao->CreateTable("account_device");
        
        $this->output(0, "OK");
    }

}
