<?php
class UploadController extends PublicController {
	/**
	 * 上传心跳信息
	 */
	public function actionHeart() {
		$this->check_key ();
		$distance = Frame::getStringFromRequest ( 'distance' );
		$lClickNum = Frame::getIntFromRequest ( 'lClickNum' );
		$rClickNum = Frame::getIntFromRequest ( 'rClickNum' );
		$isCount = Frame::getIntFromRequest ( 'isCount' );
		$distance = doubleval ( $distance );
		if (empty ( $distance ) && empty ( $lClickNum ) && empty ( $rClickNum )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$model = new TdHeart ();
		$model->userid = $member_id;
		$model->distance = $distance;
		$model->click_num = $lClickNum + $rClickNum;
		$model->left_click_num = $lClickNum;
		$model->right_click_num = $rClickNum;
		$model->knock_counters = 0;
		$model->created_time = time ();

		if (! $model->save ()) {
			$result ['ret_num'] = 911;
			$result ['ret_msg'] = '添加失败';
			echo json_encode ( $result );
			die ();
		}

		if (empty ( $isCount ) || $isCount == 0) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '添加成功';
		} else {
			// 统计总点击数据和移动距离
			$arr2 = array (
				"select" => "sum(distance) distance, sum(left_click_num) left_click_num, sum(right_click_num) right_click_num", // 要查询的字段
				"condition" => "userid='{$member_id}' "  // 查询条件
			);
			$info2 = $model->find ( $arr2 );
			if ($info2) {
				$distanceAll = $info2->distance;
				$lClickNumAll = $info2->left_click_num;
				$rClickNumAll = $info2->right_click_num;

				$result ["sync"] = array (
					"distanceAll" => $distanceAll,
					"lClickNumAll" => $lClickNumAll,
					"rClickNumAll" => $rClickNumAll
				);
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 312;
				$result ['ret_msg'] = '总记录数不存在';
			}
		}

		echo json_encode ( $result );
	}


	/**
	 * 91.获取键盘点击次数
	 */
	public function actionGetKeyboardCounters(){
		$result = array();

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$heart_model = new TdHeart ();
		// 查询是否已存在用户数据
		$heart_info = $heart_model->find(
			array(
				'condition' => "userid = '{$member_id}'",
				'order' => "id desc"
			)
		);

		$knock_counters = Frame::getIntFromRequest('knockCounters');
		if(empty($knock_counters)){
			// 如果没有上传点击次数,则返回键盘敲击次数
			if(!empty($heart_info)){
				$knock_counters = $heart_info['knock_counters'];
			}else{
				// 没有数据时依旧返回0
				$knock_counters = 0;
			}

			$result['ret_num'] = 0;
			$result['ret_msg'] = 'ok';
			$result['knock_counters'] = $knock_counters;
			echo json_encode ( $result );
			die();
		}

		if(!empty($heart_info)){
			// 有数据的情况下进行编辑
			$heart_model->isNewRecord = false;
			$heart_model->id = $heart_info->id;
		}
		$heart_model->userid = $member_id;
		$heart_model->distance = 0;
		$heart_model->click_num = 0;
		$heart_model->left_click_num = 0;
		$heart_model->right_click_num = 0;
		$heart_model->knock_counters = $knock_counters;
		if(empty($heart_info)){
			$heart_model->created_time = time();
		}

		// 保存键盘敲击次数
		if($heart_model->save()){
			$result['ret_num'] = 0;
			$result['ret_msg'] = 'ok';
			$result['knock_counters'] = $knock_counters;
		}else{
			$result['ret_num'] = 905;
			$result['ret_msg'] = '保存时发生系统错误，请重新操作';
		}

		echo json_encode ( $result );
	}

	
	/**
	 * 数据清零
	 */
	public function actionClearData() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		TdHeart::model ()->deleteAll ( "userid={$member_id}" );

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';

