<?php
class Frame {
	public static function DRLogout($msg = 'test') {
		var_dump ( $msg );
	}
	
	// 保存图片
	public static function saveImage($postName) {
		if (empty ( $_FILES [$postName] ['name'] ))
			return '';
		if (is_array($_FILES [$postName] ['name'])){
			$up = CUploadedFile::getInstancesByName ( $postName );
			$data = array();
			foreach ($up as $val){
				$data[] =  Frame::createFile ( $val, "images", "create" );
			}
			return $data;
		}else{
			$up = CUploadedFile::getInstanceByName ( $postName );
			return Frame::createFile ( $up, "images", "create" );
		}
	}
	// 保存文件
	public static function createFile($upload, $type, $act, $imgurl = '') {
		if (! empty ( $imgurl ) && $act === 'update') {
			// 更新文件前删除旧文件
			$deleteFile = Yii::app ()->basePath . '/../' . $imgurl;
			if (is_file ( $deleteFile ))
				unlink ( $deleteFile );
		}
		$dirPath = '/uploads/' . $type . '/' . date ( 'Y-m', time () );
		$uploadDir = dirname ( __FILE__ ) . '/..' . $dirPath;
		self::recursionMkDir ( $uploadDir );
		$imgname = time () . '-' . rand () . '.' . $upload->extensionName;
		// 图片展示路径
		$imageurl = $dirPath . '/' . $imgname;
		// 存储使用绝对路径
		$uploadPath = $uploadDir . '/' . $imgname;
		if ($upload->saveAs ( $uploadPath )) {
			return $imageurl;
		} else {
			return null;
		}
	}
	private static function recursionMkDir($dir) {
		if (! is_dir ( $dir )) {
			self::recursionMkDir ( dirname ( $dir ) );
			mkdir ( $dir, 0777 );
		}
	}
	
	// 生成
	public static function createUUID() {
		if (function_exists ( 'com_create_guid' )) {
			return com_create_guid ();
		} else {
			mt_srand ( ( double ) microtime () * 10000 ); // optional for php 4.2.0 and up.
			$charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) );
			$hyphen = chr ( 45 ); // "-"
			$uuid = chr ( 123 ) . 			// "{"
			substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 ) . chr ( 125 ); // "}"
			return $uuid;
		}
	}
	public static function truncate_utf8_string($string, $length, $etc = '...') {
		$result = '';
		$string = html_entity_decode ( trim ( strip_tags ( $string ) ), ENT_QUOTES, 'UTF-8' );
		$strlen = strlen ( $string );
		for($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
			if ($number = strpos ( str_pad ( decbin ( ord ( substr ( $string, $i, 1 ) ) ), 8, '0', STR_PAD_LEFT ), '0' )) {
				if ($length < 1.0) {
					break;
				}
				$result .= substr ( $string, $i, $number );
				$length -= 1.0;
				$i += $number - 1;
			} else {
				$result .= substr ( $string, $i, 1 );
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars ( $result, ENT_QUOTES, 'UTF-8' );
		if ($i < $strlen) {
			$result .= $etc;
		}
		return $result;
	}
	public static function getStringFromRequest($key, $defaultValue = '') {
		return addslashes ( Yii::app ()->request->getParam ( $key, $defaultValue ) );
	}
	public static function getIntFromRequest($key, $defaultValue = 0) {
		return intval ( Yii::app ()->request->getParam ( $key, $defaultValue ) );
	}
	public static function getStringFromObject($obj, $key, $defalutValue = '') {
		if (empty ( $obj ) || empty ( $key ) || empty ( $obj->$key ))
			return $defalutValue;
		return $obj->$key;
	}
	public static function getArrayFromObject($obj, $key, $defalutValue = array()) {
		if (empty ( $obj ) || empty ( $key ) || empty ( $obj->$key ))
			return $defalutValue;
		return $obj->$key;
	}
	public static function getStringFromArray($array, $key, $defalutValue = '') {
		if (empty ( $array ) || empty ( $key ) || empty ( $array [$key] ))
			return $defalutValue;
		return $array [$key];
	}
	public static function getArrayFromArray($array, $key, $defalutValue = array()) {
		if (empty ( $array ) || empty ( $key ) || empty ( $array [$key] ))
			return $defalutValue;
		return $array [$key];
	}
	
	// 发邮件
	public static function sendMail($to, $topic, $message, &$error = '') {
		$validator = new CEmailValidator ();
		if (! $validator->validateValue ( $to )) {
			$error = '邮箱不合法';
			return false;
		}
		if (empty ( $topic ) || ! trim ( $topic )) {
			$error = '主题不能为空';
			return false;
		}
		if (empty ( $message ) || ! trim ( $message )) {
			$error = '邮件内容不能为空';
			return false;
		}
		$mailer = Yii::createComponent ( 'webroot.lib.mailer.EMailer' );
		$mailer->Host = 'smtp.163.com';
		$mailer->IsSMTP ();
		$mailer->SMTPAuth = true;
		$mailer->From = 'DataRenaissance@163.com'; // 设置发件地址
		                                           // $mailer->AddReplyTo('DataRenaissance@163.com');
		$mailer->AddAddress ( $to ); // 设置收件件地址
		$mailer->FromName = '数据复兴'; // 这里设置发件人姓名
		$mailer->Username = 'DataRenaissance'; // 这里输入发件地址的用户名
		$mailer->Password = 'drzaq12wsx'; // 这里输入发件地址的密码
		$mailer->SMTPDebug = false; // 设置SMTPDebug为true，就可以打开Debug功能，根据提示去修改配置
		$mailer->CharSet = 'UTF-8';
		$mailer->Subject = Yii::t ( 'DR', $topic ); // 设置邮件的主题
		$mailer->Body = $message;
		return $mailer->Send ();
	}
	//判断key
	public static function appkey($key){
		if(($key != APPKEY_IPHONE) && ($key != APPKEY_ANDROID)&& ($key != APPKEY_PC)){
			$result ['ret_num'] = 2006;
			$result ['ret_msg'] = 'key值不合法';
			echo json_encode ( $result );
			die ();
		}
	}
}

