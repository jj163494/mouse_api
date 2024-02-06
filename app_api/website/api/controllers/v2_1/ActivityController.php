<?php
class ActivityController extends PublicController {
	/**
	 * 取得活动列表
	 */
	public function actionGetActivityList() {
		$type = Frame::getIntFromRequest ( 'type' );
		if (empty ( $type )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		if ($type == 1){
			$activitys = TdActivity::model()->findAll("start_time>unix_timestamp(now())");
		} else if ($type == 2) {
			$activitys = TdActivity::model()->findAll("start_time<=unix_timestamp(now()) and end_time >= unix_timestamp(now())");
		} else {
			$activitys = TdActivity::model()->findAll("end_time<unix_timestamp(now())");
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		if ($activitys) {
			foreach ($activitys as $key=>$value){
				$arr [] = array (
						"activity_id"=>$value->id,
						"activity_image"=>$value->activity_image,
						"title" => $value->title,
						"message"=>$value->message,
						"start_time" => $value->start_time,
						"end_time"=>$value->end_time
				);
			}
			$result ['activitys'] = $arr;
		} else {
			$result ["activitys"] = null;
		}
	
		echo json_encode ( $result );

	}
	
	/**
	 * 取得活动详情
	 */
	public function actionGetActivityDetail() {
		$activityId = Frame::getIntFromRequest ( 'activityId' );
		if (empty ( $activityId )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$isAlarm = 0;

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];
		
		$activity = TdActivity::model()->find("id={$activityId}");
		if ($activity) {
			// 判断是否设置过预约
			if ($activity->start_time > time()) {
				$alarm = TdActivityAlarm::model()->find("activity_id={$activityId} and userid={$member_id}");
				if ($alarm){
					$isAlarm = 1;
				}
			}
			
			$result ['activity'] = array (
				"activity_image"=>$activity->activity_image,
				"title" => $activity->title,
				"start_time" => $activity->start_time,
				"end_time"=>$activity->end_time,
				"message"=>$activity->message,
				"activity_url"=>$activity->activity_url,
				"is_alarm"=>$isAlarm
			);
		} else {
			$result ["activity"] = null;
		}

		echo json_encode ( $result );
	}
	
	
	/**
	 * 设置活动预约提醒
	 */
	public function actionSetActivityAlarm() {
		$activityId = Frame::getIntFromRequest ( 'activityId' );
		if (empty ( $activityId )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();

		$alarm = new TdActivityAlarm();
		$alarm->activity_id = $activityId;
		$alarm->userid = $member_info['member_id'];
		$alarm->created_time= time();
		if ($alarm->save()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else {
			$result ['ret_num'] = 901;
			$result ['ret_msg'] = '信息添加失败	';
		}

		echo json_encode ( $result );
	}
}
