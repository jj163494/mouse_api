<?php

class OptionController extends PublicController {
	/**
	 * 忘记密码
	 */
	public function actionFogpwd() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		if ((Yii::app ()->request->isPostRequest)) {
			// 获取key
			// echo time();die;
			$this->check_key ();
			$phone = Frame::getStringFromRequest ( 'phone' );
			$pwd = Frame::getStringFromRequest ( 'password' );
			$repwd = Frame::getStringFromRequest ( 'repassword' );
			$verify = Frame::getIntFromRequest ( 'code' );
			// 判断是否空
			if (empty ( $verify ) || empty ( $pwd ) || empty ( $repwd ) || empty ( $phone )) {
				$result ['ret_num'] = 201;
				$result['ret_msg'] = $this->language->get('miss_param');
				echo json_encode ( $result );
				die ();
			}
			// 判断密码是否一致
			if ($pwd != $repwd) {
				$result ['ret_num'] = 202;
				$result['ret_msg'] = $this->language->get('not_identical');
				echo json_encode ( $result );
				die ();
			}
			if (! $this->checkpwd ( $pwd )) {
				$result ['ret_num'] = 208;
				$result['ret_msg'] = $this->language->get('incorrect_pwd');
				echo json_encode ( $result );
				die ();
			}
			if (! $this->checkphone ( $phone )) {
				$result ['ret_num'] = 207;
				$result['ret_msg'] = $this->language->get('incorrect_mobile');
				echo json_encode ( $result );
				die ();
			}
			// 判断是否存在用户
			$user = TaidushopMember::model ()->find ( "member_mobile = '{$phone}'" );
			if (empty ( $user )) {
				$result ['ret_num'] = 306;
				$result['ret_msg'] = $this->language->get('member_lost');
				echo json_encode ( $result );
				die ();
			}
			
			// 判断手机号是否正确
			$codelist = TdVerify::model ()->find ( "phone='{$phone}' and type=2 order by created_time desc" );
			$code = "";
			if ($codelist) {
				$codetime = time () - ($codelist->created_time);
				$code = $codelist->verify;
				if ($codetime > 300) {
					$result ['ret_num'] = 203;
					$result['ret_msg'] = $this->language->get('code_expired');
					echo json_encode ( $result );
					die ();
				}
			} else {
				$result ['ret_num'] = 302;
				$result['ret_msg'] = $this->language->get('login_fail');
				echo json_encode ( $result );
				die ();
			}
			if ($code != $verify) {
				$result ['ret_num'] = 204;
				$result['ret_msg'] = $this->language->get('code_wrong');
				echo json_encode ( $result );
				die ();
			}
			
			//开启事物
			$transaction = Yii::app()->db->beginTransaction(); //创建事务
			
			// 修改密码
			$user->member_passwd = md5 ( $pwd );
			try{
				if ($user->update ()) {

					include_once '../../includes/bbs/config/config_ucenter.php';
					include_once '../../includes/bbs/uc_client/client.php';
					
					$bbsret = uc_user_edit($user->member_name, '', $pwd, $user->member_email, $ignoreoldpw = 1);
					if ($bbsret < 0) {
						$result ['ret_num'] = 906;
						$result['ret_msg'] = $this->language->get('update_fail');
						$transaction->rollback(); //回滚事务
					} else {
						// 修改密码后注销 cookie
						setcookie('code', '', time() - 3600);
						setcookie('receive', '', time() - 3600);
						unset($_SESSION ['code']);
						unset($_SESSION ['captcha_word']);
						
						$result ['ret_num'] = 0;
						$result ['ret_msg'] = 'ok';
						$transaction->commit(); //提交事务
					}
				}
			} catch(Exception $ex){
				$result ['ret_num'] = 906;
				$result['ret_msg'] = $this->language->get('update_fail');
				$transaction->rollback(); //回滚事务
			}
			
			echo json_encode ( $result );
		}
	}


	/**
	 * 检查验证码
	 */
	public function actionCheckVerificationCode() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		if ((Yii::app ()->request->isPostRequest)) {
			$this->check_key ();
			$verify = Frame::getStringFromRequest ( 'verify' );
			$phone = Frame::getStringFromRequest ( 'phone' );
			$type = Frame::getIntFromRequest ( 'type' );
			if (empty ( $verify ) || empty ( $phone ) || empty ( $type )) {
				$result ['ret_num'] = 201;
				$result['ret_msg'] = $this->language->get('miss_param');
				echo json_encode ( $result );
				die ();
			}
			
			// 判断手机号是否正确
			$codelist = TdVerify::model ()->find ( "phone='{$phone}' and type={$type} order by created_time desc" );
			$code = "";
			if ($codelist) {
				$codetime = time () - ($codelist->created_time);
				$code = $codelist->verify;
				if ($codetime > 300) {
					$result ['ret_num'] = 203;
					$result['ret_msg'] = $this->language->get('code_expired');
					echo json_encode ( $result );
					die ();
				}
			} else {
				$result ['ret_num'] = 305;
				$result['ret_msg'] = $this->language->get('verify_mismatching');
				echo json_encode ( $result );
				die ();
			}
			if ($code != $verify) {
				$result ['ret_num'] = 204;
				$result['ret_msg'] = $this->language->get('code_wrong');
				echo json_encode ( $result );
				die ();
			}
			
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'OK';
			echo json_encode ( $result );
		}
	}
	

	/**
	 * 修改密码
	 */
	public function actionChangepwd() {
		if ((Yii::app ()->request->isPostRequest)) {
			$this->check_key ();
			$oldpwd = Frame::getStringFromRequest ( 'oldpassword' );
			$newpwd = Frame::getStringFromRequest ( 'newpassword' );
			$newrepwd = Frame::getStringFromRequest ( 'newrepassword' );
			if (empty ( $oldpwd ) || empty ( $newpwd ) || empty ( $newrepwd )) {
				$result ['ret_num'] = 201;
				$result ['ret_msg'] = '输入信息不完整';
				echo json_encode ( $result );
				die ();
			}
			if ($newpwd != $newrepwd) {
				$result ['ret_num'] = 202;
				$result ['ret_msg'] = '两次输入密码不一致';
				echo json_encode ( $result );
				die ();
			}
			if (! $this->checkpwd ( $newpwd )) {
				$result ['ret_num'] = 208;
				$result ['ret_msg'] = '密码不符合';
				echo json_encode ( $result );
				die ();
			}

			// 获取用户信息
			$member_info = $this->check_user();

			if ($member_info->member_passwd == md5 ( $oldpwd )) {
				$member_info->member_passwd = md5 ( $newpwd );
				//开启事物
				$transaction = Yii::app()->db->beginTransaction(); //创建事务

				try{
					if ($member_info->update ()) {
						// 修改uccenter用户密码
						include_once '../../includes/bbs/config/config_ucenter.php';
						include_once '../../includes/bbs/uc_client/client.php';

						$bbsret = uc_user_edit($member_info->member_name, '', $newpwd, $member_info->member_email, $ignoreoldpw = 1);
						if ($bbsret < 0) {
							$result ['ret_num'] = 909;
							$result ['ret_msg'] = '数据更新失败';
							$transaction->rollback(); //回滚事务
						} else {
							// 修改密码后注销 cookie
							setcookie('code', '', time() - 3600);
							setcookie('receive', '', time() - 3600);
							unset($_SESSION ['code']);
							unset($_SESSION ['captcha_word']);

							$result ['ret_num'] = 0;
							$result ['ret_msg'] = '密码修改成功';
							$transaction->commit(); //提交事务
						}

						echo json_encode ( $result );
					}
				} catch(Exception $ex){
					$result ['ret_num'] = 909;
					$result ['ret_msg'] = '数据更新失败';
					$transaction->rollback(); //回滚事务
					echo json_encode ( $result );
				}
			} else {
				$result ['ret_num'] = 205;
				$result ['ret_msg'] = '输入的原密码不一致';
				echo json_encode ( $result );
				die ();
			}
		}
	}


	/**
	 * 修改用户资料
	 */
	public function actionChangeuser() {	
		$this->check_key ();
		// 头像
		$image = CUploadedFile::getInstanceByName ( 'header' );
		// 用户名
		$username = Frame::getStringFromRequest ( 'username' );
		// 性别
		$sex = Frame::getStringFromRequest ( 'sex' );
		// 生日
		$birthday = Frame::getStringFromRequest ( 'birthday' );
		// 游戏信息
		$gameInfo = Frame::getStringFromRequest ( 'gameInfo' );
		// 身高
		$height = Frame::getStringFromRequest ( 'height' );
		// 体重
		$weight = Frame::getStringFromRequest ( 'weight' );
		// 职业id
		$professionId = Frame::getIntFromRequest ( 'professionId' );
		// 婚姻状况
		$marriedType = Frame::getIntFromRequest ( 'marriedType' );
		// 兴趣爱好
		$interest = Frame::getStringFromRequest ( 'interest' );
		// 视力
		$vision = Frame::getStringFromRequest ( 'vision' );
		// 所在地
		$address = Frame::getStringFromRequest ( 'address' );
		if (empty ( $username )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}
		// 校验图片后缀名
		if ($image){
			$arr_img = array("bmp", "jpg", "jpeg", "gif", "png");
			if (!in_array(strtolower($image->getExtensionName()), $arr_img)){
				$result ['ret_num'] = 210;
				$result ['ret_msg'] = '上传的图片格式不正确';
				echo json_encode ( $result );
				die ();
			}
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		if ($image) {
			// 上传头像
			$path = $this->uploadPic ( $image );
			if (! $path) {
				$result ['ret_num'] = 908;
				$result ['ret_msg'] = '图片上传失败';
				echo json_encode ( $result );
				die ();
			}

			$member_info->member_avatar = $path;
		}

		//开启事物
		$transaction = Yii::app()->db->beginTransaction(); //创建事务

		TdUserGame::model()->deleteAll("userid={$member_id}");
		if ($gameInfo) {
			$arr = explode(";", $gameInfo);
			foreach($arr as $u){
				$model = new TdUserGame();
				if(empty($u)){
					continue;
				}

				$strarr = explode(",",$u);
				$model->userid = $member_id;
				$model->game_id = $strarr[0];
				$model->proficiency = $strarr[1];
				$model->created_time = time();
				if (!$model->save()){
					Yii::log("插入游戏失败：", "info", "jeff.test");
					$transaction->rollback(); //回滚事务

					$result ['ret_num'] = 909;
					$result ['ret_msg'] = '数据更新失败';
					echo json_encode ( $result );
					die ();
				}
			}
		}

		$member_info->member_name = $username;
		$member_info->address = $address;
		$member_info->member_sex = $sex;
		$member_info->member_birthday = $birthday;
		if (empty ($height)){
			$member_info->height = NULL;
		} else {
			$member_info->height = $height;
		}
		if (empty ($weight)){
			$member_info->weight = NULL;
		} else {
			$member_info->weight = $weight;
		}
		$member_info->profession_id = $professionId;
		$member_info->married_type = $marriedType;
		if (empty ($interest)){
			$member_info->interest = NULL;
		} else {
			$member_info->interest = $interest;
		}
		if (empty ($vision)){
			$member_info->vision = NULL;
		} else {
			$member_info->vision = $vision;
		}
		$member_info->member_login_time = time();
		if ($member_info->update ()) {
			Yii::log("更新个人资料成功：", "info", "jeff.test");
			$transaction->commit(); //提交事务
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
		} else {
			Yii::log("更新资料失败：", "info", "jeff.test");
			$transaction->rollback(); //回滚事务
			$result ['ret_num'] = 909;
			$result ['ret_msg'] = '数据更新失败';
		}

		echo json_encode ( $result );
	}
}
