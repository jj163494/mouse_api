<?php
class RecordController extends PublicController {
	public function actionUploadRank() {
		$connection = Yii::app ()->db;
		$command = $connection->createCommand ( "TRUNCATE TABLE td_rank" );
		
		//ignore_user_abort (); // 关掉浏览器，PHP脚本也可以继续执行.
		//set_time_limit ( 0 ); // 通过set_time_limit(0)可以让程序无限制的执行下去
		//$interval = 60 * 60 * 24; // 每天运行
		//while ( 1 ) {
			// TdRank::model()->deleteAll();
			$resultc = $command->execute ();

			// 取得当前月份
			$currentMonth = date("Ym");
			// 取得上个月份
			$forwardMonth = $this::GetForwardMonth();
			// 取得这个月初时间戳
			$firstDayTime = strtotime(date("Y-m")."-01");
			// 判断当前日期是否是这个月最后一天
			$BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
			$endDate = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
			$isEndDate = false;
			if (date('Y-m-d') == $endDate){
				$isEndDate = true;
			}
			// 用户等级
			$userLevel = 1;
			
			// 取得配置信息
			$setting = TdSetting::model()->findAll("main_key=1001");
			if ($setting){
				foreach ($setting as $key =>$value ) {
					if ($value->sub_key == "scof1"){
						$scof1 = $value->value;
					} else if ($value->sub_key == "scof2") {
						$scof2 = $value->value;
					} else if ($value->sub_key == "scof3") {
						$scof3 = $value->value;
					} else if ($value->sub_key == "scof4") {
						$scof4 = $value->value;
					} else if ($value->sub_key == "scof5") {
						$scof5 = $value->value;
					} else if ($value->sub_key == "scof6") {
						$scof6 = $value->value;
					} else if ($value->sub_key == "scof7") {
						$scof7 = $value->value;
					} else if ($value->sub_key == "scof8") {
						$scof8 = $value->value;
					} else if ($value->sub_key == "scof9") {
						$scof9 = $value->value; //用户最少测试次数
					}
				}
			}

			for($gameType = 1; $gameType <= 3; $gameType ++) {
				// 取得当前月份所有测试记录
				$db = Yii::app()->db;
				$sql = "select userid, count(*) as gameCount, avg(time_long) avgTimeLong
						from td_upload_test_data 
						where game_type={$gameType} and created_time>= {$firstDayTime}
						group by userid
						having count(*)>={$scof9}";
				$command = $db->createCommand($sql);
				$result = $command->queryAll();

				foreach ( $result as $key => $value ) {
					$userId = $value["userid"];
					
					// 判断用户比赛的场数是否达到最低要求
					if( $value["gameCount"] < $scof9){
						continue;
					}
					// 取得该用户排行榜上月得分
					$rankHistory = TdRankHistory::model()->find("userid={$userId} and game_type={$gameType} and month='{$forwardMonth}'");
					if ($rankHistory){
						$forwardScore = $rankHistory->target_num;
					} else {
						$forwardScore = 0;
					}
					// 计算基础得分
					// 基础分得分 = (scof1 * 排行榜上月得分^scof2) + (用户等级 * scof8)
					// 基础分得分有上限，上限为附表scoflist的scof7
					$baseScore = $scof1 * pow($forwardScore, $scof2) + $userLevel * $scof8;
					if ($baseScore > $scof7){
						$baseScore = $scof7;
					}
					
					// 计算场次得分
					// 场次得分 = (本月测试次数 * scof3) * 测试平均时长/scof4
					// 场次得分设置上限，上限值见附表scoflist的scof5
					$gameScore = $value["gameCount"] * $scof3 * $value["avgTimeLong"] / 60 / $scof4;
					if ($gameScore > $scof5){
						$gameScore = $scof5;
					}

					// 取得用户本月单场职业最高指数之和
					$sql = "select sum(a.final_score) final_score 
							from (select final_score from td_upload_test_data
					              where userId = {$userId} and game_type={$gameType} and created_time>= {$firstDayTime}
					              order by final_score desc
					              limit {$scof9}) a";
					$command = $db->createCommand($sql);
					$rows = $command->query();
					foreach ($rows as $k => $v ){
						$finalScore = $v['final_score'];
						break;
					}
					
					// 计算职业评分
					// 职业评分 = 本月10场单场职业指数最高得分之和 * scof6
					$professionScore = $finalScore * $scof6;
					
					// 排行榜得分 = 基础分 + 场次得分 + 职业评分
					$rankScore = round($baseScore + $gameScore + $professionScore);

					if ($isEndDate){
						// 如果是最后一天 插入月度排行榜历史记录
						$modelHistory = new TdRankHistory();
						$modelHistory->userId = $userId;
						$modelHistory->month = $currentMonth;
						$modelHistory->game_type = $gameType;
						$modelHistory->target_num = $rankScore;
						$modelHistory->save();
					} else {
						// 插入排行榜
						$model = new TdRank();
						$model->userId = $userId;
						$model->order_type = 2;
						$model->game_type = $gameType;
						$model->target_num = $rankScore;
						$model->created_time = time();
						$model->save();
					}
				}
			}
			//sleep ( $interval ); // 等待一天
		//}
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v3_1/Record/UserAction",
	 *     summary="28.记录用户动作",
	 *     tags={"Record"},
	 *     description="储存用户的操作日志",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="open_id",
	 *         in="query",
	 *         description="用户标识",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="softType",
	 *         in="query",
	 *         description="软件类型",
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi",
	 *         default="1",
	 *         enum={"1", "2", "3"}
	 *     ),
	 *     @SWG\Parameter(
	 *         name="deviceType",
	 *         in="query",
	 *         description="设备类型",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="3",
	 *         enum={"1", "2", "3"}
	 *     ),
	 *     @SWG\Parameter(
	 *         name="networkType",
	 *         in="query",
	 *         description="网络类型",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="content",
	 *         in="query",
	 *         description="操作日志",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Parameter(
	 *         name="mac_address",
	 *         in="query",
	 *         description="MAC地址",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="操作成功",
	 *         @SWG\Schema(ref="#/definitions/ApiResponse")
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="905",
	 *         description="保存信息时发生错误",
	 *     ),
	 * )
	 */
	public function actionUserAction() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$open_id = Frame::getStringFromRequest ( 'open_id' );
		$soft_type = Frame::getIntFromRequest ( 'softType' );
		$device_type = Frame::getIntFromRequest ( 'deviceType' );
		$network_type = Frame::getStringFromRequest ( 'networkType' );
		$content = Frame::getStringFromRequest ( 'content' );
		$mac_address = Frame::getStringFromRequest ( 'mac_address' );
		if(empty($content)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode($result);
			die();
		}

