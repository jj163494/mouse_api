<?php

class MacroController extends PublicController {


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/macro/uploadMacro",
	 *     summary="34.上传宏",
	 *     tags={"Macro"},
	 *     description="上传用户配置的宏",
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
	 *         name="macroName",
	 *         in="query",
	 *         description="宏名称",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="keyMacro",
	 *         in="query",
	 *         description="宏内容",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
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
	 *         response="222",
	 *         description="未输入用户标识",
	 *     ),
	 *     @SWG\Response(
	 *         response="304",
	 *         description="登录信息过期",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="用户不存在",
	 *     ),
	 *     @SWG\Response(
	 *         response="905",
	 *         description="保存信息时发生错误",
	 *     ),
	 *     @SWG\Response(
	 *         response="906",
	 *         description="更新信息时发生错误",
	 *     ),
	 * )
	 */
	public function actionUploadMacro() {
		$macro_name = Frame::getStringFromRequest('macroName');
		$macro_content = Frame::getStringFromRequest('keyMacro');
		if(empty($macro_name) || empty($macro_content)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '参数输入不完整';
			echo json_encode($result);
			die();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取用户的鼠标改键宏
		$macro_model = new TdMacros();
		$where = "member_id = {$member_id} AND macro_name = '{$macro_name}'";
		$macro_info = $macro_model->getMacroInfo($where);
		if (!empty($macro_info)){
			// 有数据的情况进行更新
			$macro_info->macro_content = $macro_content;
			$macro_info->update_time = time();
			if($macro_info->save()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = 'ok';
			}else{
				$result['ret_num'] = 906;
				$result['ret_msg'] = '更新信息时发生错误';
			}
		}else{
			// 没数据的情况进行新增
			$macro_model->member_id = $member_id;
			$macro_model->macro_name = $macro_name;
			$macro_model->macro_content = $macro_content;
			$macro_model->created_time = time();
			if($macro_model->save()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = 'ok';
			}else{
				$result['ret_num'] = 905;
				$result['ret_msg'] = '保存信息时发生错误';
			}
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/macro/getMacroInfo",
	 *     summary="35.获取宏信息",
	 *     tags={"Macro"},
	 *     description="根据用户标识和宏名称或者该宏的信息",
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
	 *         name="macroName",
	 *         in="query",
	 *         description="宏名称",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="操作成功",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="macro_id",
	 *                 description="宏id",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="cmacro_id",
	 *                 description="角色宏id",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="macro_name",
	 *                 description="宏名称",
	 *                 type="string",
	 *             ),
	 *             @SWG\Property(
	 *                 property="macro_content",
	 *                 description="宏内容",
	 *                 type="string",
	 *             ),
	 *             @SWG\Property(
	 *                 property="chara_image",
	 *                 description="角色图像",
	 *                 type="string",
	 *             ),
	 * 		   ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="222",
	 *         description="未输入用户标识",
	 *     ),
	 *     @SWG\Response(
	 *         response="304",
	 *         description="登录信息过期",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetMacroInfo() {
		$macro_name = Frame::getStringFromRequest('macroName');
		if(empty($macro_name)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '参数输入不完整';
			echo json_encode($result);
			die();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取用户的鼠标改键宏
		$macro_model = new TdMacros();
		$where = "member_id = {$member_id} AND macro_name = '{$macro_name}'";
		$macro_info = $macro_model->getMacroInfo($where);
		if(!empty($macro_info)){
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'macro_id' => $macro_info['macro_id'],									// 宏id
				'cmacro_id' => $macro_info['cmacro_id'],								// 角色宏id
				'macro_name' => $macro_info['macro_name'],								// 宏名称
				'macro_content' => $macro_info['macro_content'],						// 宏内容
				'chara_image' => $macro_info['chara_image'],							// 角色图像
			);
		}else {
			$result = array(
				'ret_num' => 309,
				'ret_msg' => '没有该记录',
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/macro/getMacroList",
	 *     summary="46.获取用户宏列表",
	 *     tags={"Macro"},
	 *     description="根据用户标识获取该用户的所有宏列表",
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
	 *     @SWG\Response(
	 *         response="200",
	 *         description="操作成功",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="macro_list",
	 *                 description="宏列表",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="macro_id",
	 *                             description="宏id",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="cmacro_id",
	 *                             description="角色宏id",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="macro_name",
	 *                             description="宏名称",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="macro_content",
	 *                             description="宏内容",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="chara_image",
	 *                             description="角色图像",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="created_time",
	 *                             description="创建时间",
	 *                             type="integer",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 * 		   ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="222",
	 *         description="未输入用户标识",
	 *     ),
	 *     @SWG\Response(
	 *         response="304",
	 *         description="登录信息过期",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetMacroList() {
		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 获取用户的鼠标改键宏
		$macro_model = new TdMacros();
		$where = "member_id = {$member_id}";
		$data_list = $macro_model->getMacroList($where);
		if(!empty($data_list)){
			$macro_list = array();
			foreach($data_list as $k => $macro_info){
				$macro_list[$k]['macro_id'] = $macro_info['macro_id'];					// 宏id
				$macro_list[$k]['cmacro_id'] = $macro_info['cmacro_id'];				// 角色宏id
				$macro_list[$k]['macro_name'] = $macro_info['macro_name'];				// 宏名称
				$macro_list[$k]['macro_content'] = $macro_info['macro_content'];		// 宏内容
				$macro_list[$k]['chara_image'] = $macro_info['chara_image'];			// 角色图像
				$macro_list[$k]['created_time'] = $macro_info['created_time'];			// 创建时间
			}

			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'macro_list' => $macro_list												// 宏列表
			);
		}else{
			$result = array(
				'ret_num' => 309,
				'ret_msg' => '没有该记录',
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Delete(
	 *     path="/app_api/website/api.php/v2_2/macro/deleteMacro",
	 *     summary="45.删除宏",
	 *     tags={"Macro"},
	 *     description="上传用户配置的宏",
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
	 *         name="macroNames",
	 *         in="query",
	 *         description="宏名称",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
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
	 *         response="222",
	 *         description="未输入用户标识",
	 *     ),
	 *     @SWG\Response(
	 *         response="304",
	 *         description="登录信息过期",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="用户不存在",
	 *     ),
	 * )
	 */
	public function actionDeleteMacro() {
		$macro_name_list = Frame::getStringFromRequest('macroNames');
		if(empty($macro_name_list)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '参数输入不完整';
			echo json_encode($result);
			die();
		}

		// 获取用户信息
		$member_info = $this->check_user();
		$member_id = $member_info['member_id'];

		// 删除宏
		$macro_model = new TdMacros();
		// 将宏名称分割用于删除
		$macro_name_arr = explode('$', $macro_name_list);
		foreach($macro_name_arr as $macro_name) {
			$where = "member_id = {$member_id} AND macro_name = '{$macro_name}'";
			$macro_model->deleteAll($where);
		}

		$result = array(
			'ret_num' => 0,
			'ret_msg' => 'ok'
		);

		echo json_encode($result);
	}
}

?>