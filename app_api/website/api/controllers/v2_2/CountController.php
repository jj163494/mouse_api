<?php
class CountController extends PublicController {


	/**
	 * 12.查询测试明细数据
	 */
	public function actionHistoryDetail() {
		$this->check_key ();
		$testId = Frame::getIntFromRequest('testId');

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$testData = TdUploadTestData::model ()->find ( "id={$testId} and userId = {$member_id}" );
		if ($testData) {
			$result['ret_num'] = 0;
			$result['ret_msg'] = 'ok';
			$result['click_num'] = $testData->click_num;
			$result['move_distance'] = $testData->move_distance;
			$result['move_distance_detail'] = $testData->move_distance_detail;
			$result['move_num'] = $testData->move_num;
			$result['move_num_detail'] = $testData->move_num_detail;
			$result['max_apm'] = $testData->max_apm;
			$result['avg_apm'] = $testData->avg_apm;
			$result['apm_detail'] = $testData->apm_detail;
			$result['max_heart'] = $testData->max_heart;
			$result['avg_heart'] = $testData->avg_heart;
			$result['heart_detail'] = $testData->heart_detail;
			$result['heart_num'] = $testData->heart_num;
			$result['max_g'] = $testData->max_g;
			$result['avg_g'] = $testData->avg_g;
			$result['g_detail'] = $testData->g_detail;
			$result['final_score'] = $testData->final_score;
			$result['game_type'] = $testData->game_type;
			$result['time_long'] = $testData->time_long;
			$result['grade'] = $testData['grade'];
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}

	
	/**
	 * 获取未被选中的明星dpi数据
	 */
	public function actionStarDpi() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取所有明星DPI设置
		$dpis = TdDpi::model ()->findAll ();
		if ($dpis) {
			$userDpis = TdUserDpi::model ()->findAll ( "userId={$member_id} && dpi_id >0" );
			foreach ( $dpis as $key => $value ) {
				$arr [] = array (
						"dpiId" => $value->id,
						"dpi" => $value->dpi,
						"nick" => $value->nick,
						"usernameCn" => $value->username_cn,
						"usernameEn" => $value->username_en,
						"profession" => $value->profession,
						"achievement" => $value->achievement,
						"equipment" => $value->equipment,
						"desc" => $value->desc,
						"equipment" => $value->equipment,
						"star_type" => $value->star_type
				);
			}
		} else {
			$arr = array ();
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['dpis'] = $arr;

		echo json_encode ( $result );
	}
	
	/**
	 * 获取用户自定义dpi数据
	 */
	public function actionGetCustomDpis() {
		$mouseSpec = Frame::getStringFromRequest ( 'mouseSpec' );

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取所有明星DPI设置
		$dpis = TdDpi::model ()->findAll ();

		// 获取用户设置的dpi
		if (empty ( $mouseSpec )) {
			$selectedDpis = TdUserDpi::model ()->findAll ( "userid={$member_id} && (mouse_spec is null or mouse_spec='')" );
		} else {
			$selectedDpis = TdUserDpi::model ()->findAll ( "userid={$member_id} && mouse_spec='{$mouseSpec}'" );
		}
		if ($selectedDpis) {
			foreach ( $selectedDpis as $key1 => $value1 ) {
				if ($value1->dpi_id == 0) {
					$customDpis [] = array (
							"cdpiindex" => $value1->custom_dpi_index,
							"cdpiname" => $value1->custom_dpi_name,
							"cdpivalue" => $value1->custom_dpi_value
					);
				}
			}
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['dpis'] = $customDpis;

		echo json_encode ( $result );
	}
	
	/**
	 * 新增自定义的dpi数据
	 */
	public function actionAddCustomDpi() {
		$dpiIndex = Frame::getIntFromRequest ( 'dpiIndex' );
		$dpiName = Frame::getStringFromRequest ( 'dpiName' );
		$dpiValue = Frame::getIntFromRequest ( 'dpiValue' );

		// 获取用户信息
		$member_info = $this->check_user();

		// 获取之前的选中dpi
		$model = new TdUserDpi ();
		$model->userId = $member_info['member_id'];
		$model->dpi_id = 0;
		$model->custom_dpi_index = $dpiIndex;
		$model->custom_dpi_name = $dpiName;
		$model->custom_dpi_value = $dpiValue;
		$model->created_time = time ();
		if ($model->save ()) {
			$result ['userDpiId'] = $model->attributes ['id'];
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 905;
			$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 上传自定义的dpi数据
	 */
	public function actionUploadCustomDpi() {
		$mouseSpec = Frame::getStringFromRequest ( 'mouseSpec' );
		$dpiIndex = Frame::getIntFromRequest ( 'dpiIndex' );
		$dpiName = stripslashes ( Frame::getStringFromRequest ( 'dpiName' ) );
		$dpiValue = Frame::getIntFromRequest ( 'dpiValue' );

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取之前的选中dpi
		$model = new TdUserDpi ();
		if (empty ( $mouseSpec )) {
			$dpi = $model->find ( "userid={$member_id} && custom_dpi_index={$dpiIndex} && (mouse_spec='' OR mouse_spec IS null )" );
			if ($dpi) {
				$dpi->custom_dpi_name = $dpiName;
				$dpi->custom_dpi_value = $dpiValue;
				$dpi->created_time = time ();
				if ($dpi->update ()) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 906;
					$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
				}
			} else {
				$model->userId = $member_id;
				$model->dpi_id = 0;
				$model->mouse_spec = '';
				$model->custom_dpi_index = $dpiIndex;
				$model->custom_dpi_name = $dpiName;
				$model->custom_dpi_value = $dpiValue;
				$model->created_time = time ();
				if ($model->save ()) {
					$result ['userDpiId'] = $model->attributes ['id'];
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 905;
					$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
				}
			}
		} else {
			$dpi = $model->find ( "userid={$member_id} && custom_dpi_index={$dpiIndex} && mouse_spec='{$mouseSpec}' " );
			if ($dpi) {
				$dpi->mouse_spec = $mouseSpec;
				$model->custom_dpi_index = $dpiIndex;
				$dpi->custom_dpi_name = $dpiName;
				$dpi->custom_dpi_value = $dpiValue;
				$dpi->created_time = time ();
				if ($dpi->update ()) {
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 906;
					$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
				}
			} else {
				$model->userId = $member_id;
				$model->dpi_id = 0;
				$model->mouse_spec = $mouseSpec;
				$model->custom_dpi_index = $dpiIndex;
				$model->custom_dpi_name = $dpiName;
				$model->custom_dpi_value = $dpiValue;
				$model->created_time = time ();
				if ($model->save ()) {
					$result ['userDpiId'] = $model->attributes ['id'];
					$result ['ret_num'] = 0;
					$result ['ret_msg'] = 'ok';
				} else {
					$result ['ret_num'] = 905;
					$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
				}
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * dpi设定值
	 */
	public function actionDpi() {
		$this->check_key ();
		$dpi = Frame::getIntFromRequest ( 'dpi' );

		// 获取用户信息
		$member_info = $this->check_user();

		$model = new TdDpi ();
		$model->userId = $member_info['member_id'];
		$model->dpi = $dpi;
		$model->created_time = time ();
		if ($model->save ()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 907;
			$result ['ret_msg'] = '录入失败';
		}
		echo json_encode ( $result );
	}
	
	/**
	 * 获取推荐dpi
	 */
	public function actionGetRecommendedDpi() {
		// $this->check_key ();
		$screenType = Frame::getIntFromRequest ( 'screenType' );
		$mousePadType = Frame::getIntFromRequest ( 'mousePadType' );

		// 获取用户信息
		$member_info = $this->check_user();

		// 获取推荐DPI
		$recommendedDpi = TdRecommendedDpi::model ()->find ( "screen_type=$screenType && mouse_pad_type=$mousePadType" );
		if ($recommendedDpi) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['recommended_dpi'] = $recommendedDpi->recommended_dpi;
		} else {
			$result ['ret_num'] = 313;
			$result ['ret_msg'] = '没有该推荐的dpi';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 灯光设定值
	 */
	public function actionLight() {
		$this->check_key ();
		$Lstatus = Frame::getIntFromRequest ( 'Lstatus' );
		$Bstatus = Frame::getIntFromRequest ( 'Bstatus' );
		$colorNum = Frame::getStringFromRequest ( 'colorNum' );
		$brightness = Frame::getStringFromRequest ( 'brightness' );
		if (empty ( $colorNum ) || empty ( $brightness )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();

		$model = new TdLight ();
		$model->userId = $member_info['member_id'];
		$model->Lstatus = $Lstatus;
		$model->Bstatus = $Bstatus;
		$model->color_num = $colorNum;
		$model->brightness = $brightness;
		if ($model->save ()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 907;
			$result ['ret_msg'] = '录入失败';
		}
		echo json_encode ( $result );
	}
	/**
	 * 取得排行榜
	 */
	public function actionRank() {
		$gameType = Frame::getIntFromRequest ( 'gameType' );
		$orderType = Frame::getIntFromRequest ( 'orderType' );
		// 实例化模型
		if (empty ( $gameType ) || empty ( $orderType )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];
			
		// 取得排行榜排名
		$arr = array (
				"select" => "userId,target_num", // 要查询的字段
				"condition" => "order_type = $orderType and game_type = $gameType ",
				"order" => "target_num desc limit 50"  // 查询条件
					);
		$rank = new TdRank ();
		$rankList = $rank->findAll ( $arr );

		// 取得自己的排名
		$rankNo = 0;
		foreach ( $rankList as $key => $value ) {
			$rankNo ++;
			if ($value->userId == $member_id) {
				$array ["userId"] = $member_id;
				$array ["rank"] = $rankNo;
				$array ["userName"] = $member_info->username;
				if ($member_info->member_avatar) {
					if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
						$array ["header"] = $member_info->member_avatar;
					} else {
						$array ["header"] = $this->webroot () . $member_info->member_avatar;
					}
				} else {
					$array ["header"] = "";
				}
				if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
					$headImg = $member_info->member_avatar;
				} else {
					$headImg = $this->webroot () . $member_info->member_avatar;
				}
				$array ["target_num"] = $value->target_num;
				$array ["level"] = 1;
				$array ["is_myself"] = 1;
				$listdata [] = $array;
				break;
			}
		}

		// 取得各用户的排名
		$rankNo = 0;
		foreach ( $rankList as $key => $value ) {
			$member = TaidushopMember::model ()->find ( "member_id ={$value->userId}" );
			if ($member) {
				$array ["userName"] = $member->member_name;
				if ($member->member_avatar) {
					if (strpos ( $member->member_avatar, "http://" ) === 0) {
						$array ["header"] = $member->member_avatar;
					} else {
						$array ["header"] = $this->webroot () . $member->member_avatar;
					}
				} else {
					$array ["header"] = "";
				}
				$array ["level"] = 1;
			} else {
				continue;
			}
			$rankNo ++;
			$array ["rank"] = $rankNo;
			$array ["target_num"] = $value->target_num;
			$member = TaidushopMember::model ()->find ( "member_id ={$value->userId}" );
			$array ["userId"] = $member->member_id;

			$array ["is_myself"] = 0;
			$listdata [] = $array;
		}

		if ($listdata) {
			foreach ( $listdata as $key => $value ) {
				$result ["data"] [] = $value;
			}
		} else {
			$result ["data"] = null;
		}

		if ($result) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}


	/**
	 * 分析自己的测试数据
	 */
	public function actionTestDataCount() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$rank = new TdRank ();
		$rts = TdUploadTestData::model ()->count ( "userId = {$member_id} and game_type=1" );
		$moba = TdUploadTestData::model ()->count ( "userId = {$member_id} and game_type=2" );
		$fps = TdUploadTestData::model ()->count ( "userId = {$member_id} and game_type=3" );
		$other = TdUploadTestData::model ()->count ( "userId = {$member_id} and game_type=4" );

		$result ["rts"] = $rts;
		$result ["moba"] = $moba;
		$result ["fps"] = $fps;
		$result ["other"] = $other;
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		echo json_encode ( $result );
	}


	/**
	 * 取得累计数据
	 */
	public function actionTabulateData() {
		$userId = Frame::getStringFromRequest ( 'userId' );

		// 获取用户信息
		$member_info = $this->check_user();

		if (empty ( $userId )) {
			$userId = $member_info['member_id'];
		}
		$testCount = 0;
		$testTime = 0;
		$testSCount = 0;
		$testACount = 0;
		$testDatas = TdUploadTestData::model ()->findAll ( "userId = {$userId}" );
		foreach ( $testDatas as $k => $v ) {
			$testCount ++;
			$testTime += $v->time_long;
			if ($v->final_score >= 7001) {
				$testSCount ++;
			} else if ($v->final_score >= 5601 && $v->final_score <= 7000) {
				$testACount ++;
			}
		}

		// 取得个人信息
		if ($userId != $member_info->member_id) {
			$member_info = TaidushopMember::model ()->find ( "member_id='{$userId}'" );
		}
		$result ["user_name"] = $member_info->member_name;
		if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
			$result ["header"] = $member_info->member_avatar;
		} else {
			$result ["header"] = $this->webroot () . $member_info->member_avatar;
		}
		$result ["sex"] = $member_info->member_sex;
		$result ["birthday"] = $member_info->member_birthday;

		$result ["testCount"] = $testCount;
		$result ["testTime"] = $testTime;
		$result ["testSCount"] = $testSCount;
		$result ["testACount"] = $testACount;
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		echo json_encode ( $result );
	}
}