		echo json_encode ( $result );
	}
	
	/**
	 * 数据同步
	 */
	public function actionSync() {
		$this->check_key ();
		$createdTime = Frame::getIntFromRequest ( 'createdTime' );
		if (empty ( $createdTime )) {
			$result ['ret_num'] = 206;
			$result ['ret_msg'] = '上次获取时间或用户标识为空';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$model = new TdHeart ();
		$arr1 = array (
				"select" => "distance,click_num", // 要查询的字段
				"condition" => "userid='{$member_id}' and created_time between '{$createdTime}' and " . time () . ""  // 查询条件
					);
		$arr2 = array (
				"select" => "distance,click_num", // 要查询的字段
				"condition" => "userid='{$member_id}' "  // 查询条件
					);
		$info1 = $model->findAll ( $arr1 );
		$info2 = $model->findAll ( $arr2 );
		$newDistance = 0;
		$newClickNum = 0;
		$distanceAll = 0;
		$clickNumAll = 0;
		if ($info1) {
			foreach ( $info1 as $key => $value ) {
				$newDistance = $newDistance + $value ['distance'];
				$newClickNum = $newClickNum + $value ['click_num'];
			}
		} else {
			$result ['ret_num'] = 313;
			$result ['ret_msg'] = '该区间没有记录';
			echo json_encode ( $result );
		}
		if ($info2) {
			foreach ( $info2 as $key => $value ) {
				$distanceAll = $distanceAll + $value ['distance'];
				$clickNumAll = $clickNumAll + $value ['click_num'];
			}
		} else {
			$result ['ret_num'] = 312;
			$result ['ret_msg'] = '总记录数不存在';
			echo json_encode ( $result );
		}
		// echo $distanceAll."<br>".$newDistance."<br>";echo $clickNumAll."<br>".$newClickNum;die;
		$result ["sync"] = array (
				"newDistance" => $newDistance,
				"newClickNum" => $newClickNum,
				"distanceAll" => $distanceAll,
				"clickNumAll" => $clickNumAll
		);
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		echo json_encode ( $result );
	}
	/**
	 * 上传测试数据
	 */
	public function actionUploadData() {
		// 点击次数
		$clickNum = Frame::getIntFromRequest ( 'clickNum' );
		// 移动距离
		$moveDistance = Frame::getStringFromRequest ( 'moveDistance' );
		// 移动距离明细
		$moveDistanceDetail = Frame::getStringFromRequest ( 'moveDistanceDetail' );
		// 移动次数
		$moveNum = Frame::getIntFromRequest ( 'moveNum' );
		// 移动次数明细
		$moveNumDetail = Frame::getStringFromRequest ( 'moveNumDetail' );
		// 游戏时长(秒)
		$timeLong = Frame::getIntFromRequest ( 'timeLong' );
		// 游戏类型
		$gameType = Frame::getIntFromRequest ( 'gameType' );
		// 最低心率
		$minHeart = Frame::getIntFromRequest ( 'minHeart' );
		// 最高心率
		$maxHeart = Frame::getIntFromRequest ( 'maxHeart' );
		// 平均心率
		$avgHeart = Frame::getIntFromRequest ( 'avgHeart' );
		// 心跳次数
		$heartNum = Frame::getIntFromRequest ( 'heartNum' );
		// 心率明细
		$heartDetail = Frame::getStringFromRequest ( 'heartDetail' );
		// 最高手速
		$maxApm = Frame::getIntFromRequest ( 'maxApm' );
		// 平均手速
		$avgApm = Frame::getIntFromRequest ( 'avgApm' );
		// apm手速明细
		$apmDetail = Frame::getStringFromRequest ( 'apmDetail' );
		// 最低加速度
		$minG = Frame::getIntFromRequest ( 'minG' );
		// 最高加速度
		$maxG = Frame::getIntFromRequest ( 'maxG' );
		// 平均加速度
		$avgG = Frame::getIntFromRequest ( 'avgG' );
		// 加速度明细
		$gDetail = Frame::getStringFromRequest ( 'gDetail' );
		// 综合得分
		$finalScore = Frame::getIntFromRequest ( 'finalScore' );
		// 综合评分
		$grade = Frame::getStringFromRequest ( 'grade' );

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$model = new TdUploadTestData ();
		$model->userId = $member_id;
		$model->click_num = $clickNum;
		$model->move_distance = $moveDistance;
		$model->move_distance_detail = $moveDistanceDetail;
		$model->move_num = $moveNum;
		$model->move_num_detail = $moveNumDetail;
		$model->heart_num = $heartNum;
		$model->time_long = $timeLong;
		$model->max_apm = $maxApm;
		$model->avg_apm = $avgApm;
		$model->apm_detail = $apmDetail;
		$model->min_heart = $minHeart;
		$model->max_heart = $maxHeart;
		$model->avg_heart = $avgHeart;
		$model->heart_detail = $heartDetail;
		$model->min_g = $minG;
		$model->max_g = $maxG;
		$model->avg_g = $avgG;
		$model->g_detail = $gDetail;
		$model->final_score = $finalScore;
		$model->created_time = time ();
		$model->game_type = $gameType;
		$model->grade = $grade;

		if ($model->save ()) {
			// 查询日排行榜
			$dayRank = TdRank::model ()->find ( "userId={$member_id} and order_type=1 and game_type={$gameType}" );
			if ($dayRank) {
				if ($dayRank->target_num < $finalScore) {
					// 更新日排行榜
					$dayRank->target_num = $finalScore;
					$dayRank->created_time = time ();
					if (! $dayRank->save ()) {
						$result ['ret_num'] = 905;
						$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
						echo json_encode ( $result );
						die ();
					}
				}
			} else {
				// 插入日排行榜
				$modelRank = new TdRank ();
				$modelRank->userId = $member_id;
				$modelRank->order_type = 1;
				$modelRank->game_type = $gameType;
				$modelRank->target_num = $finalScore;
				$modelRank->created_time = time ();
				if (! $modelRank->save ()) {
					$result ['ret_num'] = 905;
					$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
					echo json_encode ( $result );
					die ();
				}
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 905;
			$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
		}
		echo json_encode ( $result );
	}
		
	/**
	 * 数据统计
	 */
	public function actionStatistics() {
		$this->check_key ();

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$model = new TdHeart ();
		$today = strtotime ( date ( "Y-m-d" ) );
		$arr1 = array (
				"select" => "distance,click_num,left_click_num,right_click_num", // 要查询的字段
				"condition" => "userid='{$member_id}' and created_time between '{$today}' and " . time () . ""  // 查询条件
					);
		$arr2 = array (
				"select" => "distance,click_num,left_click_num,right_click_num", // 要查询的字段
				"condition" => "userid='{$member_id}' "  // 查询条件
					);
		$info1 = $model->findAll ( $arr1 );
		$info2 = $model->findAll ( $arr2 );
		$newDistance = 0;
		$newLClickNum = 0;
		$newRClickNum = 0;
		$distanceAll = 0;
		$lClickNumAll = 0;
		$rClickNumAll = 0;
		if ($info1) {
			foreach ( $info1 as $key => $value ) {
				$newDistance = $newDistance + $value ['distance'];
				$newLClickNum = $newLClickNum + $value ['left_click_num'];
				$newRClickNum = $newRClickNum + $value ['right_click_num'];
			}
		}
		if ($info2) {
			foreach ( $info2 as $key => $value ) {
				$distanceAll = $distanceAll + $value ['distance'];
				$lClickNumAll = $lClickNumAll + $value ['left_click_num'];
				$rClickNumAll = $rClickNumAll + $value ['right_click_num'];
			}
		}

		$result ["count"] = array (
				"newDistance" => $newDistance,
				"lClickNum" => $newLClickNum,
				"rClickNum" => $newRClickNum,
				"distanceAll" => $distanceAll,
				"lClickNumAll" => $lClickNumAll,
				"rClickNumAll" => $rClickNumAll
		);
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		echo json_encode ( $result );
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v1_1/upload/getTrainingInfoByDate",
	 *     summary="89.查询指定天数训练数据详情",
	 *     tags={"Upload"},
	 *     description="根据用户标识和日期获取当天的训练数据详情",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="open_id",
	 *         in="query",
	 *         description="用户标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="date",
	 *         in="query",
	 *         description="日期",
	 *         required=true,
	 *         type="string",
	 *     	   format="date",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         maxLength=8,
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户设备列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="total_training_counters",
	 *                 description="合计训练次数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="total_time_long",
	 *                 description="总训练时长(秒)",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="total_final_score",
	 *                 description="合计职业指数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="avg_final_score",
	 *                 description="平均职业指数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="training_data",
	 *                 description="训练详情",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="click_num",
	 *                             description="点击次数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_distance",
	 *                             description="移动距离(毫米)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_apm",
	 *                             description="平均手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="apm_detail",
	 *                             description="手速明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_heart",
	 *                             description="平均心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="heart_detail",
	 *                             description="心率明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_g",
	 *                             description="平均加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="g_detail",
	 *                             description="加速度明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="game_type",
	 *                             description="游戏类型",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="time_long",
	 *                             description="训练时长(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="final_score",
	 *                             description="职业指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="created_time",
	 *                             description="创建时间",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_num",
	 *                             description="移动次数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="grade",
	 *                             description="职业综合评价",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="apm_score",
	 *                             description="手速得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="mental_score",
	 *                             description="心态得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="agi_score",
	 *                             description="敏捷得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="mc_score",
	 *                             description="移动次数得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="md_score",
	 *                             description="移动距离得分",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *             @SWG\Property(
	 *                 property="best_data",
	 *                 description="最佳数据",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="final_score",
	 *                             description="职业指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="grade",
	 *                             description="职业综合评价",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="time_long",
	 *                             description="训练时长(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="created_time",
	 *                             description="训练时间",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_distance",
	 *                             description="移动距离(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_num",
	 *                             description="移动次数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_apm",
	 *                             description="最高手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_heart",
	 *                             description="最高心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_g",
	 *                             description="最大加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_apm",
	 *                             description="平均手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_heart",
	 *                             description="平均心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_g",
	 *                             description="平均加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="game_type",
	 *                             description="游戏类型",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetTrainingInfoByDate() {
		$date = Frame::getStringFromRequest ( 'date' );
		if (empty ( $date )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取时间戳
		$begin_time = strtotime ( $date );
		$time = date ( 'Y-m-d', $begin_time );
		$end_time = strtotime ( $time ) + 86400 - 1;

		// 获取当前用户在指定时间段的所有数据
		$upload_model = new TdUploadTestData();
		$where = "userId = '{$member_id}' and created_time BETWEEN '{$begin_time}' AND '{$end_time}'";
		$test_data_list = $upload_model->getUploadTestDataList($where);

		// 计算当日训练次数,总训练时间,平均得分 2016/11/16
		if(!empty($test_data_list)){
			// 合计训练次数
			$total_training_counters = count($test_data_list);
			// 总训练时长 (秒)
			$total_time_long = 0;
			// 合计得分
			$total_final_score = 0;

			$training_data = array();
			foreach($test_data_list as $k => $data_info){
				$total_time_long += $data_info['time_long'];
				$total_final_score += $data_info['final_score'];

				// 记录历史数据
				$training_data[$k] = array(
					'click_num' => $data_info['click_num'],																	// 点击次数
					'move_distance' => $data_info['move_distance'],															// 移动距离
					'avg_apm' => $data_info['avg_apm'],																		// 平均手速
					'apm_detail' => $data_info['apm_detail'],																// 手速明细
					'avg_heart' => $data_info['avg_heart'],																	// 平均心率
					'heart_detail' => $data_info['heart_detail'],															// 心率明细
					'avg_g' => $data_info['avg_g'],																			// 平均加速度
					'g_detail' => $data_info['g_detail'],																	// 加速度明细
					'game_type' => $data_info['game_type'],																	// 游戏类型
					'time_long' => $data_info['time_long'],																	// 训练时长
					'final_score' => $data_info['final_score'],																// 职业指数
					'created_time' => $data_info['created_time'],															// 创建时间
					'move_num' => $data_info['move_num'],																	// 移动次数
					'grade' => $data_info['grade'],																			// 职业综合评价
					'apm_score' => intval($data_info['apm_score']),															// 手速得分
					'mental_score' => intval($data_info['mental_score']),													// 心态得分
					'agi_score' => intval($data_info['agi_score']),															// 敏捷得分
					'mc_score' => intval($data_info['mc_score']),															// 移动次数得分
					'md_score' => intval($data_info['md_score']),															// 移动距离得分
				);
			}

			// 查询符合条件的最高分数据
			$best_data = $upload_model->getUploadTestDataInfo($where, "*", 'final_score DESC, created_time DESC, time_long ASC');

			$result = array (
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'total_training_counters' => $total_training_counters,														// 合计训练次数
				'total_time_long' => $total_time_long,																		// 总训练时长(秒)
				'total_final_score' => $total_final_score,																	// 合计职业指数
				'avg_final_score' => floor($total_final_score / $total_training_counters),									// 平均职业指数
				'training_data' => $training_data,																			// 训练历史记录
				'best_data' => array(
					// 最佳数据
					'final_score' => $best_data['final_score'],																// 职业指数
					'grade' => $best_data['grade'],																			// 职业综合评价
					'time_long' => $best_data['time_long'],																	// 训练时长
					'created_time' => $best_data['created_time'],															// 训练时间
					'move_distance' => $best_data['move_distance'],															// 移动距离
					'move_num' => $best_data['move_num'],																	// 移动次数
					'max_apm' => $best_data['max_apm'],																		// 最高手速
					'max_heart' => $best_data['max_heart'],																	// 最高心率
					'max_g' => $best_data['max_g'],																			// 最大加速度
					'avg_apm' => $best_data['avg_apm'],																		// 平均手速
					'avg_heart' => $best_data['avg_heart'],																	// 平均心率
					'avg_g' => $best_data['avg_g'],																			// 平均加速度
					'game_type' => $best_data['game_type'],																	// 游戏类型
				)
			);
		}else{
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v1_1/upload/getTrainingResult",
	 *     summary="90.测试数据计算3.4",
	 *     tags={"Upload"},
	 *     description="上传训练数据计算训练结果",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="open_id",
	 *         in="query",
	 *         description="用户标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="clickNum",
	 *         in="query",
	 *         description="点击数",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="timeLong",
	 *         in="query",
	 *         description="训练时长",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="maxApm",
	 *         in="query",
	 *         description="最高手速",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="avgApm",
	 *         in="query",
	 *         description="平均手速",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="apmDetail",
	 *         in="query",
	 *         description="手速详细",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="heartNum",
	 *         in="query",
	 *         description="心跳次数",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="minHeart",
	 *         in="query",
	 *         description="最低心率",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="maxHeart",
	 *         in="query",
	 *         description="最高心率",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="avgHeart",
	 *         in="query",
	 *         description="平均心率",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="heartDetail",
	 *         in="query",
	 *         description="心率详细",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="minG",
	 *         in="query",
	 *         description="最低加速度",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="maxG",
	 *         in="query",
	 *         description="最高加速度",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="avgG",
	 *         in="query",
	 *         description="平均加速度",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gDetail",
	 *         in="query",
	 *         description="加速度详细",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="moveNum",
	 *         in="query",
	 *         description="移动次数",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="moveNumDetail",
	 *         in="query",
	 *         description="移动次数详细",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="moveDistanceDetail",
	 *         in="query",
	 *         description="移动距离明细",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gameType",
	 *         in="query",
	 *         description="游戏类型",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="maxMoveCount",
	 *         in="query",
	 *         description="移动次数最大值",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="maxMoveDistance",
	 *         in="query",
	 *         description="移动距离最大值",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户设备列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="apm_score",
	 *                 description="手速得分",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="mc_score",
	 *                 description="移动次数得分",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="md_score",
	 *                 description="移动距离得分",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="mental_info",
	 *                 description="心态明细",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="heart_score",
	 *                             description="稳定指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="heart_rank",
	 *                             description="稳定评价",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="mental_score",
	 *                             description="心态得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="excited_time",
	 *                             description="激动时间百分比",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="full_heart",
	 *                             description="完整心率",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *             @SWG\Property(
	 *                 property="agi_info",
	 *                 description="敏捷明细",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="agi_score",
	 *                             description="敏捷得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="agi_rank",
	 *                             description="敏捷评价",
	 *                             type="string",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *             @SWG\Property(
	 *                 property="pro_info",
	 *                 description="职业评价明细",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="final_score",
	 *                             description="职业指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="grade",
	 *                             description="职业综合评价",
	 *                             type="string",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="212",
	 *         description="游戏类型不符合规则",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="905",
	 *         description="保存失败",
	 *     ),
	 * )
	 */
	public function actionGetTrainingResult(){
		// 获取用户信息
		$member_info = $this->check_user();

		// 获取时间长度
		$time_long = Frame::getIntFromRequest('timeLong');
		// 获取游戏类型
		$game_type = Frame::getIntFromRequest('gameType');
		if(empty($game_type) || !in_array($game_type, array(1, 2, 3))){
			$result ['ret_num'] = 212;
			$result ['ret_msg'] = '游戏类型不符合规则';
			echo json_encode ( $result );
			die ();
		}

		// 1.计算apm得分
		$avg_apm = Frame::getIntFromRequest('avgApm');
		$apm_score = $this->_get_apm_score($avg_apm, $game_type);

		// 2.计算心态得分
		$avg_heart = Frame::getIntFromRequest('avgHeart');
		$min_heart = Frame::getIntFromRequest('minHeart');
		$max_heart = Frame::getIntFromRequest('maxHeart');
		$mental_info = $this->_get_mental_info($avg_heart, $min_heart, $max_heart, $game_type, $time_long);

		// 3.计算敏捷度得分
		$avg_agi = Frame::getIntFromRequest('avgG');
		$min_agi = Frame::getIntFromRequest('minG');
		$max_agi = Frame::getIntFromRequest('maxG');
		$agi_info = $this->_get_agi_info($avg_agi, $min_agi, $max_agi, $game_type);

		// 5.计算移动次数得分
		$move_num = Frame::getIntFromRequest('moveNum');
		$max_move_count = Frame::getIntFromRequest('maxMoveCount');
		$mc_score = $this->_get_move_count_score($move_num, $max_move_count, $game_type, $time_long);

		// 6.计算移动距离得分
		$move_distance = Frame::getIntFromRequest('moveDistance');
		$max_move_distance = Frame::getIntFromRequest('maxMoveDistance');
		$md_score = $this->_get_move_distance_score($move_distance, $max_move_distance, $game_type, $time_long);

		// 4.计算职业指数
		$pro_info = $this->_get_pro_info($apm_score, $mental_info['mental_score'], $agi_info['agi_score'], $mc_score, $md_score, $game_type, $time_long);


		// 保存数据
		$td_upload_model = new TdUploadTestData();
		$td_upload_model->userId = $member_info['member_id'];											// 用户id
		$td_upload_model->click_num = Frame::getIntFromRequest('clickNum');								// 点击数
		$td_upload_model->move_distance = $move_distance;												// 移动距离
		$td_upload_model->max_apm = Frame::getIntFromRequest('maxApm');									// 最高手速
		$td_upload_model->avg_apm = $avg_apm;															// 平均手速
		$td_upload_model->apm_detail = Frame::getStringFromRequest('apmDetail');						// 手速详细
		$td_upload_model->min_heart = $min_heart;														// 最低心率
		$td_upload_model->max_heart = $max_heart;														// 最高心率
		$td_upload_model->avg_heart = $avg_heart;														// 平均心率
		$td_upload_model->heart_detail = Frame::getStringFromRequest('heartDetail');					// 心率详细
		$td_upload_model->heart_num = Frame::getIntFromRequest('heartNum');								// 心跳次数
		$td_upload_model->min_g = $min_agi;																// 最低加速度
		$td_upload_model->max_g = $max_agi;																// 最高加速度
		$td_upload_model->avg_g = $avg_agi;																// 平均加速度
		$td_upload_model->g_detail = Frame::getStringFromRequest('gDetail');							// 加速度详细
		$td_upload_model->final_score = $pro_info['pro_score'];											// 职业指数
		$td_upload_model->game_type = $game_type;														// 游戏类型
		$td_upload_model->time_long = $time_long;														// 时间长度
		$td_upload_model->created_time = time();														// 创建时间
		$td_upload_model->move_distance_detail = Frame::getStringFromRequest('moveDistanceDetail');		// 移动距离明细
		$td_upload_model->move_num = $move_num;															// 移动次数
		$td_upload_model->move_num_detail = Frame::getStringFromRequest('moveNumDetail');				// 移动次数详细
		$td_upload_model->mouse_spec = Frame::getIntFromRequest('mouseSpec');							// 鼠标规格
		$td_upload_model->grade = $pro_info['pro_rank'];												// 职业综合评价
		$td_upload_model->apm_score = $apm_score;														// apm得分
		$td_upload_model->mental_score = $mental_info['mental_score'];									// 心态得分
		$td_upload_model->agi_score = $agi_info['agi_score'];											// 敏捷得分
		$td_upload_model->mc_score = $mc_score;															// 移动次数得分
		$td_upload_model->md_score = $md_score;															// 移动距离得分

		if ($td_upload_model->save ()) {
			// 返回数据结果集
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'apm_score' => $apm_score,									// apm得分
				'mc_score' => $mc_score,									// 移动次数得分
				'md_score' => $md_score,									// 移动距离得分
				'mental_info' => array(
					// 心态明细
					'heart_score' => $mental_info['heart_score'],			// 稳定指数
					'heart_rank' => $mental_info['heart_rank'],				// 稳定评价
					'mental_score' => $mental_info['mental_score'],			// 心态得分
					'excited_time' => $mental_info['excited_time'],			// 激动时间百分比
					'full_heart' => $mental_info['full_heart'],				// 完整心率
				),
				'agi_info' => array(
					// 敏捷明细
					'agi_score' => $agi_info['agi_score'],					// 敏捷得分
					'agi_rank' => $agi_info['agi_rank'],					// 敏捷评价
				),
				'pro_info' => array(
					// 职业评价明细
					'final_score' => $pro_info['pro_score'],				// 职业指数
					'grade' => $pro_info['pro_rank'],						// 职业综合评价
				),
			);
		}else{
			$result ['ret_num'] = 905;
			$result ['ret_msg'] = '保存时发生系统错误，请重新操作';
		}
		echo json_encode ( $result );
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v1_1/upload/getTrainingSummary",
	 *     summary="92.查询指定时间段训练数据摘要",
	 *     tags={"Upload"},
	 *     description="根据用户标识获取指定时间段的训练数据",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="open_id",
	 *         in="query",
	 *         description="用户标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="dateType",
	 *         in="query",
	 *         description="时间类型",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户设备列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="total_training_days",
	 *                 description="合计训练天数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="total_training_counters",
	 *                 description="合计训练次数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="total_time_long",
	 *                 description="合计训练时间(秒)",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="avg_time_long",
	 *                 description="平均每日训练时间(秒)",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="total_final_score",
	 *                 description="合计职业指数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="avg_final_score",
	 *                 description="平均职业指数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="s_rank_counters",
	 *                 description="S评价次数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="a_rank_counters",
	 *                 description="A评价次数",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="best_data",
	 *                 description="最佳数据",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="final_score",
	 *                             description="职业指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="grade",
	 *                             description="职业综合评价",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="time_long",
	 *                             description="训练时长(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="created_time",
	 *                             description="训练时间",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_distance",
	 *                             description="移动距离(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="move_num",
	 *                             description="移动次数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_apm",
	 *                             description="最高手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_heart",
	 *                             description="最高心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="max_g",
	 *                             description="最大加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_apm",
	 *                             description="平均手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_heart",
	 *                             description="平均心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_g",
	 *                             description="平均加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="game_type",
	 *                             description="游戏类型",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetTrainingSummary(){
		$result = array();
		$date_type = Frame::getIntFromRequest('dateType');

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取查询时间段
		$begin_time = $this->_get_begin_time($date_type);
		// 结束时间为当天23:59:59
		$end_time = strtotime(date('Y-m-d', time())) + 86400 - 1;

		// 获取训练数据摘要
		$upload_model = new TdUploadTestData();
		// 获取符合条件的训练天数,训练次数,训练时长,职业指数
		$table = $upload_model->tableName();
		$sub_field = "FROM_UNIXTIME(created_time, '%Y-%m-%d') AS training_date, COUNT(created_time) AS training_counters, SUM(time_long) AS time_long, SUM(final_score) AS final_score";
		$where = " userId = '{$member_id}' AND time_long IS NOT NULL ";
		// 如果没有初始时间则不设时间段查询
		if(!empty($begin_time)){
			$where .= "AND created_time >= '{$begin_time}' AND created_time <= '{$end_time}' ";
		}
		$group = "FROM_UNIXTIME(created_time, '%Y-%m-%d')";
		$sub_sql = "SELECT {$sub_field} FROM {$table} WHERE {$where} GROUP BY {$group}";

		// 使用子查询计算合计训练天数,合计训练次数,合计训练时长,合计职业指数
		$main_field = "COUNT(*) AS total_training_days, SUM(a.training_counters) AS total_training_counters, SUM(a.time_long) AS total_time_long, SUM(a.final_score) AS total_final_score";
		$main_sql = "SELECT {$main_field} FROM ({$sub_sql}) AS a";
		$command = Yii::app ()->db->createCommand($main_sql);
		$data_list = $command->queryAll();

		if($data_list[0]['total_training_days'] <= 0){
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
			echo json_encode($result);
			die();
		}

		// 查询符合条件的S级的次数
		$s_rank_list = $upload_model->getUploadTestDataInfo($where." AND grade = 'S' ", "count(id) AS id");

		// 查询符合条件的A级的次数
		$a_rank_list = $upload_model->getUploadTestDataInfo($where." AND grade = 'A' ", "count(id) AS id");

		// 查询符合条件的最高分数据
		$best_data = $upload_model->getUploadTestDataInfo($where, "*", 'final_score DESC, created_time DESC, time_long ASC');
		if(empty($best_data)){
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
			echo json_encode($result);
			die();
		}

		// 返回所需数据
		$total_training_days = (!empty($data_list[0]['total_training_days'])) ? $data_list[0]['total_training_days'] : 1;
		$total_training_counters = (!empty($data_list[0]['total_training_counters'])) ? $data_list[0]['total_training_counters'] : 1;
		$total_time_long = (!empty($data_list[0]['total_time_long'])) ? $data_list[0]['total_time_long'] : 0;
		$total_final_score = (!empty($data_list[0]['total_final_score'])) ? $data_list[0]['total_final_score'] : 0;

		$result = array(
			'ret_num' => 0,
			'ret_msg' => 'ok',
			'total_training_days' => $total_training_days,																// 合计训练天数
			'total_training_counters' => $total_training_counters,														// 合计训练次数
			'total_time_long' => $total_time_long,																		// 合计训练时间(秒)
			'avg_time_long' => floor($total_time_long / $total_training_days),											// 平均每日训练时间(秒)
			'total_final_score' => $total_final_score,																	// 合计职业指数
			'avg_final_score' => floor($total_final_score / $total_training_counters),									// 平均职业指数
			's_rank_counters' => (!empty($s_rank_list['id'])) ? $s_rank_list['id'] : 0,									// S评价次数
			'a_rank_counters' => (!empty($a_rank_list['id'])) ? $a_rank_list['id'] : 0,									// A评价次数
			'best_data' => array(
				// 最佳数据
				'final_score' => $best_data['final_score'],																// 职业指数
				'grade' => $best_data['grade'],																			// 职业综合评价
				'time_long' => $best_data['time_long'],																	// 训练时长
				'created_time' => $best_data['created_time'],															// 训练时间
				'move_distance' => $best_data['move_distance'],															// 移动距离
				'move_num' => $best_data['move_num'],																	// 移动次数
				'avg_apm' => $best_data['avg_apm'],																		// 平均手速
				'avg_heart' => $best_data['avg_heart'],																	// 平均心率
				'avg_g' => $best_data['avg_g'],																			// 平均加速度
				'max_apm' => $best_data['max_apm'],																		// 最高手速
				'max_heart' => $best_data['max_heart'],																	// 最高心率
				'max_g' => $best_data['max_g'],																			// 最大加速度
				'game_type' => $best_data['game_type'],																	// 游戏类型
			),
		);

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v1_1/upload/getTrainingList",
	 *     summary="93.查询指定时间段训练数据列表",
	 *     tags={"Upload"},
	 *     description="根据用户标识获得特定用户在指定时间段内的训练数据",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="open_id",
	 *         in="query",
	 *         description="用户标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="userId",
	 *         in="query",
	 *         description="用户id",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="dateType",
	 *         in="query",
	 *         description="时间类型",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gameType",
	 *         in="query",
	 *         description="游戏类型",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="page",
	 *         in="query",
	 *         description="当前页",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="pageSize",
	 *         in="query",
	 *         description="页码长度",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *		   default=10,
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户设备列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="training_list",
	 *                 description="训练数据",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="id",
	 *                             description="主键id",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="created_time",
	 *                             description="创建时间",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="click_num",
	 *                             description="点击次数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_apm",
	 *                             description="平均手速",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="apm_detail",
	 *                             description="手速明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_heart",
	 *                             description="平均心率",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="heart_detail",
	 *                             description="心率明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="avg_g",
	 *                             description="平均加速度",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="g_detail",
	 *                             description="加速度明细",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="game_type",
	 *                             description="游戏类型",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="time_long",
	 *                             description="训练时长(秒)",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="final_score",
	 *                             description="职业指数",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="grade",
	 *                             description="职业综合评价",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="apm_score",
	 *                             description="手速得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="mental_score",
	 *                             description="心态得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="agi_score",
	 *                             description="敏捷得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="mc_score",
	 *                             description="移动次数得分",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="md_score",
	 *                             description="移动距离得分",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="212",
	 *         description="游戏类型不符合规则",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetTrainingList(){
		// 获取用户信息
		$member_info = $this->check_user();

		$user_id = Frame::getStringFromRequest('userId');
		// 如果有输入用户id的情况下使用该id作为查询条件
		$member_id = (!empty($user_id)) ? $user_id : $member_info['member_id'];

		$game_type = Frame::getIntFromRequest('gameType');
		if(!empty($game_type) && !in_array($game_type, array(1, 2, 3))){
			$result ['ret_num'] = 212;
			$result ['ret_msg'] = '游戏类型不符合规则';
			echo json_encode ( $result );
			die ();
		}

		$date_type = Frame::getIntFromRequest('dateType');
		// 获取查询时间段
		$begin_time = $this->_get_begin_time($date_type);
		// 结束时间为当天23:59:59
		$end_time = strtotime(date('Y-m-d', time())) + 86400 - 1;

		$page = Frame::getIntFromRequest('page');
		$page_size = Frame::getIntFromRequest('pageSize');
		// 设置默认页数和页码
		if(empty($page) || $page <= 0){
			$page = 0;
		}
		if(empty($page_size)){
			$page_size = 10;
		}

		// 拼接查询条件
		$where = " userId = '{$member_id}' AND time_long IS NOT NULL ";
		// 如果没有初始时间则不设时间段查询
		if(!empty($begin_time)){
			$where .= " AND created_time >= '{$begin_time}' AND created_time <= '{$end_time}' ";
		}
		// 没有游戏类型时不筛选游戏类型
		if(!empty($game_type)){
			$where .= " AND game_type = '{$game_type}' ";
		}

		// 查询数据
		$upload_model = new TdUploadTestData();
		$data_list = $upload_model->getUploadTestDataList($where, "*", $page_size, $page * $page_size);
		if(!empty($data_list)){
			$training_list = array();
			foreach($data_list as $k => $training_data){
				$training_list[$k] = array(
					'id' => $training_data['id'],								// 主键id
					'created_time' => $training_data['created_time'],			// 创建时间
					'click_num' => $training_data['click_num'],					// 点击次数
					'avg_apm' => $training_data['avg_apm'],						// 平均手速
					'apm_detail' => $training_data['apm_detail'],				// 手速明细
					'avg_heart' => $training_data['avg_heart'],					// 平均心率
					'heart_detail' => $training_data['heart_detail'],			// 心率明细
					'avg_g' => $training_data['avg_g'],							// 平均加速度
					'g_detail' => $training_data['g_detail'],					// 加速度明细
					'game_type' => $training_data['game_type'],					// 游戏类型
					'time_long' => $training_data['time_long'],					// 游戏时长(秒)
					'final_score' => $training_data['final_score'],				// 职业指数
					'grade' => $training_data['grade'],							// 职业指数评价
					'apm_score' => intval($training_data['apm_score']),			// 手速得分
					'mental_score' => intval($training_data['mental_score']),	// 心态得分
					'agi_score' => intval($training_data['agi_score']),			// 敏捷得分
					'mc_score' => intval($training_data['mc_score']),			// 移动次数得分
					'md_score' => intval($training_data['md_score']),			// 移动距离得分
				);
			}

			// 拼接返回数组
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'training_list' => $training_list			// 符合查询条件的训练数据
			);
		}else{
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode($result);
	}


	/**
	 * 根据时间类型获取查询的开始时间
	 * @param $date_type
	 * @return int
	 */
	private function _get_begin_time($date_type){
		// 结束时间为当天
		$now = time() + 86400;
		switch ($date_type){
			case 1:
				// 周
				$begin_time = strtotime(date('Y-m-d', strtotime('-7 day', $now)));
				break;
			case 2:
				// 月
				$begin_time = strtotime(date('Y-m-d', strtotime('-1 month', $now)));
				break;
			case 3:
				// 季
				$begin_time = strtotime(date('Y-m-d', strtotime('-3 month', $now)));
				break;
			case 4:
				// 年
				$begin_time = strtotime(date('Y-m-d', strtotime('-1 year', $now)));
				break;
			default:
				$begin_time = 0;
		}

		return $begin_time;
	}


	/**
	 * 计算apm得分
	 * @param $avg_apm
	 * @param $game_type
	 * @return float
	 */
	private function _get_apm_score($avg_apm, $game_type){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$apm_conf1 = RTS_APM_CONF1;
				$apm_conf2 = RTS_APM_CONF2;
				$apm_conf3 = RTS_APM_CONF3;
				$apm_conf4 = RTS_APM_CONF4;
				$apm_conf5 = RTS_APM_CONF5;
				break;
			case 2:
				// MOBA
				$apm_conf1 = MOBA_APM_CONF1;
				$apm_conf2 = MOBA_APM_CONF2;
				$apm_conf3 = MOBA_APM_CONF3;
				$apm_conf4 = MOBA_APM_CONF4;
				$apm_conf5 = MOBA_APM_CONF5;
				break;
			case 3:
				// FPS
				$apm_conf1 = FPS_APM_CONF1;
				$apm_conf2 = FPS_APM_CONF2;
				$apm_conf3 = FPS_APM_CONF3;
				$apm_conf4 = FPS_APM_CONF4;
				$apm_conf5 = FPS_APM_CONF5;
				break;
			default:
				break;
		}

		// 公式: apmscore = apmcof4 + avgApm * apmcof5 * (1 + (avgApm - apmcof1 ) / apmcof2 ) ^ apmcof3, 四舍五入取整
		$apm_score = round( $apm_conf4 + $avg_apm * $apm_conf5 * pow( ( 1 + ($avg_apm - $apm_conf1) / $apm_conf2 ), $apm_conf3 ) );
		return $apm_score;
	}


	/**
	 * 计算心态得分
	 * @param $avg_heart
	 * @param $min_heart
	 * @param $max_heart
	 * @param $game_type
	 * @param $time_long
	 * @return array
	 */
	private function _get_mental_info($avg_heart, $min_heart, $max_heart, $game_type, $time_long){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$heart_conf1 = RTS_HEART_CONF1;
				$heart_conf2 = RTS_HEART_CONF2;
				$heart_conf3 = RTS_HEART_CONF3;
				$heart_conf4 = RTS_HEART_CONF4;
				$heart_tfilter = RTS_HEART_TFILTER;
				break;
			case 2:
				// MOBA
				$heart_conf1 = MOBA_HEART_CONF1;
				$heart_conf2 = MOBA_HEART_CONF2;
				$heart_conf3 = MOBA_HEART_CONF3;
				$heart_conf4 = MOBA_HEART_CONF4;
				$heart_tfilter = MOBA_HEART_TFILTER;
				break;
			case 3:
				// FPS
				$heart_conf1 = FPS_HEART_CONF1;
				$heart_conf2 = FPS_HEART_CONF2;
				$heart_conf3 = FPS_HEART_CONF3;
				$heart_conf4 = FPS_HEART_CONF4;
				$heart_tfilter = FPS_HEART_TFILTER;
				break;
			default:
				break;
		}

		// 如果平均心率,最高心率,最低心率其中一值为0,则不计算心态得分
		if(empty($avg_heart) || empty($min_heart) || empty($max_heart)){
			$mental_info = array(
				'heart_score' => 0,							// 稳定指数
				'heart_rank' => "E",						// 稳定评价
				'mental_score' => 0,						// 心态得分
				'excited_time' => 0,						// 激动时间
				'full_heart' => 0,							// 完整心率
				'min_heart' => $min_heart,					// 最低心率
				'max_heart' => $max_heart,					// 最高心率
			);
		}else{
			$min_heart = ($min_heart < $heart_tfilter) ? $heart_tfilter : $min_heart;			// 最低心率低于设定值按设定值计算
			$max_heart = ($max_heart < $min_heart) ? $min_heart : $max_heart;					// 最高心率不能低于最低心率

			// 稳定指数,四舍五入取整
			// 公式: heartscore = ((avgHeart - heartmin)^2+(heartmax - heartavg)^2) * (heartmax - heartmin) * heartcof1 / heartavg
			$heart_score = ($avg_heart != 0) ? round(( pow(($avg_heart - $min_heart), 2) + pow(($max_heart - $avg_heart), 2) ) * ($max_heart - $min_heart) * $heart_conf1 / $avg_heart) : 0 ;

			// 稳定指数评价转换表
			$heart_rank = get_heart_rank($heart_score);

			// 心态得分计算,四舍五入取整
			// 公式: mentalscore = heartcof2 + heartcof3 / (heartscore + heartcof4)
			$mental_score = round($heart_conf2 + $heart_conf3 / ($heart_score + $heart_conf4));

			// 激动时间百分比 2016/11/15 开会决定暂时不计算
			$excited_time = 0;

			// 完整心率计算
			// 公式: 完整心率 = 有效心率平均值 * 测量总时间
			$full_heart = $avg_heart * $time_long;

			$mental_info = array(
				'heart_score' => $heart_score,				// 稳定指数
				'heart_rank' => $heart_rank,				// 稳定评价
				'mental_score' => $mental_score,			// 心态得分
				'excited_time' => $excited_time,			// 激动时间
				'full_heart' => $full_heart,				// 完整心率
				'min_heart' => $min_heart,					// 最低心率
				'max_heart' => $max_heart,					// 最高心率
			);
		}
		return $mental_info;
	}


	/**
	 * 敏捷度得分计算
	 * @param $avg_agi
	 * @param $min_agi
	 * @param $max_agi
	 * @param $game_type
	 * @return array
	 */
	private function _get_agi_info($avg_agi, $min_agi, $max_agi, $game_type){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$agi_conf1 = RTS_AGI_CONF1;
				$agi_conf2 = RTS_AGI_CONF2;
				break;
			case 2:
				// MOBA
				$agi_conf1 = MOBA_AGI_CONF1;
				$agi_conf2 = MOBA_AGI_CONF2;
				break;
			case 3:
				// FPS
				$agi_conf1 = FPS_AGI_CONF1;
				$agi_conf2 = FPS_AGI_CONF2;
				break;
			default:
				break;
		}

		// agiavg、agimin、agimax的最小值以1处理，不可小于1，最大值以10000处理，不可大于10000
		$avg_agi = ($avg_agi < 1) ? 1 : $avg_agi;
		$avg_agi = ($avg_agi > 10000) ? 10000 : $avg_agi;
		$min_agi = ($min_agi < 1) ? 1 : $min_agi;
		$min_agi = ($min_agi > 10000) ? 10000 : $min_agi;
		$max_agi = ($max_agi < 1) ? 1 : $max_agi;
		$max_agi = ($max_agi > 10000) ? 10000 : $max_agi;

		// 敏捷得分,四舍五入取整
		// 公式: agiscore = ( 1 + agiavg/agimax) * agicof1 * (agimin + agimax)^agicof2
		$agi_score = round((1 + $avg_agi / $max_agi) * $agi_conf1 * pow(($min_agi + $max_agi), $agi_conf2));

		// 敏捷得分评价转换表
		$agi_rank = get_agi_rank($agi_score);

		$agi_info = array(
			'agi_score' => $agi_score,					// 敏捷得分
			'agi_rank' => $agi_rank,					// 敏捷评价
			'avg_agi' => $avg_agi,						// 平均加速度
			'min_agi' => $min_agi,						// 最低加速度
			'max_agi' => $max_agi,						// 最高加速度
		);
		return $agi_info;
	}


	/**
	 * 职业指数计算
	 * @param $apm_score
	 * @param $mental_score
	 * @param $agi_score
	 * @param $mc_score
	 * @param $md_score
	 * @param $game_type
	 * @param $time_long
	 * @return float
	 */
	private function _get_pro_info($apm_score, $mental_score, $agi_score, $mc_score, $md_score, $game_type, $time_long){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$pro_conf1 = RTS_PRO_CONF1;
				$pro_conf2 = RTS_PRO_CONF2;
				$pro_conf3 = RTS_PRO_CONF3;
				$pro_conf4 = RTS_PRO_CONF4;
				$pro_conf5 = RTS_PRO_CONF5;
				break;
			case 2:
				// MOBA
				$pro_conf1 = MOBA_PRO_CONF1;
				$pro_conf2 = MOBA_PRO_CONF2;
				$pro_conf3 = MOBA_PRO_CONF3;
				$pro_conf4 = MOBA_PRO_CONF4;
				$pro_conf5 = MOBA_PRO_CONF5;
				break;
			case 3:
				// FPS
				$pro_conf1 = FPS_PRO_CONF1;
				$pro_conf2 = FPS_PRO_CONF2;
				$pro_conf3 = FPS_PRO_CONF3;
				$pro_conf4 = FPS_PRO_CONF4;
				$pro_conf5 = FPS_PRO_CONF5;
				break;
			default:
				break;
		}

		// 获取时间系数
		$time_conf = get_time_config($time_long);

		// 职业指数,四舍五入取整
		// 公式: proscore = （ apmscore * procof1 + mentalscore * procof2 + agiscore * procof3 + mcscore * procof4 + mdscore * procof5） * timecof
		$pro_score = round(($apm_score * $pro_conf1 + $mental_score * $pro_conf2 + $agi_score * $pro_conf3 + $mc_score * $pro_conf4 + $md_score * $pro_conf5 ) * $time_conf);

		// 获取职业综合评价
		$pro_rank = get_pro_rank($pro_score);

		$pro_info = array(
			'pro_score' => $pro_score,					// 职业指数
			'pro_rank' => $pro_rank,					// 职业综合评价

		);
		return $pro_info;
	}


	/**
	 * 移动次数得分计算
	 * @param $move_count
	 * @param $max_mc
	 * @param $game_type
	 * @param $time_long
	 * @return float
	 */
	private function _get_move_count_score($move_count, $max_mc, $game_type, $time_long){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$mc_conf1 = RTS_MC_CONF1;
				$mc_conf2 = RTS_MC_CONF2;
				break;
			case 2:
				// MOBA
				$mc_conf1 = MOBA_MC_CONF1;
				$mc_conf2 = MOBA_MC_CONF2;
				break;
			case 3:
				// FPS
				$mc_conf1 = FPS_MC_CONF1;
				$mc_conf2 = FPS_MC_CONF2;
				break;
			default:
				break;
		}

		// 移动次数平均值 取值范围[0,5]
		// 公式: 移动次数平均值 = 移动次数总和 / 总秒数
		$avg_mc = $move_count / $time_long;
		$avg_mc = ($avg_mc < 0) ? 0 : $avg_mc;
		$avg_mc = ($avg_mc > 5) ? 5 : $avg_mc;

		// 移动次数最大值 取值范围[0,14]
		$max_mc = ($max_mc < 0) ? 0 : $max_mc;
		$max_mc = ($max_mc > 14) ? 14 : $max_mc;

		// 移动次数得分,四舍五入取整
		// 公式: mcscore = mcavg * mcmax * mccof1 + mccof2
		$mc_score = round($avg_mc * $max_mc * $mc_conf1 + $mc_conf2);

		return $mc_score;
	}


	/**
	 * 移动距离得分计算
	 * @param $move_distance
	 * @param $max_md
	 * @param $game_type
	 * @param $time_long
	 * @return int
	 */
	private function _get_move_distance_score($move_distance, $max_md, $game_type, $time_long){
		// 根据游戏类型变更设定值
		switch($game_type){
			case 1:
				// RTS
				$md_conf1 = RTS_MD_CONF1;
				$md_conf2 = RTS_MD_CONF2;
				$md_conf3 = RTS_MD_CONF3;
				break;
			case 2:
				// MOBA
				$md_conf1 = MOBA_MD_CONF1;
				$md_conf2 = MOBA_MD_CONF2;
				$md_conf3 = MOBA_MD_CONF3;
				break;
			case 3:
				// FPS
				$md_conf1 = FPS_MD_CONF1;
				$md_conf2 = FPS_MD_CONF2;
				$md_conf3 = FPS_MD_CONF3;
				break;
			default:
				break;
		}

		// 接口传过来的移动距离单位是0.01毫米
		$move_distance = $move_distance / 100;
		$max_md = $max_md / 100;

		// 移动次数平均值 取值范围[0,50]
		// 公式: 移动次数平均值 = 移动次数总和 / 总秒数
		$avg_md = ($time_long != 0) ? $move_distance / $time_long : 0;
		$avg_md = ($avg_md < 0) ? 0 : $avg_md;
		$avg_md = ($avg_md > 50) ? 50 : $avg_md;

		// 移动次数最大值 取值范围[0,300]
		$max_md = ($max_md < 0) ? 0 : $max_md;
		$max_md = ($max_md > 300) ? 300 : $max_md;

		// 移动距离得分,四舍五入取整
		// 公式: mdscore = mdavg * (mdmax^mdcof1) * mdcof2 + mdcof3
		$md_score = round($avg_md * pow($max_md, $md_conf1) * $md_conf2 + $md_conf3);

		return $md_score;
	}


}