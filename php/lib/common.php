<?php

/**
 * 函数定义
 */
/*%**************************************************************************************************************%*/
// Service 相关

//获取bucket列表
function get_service($obj){
	$response = $obj->list_bucket();
	_format($response);
}

/*%**************************************************************************************************************%*/
// Bucket 相关

//创建bucket
function create_bucket($obj){
	$bucket = 'uploadserver';
	//$acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
	$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
	//$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
	
	$response = $obj->create_bucket($bucket,$acl);
	_format($response);
}

//删除bucket
function delete_bucket($obj){
	$bucket = 'uploadserver';
	
	$response = $obj->delete_bucket($bucket);
	_format($response);
}

/*%**************************************************************************************************************%*/
// Object 相关

//获取object列表
function list_object($obj){
	$options = array(
		'delimiter' => '',
		'prefix' => '',
		'max-keys' => 100,
		//'marker' => 'myobject-1330850469.pdf',
	);
	
	$response = $obj->list_object($bucket,$options);	
	//_format($response);
}

//通过路径上传文件
function upload_by_file($obj, $remotefilepath, $localfilepath){
	$object = $remotefilepath;	
	$file_path = $localfilepath;
	
	$response = $obj->upload_file_by_file(UPLOAD_SERVER_BUCKET,$object,$file_path);
	//_format($response);
}

//删除object
function delete_object($obj, $filepath){
	$object = $filepath;
	$response = $obj->delete_object(UPLOAD_SERVER_BUCKET,$object);
	//_format($response);
}

/*%**************************************************************************************************************%*/
// 结果 相关

function simplelog($msg){
	echo "[".date("Y-m-d H:i:s")."] ".$msg."\n";
}

//格式化返回结果
function _format($response) {
	echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
	echo '|-Status:' . $response->status . "\n";
	echo '|-Body:' ."\n"; 
	echo $response->body . "\n";
	echo "|-Header:\n";
	print_r ( $response->header );
	echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
}


/**
 * Clean user input
 * @param type $param
 * @return type 
 */
function cleanInput( $param ){
    if (is_array($param)){
        foreach ($param as $k => $v){
            $param[$k] = cleanInput($v); //recursive
        }
    }
    elseif (is_string($param)){
        $param = trim($param);
        
        // filter XSS
        $param = htmlspecialchars( $param );
        // filter SQL injection
        $trans = array(
            '"' => '&quot;',
            '\'' => ''
        );
        $param = strtr($param,$trans);
    }
    return $param;
}

/**
 * output json result
 * 
 * @param type $iRetcode
 * @param type $sErrorMsg
 * @param type $vmResult 
 */
function jsonp_output($funcName, $iRetcode, $sErrorMsg, $vmResult = array()) {
    $res = array(
        'retCode' => $iRetcode,
        'retInfo' => $sErrorMsg,
        'list' => $vmResult
    );

    echo ";".$funcName."(".json_encode($res).")";
}

/**
 * GBK转UTF8，传入的数据可为数组或字符串
 * 数组则继续解析到字符串
 * @param $str
 * @return unknown_type
 */
function GBKtoUTF8($str)
{
    if(is_array($str))
    {
        foreach ($str as &$value) 
        {
            $value = GBKtoUTF8($value);
        }
        return $str;
    }
    elseif (is_string($str))
    {   
        $str = iconv("GBK", "UTF-8//IGNORE", $str);
        return $str;
    }
    else
    {
        return $str;
    }
}

/**
 * UTF8转GBK，传入的数据可为数组或字符串
 * 数组则继续解析到字符串
 * @param $str
 * @return unknown_type
 */
function UTF8toGBK(&$str)
{
    if(is_array($str))
    {
        foreach ($str as &$value) 
        {
            $value = UTF8toGBK($value);
        }
        return $str;			
    }
    elseif (is_string($str))
    {   
        $str = iconv("UTF-8", "GBK//IGNORE", $str);
        return $str;
    }
    else
    {
        return $str;
    }
}
