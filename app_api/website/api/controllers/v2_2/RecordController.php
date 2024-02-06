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
	 * 记录用户操作
	 */
	public function actionUserAction() {
		$userId = Frame::getStringFromRequest ( 'userId' );
		$actionType = Frame::getIntFromRequest ( 'actionType' );
		$softType = Frame::getIntFromRequest ( 'softType' );
		$deviceType = Frame::getIntFromRequest ( 'deviceType' );
		$networkType = Frame::getStringFromRequest ( 'networkType' );

		$model = new TdUserAction();
		$model->userid = $userId;
		$model->action_type = $actionType;
		$model->soft_type = $softType;
		$model->device_type = $deviceType;
		if (!empty($networkType)) {
			$model->networkType = $networkType;
		}
		$model->created_time = time ();
		$model->save ();
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
			$result ['ret_num'] = 310;
			$result ['ret_msg'] = '没有需要完成的任务';
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