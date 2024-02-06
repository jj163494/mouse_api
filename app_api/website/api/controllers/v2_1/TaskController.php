<?php
class TaskController extends PublicController {
	/*
	 * 分配任务
	 */
	public function actionAllocateTask() {
		// 获取所有新用户或者已完成任务的用户
		$sql = "select ";
		$users = TaidushopMember::model ()->findAll ( "usertype='0'" );
		
		// if(empty($users)){
		// $result ['ret_num'] =901;
		// $result ['ret_msg'] = '没有需要分配任务的用户';
		// echo json_encode ( $result );
		// die ();
		// }
		
		// 获取任务列表
		$tasks = TdTask::model ()->findAll ();
		
		// 获取所有明星DPI设置
		// $dpis = TdDpi::model()->findAll();
		
		// 循环用户
		foreach ( $users as $user ) {
			$userId = $user->member_id;
			$gameType = Frame::getIntFromRequest ( 'taskid' );
			
			// 取得该用户所有未完成的任务
			$model = new TdUserTask ();
			$noneDoingTasks = $model->findAll ( "userid={$userId} &&task_status=0" );
			if (count($noneDoingTasks) >= 3){
				continue;
			}
			
			do{
				// 随机分配任务
				$taskIndex = rand ( 0, count ( $tasks ) - 1 );
				$task = $tasks [$taskIndex];
				if (count($noneDoingTasks) == 0){
					break;
				} else if (count($noneDoingTasks) == 1){
					if ($noneDoingTasks[0]->id != $task->id)
					{
						break;
					}
				} else if (count($noneDoingTasks) == 2){
					if ($noneDoingTasks[0]->id != $task->id && $noneDoingTasks[1]->id != $task->id)
					{
						break;
					}
				}
			}while(1);
			
			
			// $taskType=6;
			$taskGoal = $task->task_goal;
			// // 明星dpi设置
			// if ($taskType == 6){
			// // 随机分配明星
			// $dpiIndex = rand(0, count($dpis) - 1);
			// $dpi = $dpis[$dpiIndex];
			// $taskGoal = $dpi->dpi;
			// }
			
			$model = new TdUserTask ();
			$model->userid = $userId;
			$model->task_id = $task->id;
			$model->task_name = $task->task_name;
			$model->task_type = $task->task_type;
			$model->task_goal = $taskGoal;
			$model->task_reward = $task->task_reward;
			$model->task_status = 0;
			$model->created_time = time ();
			$model->save ();
		}
	}
	
	/**
	 * 获取未完成的任务
	 */
	public function actionGetTasks() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$tasks = TdUserTask::model ()->findAll ( "userid={$member_id} && task_status=0" );
		if ($tasks){
			foreach($tasks as $key=>$value ) {
				$arr[] = array (
						"taskId" => $value->task_id,
						"taskName" => $value->task_name, // 要查询的字段
						"taskGoal" => $value->task_goal
				);
			}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['history'] = $arr;
		} else {
			$result ['ret_num'] = 310;
			$result ['ret_msg'] = '没有需要完成的任务';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 切换任务
	 */
	public function actionChangeTask() {
		$taskId = Frame::getIntFromRequest ( 'taskId' );

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$model = new TdUserTask ();
		$task = $model->find ( "userid={$member_id} && task_id={$taskId}" );
		if ($task){
			// 获取任务列表
			$tasks = TdTask::model ()->findAll ();

			do{
				// 随机分配任务
				$taskIndex = rand ( 0, count ( $tasks ) - 1 );

				if ($task->task_id != $tasks [$taskIndex]->task_id) {
					break;
				}
			}while(1);
			$task->task_id = $tasks [$taskIndex]->task_id;
			$task->task_name = $tasks [$taskIndex]->task_name;
			$task->task_target = $tasks [$taskIndex]->task_target;
			$task->task_goal = $tasks [$taskIndex]->task_goal;
			$task->task_type = $tasks [$taskIndex]->task_type;
			if ($task->update ())
			{
				$arr = array (
						"taskId" => $task->task_id,
						"taskName" => $task->task_name, // 要查询的字段
						"taskGoal" => $task->task_goal
				);
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
				$result ['history'] = $arr;
			} else {
				$result ['ret_num'] = 910;
				$result ['ret_msg'] = '用户任务保存失败';
			}
		} else {
			$result ['ret_num'] = 311;
			$result ['ret_msg'] = '没有该需要替换的任务';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 获取用户的成就
	 */
	function actionGetUserAchievements() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$achievements = TdAchievement::model()->findAll ("achieve_level=1");
		if ($achievements){
			$userAchievements = TdUserAchievement::model()->findall("userid={$member_id}");
			foreach($achievements as $key1=>$value1){
				$achieveLevel = 0;
				$userValue = 0;
				foreach ($userAchievements as $key2 => $value2) {
					if ($value1->achieve_type == $value2->achieve_type){
						$achieveLevel = $value2->achieve_level;
						$userValue = $value2->user_value;
						break;
					}
				}
				$arr[] = array(
					"achieveType" => $value1->achieve_type,
					"achieveName" => $value1->achieve_name,
					"achieveLevel" => $achieveLevel,
					"userValue" => $userValue
				);
			}

			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['history'] = $arr;
		} else {
			$result ['ret_num'] = 311;
			$result ['ret_msg'] = '没有该需要替换的任务';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 获取用户累计数据
	 */
	function actionGetUserGrandData() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$achievements = TdUserAchievement::model()->findAll ("userid={$member_id} and achieve_type in (1,2,3,4)");
		if ($achievements){
			foreach($achievements as $key=>$value){
				if ($value->achieve_type == 1){
					$result ['total_trans_count'] = $value->user_value;
				} else if ($value->achieve_type == 2){
					$result ['total_trans_timer'] = $value->user_value;
				} else if ($value->achieve_type == 3){
					$result ['total_s_level'] = $value->user_value;
				} else if ($value->achieve_type == 4){
					$result ['total_a_level'] = $value->user_value;
				}
			}
		} else {
			$result ['total_trans_count'] = 0;
			$result ['total_trans_timer'] = 0;
			$result ['total_s_level'] = 0;
			$result ['total_a_level'] = 0;
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';

		echo json_encode ( $result );
	}
}
