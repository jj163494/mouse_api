<?php
class UserController extends PublicController {
	/**
	 * 用户注册
	 */
	public function actionRegister() {
		if ((Yii::app ()->request->isPostRequest)) {
			$this->check_key ();
			$key = Frame::getStringFromRequest ( 'key' );
			$username = Frame::getStringFromRequest ( 'username' );
			$phone = Frame::getStringFromRequest ( 'phone' );
			$birthday = Frame::getStringFromRequest ( 'birthday' );
			$sex = Frame::getIntFromRequest ( 'sex' );
			$pwd = Frame::getStringFromRequest ( 'password' );
			$repwd = Frame::getStringFromRequest ( 'repassword' );
			// 头像
			$image = CUploadedFile::getInstanceByName ( 'header' );
			// 职业id
			$professionId = Frame::getIntFromRequest ( 'professionId' );
			// 婚姻状况
			$marriedType = Frame::getIntFromRequest ( 'marriedType' );
			// 所在地
			$address = Frame::getStringFromRequest ( 'address' );
			
			// 判断是否填写完整
			if (empty ( $username ) || empty ( $pwd ) || empty ( $repwd ) || empty ( $phone )) {
				$result ['ret_num'] = 201;
				$result ['ret_msg'] = '输入信息不完整';
				echo json_encode ( $result );
				die ();
			}
			// 判断俩次密码输入是否一致
			if ($pwd != $repwd) {
				$result ['ret_num'] = 202;
				$result ['ret_msg'] = '两次密码输入不一致';
				echo json_encode ( $result );
				die ();
			}
			if (! $this->checkphone ( $phone )) {
				$result ['ret_num'] = 207;
				$result ['ret_msg'] = '手机号不符合';
				echo json_encode ( $result );
				die ();
			}
			if (! $this->checkpwd ( $pwd )) {
				$result ['ret_num'] = 208;
				$result ['ret_msg'] = '密码不符合';
				echo json_encode ( $result );
				die ();
			}
			
			// 校验图片后缀名
			if ($image) {
				$arr_img = array (
						"bmp",
						"jpg",
						"jpeg",
						"gif",
						"png" 
				);
				if (! in_array ( strtolower ( $image->getExtensionName () ), $arr_img )) {
					$result ['ret_num'] = 210;
					$result ['ret_msg'] = '上传的图片格式不正确';
					echo json_encode ( $result );
					die ();
				}
			}
			
			// 判断手机号有没有注册过
			$re = TaidushopMember::model ()->find ( "member_mobile = {$phone}" );
			if ($re) {
				$result ['ret_num'] = 301;
				$result ['ret_msg'] = '该手机号码已注册';
				echo json_encode ( $result );
				die ();
			}
			
			// 判断用户名有没有注册过
			$re = TaidushopMember::model ()->find ( "member_name = '{$username}'" );
			if ($re) {
				$result ['ret_num'] = 315;
				$result ['ret_msg'] = '该用户名已被注册';
				echo json_encode ( $result );
				die ();
			}
			
			// 开启事物
			$transaction = Yii::app ()->db->beginTransaction (); // 创建事务
			
			$user = new TaidushopMember ();
			$user->member_name = $username;
			$user->member_mobile = $phone;
			$user->member_birthday = $birthday;
			$user->member_sex = $sex;
			$user->member_passwd = md5 ( $pwd );
			$user->address = $address;
			$user->profession_id = $professionId;
			$user->married_type = $marriedType;
			$user->member_time = time ();
			$user->member_login_time = time ();
			$user->member_login_ip = $_SERVER ['SERVER_ADDR'];
			$user->member_mobile_bind = 1;
			if ($image) {
				Yii::log ( "开始上传头像：", "info", "jeff.test" );
				// 上传头像
				$path = $this->uploadPic ( $image );
				if (! $path) {
					Yii::log ( "上传头像失败：", "info", "jeff.test" );
					$result ['ret_num'] = 908;
					$result ['ret_msg'] = '图片上传失败';
					echo json_encode ( $result );
					die ();
				}
				
				$user->member_avatar = $path;
				$headImg = $user->member_avatar;
			}else{
				$headImg = '';
			}

			if ($user->save ()) {
				// 注册成功后插入member_common表
				$insert_id = Yii::app ()->db->getLastInsertID();

				$member_common_sql = "INSERT INTO taidushop_member_common (member_id) VALUES (:member_id)";
				$member_common_result = Yii::app ()->db->createCommand($member_common_sql)->query(array(
					':member_id' => $insert_id,
				));

				if(empty($member_common_result)){
					$result ['ret_num'] = 901;
					$result ['ret_msg'] = '信息添加失败	';
					$transaction->rollback(); //回滚事务
				}

				// 生成欢迎消息
				$message = new TdMessage ();
				$message->userid = $user->member_id;
				$message->title = "";
				$message->message = "亲爱的" . $user->member_name . "，我是钛斯基，欢迎加入钛度车队。
先简单介绍一下新版钛度电竞的功能
工具功能，需要钛度系装备绑定联合使用，想要在游戏中提高致命伤害，还不快快用起来？
发现功能，关注钛度新动态，陆续还会有更多好玩的功能等着大家哦~
我的功能，查看自己的相关信息。
好啦，就酱，赶紧去尝鲜一下吧！
最后钛斯基希望能把你培养成电竞大神中的一员，让你在任何类型的电竞游戏中脱颖而出，驰骋电竞场！";
				$message->message_type = 1;
				$message->send_userid = 2;
				$message->send_username = "钛斯基";
				$message->is_read = 0;
				$message->created_time = time ();
				$message->save ();
				
				// 注册成功后进行登录
				// 更改openid的生成规则 bug#316 2016/11/14
				$openid = md5( uniqid( $insert_id.'_'.time(), true) );
				$user->openid = $openid;
				$user->update ();
				// 写session
				Yii::app ()->session ['openid'] = $openid;
				
				// 返回用户信息
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result ['user'] = array (
						"memberid" => $user->member_id,
						"username" => $user->member_name,
						"realname" => $user->member_truename,
						'mobile' => $user->member_mobile,
						'address' => $this->eraseNull ( $user->address ),
						'email' => $this->eraseNull ( $user->member_email ),
						'usertype' => $user->usertype,
						'sex' => $user->member_sex,
						'birthday' => $this->eraseNull ( $user->member_birthday ),
					/*'idCard' => $user->idCard,
					'isCanEmail' =>$user->isCanEmail,
					'isCanSms' => $user->isCanSms,
					'isValSms' =>$user->isValSms,
					'isValEmail' =>$user->isValEmail,
					'isValCard' =>$user->isValCard,
					'jifen' =>$user->jifen,
					
					'sbs_id' => $user->sbs_id,*/
					'header' => $headImg,
						'openid' => $user->openid,
						'regip' => $user->member_login_ip,
						'regtime' => $user->member_time,
						'updateTime' => $user->member_login_time 
				);
				
				$email_array = array (
						'yourdomain.com',
						'yourdomain.cn' 
				);
				$rand_email = substr ( md5 ( $username ), 8, 16 ) . "@" . $email_array [rand ( 0, 1 )];
				$email = isset ( $_POST ['email'] ) ? $_POST ['email'] : $rand_email;
				
				Yii::log ( "开始同步uccenter", "info", "jeff.test" );
				
				include_once '../../includes/bbs/config/config_ucenter.php';
				include_once '../../includes/bbs/uc_client/client.php';
				// 论坛注册

				$uid = uc_user_register ( $username, $pwd, $email );
				if ($uid < 1) {
					Yii::log ( "论坛注册失败,uid:" . $uid, "info", "jeff.test" );
					if ($uid == - 1) {
						// echo '用户名不合法';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 316;
						$result ['ret_msg'] = '用户名不合法,论坛注册失败';
					} elseif ($uid == - 2) {
						// echo '包含要允许注册的词语';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 317;
						$result ['ret_msg'] = '包含敏感字符,论坛注册失败';
					} elseif ($uid == - 3) {
						// echo '用户名已经存在';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 315;
						$result ['ret_msg'] = '用户名已经存在,论坛注册失败';
					} else {
						// echo '未定义';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 318;
						$result ['ret_msg'] = '未定义,论坛注册失败';
					}
					// Yii::log("论坛注册失败,uid:".$uid, "info", "jeff.test");
					// $result ['ret_num'] = 301;
					// $result ['ret_msg'] = '论坛注册失败';
				} else {

					$transaction->commit (); // 提交事务
					Yii::log ( "论坛注册成功,uid:" . $uid, "info", "jeff.test" );
					$lifetime = 31536000;
					session_set_cookie_params ( $lifetime );
					if (! isset ( $_SESSION )) {
						session_start ();
					}
					session_regenerate_id ( true );
					$_SESSION ['user_id'] = $user->member_id;
					$_SESSION ['user_name'] = $username;
					$_SESSION ['email'] = $email;

					Yii::log ( "开始uc_user_login", "info", "jeff.test" );
					// 登录
					list ( $uid, $username, $pwd, $email ) = uc_user_login ( $username, $pwd );
					if ($uid > 0) {
						Yii::log ( "uid >0,开始uc_user_synlogin", "info", "jeff.test" );
						// $ucsynlogin = uc_user_synlogin ( $uid );
						// $_SESSION ['td_bbs_login'] = $ucsynlogin;
						// // 新增的start
						// $time = time () + $lifetime;
						// setcookie ( "username", $username, $time, "/", "");
						// setcookie ( "user_id", $user->member_id, $time, "/", "");
						// setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );

						$ucsynlogin = uc_user_synlogin ( $uid );
						$_SESSION ['td_bbs_login'] = $ucsynlogin;

						$_SESSION ['user_id'] = $user->member_id;
						$_SESSION ['user_name'] = $user->member_name;
						$_SESSION ['email'] = $this->eraseNull ( $user->member_email );
						$time = time () + $lifetime;
						setcookie ( "username", $user->member_name, $time, "/", "" );
						setcookie ( "user_id", $user->member_id, $time, "/", "" );
						setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );

						$time = time () + 3600 * 24 * 15;
						setcookie ( "ECS[username]", $username, $time, "/", "" );
						setcookie ( "ECS[user_id]", $user->member_id, $time, "/", "" );
						setcookie ( "ECS[password]", $pwd, $time, "/", "" );
					}

					// 登陆后注销cookie
					setcookie ( 'mobile', '', time () - 3600 );
					setcookie ( 'code', '', time () - 3600 );
					unset ( $_SESSION ['code'] );
					unset ( $_SESSION ['captcha_word'] );

					Yii::log ( "uccenter同步完成", "info", "jeff.test" );
				}
				mysql_query ( "END" );
			} else {
				$result ['ret_num'] = 901;
				$result ['ret_msg'] = '信息添加失败	';

				$transaction->rollback(); //回滚事务
			}
			echo json_encode ( $result );
		}
	}
	
	/**
	 * 用户注册(仅供内部使用)
	 */
	public function actionRegisterTestUser() {
		if ((Yii::app ()->request->isPostRequest)) {
			$this->check_key ();
			$key = Frame::getStringFromRequest ( 'key' );
			$username = Frame::getStringFromRequest ( 'username' );
			$phone = Frame::getStringFromRequest ( 'phone' );
			$birthday = Frame::getStringFromRequest ( 'birthday' );
			$sex = Frame::getIntFromRequest ( 'sex' );
			$pwd = Frame::getStringFromRequest ( 'password' );
			$repwd = Frame::getStringFromRequest ( 'repassword' );
			$verify = Frame::getStringFromRequest ( 'code' );
			// 判断是否填写完整
			if (empty ( $username ) || empty ( $pwd ) || empty ( $repwd )) {
				$result ['ret_num'] = 201;
				$result ['ret_msg'] = '输入信息不完整';
				echo json_encode ( $result );
				die ();
			}
			// 判断俩次密码输入是否一致
			if ($pwd != $repwd) {
				$result ['ret_num'] = 202;
				$result ['ret_msg'] = '两次密码输入不一致';
				echo json_encode ( $result );
				die ();
			}
			
			if (! $this->checkpwd ( $pwd )) {
				$result ['ret_num'] = 208;
				$result ['ret_msg'] = '密码不符合';
				echo json_encode ( $result );
				die ();
			}
			
			if ("#td#" != $verify) {
				$result ['ret_num'] = 204;
				$result ['ret_msg'] = '验证码错误';
				echo json_encode ( $result );
				die ();
			}
			
			// 判断用户名有没有注册过
			$re = TaidushopMember::model ()->find ( "member_name = '{$username}'" );
			if ($re) {
				$result ['ret_num'] = 315;
				$result ['ret_msg'] = '该用户名已被注册';
				echo json_encode ( $result );
				die ();
			}
			
			// 开启事物
			$transaction = Yii::app ()->db->beginTransaction (); // 创建事务
			
			$user = new TaidushopMember ();
			$user->member_name = $username;
			$user->member_mobile = $phone;
			$user->member_birthday = $birthday;
			$user->member_sex = $sex;
			$user->member_passwd = md5 ( $pwd );
			$user->member_time = time ();
			$user->member_login_time = time ();
			$user->member_login_ip = $_SERVER ['SERVER_ADDR'];
			if ($user->save ()) {
				// 注册成功后进行登录
				// 生成openid
				$openid = md5 ( time () );
				$user->openid = $openid;
				$user->update ();
				// 写session
				Yii::app ()->session ['openid'] = $openid;
				
				// 返回用户信息
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				$result ['user'] = array (
						"memberid" => $user->member_id,
						"username" => $user->member_name,
						"realname" => $user->member_truename,
						'mobile' => $user->member_mobile,
						'address' => $this->eraseNull ( $user->address ),
						'email' => $this->eraseNull ( $user->member_email ),
						'usertype' => $user->usertype,
						'sex' => $user->member_sex,
						'birthday' => $this->eraseNull ( $user->member_birthday ),
						/*'idCard' => $user->idCard,
						 'isCanEmail' =>$user->isCanEmail,
				'isCanSms' => $user->isCanSms,
				'isValSms' =>$user->isValSms,
				'isValEmail' =>$user->isValEmail,
				'isValCard' =>$user->isValCard,
				'jifen' =>$user->jifen,
					
				'sbs_id' => $user->sbs_id,*/
						'openid' => $user->openid,
						'regip' => $user->member_login_ip,
						'regtime' => $user->member_time,
						'updateTime' => $user->member_login_time 
				);
				
				$email_array = array (
						'yourdomain.com',
						'yourdomain.cn' 
				);
				$rand_email = substr ( md5 ( $username ), 8, 16 ) . "@" . $email_array [rand ( 0, 1 )];
				$email = isset ( $_POST ['email'] ) ? $_POST ['email'] : $rand_email;
				
				Yii::log ( "开始同步uccenter", "info", "jeff.test" );
				
				include_once '../../includes/bbs/config/config_ucenter.php';
				include_once '../../includes/bbs/uc_client/client.php';
				// 论坛注册
				
				$uid = uc_user_register ( $username, $pwd, $email );
				if ($uid < 1) {
					Yii::log ( "论坛注册失败,uid:" . $uid, "info", "jeff.test" );
					if ($uid == - 1) {
						// echo '用户名不合法';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 316;
						$result ['ret_msg'] = '用户名不合法,论坛注册失败';
					} elseif ($uid == - 2) {
						// echo '包含要允许注册的词语';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 317;
						$result ['ret_msg'] = '包含要允许注册的词语,论坛注册失败';
					} elseif ($uid == - 3) {
						// echo '用户名已经存在';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 315;
						$result ['ret_msg'] = '用户名已经存在,论坛注册失败';
					} else {
						// echo '未定义';
						$transaction->rollback (); // 回滚事务
						$result ['ret_num'] = 318;
						$result ['ret_msg'] = '未定义,论坛注册失败';
					}
					// Yii::log("论坛注册失败,uid:".$uid, "info", "jeff.test");
					// $result ['ret_num'] = 301;
					// $result ['ret_msg'] = '论坛注册失败';
				} else {
					
					$transaction->commit (); // 提交事务
					Yii::log ( "论坛注册成功,uid:" . $uid, "info", "jeff.test" );
					$lifetime = 31536000;
					session_set_cookie_params ( $lifetime );
					if (! isset ( $_SESSION )) {
						session_start ();
					}
					session_regenerate_id ( true );
					$_SESSION ['user_id'] = $user->member_id;
					$_SESSION ['user_name'] = $username;
					$_SESSION ['email'] = $email;
					
					Yii::log ( "开始uc_user_login", "info", "jeff.test" );
					// 登录
					list ( $uid, $username, $pwd, $email ) = uc_user_login ( $username, $pwd );
					if ($uid > 0) {
						Yii::log ( "uid >0,开始uc_user_synlogin", "info", "jeff.test" );
						// $ucsynlogin = uc_user_synlogin ( $uid );
						// $_SESSION ['td_bbs_login'] = $ucsynlogin;
						// // 新增的start
						// $time = time () + $lifetime;
						// setcookie ( "username", $username, $time, "/", "");
						// setcookie ( "user_id", $user->member_id, $time, "/", "");
						// setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );
						
						$ucsynlogin = uc_user_synlogin ( $uid );
						$_SESSION ['td_bbs_login'] = $ucsynlogin;
						
						$_SESSION ['user_id'] = $user->member_id;
						$_SESSION ['user_name'] = $user->member_name;
						$_SESSION ['email'] = $this->eraseNull ( $user->member_email );
						$time = time () + $lifetime;
						setcookie ( "username", $user->member_name, $time, "/", "" );
						setcookie ( "user_id", $user->member_id, $time, "/", "" );
						setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );
						
						$time = time () + 3600 * 24 * 15;
						setcookie ( "ECS[username]", $username, $time, "/", "" );
						setcookie ( "ECS[user_id]", $user->member_id, $time, "/", "" );
						setcookie ( "ECS[password]", $pwd, $time, "/", "" );
					}
					
					// 登陆后注销cookie
					setcookie ( 'mobile', '', time () - 3600 );
					setcookie ( 'code', '', time () - 3600 );
					unset ( $_SESSION ['code'] );
					unset ( $_SESSION ['captcha_word'] );
					
					Yii::log ( "uccenter同步完成", "info", "jeff.test" );
				}
				mysql_query ( "END" );
			} else {
				$result ['ret_num'] = 901;
				$result ['ret_msg'] = '信息添加失败	';
			}
			echo json_encode ( $result );
		}
	}

	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v1_1/user/login",
	 *     summary="1.用户登录",
	 *     tags={"User"},
	 *     description="用户登录,返回用户信息",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="key",
	 *         in="query",
	 *         description="系统类型,iphone,android,pc",
	 *         required=true,
	 *         type="string",
	 *		   default="pc",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="phone",
	 *         in="query",
	 *         description="用户名",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="password",
	 *         in="query",
	 *         description="密码",
	 *         required=true,
	 *         type="string",
	 *		   format="password",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户信息",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 * 			       property="user",
	 *                 description="用户信息",
	 *                 type="array",
	 *				   ref="#/definitions/User"
	 *			   ),
	 *		   )
	 *     ),
	 *     @SWG\Response(
	 *         response="208",
	 *         description="密码不符合",
	 *     ),
	 *     @SWG\Response(
	 *         response="209",
	 *         description="请输入登陆密码",
	 *     ),
	 *     @SWG\Response(
	 *         response="302",
	 *         description="账号或密码错误",
	 *     ),
	 * )
	 */
	public function actionLogin() {
		// 调用public控制器中的check_key()获得key
		$this->check_key ();
		// 手机号，密码
		$key = Frame::getStringFromRequest ( 'key' );
		$phone = Frame::getStringFromRequest ( 'phone' );
		$password = Frame::getStringFromRequest ( 'password' );

		if (empty ( $password )) {
			$result ['ret_num'] = 209;
			$result ['ret_msg'] = '请输入登陆密码';
			echo json_encode ( $result );
			die ();
		}

		if (! $this->checkpwd ( $password )) {
			$result ['ret_num'] = 208;
			$result ['ret_msg'] = '密码不符合';
			echo json_encode ( $result );
			die ();
		}

		// 验证手机号密码是否匹配
		$pwd = md5 ( $password );
		$user = TaidushopMember::model ()->find ( "(member_mobile = '{$phone}' or member_email = '{$phone}' or member_name = '{$phone}')  and member_passwd = '{$pwd}'" );
		if (empty ( $user )) {
			$result ['ret_num'] = 302;
			$result ['ret_msg'] = '账号或密码错误';
		} else {
			// 更改openid的生成规则 bug#316 2016/11/14
			$openid = md5( uniqid( $user['member_id'].'_'.time(), true) );
			$user->openid = $openid;
			$user->update ();

			// 写session
			Yii::app ()->session ['openid'] = $openid;
			// 返回用户信息
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			if ($user->member_avatar) {
				if (strpos ( $user->member_avatar, "http://" ) === 0) {
					$headImg = $user->member_avatar;
				} else {
					$headImg = $this->webroot () . $user->member_avatar;
				}
			}else{
				$headImg = '';
			}
			$result ['user'] = array (
				"memberid" => $user->member_id,
				"username" => $user->member_name,
				"realname" => $user->member_truename,
				'mobile' => $user->member_mobile,
				'address' => $this->eraseNull ( $user->address ),
				'email' => $this->eraseNull ( $user->member_email ),
				'usertype' => $user->usertype,
				'sex' => $user->member_sex,
				'birthday' => $this->eraseNull ( $user->member_birthday ),
				/*'idCard' => $user->idCard,
                'isCanEmail' =>$user->isCanEmail,
                'isCanSms' => $user->isCanSms,
                'isValSms' =>$user->isValSms,
                'isValEmail' =>$user->isValEmail,
                'isValCard' =>$user->isValCard,
                'jifen' =>$user->jifen,
                'sbs_id' => $user->sbs_id,	*/
				'regtime' => $user->member_time,
				'header' => $headImg,
				'game' => $user->game,
				'regip' => $_SERVER ['SERVER_ADDR'],
				'openid' => $user->openid,
				'updateTime' => $user->member_login_time,
				'level' => 2
			);
			/*
			 * $lifetime = 604800; session_set_cookie_params($lifetime); if (!isset($_SESSION)) { session_start(); } session_regenerate_id(true); include_once '../../includes/bbs/config/config_ucenter.php'; include_once '../../includes/bbs/uc_client/client.php'; //登录 list($uid, $username, $password, $email) = uc_user_login($user->username, $password); if ($uid > 0) { $ucsynlogin = uc_user_synlogin($uid); $_SESSION['td_bbs_login'] = $ucsynlogin; $_SESSION['user_id'] = $user->member_id; $_SESSION['user_name'] = $user->username; $_SESSION['email'] = $this->eraseNull($user->email); $time = time() + $lifetime; setcookie("username", $user->username, $time, "/", ""); setcookie("user_id", $user->member_id, $time, "/", ""); setcookie("td_bbs_login", $ucsynlogin, $time, "/", ""); $time = time() + 3600 * 24 * 15; setcookie("ECS[username]", $username, $time, "/", ""); setcookie("ECS[user_id]", $user->member_id, $time, "/", ""); setcookie("ECS[password]", $password, $time, "/", ""); } else { // 摧毁cookie $time = time() - 3600; setcookie("ECS[user_id]", '', $time, "/"); setcookie("ECS[password]", '', $time, "/"); setcookie("username", '', $time, "/"); setcookie("user_id", '', $time, "/"); }
			 */
		}
		echo json_encode ( $result );
	}
	
	/*
	 * 退出登录
	 */
	public function actionLogout() {
		// 获取用户信息
		$member_info = $this->check_user();

		include_once '../../includes/bbs/config/config_ucenter.php';
		include_once '../../includes/bbs/uc_client/client.php';

		$time = time () - 3600;
		setcookie ( "username", '', $time, '/' );
		setcookie ( "user_id", '', $time, '/' );
		setcookie ( "td_bbs_login", '', $time, '/' );

		$ret = uc_user_synlogout ();

		$_SESSION ['td_bbs_logout'] = $ret;

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';

		echo json_encode ( $result );
	}
	
	/**
	 * 获取验证码
	 */
	public function actionVerify() {
		$phone = Frame::getStringFromRequest ( 'phone' );
		$type = Frame::getStringFromRequest ( 'type' );
		// 判断是否为空
		if (empty ( $phone )) {
			$result ['ret_num'] = 209;
			$result ['ret_msg'] = '请输入手机号';
			echo json_encode ( $result );
			die ();
		}
		if (! $this->checkphone ( $phone )) {
			$result ['ret_num'] = 207;
			$result ['ret_msg'] = '手机号不符合';
			echo json_encode ( $result );
			die ();
		}
		// 判断手机号是否存在
		$mobile_exist = TaidushopMember::model ()->find ( "member_mobile='{$phone}'" );
		$apikey = APIKEY; // 请用自己的apikey代替
		                  // 生成验证码
		$code = rand ( 1000, 9999 );
		// 判断用户是注册还是忘记密码
		if ($type == 1) {
			$text = "【钛度科技】感谢您注册钛度账号，您的验证码是{$code}，在5分钟内有效。如非本人操作请忽略本短信。";
			if ($mobile_exist == null) {
				// 短信接口发送
				$result = $this->send_sms ( $apikey, $text, $phone ); // 返回值
				$result = json_decode ( $result, true );
				// 判断返回值是否正确
				if ($result && strtolower ( $result ['msg'] ) == "ok") {
					// 成功生成验证码后加入数据库
					$model = new TdVerify ();
					$model->phone = $phone;
					$model->verify = $code;
					$model->created_time = time ();
					$model->type = $type;
					$model->save ();
					// $result['verify'] = $code;
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 902;
					$result ['ret_msg'] = '短信接口出现错误';
				}
			} else {
				$result ['ret_num'] = 303;
				$result ['ret_msg'] = '号码已注册';
			}
			echo json_encode ( $result );
		} // 忘记密码 生成验证码
		if ($type == 2) {
			$text = "【钛度科技】您的验证码是{$code}，在5分钟内有效。如非本人操作请忽略本短信。";
			if ($mobile_exist) {
				// 短信接口发送
				$result = $this->send_sms ( $apikey, $text, $phone ); // 返回值
				$result = json_decode ( $result, true );
				// 判断返回值是否正确
				if ($result && strtolower ( $result ['msg'] ) == "ok") {
					// 成功生成验证码后加入数据库
					$model = new TdVerify ();
					$model->phone = $phone;
					$model->verify = $code;
					$model->created_time = time ();
					$model->type = $type;
					$model->save ();
					// $result['verify'] = $code;
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 902;
					$result ['ret_msg'] = '短信接口出现错误';
				}
			} else {
				$result ['ret_num'] = 305;
				$result ['ret_msg'] = '手机号未注册';
			}
			echo json_encode ( $result );
		}
	}

	/**
	 * 绑定设备
	 */
	public function actionBindDevice() {
		$openId = Frame::getStringFromRequest ( 'openId' );
		$deviceCode = Frame::getStringFromRequest ( 'deviceCode' );
		$deviceType = Frame::getIntFromRequest ( 'deviceType' );
		if (empty ( $openId ) || empty ( $deviceCode ) || empty ( $deviceType )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		$user = TaidushopMember::model ()->find ( "openid='{$openId}'" );
		$userId = $user->member_id;
		if ($user) {
			$bindDevice = TdUserDevice::model ()->find ( "userId={$userId} && device_code='{$deviceCode}'" );
			if (empty ( $bindDevice )) {
				$model = new TdUserDevice ();
				$model->userId = $userId;
				$model->device_code = $deviceCode;
				$model->device_type = $deviceType;
				$model->created_time = time ();
				if ($model->save ()) {
					// 查找设备表中有没有该设备
					$device = TdDevice::model ()->find ( "device_code='{$deviceCode}'" );
					if (! $device) {
						// 插入
						$modelDevice = new TdDevice ();
						if ($deviceType == 1) {
							$modelDevice->device_name = $user->member_name . "的鼠标";
						} elseif ($deviceType == 2) {
							$modelDevice->device_name = $user->member_name . "的键盘";
						} elseif ($deviceType == 3) {
							$modelDevice->device_name = $user->member_name . "的300鼠标";
						} elseif ($deviceType == 4) {
							$modelDevice->device_name = $user->member_name . "的600鼠标";
						}
						$modelDevice->device_code = $deviceCode;
						$modelDevice->device_type = $deviceType;
						$modelDevice->created_time = time ();
						$modelDevice->save ();
					}
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 903;
					$result ['ret_msg'] = '绑定失败';
				}
			} else {
				$result ['ret_num'] = 304;
				$result ['ret_msg'] = '该设备已被别的用户绑定，请先解绑！';
			}
		} else {
			$result ['ret_num'] = 306;
			$result ['ret_msg'] = '用户不存在';
		}
		echo json_encode ( $result );
	}


	/**
	 * 修改设备名称
	 */
	public function actionUpdateDeviceName(){
		$openId = Frame::getStringFromRequest ( 'openId' );
		$deviceCode = Frame::getStringFromRequest ( 'deviceCode' );
		$device_name = Frame::getStringFromRequest ( 'deviceName' );
		if (empty ( $openId ) || empty ( $deviceCode ) || empty ( $device_name )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}
		$user=TaidushopMember::model()->find(" openid='{$openId}'");
		$userId = $user->member_id;
		if ($user){
			$find_devicename=TdUserDevice::model()->find(" userid='{$userId}' && device_code='{$deviceCode}'");
			if ($find_devicename){
				$find_devicename->device_nickname=$device_name;
				if($find_devicename->update()){
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				}
			}else {
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
			}
			$find_devicenickname=TdEquipmentData::model()->find(" userid='{$userId}' && device_id='{$deviceCode}'");
			if ($find_devicenickname){
				$find_devicenickname->device_name=$device_name;
				if($find_devicename->update()){
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				}
			}else {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			}
		}else {
			$result ['ret_num'] = 306;
			$result ['ret_msg'] = '用户不存在或用户已在其他设备登录，请重新登录';
		
		}	
		echo json_encode ( $result );
	}


	/**
	 * 解除设备
	 */
	public function actionRemoveDevice() {
		$openId = Frame::getStringFromRequest ( 'openId' );
		$deviceCode = Frame::getStringFromRequest ( 'deviceCode' );
		if (empty ( $openId ) || empty ( $deviceCode )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}
		
		$user = TaidushopMember::model ()->find ( "openid='{$openId}'" );
		$userId = $user->member_id;
		if ($user) {
			$count = TdUserDevice::model ()->deleteAll ( "userId={$userId} && device_code='{$deviceCode}'" );
			if ($count > 0) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 904;
				$result ['ret_msg'] = '解绑失败';
			}
			echo json_encode ( $result );
		} else {
			$result ['ret_num'] = 306;
			$result ['ret_msg'] = '用户不存在或用户已在其他设备登录，请重新登录';
			echo json_encode ( $result );
		}
	}
	
	/**
	 * 23.获取已绑定的设备
	 */
	public function actionGetUserDevices() {
		$open_id = Frame::getStringFromRequest ( 'openId' );
		if (empty ( $open_id )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		$user_info = TaidushopMember::model ()->find ( "openid='{$open_id}'" );
		$userId = $user_info['member_id'];
		if ($user_info) {
			$user_device_list = array();

			// 获取设备数据
			$ud_table_name = TdUserDevice::model()-> tableName();
			$d_table_name = TdDevice::model()-> tableName();
			$where = "userId = {$userId}";
			$sql = "SELECT ud.*, d.* FROM {$ud_table_name} AS ud LEFT JOIN {$d_table_name} AS d ON ud.device_code = d.device_code WHERE {$where} ORDER BY ud.id DESC";

			$command = Yii::app ()->db->createCommand($sql);
			$data_list = $command->queryAll();
			if ($data_list) {
				foreach ( $data_list as $key => $user_device_info ) {
					if(!empty($user_device_info['device_name'])){
						// 设备名称不存在的话不显示
						$user_device_list[] = array (
							"device_code" => $user_device_info['device_code'],					// 设备编号
							"device_type" => $user_device_info['device_type'],					// 设备类型
							"device_name" => $user_device_info['device_name'],					// 设备名称
							"device_state" => 0													// 设备状态
						);
					}
				}
			}

			// 没有数据的情况返回错误信息
			if(empty($user_device_list)){
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
				echo json_encode ( $result );
				die ();
			}

			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['user_device_list'] = $user_device_list;
		} else {
			$result ['ret_num'] = 306;
			$result ['ret_msg'] = '用户不存在或用户已在其他设备登录，请重新登录';
		}
		echo json_encode ( $result );
	}
	
	/**
	 * 取得蓝牙设备名称
	 */
	public function actionGetDeviceName() {
		$deviceCode = Frame::getStringFromRequest ( 'deviceCode' );
		if (empty ( $deviceCode )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}	
			$device = TdDevice::model ()->find ( "device_code='{$deviceCode}'" );
			if ($device) {
				$result ['device_name'] = $device->device_name;
				$result ['device_type'] = $device->device_type;
			} else {
				$result ['device_name'] = "";
				$result ['device_type'] = "";
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';		
			echo json_encode ( $result );
		
	}

	
	/**
	 * 取得用户信息
	 */
	public function actionGetUserInfo() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取游戏信息
		$userGames = TdUserGame::model ()->findAll ( "userid='{$member_id}'" );
		$arr = array();
		if ($userGames) {
			$games = TdGame::model ()->findAll ();
			foreach ( $userGames as $key => $value ) {
				foreach ( $games as $key1 => $value1 ) {
					if ($value1->id == $value->game_id) {
						$arr [] = array (
								"game_type" => $value1->game_type,
								"game_name" => $value1->game_name,
								"proficiency" => $value->proficiency
						);
						break;
					}
				}
			}
		}

		if ($member_info->member_avatar) {
			if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
				$headImg = $member_info->member_avatar;
			} else {
				$headImg = $this->webroot () . $member_info->member_avatar;
			}
		}else{
			$headImg = "";
		}

		// 获取职业信息
		$professions = TdProfession::model ()->findAll ();
		if ($professions) {
			foreach ( $professions as $key => $value ) {
				if ($value->id == $member_info->profession_id) {
					$profession = $value->profession;
					break;
				}
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['user'] = array (
				"memberid" => $member_id,
				"username" => $member_info->member_name,
				"realname" => $member_info->member_truename,
				'mobile' => $member_info->member_mobile,
				'address' => $this->eraseNull ( $member_info->address ),
				'email' => $this->eraseNull ( $member_info->member_email ),
				'usertype' => $member_info->usertype,
				'sex' => $member_info->member_sex,
				'birthday' => $this->eraseNull ( $member_info->member_birthday ),
				'header' => $headImg,
				'height' => $member_info->height,
				'weight' => $member_info->weight,
				'gameInfo' => $arr,
				'profession' => $profession,
				'married_type' => $member_info->married_type,
				'interest' => $member_info->interest,
				'vision' => $member_info->vision,
				'regtime' => $member_info->member_time
		);

		echo json_encode ( $result );
	}
	
	/**
	 * 取得所有游戏
	 */
	public function actionGetGames() {
		// 获取用户信息
		$member_info = $this->check_user();

		$games = TdGame::model ()->findAll ();
		if ($games) {
			foreach ( $games as $key => $value ) {
				$arr [] = array (
						"game_id" => $value->id,
						"game_type" => $value->game_type,
						"game_name" => $value->game_name
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['games'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 取得所有职业
	 */
	public function actionGetProfessions() {
		$professions = TdProfession::model ()->findAll ();
		if ($professions) {
			foreach ( $professions as $key => $value ) {
				$arr [] = array (
						"id" => $value->id,
						"profession" => $value->profession 
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['professions'] = $arr;
		
		echo json_encode ( $result );
	}
	
	/**
	 * 取得所有QA列表
	 */
	public function actionGetQAList() {
		$softType = Frame::getStringFromRequest ( 'softType' );
		if (empty ( $softType )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();

		$qas = TdQa::model ()->findAll ( "soft_type={$softType}" );
		if ($qas) {
			foreach ( $qas as $key => $value ) {
				$arr [] = array (
						"question" => $value->question,
						"answer" => $value->answer
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['qas'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 取得所有问题列表
	 */
	public function actionGetFeedbackList() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$feedbacks = TdFeedback::model ()->findAll ( "userid={$member_id} " );
		if ($feedbacks) {
			foreach ( $feedbacks as $key => $value ) {
				if ($value->image1) {
					if (strpos ( $value->image1, "http://" ) === 0)
						$questionImg1 = $value->image1;
					else
						$questionImg1 = $this->webroot () . $value->image1;
				} else {
					$questionImg1 = "";
				}
				if ($value->image2) {
					if (strpos ( $value->image2, "http://" ) === 0)
						$questionImg2 = $value->image2;
					else
						$questionImg2 = $this->webroot () . $value->image2;
				} else {
					$questionImg2 = "";
				}
				if ($value->image3) {
					if (strpos ( $value->image3, "http://" ) === 0)
						$questionImg3 = $value->image3;
					else
						$questionImg3 = $this->webroot () . $value->image3;
				} else {
					$questionImg3 = "";
				}

				$arr [] = array (
						"id" => $value->id,
						"question" => $value->question,
						"question_type" => $value->question_type,
						"image1" => $questionImg1,
						"image2" => $questionImg2,
						"image3" => $questionImg3,
						"created_time" => $value->created_time
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['feedbacks'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 问题反馈
	 */
	public function actionQuestionFeedback() {
		$questionType = Frame::getStringFromRequest ( 'questionType' );
		$question = Frame::getStringFromRequest ( 'question' );
		$image1 = CUploadedFile::getInstanceByName ( 'image1' );
		$image2 = CUploadedFile::getInstanceByName ( 'image2' );
		$image3 = CUploadedFile::getInstanceByName ( 'image3' );
		if (empty ( $questionType ) || empty ( $question )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}
		
		// 校验图片后缀名
		if ($image1) {
			$arr_img = array (
					"bmp",
					"jpg",
					"jpeg",
					"gif",
					"png" 
			);
			if (! in_array ( strtolower ( $image1->getExtensionName () ), $arr_img )) {
				$result ['ret_num'] = 210;
				$result ['ret_msg'] = '上传的图片格式不正确';
				echo json_encode ( $result );
				die ();
			}
		}
		if ($image2) {
			$arr_img = array (
					"bmp",
					"jpg",
					"jpeg",
					"gif",
					"png" 
			);
			if (! in_array ( strtolower ( $image2->getExtensionName () ), $arr_img )) {
				$result ['ret_num'] = 210;
				$result ['ret_msg'] = '上传的图片格式不正确';
				echo json_encode ( $result );
				die ();
			}
		}
		if ($image3) {
			$arr_img = array (
					"bmp",
					"jpg",
					"jpeg",
					"gif",
					"png" 
			);
			if (! in_array ( strtolower ( $image3->getExtensionName () ), $arr_img )) {
				$result ['ret_num'] = 210;
				$result ['ret_msg'] = '上传的图片格式不正确';
				echo json_encode ( $result );
				die ();
			}
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		if ($image1) {
			// 上传图片
			$path1 = $this->uploadPic ( $image1 );
			if (! $path1) {
				$result ['ret_num'] = 908;
				$result ['ret_msg'] = '图片上传失败';
				echo json_encode ( $result );
				die ();
			}
		}
		if ($image2) {
			// 上传图片
			$path2 = $this->uploadPic ( $image2 );
			if (! $path2) {
				$result ['ret_num'] = 908;
				$result ['ret_msg'] = '图片上传失败';
				echo json_encode ( $result );
				die ();
			}
		}
		if ($image3) {
			// 上传图片
			$path3 = $this->uploadPic ( $image3 );
			if (! $path3) {
				$result ['ret_num'] = 908;
				$result ['ret_msg'] = '图片上传失败';
				echo json_encode ( $result );
				die ();
			}
		}

		$model = new TdFeedback ();
		$model->userid = $member_id;
		$model->question_type = $questionType;
		$model->question = $question;
		$model->image1 = $path1;
		$model->image2 = $path2;
		$model->image3 = $path3;
		$model->created_time = time ();
		if ($model->save ()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 905;
			$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 取得所有消息
	 */
	public function actionGetMessageList() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];
			
		// 取得当前月份所有测试记录
		$db = Yii::app ()->db;
		$sql = "select m.id, m.title, m.message, m.message_type,m.send_userid,m.send_username,m.is_read,m.created_time,
				 (select member_avatar from taidushop_member where member_id=m.userid) as header
		from td_message m
		where userid={$member_id} 
		order by created_time desc";
		$command = $db->createCommand ( $sql );
		$messages = $command->queryAll ();
		if ($messages) {
			foreach ( $messages as $key => $value ) {
				$arr [] = array (
						"message_id" => $value ['id'],
						"title" => $value ['title'],
						"message" => $value ['message'],
						"message_type" => $value ['message_type'],
						"send_userid" => $value ['send_userid'],
						"send_username" => $value ['send_username'],
						"is_read" => $value ['is_read'],
						"created_time" => $value ['created_time']
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['messages'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 取得消息详情
	 */
	public function actionGetMessageDetail() {
		$messageId = Frame::getIntFromRequest ( 'messageId' );
		if (empty ( $messageId )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();

		// 取得当前月份所有测试记录
		$db = Yii::app ()->db;
		$sql = "select m.title, m.message, m.message_type,m.send_userid,m.send_username,m.is_read,m.created_time,
		(select member_avatar from taidushop_member where member_id=m.userid) as header
		from td_message m
		where m.id={$messageId}
		order by m.created_time desc";
		$command = $db->createCommand ( $sql );
		$message = $command->query ();
		if ($message) {
			foreach ( $message as $row ) {
				$arr = array (
						"title" => $row ['title'],
						"message" => $row ['message'],
						"message_type" => $row ['message_type'],
						"send_userid" => $row ['send_userid'],
						"send_username" => $row ['send_username'],
						"created_time" => $row ['created_time']
				);

				// 更新消息为已读
				$row = Yii::app ()->getDb ()->createCommand ()->update ( 'td_message', array (
						'is_read' => 1
				), "id=:id", array (
						':id' => $messageId
				) );
			}
		} else {
			$arr = null;
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['message'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 取得未读消息数
	 */
	public function actionGetNonReadMessagesCount() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];
			
		// 取得当前月份所有测试记录
		$count = TdMessage::model ()->count ( "userid={$member_id} and is_read=0" );
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['message_count'] = $count;

		echo json_encode ( $result );
	}
}