		// 根据open_id获取用户id 2017/05/05
		$member_id = 0;
		if(!empty($open_id)){
			$app_user_model = new TdAppUsers();
			$app_user_where = "open_id = '{$open_id}'";
			$app_user_info = $app_user_model->getAppUserInfo($app_user_where);
			if(!empty($app_user_info)){
				$member_id = $app_user_info['member_id'];
			}
		}

		$transaction = Yii::app()->db->beginTransaction();
		$flag = true;

		// 解析操作内容
		$content_arr = explode("\r\n", $content);
		foreach($content_arr as $k => $v){
			if(!empty($v)){
				$action_info = explode('|', $v);

				$model = new TdUserAction();
				$model->soft_type = $soft_type;
				$model->device_type = $device_type;
				if (!empty($network_type)) {
					$model->networkType = $network_type;
				}
				if(!empty($action_info[0])){
					$model->action_time = $action_info[0];
				}
				if(!empty($action_info[1])){
					$model->userid = $action_info[1];
				}else if(!empty($member_id)){
					// 操作内容中不存在用户id时保存open_id对应的用户id
					$model->userid = $member_id;
				}
				if(!empty($action_info[2])){
					$model->action_type = $action_info[2];
				}
				if(!empty($action_info[3])){
					$model->action_detail = $action_info[3];
				}
				$model->mac_address = $mac_address;
				$model->created_time = time ();
				if(!$model->save ()){
					$flag = false;
					break;
				}
			}
		}
		
		if($flag){
			$transaction->commit();

			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
			);
		}else{
			$transaction->rollback();

			$result = array(
				'ret_num' => 905,
				'ret_msg' => $this->language->get('save_fail'),
			);
		}

		echo json_encode($result);
	}
	
	function GetForwardMonth()
	{
		//得到系统的年月
		$tmp_date=date("Ym");
		//切割出年份
		$tmp_year=substr($tmp_date,0,4);
		//切割出月份
		$tmp_mon =substr($tmp_date,4,2);
		$tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);

		//得到当前月的上一个月
		return $fm_forward_month=date("Ym",$tmp_forwardmonth);
	}
	
	/**
	 * 记录日志
	 */
	public function actionLog() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$userId = Frame::getIntFromRequest ( 'userId' );
		$softVersion = Frame::getStringFromRequest ( 'softVersion' );
		$softType = Frame::getIntFromRequest ( 'softType' );
		$deviceType = Frame::getIntFromRequest ( 'deviceType' );
		$mobile_model = Frame::getStringFromRequest ( 'model' );
		$os = Frame::getStringFromRequest ( 'os' );
		$logType = Frame::getStringFromRequest ( 'logType' );
		$section = Frame::getStringFromRequest ( 'section' );
		$message = Frame::getStringFromRequest ( 'message' );

		// 判断是否要进行解码
		if(strpos($message, '\\u') === 1){
			// 解码日志内容
			$message = $this->unicode_decode($message);
		}

		if(empty($section) && empty($message)){
			// 类和方法以及消息都为空时不保存日志
			exit;
		}

		$model = new TdLog();
		$model->userid = $userId;
 		$model->soft_version = $softVersion;
 		$model->soft_type = $softType;
 		if (!empty($deviceType)) {
 			$model->device_type = $deviceType;
 		}
 		if (!empty($mobile_model)) {
 			$model->mobile_model = $mobile_model;
 		}
 		if (!empty($os)) {
 			$model->os_version = $os;
 		}
 		$model->log_type = $logType;
 		if (!empty($section)) {
 			$model->section = $section;
 		}
 		
		$model->message = $message;
		$model->created_time = time ();
		if ($model->save ()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'OK';
		} else {
			$result = array(
				'ret_num' => 905,
				'ret_msg' => $this->language->get('save_fail'),
			);
		}

	}

	function unicode_decode($name){
		// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
		$pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
		preg_match_all($pattern, $name, $matches);
		if (!empty($matches)) {
			$name = '';
			for ($j = 0; $j < count($matches[0]); $j++) {
				$str = $matches[0][$j];
				if (strpos($str, '\\u') === 0) {
					$code = base_convert(substr($str, 2, 2), 16, 10);
					$code2 = base_convert(substr($str, 4), 16, 10);
					$c = chr($code).chr($code2);
					$c = iconv('UCS-2', 'UTF-8', $c);
					$name .= $c;
				} else {
					$name .= $str;
				}
			}
		}

		return $name;
	}

}