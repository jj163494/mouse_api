<?php

class MouseController extends PublicController {

	/**
	 * 上传鼠标改键内容
	 */
	public function actionUploadMouseKey() {
		$keyName = Frame::getStringFromRequest ( 'keyName' );
		$keyContent = Frame::getStringFromRequest ( 'keyContent' );
		if (empty($keyName) || empty($keyContent)) {
			$result ['ret_num'] =201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$mousemacro= new TdMouseMacro();
		$name=TdMouseMacro::model()->find("key_name='{$keyName}'");
		if($name){
			$name->userid = $member_id;
			$name->key_name = $keyName;
			$name->key_content = $keyContent;
			$name->created_time = time();
			if ($name->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '更新成功';
			} else {
				$result ['ret_num'] = 906;
				$result ['ret_msg'] = '替换时发生系统错误，请重新操作';
			}
		} else {
			$mousemacro->userid = $member_id;
			$mousemacro->key_name = $keyName;
			$mousemacro->key_content = $keyContent;
			$mousemacro->created_time = time();
			if($mousemacro->save()){
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '操作成功';
				//返回用户信息
				$result['id'] = $mousemacro->id;
			} else {
				$result ['ret_num'] = 901;
				$result ['ret_msg'] = '信息添加失败	';
			}
		}

		echo json_encode ( $result );	
	}


	/**
	 * 查询鼠标改键内容
	 */
	public function actionFindMouseKey(){
		$Id = Frame::getStringFromRequest('Id');
		$keyName = Frame::getStringFromRequest('keyName');

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$project=TdMouseMacro::model()->findAll("userid='{$member_id}' && id='{$Id}' || userid='{$member_id}'&& key_name='{$keyName}'");
		if($project){
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result['id'] = $project->id;
			$result['userid'] = $member_id;
			$result['key_name'] = $project->key_name;
			$result['key_content'] = $project->key_content;
			$result['created_time'] = $project->created_time;
		}else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 删除鼠标改键内容
	 */
     public function actionDeleteMouseKey(){
		 $Id = Frame::getStringFromRequest('Id');
		 $keyName = Frame::getStringFromRequest('keyName');

		 // 获取用户信息
		 $member_info = $this->check_user();

		 $deleteproject=TdMouseMacro::model()->deleteAll("id='{$Id}' || key_name='{$keyName}'");
		 if($deleteproject){
			 $result ['ret_num'] = 0;
			 $result ['ret_msg'] = '操作成功';
		 }else {
			 $result ['ret_num'] = 309;
			 $result ['ret_msg'] = '没有该记录';
		 }

		 echo json_encode ( $result );
     }
	
     /**
      * 查询鼠标全部改键方案
      */
     public function actionFindAllMouseKey(){
		 // 获取用户信息
		 $member_info = $this->check_user();
		 $member_id = $member_info['member_id'];

		 $project = TdMouseMacro::model()->findAll(
			 array(
				 'select' =>array('key_name','key_content','created_time'),
				 'condition' => "userid='{$member_id}' and key_name IS NOT NULL and key_content IS NOT NULL"
			 )
		 );
		 if(!empty($project)){
			 foreach ($project as $key => $value) {
				 $arr [] = array (
					 "key_name" => $value->key_name,
					 "key_content" => $value->key_content,
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
	 * 上传鼠标改键宏
	 */
	public function actionUploadMouseKeyMacro() {
		$macroName = Frame::getStringFromRequest ( 'macroName' );
		$keyMacro = Frame::getStringFromRequest ( 'keyMacro' );
		if ( empty($keyMacro) || empty($macroName)) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取用户的鼠标改键宏
		$mouseMacro = TdMouseMacro::model ()->find ("userId={$member_id} and macro_name='{$macroName}'");
		if ($mouseMacro) {
			$mouseMacro->macro_content = $keyMacro;
			if ($mouseMacro->save()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 909;
				$result ['ret_msg'] = '数据更新失败';
			}

		} else {
			$model = new TdMouseMacro();
			$model->userid = $member_id;
			$model->macro_name = $macroName;
			$model->macro_content = $keyMacro;
			$model->created_time = time();
			if ($model->save()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 911;
				$result ['ret_msg'] = '添加失败';
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 获取单个鼠标改键宏
	 */
	public function actionGetMouseKeyMacro() {	
		$macroName = Frame::getStringFromRequest('macroName');
		if(empty($macroName)){
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入信息不完整';
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user();

		$macro=TdMouseMacro::model()->find("macro_name='{$macroName}'");
		if($macro){
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result['macro_name'] = $macro->macro_name;
			$result['macro_content'] = $macro->macro_content;
		}else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = '没有该记录';
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 获取全部鼠标改键宏
	 */
	public function actionGetMouseKeyMacros() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		$macros=TdMouseMacro::model()->findAll(
			array(
				'select' =>array('macro_name','macro_content','created_time'),
				'condition' => "userid='{$member_id}' and macro_name IS NOT NULL and macro_content IS NOT NULL"
			)
		);

		if (!empty($macros)) {
			foreach ($macros as $key => $value) {
				$arr[]=array(
					"macro_name" => $value->macro_name,
					"macro_content" => $value->macro_content,
					"created_time" => $value->created_time
				);
			}
		}

		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['macros'] = $arr;

		echo json_encode ( $result );
	}


	/**
	 * 删除按键宏
	 */
	public function actionDeleteMacros() {
		$macroName = Frame::getStringFromRequest ( 'macroNames' );
		if (empty( $macroName )) {
			$result ['ret_num'] = 201;
			$result ['ret_msg'] = '输入不完整';
			echo json_encode ( $result );
			die ();
		}
		$macroNames = explode('$', $macroName);

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		foreach($macroNames as $macro) {
			TdMouseMacro::model()->deleteAll("userId={$member_id} and macro_name='{$macro}'");
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';

		echo json_encode ( $result );
	}
}

?>