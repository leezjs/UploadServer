<?php
/**
 * Description of AbstractAction
 *
 * @author Jensen
 */
abstract class AbstractAction {
    protected $params;
    public function __construct( $params ) {
        $this->params = $params;
    }
    
    public abstract function run();

    protected function assembleSig( $data ){
        ksort($data);
        $res = "";
        foreach( $data as $key=>$value ){
            if(empty($res)){
                $res = "{$key}={$value}";
            }
            else{
                $res .= "&{$key}={$value}";
            }
        }
        
        $sig = md5($res.PRIVATE_KEY);
        return $sig;
    }
    
    /**
     * get single dao instance 
     * 
     * @global name $name
     * @param name $name
     * @return \name 
     */
    protected function getDao($name) {
        global $$name;

        if (isset($$name))
            return $$name;
        else {
            $$name = new $name();
            return $$name;
        }
    }
    
    protected function output( $ret, $msg, $detail = array() ){
        $result = array();
        $result['iRet'] = $ret;
        $result['sMsg'] = $msg;
        $result['detail'] = $detail;
        
        echo json_encode($result);
    }
    
    /**
     * 获得分类名
     * 
     * @param type $categoryid
     * @param type $categories
     * @return string
     */
    protected function getCategoryName($categoryid, $categories) {
        foreach ($categories as $category) {
            if ($category['categoryid'] == $categoryid) {
                return $category['categoryname'];
            }
            foreach ($category['subcategories'] as $subcategory) {
                if ($subcategory['categoryid'] == $categoryid) {
                    return $subcategory['categoryname'];
                }
            }
        }
        
        return "暂未分类";
    }


}

