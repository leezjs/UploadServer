<?php

/**
 * ��������
 */
/*%**************************************************************************************************************%*/
// Service ���

//��ȡbucket�б�
function get_service($obj){
	$response = $obj->list_bucket();
	_format($response);
}

/*%**************************************************************************************************************%*/
// Bucket ���

//����bucket
function create_bucket($obj){
	$bucket = 'uploadserver';
	//$acl = ALIOSS::OSS_ACL_TYPE_PRIVATE;
	$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
	//$acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
	
	$response = $obj->create_bucket($bucket,$acl);
	_format($response);
}

//ɾ��bucket
function delete_bucket($obj){
	$bucket = 'uploadserver';
	
	$response = $obj->delete_bucket($bucket);
	_format($response);
}

/*%**************************************************************************************************************%*/
// Object ���

//��ȡobject�б�
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

//ͨ��·���ϴ��ļ�
function upload_by_file($obj, $remotefilepath, $localfilepath){
	$object = $remotefilepath;	
	$file_path = $localfilepath;
	
	$response = $obj->upload_file_by_file(UPLOAD_SERVER_BUCKET,$object,$file_path);
	//_format($response);
}

//ɾ��object
function delete_object($obj, $filepath){
	$object = $filepath;
	$response = $obj->delete_object(UPLOAD_SERVER_BUCKET,$object);
	//_format($response);
}

/*%**************************************************************************************************************%*/
// ��� ���

function simplelog($msg){
	echo "[".date("Y-m-d H:i:s")."] ".$msg."\n";
}

//��ʽ�����ؽ��
function _format($response) {
	echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
	echo '|-Status:' . $response->status . "\n";
	echo '|-Body:' ."\n"; 
	echo $response->body . "\n";
	echo "|-Header:\n";
	print_r ( $response->header );
	echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
}
