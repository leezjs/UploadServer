<?php
/**
 * aliyun 相关处理使用PHP进行异步处理
 * 定时触发进行删除
 *
 * @author Jensen Zhang
 * 2014-03-22 Create
 */
require_once 'aliyun/sdk.class.php';
require_once 'DB.class.php';
require_once 'common.php';

$oss_sdk_service = new ALIOSS();

//设置是否打开curl调试模式
$oss_sdk_service->set_debug_mode(FALSE);

// DB 获取记录
$db = new DB();
// 一天内未同步或同步失败的文件
// 超过一天同步失败的文件， 不予同步
// 一次处理100条记录
$files = $db->GetPlan("SELECT * FROM tbuseruploadfile WHERE iStatus=-1 and dtUploadTime>NOW() - INTERVAL 1 DAY order by id desc limit 100");

if( $files !== false ){
	// 循环删除
	foreach ( $files as $file ){
		try{
			$folder_prefix = ($file['iUserId']%1000)."/".$file['iUserId']."/";
			$remote_file_path = $folder_prefix.$file['sFileRemoteName'];
			delete_object($oss_sdk_service, $remote_file_path );
			
			$db->Update("UPDATE tbuseruploadfile SET iStatus=-2 WHERE id=${file['id']}");
			simplelog( "文件 ${file['id']} 同步删除到aliyun成功" );
		}catch (Exception $ex){
			simplelog( "文件 ${file['id']} 同步删除到aliyun失败 ".$ex->getMessage() );
		}
	}
}
else{
	simplelog( "没有待删除文件" );
}

