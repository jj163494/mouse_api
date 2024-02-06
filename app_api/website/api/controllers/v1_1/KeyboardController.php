<?php
class KeyboardController extends PublicController {
	/**
	 * 氛围灯设置
	 */
	public function actionSetAtmosphereLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$customName = Frame::getStringFromRequest ( 'customName' );
		$customContent = Frame::getStringFromRequest ( 'customContent' );
		if (empty ( $customName ) || empty ( $customContent ) || empty ( $device_code )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
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
				$result ['ret_msg'] = '操作成功';
				// 返回用户信息
				$result ['id'] = $atlight->id;
			} else {
				$result ['ret_num'] = 901;
				$result ['ret_msg'] = '信息添加失败	';
			}
		}else {
			$atlight_name->device_code = $device_code;
			$atlight_name->userid = $member_id;
			$atlight_name->custom_name = $customName;
			$atlight_name->custom_content = $customContent;
			$atlight_name->created_time = time ();
			if ($atlight_name->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';			
			} else {
				$result ['ret_num'] = 906;
				$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询氛围灯设置详情
	 */
	public function actionFindAtmosphereLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$customName = Frame::getStringFromRequest ( 'customName' );

		// 获取用户信息
		$member_info = $this->check_user();

		$atlight = TdAtmosphereLightSet::model ()->find ( "id='{$Id}' and device_code='{$device_code}' or custom_name='{$customName}' and device_code='{$device_code}' " );
		if ($atlight) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $atlight->id;
			$result ['device_code'] = $atlight->device_code;
			$result ['userid'] = $member_info['member_id'];
			$result ['custom_name'] = $atlight->custom_name;
			$result ['custom_content'] = $atlight->custom_content;
			$result ['created_time'] = $atlight->created_time;
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}


	/**
	 * 查询全部氛围灯设置详情
	 */
	public function actionFindAtmosphereLights() {
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

		$atmosphereLight = TdAtmosphereLightSet::model ()->findAll ( "userid = '{$member_id}' and device_code='{$device_code}'" );
		if ($atmosphereLight) {
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

		$atmosphereLight = TdAtmosphereLightSet::model ()->findAll ( "userid = '{$member_id}' and device_code='{$device_code}'" );
		if ($atmosphereLight) {
			$arr[]= array (
					"custom_name" =>'灯带全灭',
					"custom_content" => 0,
			);
			$arr[]= array (
					"custom_name" =>'灯带红色常亮',
					"custom_content" => 1,
			);
			$arr[]= array (
					"custom_name" =>'灯带红色呼吸',
					"custom_content" => 2,
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹常亮变色',
					"custom_content" => 3,
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹呼吸变色',
					"custom_content" => 4,
			);
			$arr[]= array (
					"custom_name" =>'灯带红色流动',
					"custom_content" => 5,
			);
			$arr[]= array (
					"custom_name" =>'灯带彩虹流动',
					"custom_content" => 6,
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
	 * 删除氛围灯设置方案
	 */
	public function actionDeleteSetAtmosphereLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$customName = Frame::getStringFromRequest ( 'customName' );

		// 获取用户信息
		$member_info = $this->check_user();

		$deleteatmospherelight = TdAtmosphereLightSet::model ()->deleteAll ( "id='{$Id}' and device_code='{$device_code}' || custom_name='{$customName}' and device_code='{$device_code}' " );
		if ($deleteatmospherelight) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 键帽灯设置
	 */
	public function actionSetKeyCapLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$customName = Frame::getStringFromRequest ( 'customName' );
		$customContent = Frame::getStringFromRequest ( 'customContent' );
		if (empty ( $customName ) || empty ( $customContent ) || empty ( $device_code )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$caplight_name=TdKeycapLightSet::model()->find("userid='{$member_id}' and custom_name='{$customName}'");
		if (empty($caplight_name)){
		$caplight = new TdKeycapLightSet ();
		$caplight->device_code = $device_code;
		$caplight->userid = $member_id;
		$caplight->custom_name = $customName;
		$caplight->custom_content = $customContent;
		$caplight->created_time = time ();
		if ($caplight->save ()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			// 返回用户信息
			$result ['id'] = $caplight->id;
		} else {
			$result ['ret_num'] = 901;
			$result ['ret_msg'] = '信息添加失败	';
		}
		}else {
			$caplight_name->device_code = $device_code;
			$caplight_name->userid = $member_id;
			$caplight_name->custom_name = $customName;
			$caplight_name->custom_content = $customContent;
			$caplight_name->created_time = time ();
			if ($caplight_name->update()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			} else {
				$result ['ret_num'] = 906;
				$result ['ret_msg'] = '替换时发生系统错误，请重新操作	';
			}
		}

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
	 * 查询全部键帽灯设置详情
	 */
	public function actionFindKeyCapLights() {
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

		$keycapLight = TdKeycapLightSet::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}'" );
		if ($keycapLight) {
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

		$keycapLight = TdKeycapLightSet::model ()->findAll ( "userid='{$member_id}' and device_code='{$device_code}'" );
		if ($keycapLight) {
			$arr[]= array (
					"custom_name" =>'全灭',
					"custom_content" => 0,
			);
			$arr[]= array (
					"custom_name" =>'全亮',
					"custom_content" => 1,
			);
			$arr[]= array (
					"custom_name" =>'单点亮',
					"custom_content" => 3,
			);
			$arr[]= array (
					"custom_name" =>'呼吸',
					"custom_content" => 2,
			);
			$arr[]= array (
					"custom_name" =>'贪吃蛇',
					"custom_content" => 8,
			);
			$arr[]= array (
					"custom_name" =>'波浪',
					"custom_content" => 9,
			);
			$arr[]= array (
					"custom_name" =>'涟漪',
					"custom_content" => 4,
			);
			$arr[]= array (
					"custom_name" =>'十字扩散',
					"custom_content" => 5,
			);
			$arr[]= array (
					"custom_name" =>'左右扩散',
					"custom_content" => 6,
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
	 * 删除键帽灯设置方案
	 */
	public function actionDeleteSetKeyCapLight() {
		$device_code = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$customName = Frame::getStringFromRequest ( 'customName' );
		if (! isset ( $Id ) || ! isset ( $customName )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		if ($Id) {
			$deletekeycap = TdKeycapLightSet::model ()->deleteAll ( "userid='{$member_id}' and id='{$Id}' and device_code='{$device_code}'" );
			if ($deletekeycap) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			} else {
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
			}
		} else {
			$deletekeycap = TdKeycapLightSet::model ()->deleteAll ( "userid='{$member_id}' and custom_name='{$customName}' and device_code='{$device_code}'" );
			if ($deletekeycap) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			} else {
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 上传改键方案
	 */
	public function actionUploadResconstructProject() {
		$deviceCode = Frame::getStringFromRequest ( 'device_code' );
		$projectName = Frame::getStringFromRequest ( 'projectName' );
		$projectContent = Frame::getStringFromRequest ( 'projectContent' );
		if (empty ( $projectName ) || empty ( $projectContent ) || empty ( $deviceCode )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$resconstruct = new TdResconstructProject ();
		$name = TdResconstructProject::model ()->find ( "userid='{$member_id}' and project_name='{$projectName}' and device_code='{$deviceCode}'" );
		if ($name) {
			$name->userid = $member_id;
			$name->device_code = $deviceCode;
			$name->project_name = $projectName;
			$name->project_content = $projectContent;
			$name->created_time = time ();
			if ($name->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '更新成功';
			} else {
				$result ['ret_num'] = 906;
				$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
			}
		} else {
			$resconstruct->userid = $member_id;
			$resconstruct->device_code = $deviceCode;
			$resconstruct->project_name = $projectName;
			$resconstruct->project_content = $projectContent;
			$resconstruct->created_time = time ();
			if ($resconstruct->save ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				// 返回用户信息
				$result ['id'] = $resconstruct->id;
			} else {
				$result ['ret_num'] = 901;
				$result ['ret_msg'] = '信息添加失败	';
			}
		}

		echo json_encode ( $result );
	}
	/**
	 * 查询改键方案详情
	 */
	public function actionFindResconstructproject() {
		$deviceCode = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$projectName = Frame::getStringFromRequest ( 'projectName' );

		// 获取用户信息
		$member_info = $this->check_user();

		$project = TdResconstructProject::model ()->find ( "id='{$Id}' and device_code='{$deviceCode}' or project_name='{$projectName}' and device_code='{$deviceCode}'" );
		if ($project) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $project->id;
			$result ['userid'] = $member_info['member_id'];
			$result ['device_code'] = $deviceCode;
			$result ['project_name'] = $project->project_name;
			$result ['project_content'] = $project->project_content;
			$result ['created_time'] = $project->created_time;
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}
	/**
	 * 删除改键方案
	 */
	public function actionDeleteResconstructproject() {
		$deviceCode = Frame::getStringFromRequest ( 'device_code' );
		$Id = Frame::getStringFromRequest ( 'Id' );
		$projectName = Frame::getStringFromRequest ( 'projectName' );
		if (! isset ( $Id ) || ! isset ( $projectName )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		if ($Id) {
			$deleteproject = TdResconstructProject::model ()->deleteAll ( "userid='{$userid}' and id='{$Id}' and device_code='{$deviceCode}'" );
			if ($deleteproject) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			} else {
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
			}
		} else {
			$deleteproject = TdResconstructProject::model ()->deleteAll ( "userid='{$userid}' and project_name='{$projectName}' and device_code='{$deviceCode}'" );
			if ($deleteproject) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
			} else {
				$result ['ret_num'] = 309;
				$result ['ret_msg'] = '没有该记录';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询全部改键方案
	 */
	public function actionFindAllResconstructproject() {
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

		$project = TdResconstructProject::model ()->findAll( "userid = '{$member_id}' and device_code='{$device_code}'" );
		if ($project) {
			foreach ( $project as $key => $value ) {
				$arr [] = array (
						"id" => $value->id,
						"userid" => $value->userid,
						"device_code" => $value->device_code,
						"project_name" => $value->project_name,
						"project_content" => $value->project_content,
						"created_time" => $value->created_time
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['project'] = $arr;
		echo json_encode ( $result );
	}
	
	/**
	 * 用户模式创建（包含推荐）
	 */
	public function actionAddUserGameMode() {
		$deviceCode = Frame::getStringFromRequest ( 'device_code' );
		$recommended_game_id = Frame::getStringFromRequest ( 'recommended_game_id' );
		$customer_game_id = Frame::getStringFromRequest ( 'customer_game_id' );
		$mode_name = Frame::getStringFromRequest ( 'mode_name' );
		$game_type_name = Frame::getStringFromRequest ( 'game_type_name' );
		$polling_rate = Frame::getStringFromRequest ( 'polling_rate' );
		$repeat_speed = Frame::getStringFromRequest ( 'repeat_speed' );
		$no_rush_mode = Frame::getStringFromRequest ( 'no_rush_mode' );
		$kill_keys = Frame::getStringFromRequest ( 'kill_keys' );
		$lamp_light_type = Frame::getStringFromRequest ( 'lamp_light_type' );
		$lamp_light_name = Frame::getStringFromRequest ( 'lamp_light_name' );
		$lamp_light_content = Frame::getStringFromRequest ( 'lamp_light_content' );
		$keycap_light_type = Frame::getStringFromRequest ( 'keycap_light_type' );
		$keycap_light_name = Frame::getStringFromRequest ( 'keycap_light_name' );
		$keycap_light_content = Frame::getStringFromRequest ( 'keycap_light_content' );
		$resconstruct_project_type = Frame::getStringFromRequest ( 'resconstruct_project_type' );
		$resconstruct_project_name = Frame::getStringFromRequest ( 'resconstruct_project_name' );
		if (empty ( $deviceCode ) || empty ( $mode_name ) || empty ( $no_rush_mode ) || ! isset ( $kill_keys ) || ! isset ( $customer_game_id ) || ! isset ( $recommended_game_id ) || ! isset ( $lamp_light_type ) || ! isset ( $keycap_light_type ) || ! isset ( $resconstruct_project_type )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		if ($recommended_game_id > 0) {
			$game_mode = TdUserGameMode::model ()->find ( " userid='{$member_id}' and recommended_game_id='{$recommended_game_id}'" );
			if ($game_mode) {
				$game_mode_user_id = $game_mode->id;
				$game_mode->id = $game_mode_user_id;
				$game_mode->userid = $member_id;
				$game_mode->device_code = $deviceCode;
				$game_mode->polling_rate = $polling_rate;
				$game_mode->no_rush_mode = $no_rush_mode;
				$game_mode->kill_keys = $kill_keys;
				$game_mode->lamp_light_type = $lamp_light_type;
				$game_mode->lamp_light_name = $lamp_light_name;
				$game_mode->lamp_light_content = $lamp_light_content;
				$game_mode->keycap_light_type = $keycap_light_type;
				$game_mode->keycap_light_name = $keycap_light_name;
				$game_mode->keycap_light_content = $keycap_light_content;
				$game_mode->resconstruct_project_type = $resconstruct_project_type;
				$game_mode->resconstruct_project_name = $resconstruct_project_name;
				if ($game_mode->update ()) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '更新成功';
					$result ['id'] = $game_mode->id;
				} else {
					$result ['ret_num'] = 906;
					$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
				}
			} else {
				$countrecommend = TdRecommendGameMode::model ()->find ( "id='{$recommended_game_id}' and device_code='{$deviceCode}'" );
				$type1 = $countrecommend->game_type;
				$countrecommend1 = TdRecommendGameMode::model ()->findAll ( "game_type='{$type1}' and device_code='{$deviceCode}'" );
				$countrecommend2 = count ( $countrecommend1 );
				$countrecommend3 = TdUserGameMode::model ()->find ( " userid='$member_id' and recommended_game_id='{$recommended_game_id}'" );
				$type2 = $countrecommend3->game_type;
				$countrecommend4 = TdUserGameMode::model ()->findAll ( " userid='$member_id' and game_type='{$type2}'" );
				$countrecommend5 = count ( $countrecommend4 );
				if ($countrecommend2 + $countrecommend5 < 8) {
					$recommended_game_type = TdRecommendGameMode::model ()->find ( " id='{$recommended_game_id}' and device_code='{$deviceCode}'" );
					$user_mode_type = new TdUserGameMode ();
					$user_mode_type->userid = $member_id;
					$user_mode_type->device_code = $deviceCode;
					$user_mode_type->recommended_game_id = $recommended_game_id;
					$user_mode_type->mode_name = $recommended_game_type->mode_name;
					$user_mode_type->game_type = $recommended_game_type->game_type;
					$user_mode_type->game_type_name = $recommended_game_type->game_type_name;
					$user_mode_type->polling_rate = $polling_rate;
					$user_mode_type->repeat_speed = $recommended_game_type->repeat_speed;
					$user_mode_type->no_rush_mode = $no_rush_mode;
					$user_mode_type->kill_keys = $kill_keys;
					$user_mode_type->lamp_light_type = $lamp_light_type;
					$user_mode_type->lamp_light_name = $lamp_light_name;
					$user_mode_type->lamp_light_content = $lamp_light_content;
					$user_mode_type->keycap_light_type = $keycap_light_type;
					$user_mode_type->keycap_light_name = $keycap_light_name;
					$user_mode_type->keycap_light_content = $keycap_light_content;
					$user_mode_type->resconstruct_project_type = $resconstruct_project_type;
					$user_mode_type->resconstruct_project_name = $resconstruct_project_name;

					if ($user_mode_type->save ()) {
						$result ['ret_num'] = 0;
						$result ['ret_msg'] = '操作成功';
						// 返回用户信息
						$result ['id'] = $user_mode_type->id;
					} else {
						$result ['ret_num'] = 901;
						$result ['ret_msg'] = '信息添加失败	';
					}
				} else {
					$result ['ret_num'] = 320;
					$result ['ret_msg'] = '游戏模式达到上限，创建失败	';
				}
			}
		} else if ($customer_game_id > 0) {
			$customer_game_mode = TdUserGameMode::model ()->find ( " userid='{$member_id}' and  id='{$customer_game_id}'" );
			if ($customer_game_mode) {
				$customer_game_mode->id = $customer_game_id;
				$customer_game_mode->userid = $member_id;
				$customer_game_mode->device_code = $deviceCode;
				$customer_game_mode->mode_name = $customer_game_mode->mode_name;
				$customer_game_mode->game_type = $customer_game_mode->game_type;
				$customer_game_mode->game_type_name = $customer_game_mode->game_type_name;
				$customer_game_mode->polling_rate = $polling_rate;
				$customer_game_mode->repeat_speed = $repeat_speed;
				$customer_game_mode->no_rush_mode = $no_rush_mode;
				$customer_game_mode->kill_keys = $kill_keys;
				$customer_game_mode->lamp_light_type = $lamp_light_type;
				$customer_game_mode->lamp_light_name = $lamp_light_name;
				$customer_game_mode->lamp_light_content = $lamp_light_content;
				$customer_game_mode->keycap_light_type = $keycap_light_type;
				$customer_game_mode->keycap_light_name = $keycap_light_name;
				$customer_game_mode->keycap_light_content = $keycap_light_content;
				$customer_game_mode->resconstruct_project_type = $resconstruct_project_type;
				$customer_game_mode->resconstruct_project_name = $resconstruct_project_name;
				if ($customer_game_mode->update ()) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '更新成功';
					$result ['id'] = $customer_game_mode->id;
				} else {
					$result ['ret_num'] = 906;
					$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
				}
			} else {
				$type3 = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and game_type_name='{$game_type_name}'" );
				$cut1 = count ( $type3 );
				$type4 = TdRecommendGameMode::model ()->findAll ( "game_type_name='{$game_type_name}' and device_code='{$deviceCode}'" );
				$cut2 = count ( $type4 );
				if ($cut1 + $cut2 < 8) {
					$usernamere = TdUserGameMode::model ()->find ( " userid='{$member_id}' and mode_name='{$mode_name}'" );
					if (empty ( $usernamere )) {
						$recommendedname_type = TdRecommendGameMode::model ()->find ( "game_type_name='{$game_type_name}' and device_code='{$deviceCode}'" );
						if (empty ( $recommendedname_type )) {
							$user_type_name = TdUserGameType::model ()->find ( " userid='{$member_id}' and game_name='{$game_type_name}'" );
							if (empty ( $user_type_name )) {
								$add_type = new TdUserGameType ();
								$add_type->userid = $member_id;
								$add_type->game_name = $game_type_name;
								if ($add_type->save ()) {
									$result ['ret_num'] = 0;
									$result ['ret_msg'] = '操作成功';
								}
								$AddUserGameMode = new TdUserGameMode ();
								$AddUserGameMode->userid = $member_id;
								$AddUserGameMode->device_code = $deviceCode;
								$AddUserGameMode->mode_name = $mode_name;
								$AddUserGameMode->game_type = $add_type->id;
								$AddUserGameMode->game_type_name = $game_type_name;
								$AddUserGameMode->polling_rate = $polling_rate;
								$AddUserGameMode->repeat_speed = $repeat_speed;
								$AddUserGameMode->no_rush_mode = $no_rush_mode;
								$AddUserGameMode->kill_keys = $kill_keys;
								$AddUserGameMode->lamp_light_type = $lamp_light_type;
								$AddUserGameMode->lamp_light_name = $lamp_light_name;
								$AddUserGameMode->lamp_light_content = $lamp_light_content;
								$AddUserGameMode->keycap_light_type = $keycap_light_type;
								$AddUserGameMode->keycap_light_name = $keycap_light_name;
								$AddUserGameMode->keycap_light_content = $keycap_light_content;
								$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
								$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
								if ($AddUserGameMode->save ()) {
									$result ['ret_num'] = 0;
									$result ['ret_msg'] = '操作成功';
									// 返回用户信息
									$result ['id'] = $AddUserGameMode->id;
								} else {
									$result ['ret_num'] = 901;
									$result ['ret_msg'] = '信息添加失败	';
								}
							} else {
								$AddUserGameMode = new TdUserGameMode ();
								$AddUserGameMode->userid = $member_id;
								$AddUserGameMode->device_code = $deviceCode;
								$AddUserGameMode->mode_name = $mode_name;
								$AddUserGameMode->game_type = $user_type_name->id;
								$AddUserGameMode->game_type_name = $user_type_name->game_name;
								$AddUserGameMode->polling_rate = $polling_rate;
								$AddUserGameMode->repeat_speed = $repeat_speed;
								$AddUserGameMode->no_rush_mode = $no_rush_mode;
								$AddUserGameMode->kill_keys = $kill_keys;
								$AddUserGameMode->lamp_light_type = $lamp_light_type;
								$AddUserGameMode->lamp_light_name = $lamp_light_name;
								$AddUserGameMode->lamp_light_content = $lamp_light_content;
								$AddUserGameMode->keycap_light_type = $keycap_light_type;
								$AddUserGameMode->keycap_light_name = $keycap_light_name;
								$AddUserGameMode->keycap_light_content = $keycap_light_content;
								$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
								$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
								if ($AddUserGameMode->save ()) {
									$result ['ret_num'] = 0;
									$result ['ret_msg'] = '操作成功';
									// 返回用户信息
									$result ['id'] = $AddUserGameMode->id;
								} else {
									$result ['ret_num'] = 901;
									$result ['ret_msg'] = '信息添加失败	';
								}
							}
						} else {
							$AddUserGameMode = new TdUserGameMode ();
							$AddUserGameMode->userid = $member_id;
							$AddUserGameMode->device_code = $deviceCode;
							$AddUserGameMode->recommended_game_id = $recommended_game_id;
							$AddUserGameMode->mode_name = $mode_name;
							$AddUserGameMode->game_type = $recommendedname_type->game_type;
							$AddUserGameMode->game_type_name = $game_type_name;
							$AddUserGameMode->polling_rate = $polling_rate;
							$AddUserGameMode->repeat_speed = $repeat_speed;
							$AddUserGameMode->no_rush_mode = $no_rush_mode;
							$AddUserGameMode->kill_keys = $kill_keys;
							$AddUserGameMode->lamp_light_type = $lamp_light_type;
							$AddUserGameMode->lamp_light_name = $lamp_light_name;
							$AddUserGameMode->lamp_light_content = $lamp_light_content;
							$AddUserGameMode->keycap_light_type = $keycap_light_type;
							$AddUserGameMode->keycap_light_name = $keycap_light_name;
							$AddUserGameMode->keycap_light_content = $keycap_light_content;
							$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
							$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
							if ($AddUserGameMode->save ()) {
								$result ['ret_num'] = 0;
								$result ['ret_msg'] = '操作成功';
								// 返回用户信息
								$result ['id'] = $AddUserGameMode->id;
							} else {
								$result ['ret_num'] = 901;
								$result ['ret_msg'] = '信息添加失败	';
							}
						}
					} else {
						$result ['ret_num'] = 319;
						$result ['ret_msg'] = '游戏名称重复，创建失败';
					}
				} else {
					$result ['ret_num'] = 320;
					$result ['ret_msg'] = '游戏模式达到上限，创建失败	';
				}
			}
		} else if ($customer_game_id <= 0) {
			$countrecommend6 = TdUserGameMode::model ()->findAll ( "userid='{$member_id}' and game_type_name='{$game_type_name}' and recommended_game_id<=0 and device_code='{$deviceCode}'" );
			$cout1 = count ( $countrecommend6 );
			$countrecommend7 = TdRecommendGameMode::model ()->findAll ( "game_type_name='{$game_type_name}' and device_code='{$deviceCode}'" );
			$cout2 = count ( $countrecommend7 );
			if ($cout1 + $cout2 < 8) {
				$usernameres = TdUserGameMode::model ()->find ( " userid='{$member_id}' and mode_name='{$mode_name}' and device_code='{$deviceCode}'" );
				if (empty ( $usernameres )) {
					$recommendname = TdRecommendGameMode::model ()->find ( "game_type_name='{$game_type_name}' and device_code='{$deviceCode}'" );
					if (empty ( $recommendname )) {
						$user_type_name = TdUserGameType::model ()->find ( "userid='{$member_id}' and game_name='{$game_type_name}'" );
						if (empty ( $user_type_name )) {
							$add_type = new TdUserGameType ();
							$add_type->userid = $member_id;
							$add_type->game_name = $game_type_name;
							if ($add_type->save ()) {
								$result ['ret_num'] = 0;
								$result ['ret_msg'] = '操作成功';
							}
							$AddUserGameMode = new TdUserGameMode ();
							$AddUserGameMode->userid = $member_id;
							$AddUserGameMode->device_code = $deviceCode;
							$AddUserGameMode->mode_name = $mode_name;
							$AddUserGameMode->game_type = $add_type->id;
							$AddUserGameMode->game_type_name = $game_type_name;
							$AddUserGameMode->polling_rate = $polling_rate;
							$AddUserGameMode->repeat_speed = $repeat_speed;
							$AddUserGameMode->no_rush_mode = $no_rush_mode;
							$AddUserGameMode->kill_keys = $kill_keys;
							$AddUserGameMode->lamp_light_type = $lamp_light_type;
							$AddUserGameMode->lamp_light_name = $lamp_light_name;
							$AddUserGameMode->lamp_light_content = $lamp_light_content;
							$AddUserGameMode->keycap_light_type = $keycap_light_type;
							$AddUserGameMode->keycap_light_name = $keycap_light_name;
							$AddUserGameMode->keycap_light_content = $keycap_light_content;
							$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
							$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
							if ($AddUserGameMode->save ()) {
								$result ['ret_num'] = 0;
								$result ['ret_msg'] = '操作成功';
								// 返回用户信息
								$result ['id'] = $AddUserGameMode->id;
							} else {
								$result ['ret_num'] = 901;
								$result ['ret_msg'] = '信息添加失败	';
							}
						} else {
							$AddUserGameMode = new TdUserGameMode ();
							$AddUserGameMode->userid = $member_id;
							$AddUserGameMode->device_code = $deviceCode;
							$AddUserGameMode->mode_name = $mode_name;
							$AddUserGameMode->game_type = $user_type_name->id;
							$AddUserGameMode->game_type_name = $user_type_name->game_name;
							$AddUserGameMode->polling_rate = $polling_rate;
							$AddUserGameMode->repeat_speed = $repeat_speed;
							$AddUserGameMode->no_rush_mode = $no_rush_mode;
							$AddUserGameMode->kill_keys = $kill_keys;
							$AddUserGameMode->lamp_light_type = $lamp_light_type;
							$AddUserGameMode->lamp_light_name = $lamp_light_name;
							$AddUserGameMode->lamp_light_content = $lamp_light_content;
							$AddUserGameMode->keycap_light_type = $keycap_light_type;
							$AddUserGameMode->keycap_light_name = $keycap_light_name;
							$AddUserGameMode->keycap_light_content = $keycap_light_content;
							$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
							$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
							if ($AddUserGameMode->save ()) {
								$result ['ret_num'] = 0;
								$result ['ret_msg'] = '操作成功';
								// 返回用户信息
								$result ['id'] = $AddUserGameMode->id;
							} else {
								$result ['ret_num'] = 901;
								$result ['ret_msg'] = '信息添加失败	';
							}
						}
					} else {
						$AddUserGameMode = new TdUserGameMode ();
						$AddUserGameMode->userid = $member_id;
						$AddUserGameMode->device_code = $deviceCode;
						$AddUserGameMode->recommended_game_id = $recommended_game_id;
						$AddUserGameMode->mode_name = $mode_name;
						$AddUserGameMode->game_type = $recommendname->game_type;
						$AddUserGameMode->game_type_name = $game_type_name;
						$AddUserGameMode->polling_rate = $polling_rate;
						$AddUserGameMode->repeat_speed = $repeat_speed;
						$AddUserGameMode->no_rush_mode = $no_rush_mode;
						$AddUserGameMode->kill_keys = $kill_keys;
						$AddUserGameMode->lamp_light_type = $lamp_light_type;
						$AddUserGameMode->lamp_light_name = $lamp_light_name;
						$AddUserGameMode->lamp_light_content = $lamp_light_content;
						$AddUserGameMode->keycap_light_type = $keycap_light_type;
						$AddUserGameMode->keycap_light_name = $keycap_light_name;
						$AddUserGameMode->keycap_light_content = $keycap_light_content;
						$AddUserGameMode->resconstruct_project_type = $resconstruct_project_type;
						$AddUserGameMode->resconstruct_project_name = $resconstruct_project_name;
						if ($AddUserGameMode->save ()) {
							$result ['ret_num'] = 0;
							$result ['ret_msg'] = '操作成功';
							// 返回用户信息
							$result ['id'] = $AddUserGameMode->id;
						} else {
							$result ['ret_num'] = 901;
							$result ['ret_msg'] = '信息添加失败	';
						}
					}
				} else {
					$result ['ret_num'] = 319;
					$result ['ret_msg'] = '游戏名称重复，创建失败';
				}
			} else {
				$result ['ret_num'] = 320;
				$result ['ret_msg'] = '游戏模式达到上限，创建失败	';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 删除用户自定义游戏模式
	 */
	public function actionDeleteUserGameMode() {
		$customer_game_id = Frame::getStringFromRequest ( 'customer_game_id' );
		if (empty ( $customer_game_id )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$gameType = TdUserGameMode::model ()->find ( " userid='{$member_id}' and id='{$customer_game_id}'" );
		$userGamemodetype = $gameType->game_type;
		if ($gameType) {
			$usergameType = TdUserGameMode::model ()->findAll ( " userid='{$member_id}' and game_type='{$userGamemodetype}'" );
			if (count ( $usergameType ) <= 1) {
				$deletegamemode = TdUserGameMode::model ()->deleteAll ( "userid='{$member_id}' and id='{$customer_game_id}'" );
				if ($deletegamemode) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				} else {
					$result ['ret_num'] = 309;
					$result ['ret_msg'] = '没有该记录';
				}
				$usergameType11 = $usergameType [0]->game_type;
				$deleteusergamemode1 = TdUserGameType::model ()->deleteAll ( "userid='{$member_id}' and id='{$usergameType11}'" );
				if ($deleteusergamemode1) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				} else {
					$result ['ret_num'] = 309;
					$result ['ret_msg'] = '没有该记录';
				}
			} else {
				$deletegamemode1 = TdUserGameMode::model ()->deleteAll ( "userid='{$member_id}' and id='{$customer_game_id}'" );
				if ($deletegamemode1) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = '操作成功';
				} else {
					$result ['ret_num'] = 309;
					$result ['ret_msg'] = '没有该记录';
				}
			}
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询自定义用户游戏类型
	 */
	public function actionFindGameType() {
		// 获取用户信息
		$member_info = $this->check_user();
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
			$result ['ret_msg'] = '操作成功';
			$result ['findAllmodeType'] = $arr;
		} else {
			$result ['ret_num'] = 313;
			$result ['ret_msg'] = '该区间没有记录';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询游戏类型下面的游戏模式
	 */
	public function actionGetGameModeByType() {
		$game_type_id = Frame::getStringFromRequest ( 'game_type_id' );
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
				$result ['ret_num'] = 331;
				$result ['ret_msg'] = '无此类型，查询失败';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询游戏模式详情(包含推荐)
	 */
	public function actionGetGameModeDetail() {
		$customer_game_id = Frame::getStringFromRequest ( 'customer_game_id' );
		$recommended_game_id = Frame::getStringFromRequest ( 'recommended_game_id' );
		if (! isset ( $customer_game_id ) || ! isset ( $recommended_game_id )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
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
				$result ['ret_num'] = 313;
				$result ['ret_msg'] = '该区间没有记录';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 推荐游戏模式一键恢复
	 */
	public function actionResetRecommendedGameMode() {
		$recommended_id = Frame::getStringFromRequest ( 'recommended_id' );
		if (empty ( $recommended_id )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$reset = TdRecommendGameMode::model ()->find ( "id='{$recommended_id}'" );
		$reset_id = $reset->id;
		$reset_detete = TdUserGameMode::model ()->deleteAll ( " userid='{$member_id}' and recommended_game_id='{$reset_id}'" );
		if ($reset_detete) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
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
			$result ['ret_msg'] = '操作成功';
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