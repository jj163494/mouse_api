<?php
class KeyboardController extends PublicController {
	/**
	 * 氛围灯设置
	 */
	public function actionSetAtmosphereLight() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$customName = Frame::getStringFromRequest ( 'customName' );
		$customContent = Frame::getStringFromRequest ( 'customContent' );
		if (empty ( $customName ) || empty ( $customContent ) || empty ( $device_code )) {
			$result ['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		$atlight_name=TdAtmosphereLightSet::model()->find("custom_name='{$customName}' and userid='{$member_id}'");
		if (empty($atlight_name)){
			$atlight = new TdAtmosphereLightSet ();
			$atlight->device_code = $device_code;
			$atlight->userid = $member_id;
			$atlight->custom_name = $customName;
			$atlight->custom_content = $customContent;
			$atlight->created_time = time ();
			if ($atlight->save ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
				// 返回用户信息
				$result ['id'] = $atlight->id;
			} else {
				$result ['ret_num'] = 901;
				$result['ret_msg'] = $this->language->get('add_fail');
			}
		}else {
			$atlight_name->device_code = $device_code;
			$atlight_name->userid = $member_id;
			$atlight_name->custom_name = $customName;
			$atlight_name->custom_content = $customContent;
			$atlight_name->created_time = time ();
			if ($atlight_name->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 906;
				$result['ret_msg'] = $this->language->get('update_fail');
			}
		}

		echo json_encode ( $result );
	}
	





	/**
	 * 查询全部默认氛围灯设置详情
	 */
	public function actionFindAllAtmosphereLights() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		if (empty ( $device_code )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$arr = array();

		$atmosphereLight = TdAtmosphereLightSet::model ()->findAll ( "userid = '{$member_id}' and device_code='{$device_code}'" );
		if ($atmosphereLight) {
			$arr[]= array (
					"custom_name" =>'灯带全灭',
					"custom_content" => "0",
			);
			$arr[]= array (
					"custom_name" =>'灯带红色常亮',
					"custom_content" => "1",
			);
			$arr[]= array (
					"custom_name" =>'灯带红色呼吸',
					"custom_content" => "2",
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹常亮变色',
					"custom_content" => "3",
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹呼吸变色',
					"custom_content" => "4",
			);
			$arr[]= array (
					"custom_name" =>'灯带红色流动',
					"custom_content" => "5",
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹流动',
					"custom_content" => "6",
			);
			foreach ( $atmosphereLight as $key => $value ) {
				$arr [] = array (
						"id" => $value->id,
						"device_code" => $value->device_code,
						"userid" => $value->userid,
						"custom_name" => $value->custom_name,
						"custom_content" => $value->custom_content,
						"created_time" => $value->created_time
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['atmosphereLight'] = $arr;
		echo json_encode ( $result );
	}
	

	
	/**
	 * 查询键帽灯设置详情
	 */
	public function actionFindKeyCapLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$customName = Frame::getStringFromRequest ( 'customName' );

		// 获取用户信息
		$member_info = $this->check_user();

		$caplight = TdKeycapLightSet::model ()->find ( "id='{$Id}' and device_code='{$device_code}' or custom_name='{$customName}' and device_code='{$device_code}'" );
		if ($caplight) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $caplight->id;
			$result ['device_code'] = $caplight->device_code;
			$result ['userid'] = $caplight->userid;
			$result ['custom_name'] = $caplight->custom_name;
			$result ['custom_content'] = $caplight->custom_content;
			$result ['created_time'] = $caplight->created_time;
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}

	
	/**
	 * 查询全部默认键帽灯设置详情
	 */
	public function actionFindAllKeyCapLights() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		if (empty ( $device_code )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$arr = array();

		$keycapLight = TdKeycapLightSet::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}'" );
		if ($keycapLight) {
			$arr[]= array (
					"custom_name" =>'全灭',
					"custom_content" => "0",
			);
			$arr[]= array (
					"custom_name" =>'全亮',
					"custom_content" => "1",
			);
			$arr[]= array (
					"custom_name" =>'单点亮',
					"custom_content" => "3",
			);
			$arr[]= array (
					"custom_name" =>'呼吸',
					"custom_content" => "2",
			);
			$arr[]= array (
					"custom_name" =>'贪吃蛇',
					"custom_content" => "8",
			);
			$arr[]= array (
					"custom_name" =>'波浪',
					"custom_content" => "9",
			);
			$arr[]= array (
					"custom_name" =>'涟漪',
					"custom_content" => "4",
			);
			$arr[]= array (
					"custom_name" =>'十字扩散',
					"custom_content" => "5",
			);
			$arr[]= array (
					"custom_name" =>'左右扩散',
					"custom_content" => "6",
			);
			foreach ( $keycapLight as $key => $value ) {
				$arr [] = array (
						"id" => $value->id,
						"device_code" => $value->device_code,
						"userid" => $value->userid,
						"custom_name" => $value->custom_name,
						"custom_content" => $value->custom_content,
						"created_time" => $value->created_time
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['keycapLight'] = $arr;
		echo json_encode ( $result );
	}
	

	/**
	 * 查询自定义用户游戏类型
	 */
	public function actionFindGameType() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		$findAllmodeType = TdUserGameType::model ()->findAll ( "userid = '{$member_id}'" );
		if ($findAllmodeType) {
			foreach ( $findAllmodeType as $key => $value ) {
				$arr [] = array (
						"id" => $value->id,
						"game_name" => $value->game_name
				);
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['findAllmodeType'] = $arr;
		} else {
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询游戏类型下面的游戏模式
	 */
	public function actionGetGameModeByType() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$game_type_id = Frame::getStringFromRequest ( 'game_type_id' );
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		if (empty ( $device_code )) {
			$result['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		$arr = array();
		if ($game_type_id == 0) {
			$findModeType = TdRecommendGameMode::model ()->findAll ( "device_code='{$device_code}' and hot_game='1'" );
			if (! empty ( $findModeType )) {
				foreach ( $findModeType as $key => $value ) {
					$arr [] = array (
							"custom_id" => 0,
							"recommend_id" => $value->id,
							"mode_name" => $value->mode_name,
							"image1" => $value->image1,
							"image2" => $value->image2,
							"image3" => $value->image3,

					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
		} else if ($game_type_id == 1) {
			$findModeType = TdRecommendGameMode::model ()->findAll ( "device_code='{$device_code}' and game_type='{$game_type_id}'" );
			if (! empty ( $findModeType )) {
				foreach ( $findModeType as $key => $value ) {
					$arr [] = array (
							"recommend_id" => $value->id,
							"custom_id" => 0,
							"mode_name" => $value->mode_name,
							"image1" => $value->image1,
							"image2" => $value->image2,
							"image3" => $value->image3
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
			$findModeuserType = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}' and game_type='{$game_type_id}' and recommended_game_id=0 " );
			if (! empty ( $findModeuserType )) {
				foreach ( $findModeuserType as $key1 => $value1 ) {
					$arr [] = array (
							"recommend_id" => 0,
							"custom_id" => $value1->id,
							"mode_name" => $value1->mode_name,
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
		} else if ($game_type_id == 2) {
			$findModeType = TdRecommendGameMode::model ()->findAll ( "device_code='{$device_code}' and game_type='{$game_type_id}'" );
			if (! empty ( $findModeType )) {
				foreach ( $findModeType as $key => $value ) {
					$arr [] = array (
							"recommend_id" => $value->id,
							"custom_id" => 0,
							"mode_name" => $value->mode_name,
							"image1" => $value->image1,
							"image2" => $value->image2,
							"image3" => $value->image3
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
			$findModeuserType = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}' and game_type='{$game_type_id}' and recommended_game_id=0 " );
			if (! empty ( $findModeuserType )) {
				foreach ( $findModeuserType as $key1 => $value1 ) {
					$arr [] = array (
							"recommend_id" => 0,
							"custom_id" => $value1->id,
							"mode_name" => $value1->mode_name
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
		} else if ($game_type_id == 3) {
			$findModeType = TdRecommendGameMode::model ()->findAll ( "device_code='{$device_code}' and game_type='{$game_type_id}'" );
			if (! empty ( $findModeType )) {
				foreach ( $findModeType as $key => $value ) {
					$arr [] = array (
							"recommend_id" => $value->id,
							"custom_id" => 0,
							"mode_name" => $value->mode_name,
							"image1" => $value->image1,
							"image2" => $value->image2,
							"image3" => $value->image3
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
			$findModeuserType = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}' and game_type='{$game_type_id}' and recommended_game_id=0 " );
			if (! empty ( $findModeuserType )) {
				foreach ( $findModeuserType as $key1 => $value1 ) {
					$arr [] = array (
							"recommend_id" => 0,
							"custom_id" => $value1->id,
							"mode_name" => $value1->mode_name,
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			}
		} else if ($game_type_id > 10) {
			$findModeType = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}' and game_type='{$game_type_id}'" );
			if ($findModeType [0] [game_type] > 10) {
				foreach ( $findModeType as $key => $value ) {
					$mode_id = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and id='{$value->id}'" );
					$mode_type_id = $mode_id [0]->id;
					$arr [] = array (
							"custom_id" => $value->id,
							"recommend_id" => $value->recommended_game_id,
							"mode_name" => $value->mode_name ,
							"image3"=> 'http://192.168.80.1/data/soft/game_logo/20160919/GAME_logo@3x.png'
					);
				}
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'OK';
				$result ['findModeType'] = $arr;
			} else {
				$result = array(
					'ret_num' => 309,
					'ret_msg' => $this->language->get('no_data'),
				);
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询游戏模式详情(包含推荐)
	 */
	public function actionGetGameModeDetail() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$customer_game_id = Frame::getStringFromRequest ( 'customer_game_id' );
		$recommended_game_id = Frame::getStringFromRequest ( 'recommended_game_id' );
		if (! isset ( $customer_game_id ) || ! isset ( $recommended_game_id )) {
			$result['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		if ($recommended_game_id > 0) {
			$modeDetail = TdUserGameMode::model ()->find ( "userid='{$member_id}' and recommended_game_id='{$recommended_game_id}'" );
			if ($modeDetail) {
				$recommendmodedetail = TdRecommendGameMode::model ()->find ( "id='{$recommended_game_id}'" );
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
				$result ['id'] = $modeDetail->id;
				$result ['device_code'] = $modeDetail->device_code;
				$result ['recommended_game_id'] = $recommended_game_id;
				$result ['mode_name'] = $modeDetail->mode_name;
				$result ['game_type'] = $modeDetail->game_type;
				$result ['game_type_name'] = $modeDetail->game_type_name;
				$result ['polling_rate'] = $modeDetail->polling_rate;
				$result ['repeat_speed'] = $modeDetail->repeat_speed;
				$result ['no_rush_mode'] = $modeDetail->no_rush_mode;
				$result ['kill_keys'] = $modeDetail->kill_keys;
				$result ['lamp_light_type'] = $modeDetail->lamp_light_type;
				$result ['lamp_light_project_name'] = $modeDetail->lamp_light_name;
				$result ['recommend_lamp_light_name'] = $recommendmodedetail->lamp_light_name;
				$result ['recommend_lamp_light_content'] = $recommendmodedetail->lamp_light_content;
				$result ['keycap_light_type'] = $modeDetail->keycap_light_type;
				$result ['keycap_light_project_name'] = $modeDetail->keycap_light_name;
				$result ['recommend_keycap_light_name'] = $recommendmodedetail->keycap_light_name;
				$result ['recommend_keycap_light_content'] = $recommendmodedetail->keycap_light_content;
				$result ['resconstruct_project_type'] = $modeDetail->resconstruct_project_type;
				$result ['resconstruct_project_name'] = $modeDetail->resconstruct_project_name;
				$result ['image1'] = $recommendmodedetail->image1;
				 $result ['image2'] = $recommendmodedetail->image2;
				$result ['image3'] = $recommendmodedetail->image3;
			} else {
				$recommend_detail = TdRecommendGameMode::model()->find ( "id='{$recommended_game_id}'" );
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
				$result ['id'] = 0;
				$result ['recommended_game_id'] = $recommended_game_id;
				$result ['device_code'] = $recommend_detail->device_code;
				$result ['mode_name'] = $recommend_detail->mode_name;
				$result ['game_type'] = $recommend_detail->game_type;
				$result ['game_type_name'] = $recommend_detail->game_type_name;
				$result ['polling_rate'] = $recommend_detail->polling_rate;
				$result ['repeat_speed'] = $recommend_detail->repeat_speed;
				$result ['no_rush_mode'] = $recommend_detail->no_rush_mode;
				$result ['kill_keys'] = $recommend_detail->kill_keys;
				$result ['lamp_light_type'] = $recommend_detail->lamp_light_type;
				$result ['lamp_light_project_name'] = '';
				$result ['recommend_lamp_light_name'] = $recommend_detail->lamp_light_name;
				$result ['recommend_lamp_light_content'] = $recommend_detail->lamp_light_content;
				$result ['keycap_light_type'] = $recommend_detail->keycap_light_type;
				$result ['keycap_light_project_name'] = '';
				$result ['recommend_keycap_light_name'] = $recommend_detail->keycap_light_name;
				$result ['recommend_keycap_light_content'] = $recommend_detail->keycap_light_content;
				$result ['resconstruct_project_type'] = $recommend_detail->resconstruct_project_type;
				$result ['resconstruct_project_name'] = $recommend_detail->resconstruct_project_name;
				$result ['image1'] = $recommend_detail->image1;
				 $result ['image2'] = $recommend_detail->image2;
				$result ['image3'] = $recommend_detail->image3;
			}
		} else if ($recommended_game_id == 0) {
			$usermodedetail = TdUserGameMode::model ()->find ( " userid='{$member_id}' and id='{$customer_game_id}'" );
			if ($usermodedetail) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
				$result ['id'] = $usermodedetail->id;
				$result ['device_code'] = $usermodedetail->device_code;
				$result ['recommended_game_id'] = 0;
				$result ['mode_name'] = $usermodedetail->mode_name;
				$result ['game_type'] = $usermodedetail->game_type;
				$result ['game_type_name'] = $usermodedetail->game_type_name;
				$result ['polling_rate'] = $usermodedetail->polling_rate;
				$result ['repeat_speed'] = $usermodedetail->repeat_speed;
				$result ['no_rush_mode'] = $usermodedetail->no_rush_mode;
				$result ['kill_keys'] = $usermodedetail->kill_keys;
				$result ['lamp_light_type'] = $usermodedetail->lamp_light_type;
				$result ['lamp_light_project_name'] = $usermodedetail->lamp_light_name;
				$result ['lamp_light_project_content'] = $usermodedetail->lamp_light_content;
				$result ['recommend_lamp_light_name'] = '';
				$result ['recommend_lamp_light_content'] = '';
				$result ['keycap_light_type'] = $usermodedetail->keycap_light_type;
				$result ['keycap_light_project_name'] = $usermodedetail->keycap_light_name;
				$result ['keycap_light_project_content'] = $usermodedetail->keycap_light_content;
				$result ['recommend_keycap_light_name'] = '';
				$result ['recommend_keycap_light_content'] = '';
				$result ['resconstruct_project_type'] = $usermodedetail->resconstruct_project_type;
				$result ['resconstruct_project_name'] = $usermodedetail->resconstruct_project_name;
				$result ['image2'] = 'http://192.168.80.1/data/soft/game_logo/20160919/GAME_logo@2x.png';
				$result ['image3'] = 'http://192.168.80.1/data/soft/game_logo/20160919/GAME_logo@3x.png';
				$result ['image1'] = 'http://192.168.80.1/data/soft/game_logo/20160919/PC_GAME_logo.png';
			} else {
				$result = array(
					'ret_num' => 309,
					'ret_msg' => $this->language->get('no_data'),
				);
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 推荐游戏模式一键恢复
	 */
	public function actionResetRecommendedGameMode() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$recommended_id = Frame::getStringFromRequest ( 'recommended_id' );
		if (empty ( $recommended_id )) {
			$result['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		$reset = TdRecommendGameMode::model ()->find ( "id='{$recommended_id}'" );
		$reset_id = $reset->id;
		$reset_detete = TdUserGameMode::model ()->deleteAll ( " userid='{$member_id}' and recommended_game_id='{$reset_id}'" );
		if ($reset_detete) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $reset_id;
			$result ['device_code'] = $reset->device_code;
			$result ['mode_name'] = $reset->mode_name;
			$result ['hot_game'] = $reset->hot_game;
			$result ['game_type'] = $reset->game_type;
			$result ['game_type_name'] = $reset->game_type_name;
			$result ['polling_rate'] = $reset->polling_rate;
			$result ['repeat_speed'] = $reset->repeat_speed;
			$result ['no_rush_mode'] = $reset->no_rush_mode;
			$result ['kill_keys'] = $reset->kill_keys;
			$result ['lamp_light_type'] = $reset->lamp_light_type;
			$result ['lamp_light_name'] = $reset->lamp_light_name;
			$result ['lamp_light_content'] = $reset->lamp_light_content;
			$result ['keycap_light_type'] = $reset->keycap_light_type;
			$result ['keycap_light_name'] = $reset->keycap_light_name;
			$result ['keycap_light_content'] = $reset->keycap_light_content;
			$result ['resconstruct_project_type'] = $reset->resconstruct_project_type;
			$result ['resconstruct_project_name'] = $reset->resconstruct_project_name;
			$result ['image1'] = $reset->image1;
			$result ['image2'] = $reset->image2;
			$result ['image3'] = $reset->image3;
		} else {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $reset_id;
			$result ['device_code'] = $reset->device_code;
			$result ['mode_name'] = $reset->mode_name;
			$result ['hot_game'] = $reset->hot_game;
			$result ['game_type'] = $reset->game_type;
			$result ['game_type_name'] = $reset->game_type_name;
			$result ['polling_rate'] = $reset->polling_rate;
			$result ['repeat_speed'] = $reset->repeat_speed;
			$result ['no_rush_mode'] = $reset->no_rush_mode;
			$result ['kill_keys'] = $reset->kill_keys;
			$result ['lamp_light_type'] = $reset->lamp_light_type;
			$result ['lamp_light_name'] = $reset->lamp_light_name;
			$result ['lamp_light_content'] = $reset->lamp_light_content;
			$result ['keycap_light_type'] = $reset->keycap_light_type;
			$result ['keycap_light_name'] = $reset->keycap_light_name;
			$result ['keycap_light_content'] = $reset->keycap_light_content;
			$result ['resconstruct_project_type'] = $reset->resconstruct_project_type;
			$result ['resconstruct_project_name'] = $reset->resconstruct_project_name;
			$result ['image1'] = $reset->image1;
			$result ['image2'] = $reset->image2;
			$result ['image3'] = $reset->image3;
		}

		echo json_encode ( $result );
	}
}