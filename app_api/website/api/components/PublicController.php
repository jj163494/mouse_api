<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/OSS/Common.php';
require_once dirname ( __FILE__ ) . '/Language.php';

use OSS\OssClient;
use OSS\Core\OssException;
class PublicController extends Controller {

	public $language;

	public function __construct(){
		$this->language = new Language();
	}

	/**
	 * 根据用户标识获取用户信息
	 * @param string $lang
	 * @return array|mixed|null
	 */
	public function check_user($lang = "ZH") {
		// 获取语言文件
		$this->language->read($lang);

		$open_id = Frame::getStringFromRequest("open_id");
		if(empty($open_id)){
			$result ['ret_num'] = 222;
			$result ['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 验证登录标识是否还有效
		$app_user_model = new TdAppUsers();
		$app_user_where = "open_id = '{$open_id}'";
		$app_user_info = $app_user_model->getAppUserInfo($app_user_where);
		if(empty($app_user_info)){
			$result ['ret_num'] = 306;
			$result ['ret_msg'] = $this->language->get('member_lost');
			echo json_encode ( $result );
			die ();
		}

		if($app_user_info['invalid_time'] <= time()){
			// 已超过登录失效时间,需要重新登录
			$result ['ret_num'] = 304;
			$result ['ret_msg'] = $this->language->get('token_expire');
			echo json_encode ( $result );
			die ();
		}

		$member_id = $app_user_info['member_id'];

		// 获取用户信息
		$member_model = new TaidushopMember();
		$member_where = "member_id = {$member_id}";
		$member_info = $member_model->getMemberInfo($member_where);
		if(empty($member_info)){
			$result ['ret_num'] = 309;
			$result['ret_msg'] = $this->language->get('no_data');
			echo json_encode ( $result );
			die ();
		}

		return $member_info;
	}

	
	/**
	 * 优选账号规则
	 */
	public function checkbenben($benben_id) {
		// 1.号码全相同，如222222;
		$str0 = "/^(\d)\\1{1,}$/";
		$string = $benben_id;
		if (preg_match ( $str0, $string )) {
			return false;
		}
		// 2.每个号段的起始号,如30001、400001;
		$str0 = "/^[1-9]{1}[0]{1,}[1]$/";
		$string = $benben_id;
		if (preg_match ( $str0, $string )) {
			return false;
		}
		// 3.尾号是88的,如50188;
		$su_str = substr ( $string, - 2 );
		if ($su_str == 88) {
			return false;
		}
		// 4.尾号是AABB,如551188;
		$str0 = "/^(\d)\\1{1}(\d)\\2{1}$/";
		$su_str = substr ( $string, - 4 );
		if (preg_match ( $str0, $su_str )) {
			return false;
		}
		
		// 5.尾号是ABAB,如551818;
		$str0 = "/([0-9]{2})\\1/";
		$su_str = substr ( $string, - 4 );
		if (preg_match ( $str0, $su_str )) {
			return false;
		}
		
		// 6.尾号是AAA/AAAA/AAAAA/AAAAAA等等
		$str0 = "/^[1-9]{1,}(\d)\\1{2,}$/";
		if (preg_match ( $str0, $string )) {
			return false;
		}
		
		// 7.尾号是ABC,如20123
		$su_str = substr ( $string, - 3 );
		$flag = 0;
		$len = strlen ( $su_str );
		$current = $su_str [0];
		for($i = 1; $i < $len; $i ++) {
			if ($current + 1 != $su_str [$i]) {
				$flag = 1;
				break;
			}
			$current = $su_str [$i];
		}
		if ($flag == 0) {
			return false;
		}
		
		// 8.号码降序排列,如54321
		$flag = 0;
		$len = strlen ( $string );
		$current = $string [0];
		for($i = 1; $i < $len; $i ++) {
			if ($current - 1 != $string [$i]) {
				$flag = 1;
				break;
			}
			$current = $string [$i];
		}
		if ($flag == 0) {
			return false;
		}
		// 2.号码升序排列,如12345
		$flag = 0;
		$len = strlen ( $string );
		$current = $string [0];
		for($i = 1; $i < $len; $i ++) {
			if ($current + 1 != $string [$i]) {
				$flag = 1;
				break;
			}
			$current = $string [$i];
		}
		if ($flag == 0) {
			return false;
		}
		
		// 是否是保留账号
		$reserve_phone = getphone ();
		if (in_array ( $benben_id, $reserve_phone )) {
			return false;
		}
		return true;
	}
	
	/**
	 *
	 * @return array(省市代码信息)
	 *
	 */
	public function pcinfo() {
		$connection = Yii::app ()->db;
		$sql = "SELECT bid,area_name FROM area where parent_bid = 0";
		$command = $connection->createCommand ( $sql );
		$result0 = $command->queryAll ();
		$all = "";
		$province = array ();
		foreach ( $result0 as $value ) {
			$province [$value ['bid']] = $value ['area_name'];
			$all .= $value ['bid'] . ",";
		}
		$all = trim ( $all );
		$all = trim ( $all, ',' );
		$sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
		$command = $connection->createCommand ( $sql );
		$result1 = $command->queryAll ();
		$city = array ();
		foreach ( $result1 as $value ) {
			$city [$value ['bid']] = $value ['area_name'];
			$all .= $value ['bid'] . ",";
		}
		$all = trim ( $all );
		$all = trim ( $all, ',' );
		$sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
		$command = $connection->createCommand ( $sql );
		$result1 = $command->queryAll ();
		$area = array ();
		foreach ( $result1 as $value ) {
			$area [$value ['bid']] = $value ['area_name'];
			$all .= $value ['bid'] . ",";
		}
		$all = trim ( $all );
		$all = trim ( $all, ',' );
		$sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
		$command = $connection->createCommand ( $sql );
		$result1 = $command->queryAll ();
		$street = array ();
		foreach ( $result1 as $value ) {
			$street [$value ['bid']] = $value ['area_name'];
			$all .= $value ['bid'] . ",";
		}
		return array (
				$province,
				$city,
				$area,
				$street 
		);
	}
	
	/**
	 * 根据ID获取省市信息
	 */
	public function getProCity($bid) {
		if (! $bid) {
			return false;
		}
		$connection = Yii::app ()->db;
		$sql = "SELECT bid,area_name FROM area WHERE bid in ({$bid})";
		$command = $connection->createCommand ( $sql );
		$area = $command->queryAll ();
		return $area;
	}
	public function ProCity($users) {
		// 省市代码获取
		$pro = array ();
		$pro_arr = array ();
		foreach ( $users as $value ) {
			if ($value ['province']) {
				$pro [] = $value ['province'];
			}
			if ($value ['city']) {
				$pro [] = $value ['city'];
			}
			if ($value ['area']) {
				$pro [] = $value ['area'];
			}
			if ($value ['street']) {
				$pro [] = $value ['street'];
			}
		}
		$pro_name = $this->getProCity ( implode ( ",", $pro ) );
		if ($pro_name) {
			foreach ( $pro_name as $val ) {
				$pro_arr [$val ['bid']] = $val ['area_name'];
			}
		}
		return $pro_arr;
	}
	
	/**
	 *
	 * @return array(行业代码信息)
	 *
	 */
	public function industryinfo() {
		$connection = Yii::app ()->db;
		$sql = "SELECT id,name FROM industry where parent_id = 0";
		$command = $connection->createCommand ( $sql );
		$result0 = $command->queryAll ();
		$all = "";
		$province = array ();
		foreach ( $result0 as $value ) {
			$province [$value ['id']] = $value ['name'];
			$all .= $value ['id'] . ",";
		}
		$all = trim ( $all );
		$all = trim ( $all, ',' );
		$sql = "SELECT id,name FROM industry where parent_id in ({$all})";
		$command = $connection->createCommand ( $sql );
		$result1 = $command->queryAll ();
		$city = array ();
		foreach ( $result1 as $value ) {
			$city [$value ['id']] = $value ['name'];
		}
		return array (
				$province,
				$city 
		);
	}
	
	/**
	 * 根据ID获取行业信息
	 */
	public function getIndustryinfo($bid) {
		if (! $bid) {
			return false;
		}
		$connection = Yii::app ()->db;
		$sql = "SELECT id,name FROM industry WHERE id in ({$bid})";
		$command = $connection->createCommand ( $sql );
		$area = $command->queryAll ();
		return $area;
	}
	
	/**
	 * 根据ID获取行业信息(多个)
	 */
	public function Industry($users) {
		$industry = array ();
		$industry_arr = array ();
		foreach ( $users as $value ) {
			if ($value ['industry']) {
				$industry [] = $value ['industry'];
			}
		}
		$industry_name = $this->getIndustryinfo ( implode ( ",", $industry ) );
		if ($industry_name) {
			foreach ( $industry_name as $val ) {
				$industry_arr [$val ['id']] = $val ['name'];
			}
		}
		return $industry_arr;
	}
	public function check_key() {
		$key = Frame::getStringFromRequest ( 'key' );
		Frame::appkey ( $key );
	}
	public function eraseNull($string) {
		if ($string == null) {
			$string = "";
		}
		return $string;
	}
	
	/**
	 * 添加积分
	 */
	public function addIntegral($memberId, $type) {
		$integral_array = array (
				1 => 50,
				2 => 100,
				3 => 20,
				4 => 50,
				5 => 50,
				6 => 50,
				7 => 200,
				8 => 50,
				9 => 20,
				10 => 20,
				11 => 50,
				12 => 20,
				13 => 10,
				14 => 2,
				15 => 2,
				16 => 1,
				17 => 2,
				18 => 2,
				19 => 2,
				20 => 1 
		);
		// 只需要增加一次积分
		$member = Member::model ()->findByPk ( $memberId );
		if ($type <= 12) {
			$my = MemberIntegralLog::model ()->find ( "member_id = {$memberId} and type = {$type}" );
			if ($my) {
				return;
			} else {
				// $member = Member::model()->findByPk($memberId);
				if ($member) {
					$member->integral = $member->integral + $integral_array [$type];
					$member->save ();
					$log = new MemberIntegralLog ();
					$log->member_id = $memberId;
					$log->integral = $integral_array [$type];
					$log->created_time = time ();
					$log->type = $type;
					$log->save ();
				}
			}
		} else if ($type == 14) { // 用犇犇拨号
			$connection = Yii::app ()->db;
			$sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type}";
			$command = $connection->createCommand ( $sql );
			$result0 = $command->queryAll ();
			if ($result0 [0] ['c'] <= 40) {
				$member->integral = $member->integral + $integral_array [$type];
				$member->save ();
				$log = new MemberIntegralLog ();
				$log->member_id = $memberId;
				$log->integral = $integral_array [$type];
				$log->created_time = time ();
				$log->type = $type;
				$log->save ();
			}
		} else if ($type == 20) { // 被收藏
			$connection = Yii::app ()->db;
			$sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type}";
			$command = $connection->createCommand ( $sql );
			$result0 = $command->queryAll ();
			if ($result0 [0] ['c'] <= 1000) {
				$member->integral = $member->integral + $integral_array [$type];
				$member->save ();
				$log = new MemberIntegralLog ();
				$log->member_id = $memberId;
				$log->integral = $integral_array [$type];
				$log->created_time = time ();
				$log->type = $type;
				$log->save ();
			}
		}
	}
	public function checkphone($phone) {
		$str0 = "/^1\d{10}$|^(0\d{2,3}-?|\(0\d{2,3}\))?[1-9]\d{4,7}(-\d{1,8})?$/";
		$string = $phone;
		return preg_match ( $str0, $string );
	}
	public function checkpwd($password) {
		// $str0 = "/^[0-9_.a-zA-Z]{6,20}$/";
		// $string = $password;
		// return preg_match($str0, $string);
		if (strlen ( $password ) < 6 || strlen ( $password ) > 18) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * 通用接口发短信
	 * apikey 为云片分配的apikey
	 * text 为短信内容
	 * mobile 为接受短信的手机号
	 */
	public function send_sms($apikey, $text, $mobile) {
		$url = "http://yunpian.com/v1/sms/send.json";
		$encoded_text = urlencode ( "$text" );
		$post_string = "apikey=$apikey&text=$encoded_text&mobile=$mobile";
		return $this->sock_post ( $url, $post_string );
	}
	
	/**
	 * url 为服务的url地址
	 * query 为请求串
	 */
	public function sock_post($url, $query) {
		$data = "";
		$info = parse_url ( $url );
		$fp = fsockopen ( $info ["host"], 80, $errno, $errstr, 30 );
		if (! $fp) {
			return $data;
		}
		$head = "POST " . $info ['path'] . " HTTP/1.0\r\n";
		$head .= "Host: " . $info ['host'] . "\r\n";
		$head .= "Referer: http://" . $info ['host'] . $info ['path'] . "\r\n";
		$head .= "Content-type: application/x-www-form-urlencoded\r\n";
		$head .= "Content-Length: " . strlen ( trim ( $query ) ) . "\r\n";
		$head .= "\r\n";
		$head .= trim ( $query );
		$write = fputs ( $fp, $head );
		$header = "";
		while ( $str = trim ( fgets ( $fp, 4096 ) ) ) {
			$header .= $str;
		}
		while ( ! feof ( $fp ) ) {
			$data .= fgets ( $fp, 4096 );
		}
		return $data;
	}
	public function uploadPic($image) {
		if ($image) {
			// 按时间生成目录
			$folder = date ( "YmdH", time () );
			
			// 随机生成图片名
			$picName = rand ( 100, 999 ) . md5 ( $image->name ) . "." . $image->getExtensionName ();
			$path = $folder . "/" . $picName;
			
			$dir = Yii::getPathOfAlias ( 'webroot' ) . '/images/' . $folder . "/";
			// 上传目录
			if (! is_dir ( $dir )) {
				mkdir ( $dir );
				// 目录不存在则创建
			}
			$header = $dir . $picName;
			// 文件名绝对路径
			
			$status = $image->saveAs ( $header, true );
			// 保存文件
			if (! $status) {
				$result ['ret_num'] = 702;
				$result ['ret_msg'] = '图片上传失败';
				echo json_encode ( $result );
			} else {
				// 上传文件到oss
				$bucket = Common::getBucketName();
				$ossClient = Common::getOssClient();
				$options = array();
				try {
					$ossClient->uploadFile($bucket, $path, $header, $options);
					$path = Common::getFileUrl($folder . "/" . $picName);
				} catch (OssException $e) {
					$result ['ret_num'] = 702;
					$result ['ret_msg'] = '图片上传失败';
					echo json_encode ( $result );
				}
				
			}
		}
		return $path;
	}
	public function webroot() {
		$webroot = Yii::app ()->request->hostInfo . "/images/";
		return $webroot;
	}
}