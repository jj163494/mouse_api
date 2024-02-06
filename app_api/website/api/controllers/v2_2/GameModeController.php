<?php

class GameModeController extends PublicController{

	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/gameMode/addGameMode",
	 *     summary="97.增加游戏模式",
	 *     tags={"GameMode"},
	 *     description="对指定主机进行增加游戏模式的操作",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gm_name",
	 *         in="query",
	 *         description="游戏模式名称",
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
	 *                 property="gm_id",
	 *                 description="游戏模式id",
	 *                 type="integer",
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="901",
	 *         description="添加失败",
	 *     ),
	 * )
	 */
	public function actionAddGameMode(){
		$mac_address = Frame::getStringFromRequest('mac_address');
		$gm_name = Frame::getStringFromRequest('gm_name');

		if(!empty($mac_address) && !empty($gm_name)){
			// 查询是否已存在该主机信息
			$pc_info = $this->__getComputerInfo($mac_address);

			$game_mode_model = new TdGameMode();
			$game_mode_model->pc_id = $pc_info['pc_id'];
			$game_mode_model->gm_name = $gm_name;
			if($game_mode_model->save()){
				// 获得新增游戏模式的id
				$insert_id = Yii::app()->db->getLastInsertID();

				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
					'gm_id' => $insert_id,
				);
			}else{
				$result = array(
					'ret_num' => 901,
					'ret_msg' => '添加失败',
				);
			}
		}else{
			$result = array(
				'ret_num' => 201,
				'ret_msg' => '参数输入不完整',
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/gameMode/updateGameMode",
	 *     summary="98.修改主机游戏模式",
	 *     tags={"GameMode"},
	 *     description="修改指定主机下的游戏模式信息",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gm_id",
	 *         in="query",
	 *         description="游戏模式id",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gm_name",
	 *         in="query",
	 *         description="游戏模式名称",
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
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 *     @SWG\Response(
	 *         response="332",
	 *         description="没有该主机信息",
	 *     ),
	 *     @SWG\Response(
	 *         response="905",
	 *         description="保存失败",
	 *     ),
	 * )
	 */
	public function actionUpdateGameMode(){
		$mac_address = Frame::getStringFromRequest('mac_address');
		$gm_id = Frame::getIntFromRequest('gm_id');
		$gm_name = Frame::getStringFromRequest('gm_name');
		if(empty($mac_address) || empty($gm_id) || empty($gm_name)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => '参数输入不完整',
			);
			echo json_encode ( $result );
			die();
		}

		// 查询是否已存在该主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 获取游戏模式信息
		$game_mode_model = new TdGameMode();
		$where = "gm_id = {$gm_id} AND pc_id = {$pc_id}";
		$game_mode_info = $game_mode_model->getGameModeInfo($where);
		if(!empty($game_mode_info)){
			// 更改游戏模式名称
			$game_mode_info->gm_name = $gm_name;
			if($game_mode_info->save()){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				$result = array(
					'ret_num' => 905,
					'ret_msg' => '保存失败',
				);
			}
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
	 *     path="/app_api/website/api.php/v2_2/gameMode/removeGameMode",
	 *     summary="99.删除主机游戏模式",
	 *     tags={"GameMode"},
	 *     description="逻辑删除指定主机下的游戏模式信息",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="gm_id",
	 *         in="query",
	 *         description="游戏模式id",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
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
	 *         response="332",
	 *         description="没有该主机信息",
	 *     ),
	 *     @SWG\Response(
	 *         response="905",
	 *         description="操作失败",
	 *     ),
	 * )
	 */
	public function actionRemoveGameMode(){
		$mac_address = Frame::getStringFromRequest('mac_address');
		$gm_id = Frame::getIntFromRequest('gm_id');
		if(empty($mac_address) || empty($gm_id)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => '参数输入不完整',
			);
			echo json_encode ( $result );
			die();
		}

		// 查询是否已存在该主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 获取游戏模式信息
		$game_mode_model = new TdGameMode();
		$where = "gm_id = {$gm_id} AND pc_id = {$pc_id}";
		$game_mode_info = $game_mode_model->getGameModeInfo($where);
		if(!empty($game_mode_info)){
			// 逻辑删除此游戏模式
			$game_mode_info->id_delete = 1;
			if($game_mode_info->save()){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				$result = array(
					'ret_num' => 905,
					'ret_msg' => '保存失败',
				);
			}
		}else{
			$result = array(
				'ret_num' => 309,
				'ret_msg' => '没有该记录',
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/gameMode/getGameModeList",
	 *     summary="100.获得主机游戏模式列表",
	 *     tags={"GameMode"},
	 *     description="传递主机标识,返回该主机下所添加的游戏模式列表",
	 *     operationId="",
	 *     consumes={"application/xml", "application/json"},
	 *     produces={"application/xml", "application/json"},
	 *     @SWG\Parameter(
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回游戏模式列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="game_mode_list",
	 *                 description="游戏模式列表",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="gm_id",
	 *                             description="游戏模式id",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="gm_name",
	 *                             description="游戏模式名称",
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
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 * )
	 */
	public function actionGetGameModeList(){
		$mac_address = Frame::getStringFromRequest('mac_address');
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => '参数输入不完整',
			);
			echo json_encode ( $result );
			die();
		}

		// 查询是否已存在该主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 获取游戏模式列表
		$game_mode_model = new TdGameMode();
		$where = "pc_id = {$pc_id}";
		$data_list = $game_mode_model->getGameModeList($where);
		if(!empty($data_list)){
			$game_mode_list = array();
			foreach($data_list as $key => $game_mode){
				$game_mode_list[$key]['gm_id'] = $game_mode['gm_id'];				// 游戏模式id
				$game_mode_list[$key]['gm_name'] = $game_mode['gm_name'];			// 游戏模式名称
			}

			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'game_mode_list' => $game_mode_list									// 游戏模式列表
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
	 * 获取主机信息
	 * @param $mac_address
	 * @return array|mixed|null
	 */
	private function __getComputerInfo($mac_address){
		$computer_model = new TdComputer();
		$computer_info = $computer_model->getComputerInfo("mac_address = {$mac_address}");
		if(empty($computer_info)){
			$result ['ret_num'] = 332;
			$result ['ret_msg'] = '没有该主机信息';
			echo json_encode ( $result );
			die ();
		}
		return $computer_info;
	}


}