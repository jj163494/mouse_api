<?php

class ComputerController extends PublicController{

	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/computer/addComputer",
	 *     summary="94.添加主机信息",
	 *     tags={"Computer"},
	 *     description="根据主机标识增加主机数据",
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
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	 *         response="901",
	 *         description="添加失败",
	 *     ),
	 * )
	 */
	public function actionAddComputer(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest('mac_address');
		if(!empty($mac_address)){
			// 查询是否已存在该主机信息
			$pc_model = new TdComputer();
			$computer_info = $pc_model->getComputerInfo("mac_address = '{$mac_address}'");
			if(!empty($computer_info)){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				// 新增主机信息
				$pc_model->mac_address = $mac_address;
				$pc_model->created_time = time();

				if($pc_model->save()){
					$result = array(
						'ret_num' => 0,
						'ret_msg' => 'ok',
					);
				}else{
					$result = array(
						'ret_num' => 901,
						'ret_msg' => $this->language->get('add_fail'),
					);
				}
			}
		}else{
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/computer/uploadComputerConfig",
	 *     summary="95.上传主机设置",
	 *     tags={"Computer"},
	 *     description="上传用户对这台主机进行的设置信息",
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
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="config",
	 *         in="query",
	 *         description="设置详情",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	 *         description="用户标识不能为空",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备上登录",
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
	public function actionUploadComputerConfig(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		$config = Frame::getStringFromRequest("config");
		if(empty($mac_address) || empty($config)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		$pc_config_model = new TdComputerConfig();
		// 查看该用户在该主机上是否已有设置
		$where = "pc_id = {$pc_id} AND member_id = {$member_id}";
		$config_info = $pc_config_model->getConfigInfo($where);
		if(empty($config_info)){
			// 记录该用户对此主机的设置
			$pc_config_model->pc_id = $pc_id;
			$pc_config_model->member_id = $member_id;
			$pc_config_model->config = $config;
			$pc_config_model->created_time = time();
			if($pc_config_model->save()){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				$result = array(
					'ret_num' => 905,
					'ret_msg' => $this->language->get('save_fail'),
				);
			}
		}else{
			$config_info->config = $config;
			if($config_info->save()){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				$result = array(
					'ret_num' => 905,
					'ret_msg' => $this->language->get('save_fail'),
				);
			}
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/computer/downloadComputerConfig",
	 *     summary="96.下载主机设置",
	 *     tags={"Computer"},
	 *     description="下载用户上传的主机配置信息,如此机器没有上传过配置则返回最近一条配置信息",
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
	 *         name="mac_address",
	 *         in="query",
	 *         description="主机标识",
	 *         required=true,
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="config",
	 *                 description="主机设置",
	 *                 type="string",
	 *             ),
	 *         ),
	 *     ),
	 *     @SWG\Response(
	 *         response="201",
	 *         description="参数输入不完整",
	 *     ),
	 *     @SWG\Response(
	 *         response="222",
	 *         description="用户标识不能为空",
	 *     ),
	 *     @SWG\Response(
	 *         response="306",
	 *         description="用户不存在或已在其他设备上登录",
	 *     ),
	 *     @SWG\Response(
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 *     @SWG\Response(
	 *         response="332",
	 *         description="没有该主机信息",
	 *     ),
	 * )
	 */
	public function actionDownloadComputerConfig(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		$pc_config_model = new TdComputerConfig();
		// 获取用户对该主机的配置
		$where1 = "member_id = {$member_id}";
		$where2 = $where1." AND pc_id = {$pc_id}";
		$config_info = $pc_config_model->getConfigInfo($where2);
		if(!empty($config_info)){
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'config' => $config_info['config'],
			);
		}else{
			// 如该主机没有上传过配置则获取该用户最近一次上传的配置
			$newer_config_info = $pc_config_model->getConfigInfo($where1);
			if(!empty($newer_config_info)){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
					'config' => $newer_config_info['config'],
				);
			}else{
				$result = array(
					'ret_num' => 309,
					'ret_msg' => $this->language->get('no_data'),
				);
			}
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/computer/saveComputerHistory",
	 *     summary="101.添加主机运行记录",
	 *     tags={"Computer"},
	 *     description="根据主机的开机时间和关机时间计算本次主机的运行时长及时间段",
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
	 *         name="power_on",
	 *         in="query",
	 *         description="开机时间",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="power_off",
	 *         in="query",
	 *         description="关机时间",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	 *         description="保存失败",
	 *     ),
	 * )
	 */
	public function actionSaveComputerHistory(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		$power_on = Frame::getIntFromRequest("power_on");
		$power_off = Frame::getIntFromRequest("power_off");
		if(empty($mac_address) || empty($power_on) || empty($power_off)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);

		// 计算本次主机合计运行的时间
		$time_long = $power_off - $power_on;
		// 获得开机合计时间
		$total_run_hours = round($time_long / 3600) + 1;
		// 获取开机时的小时
		$on_hours = date('H', $power_on);

		// 初始化时间段变量
		$run_time = array();
		for($i = 0; $i < 24; $i++){
			$run_time['run_time_'. $i] = 0;
		}
		// 计算开机时间段
		for($i = 0; $i < $total_run_hours; $i++){
			$run_hours = $i + $on_hours;
			$section = $run_hours % 24;

			$run_time['run_time_'. $section] += 1;
		}

		// 保存本次运行数据
		$pc_history_model = new TdComputerHistory();
		$pc_history_model->pc_id = $pc_info['pc_id'];
		$pc_history_model->time_long = $time_long;
		$pc_history_model->run_time_0 = $run_time['run_time_0'];
		$pc_history_model->run_time_1 = $run_time['run_time_1'];
		$pc_history_model->run_time_2 = $run_time['run_time_2'];
		$pc_history_model->run_time_3 = $run_time['run_time_3'];
		$pc_history_model->run_time_4 = $run_time['run_time_4'];
		$pc_history_model->run_time_5 = $run_time['run_time_5'];
		$pc_history_model->run_time_6 = $run_time['run_time_6'];
		$pc_history_model->run_time_7 = $run_time['run_time_7'];
		$pc_history_model->run_time_8 = $run_time['run_time_8'];
		$pc_history_model->run_time_9 = $run_time['run_time_9'];
		$pc_history_model->run_time_10 = $run_time['run_time_10'];
		$pc_history_model->run_time_11 = $run_time['run_time_11'];
		$pc_history_model->run_time_12 = $run_time['run_time_12'];
		$pc_history_model->run_time_13 = $run_time['run_time_13'];
		$pc_history_model->run_time_14 = $run_time['run_time_14'];
		$pc_history_model->run_time_15 = $run_time['run_time_15'];
		$pc_history_model->run_time_16 = $run_time['run_time_16'];
		$pc_history_model->run_time_17 = $run_time['run_time_17'];
		$pc_history_model->run_time_18 = $run_time['run_time_18'];
		$pc_history_model->run_time_19 = $run_time['run_time_19'];
		$pc_history_model->run_time_20 = $run_time['run_time_20'];
		$pc_history_model->run_time_21 = $run_time['run_time_21'];
		$pc_history_model->run_time_22 = $run_time['run_time_22'];
		$pc_history_model->run_time_23 = $run_time['run_time_23'];
		$pc_history_model->power_on = $power_on;
		$pc_history_model->power_off = $power_off;
		$pc_history_model->created_time = time();

		if($pc_history_model->save()){
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
			);
		}else{
			$result = array(
				'ret_num' => 905,
				'ret_msg' => $this->language->get('save_fail'),
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/computer/GetComputerSummary",
	 *     summary="102.获取主机运行概要",
	 *     tags={"Computer"},
	 *     description="根据主机标识获取主机的运行历史概要",
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
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回用户设备列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="total_run_time",
	 *                 description="最长连续运行时长(秒)",
	 *                 type="integer",
	 *             ),
	 *             @SWG\Property(
	 *                 property="usually_info",
	 *                 description="常用时段明细",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="begin_section",
	 *                             description="最多启动时段",
	 *                             type="string",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="end_section",
	 *                             description="最多结束时段",
	 *                             type="string",
	 *                         ),
	 *                     ),
	 *                 }
	 *             ),
	 *             @SWG\Property(
	 *                 property="last_info",
	 *                 description="上次运行明细",
	 *                 type="array",
	 *                 items={
	 *                     @SWG\Schema(
	 *                         @SWG\Property(
	 *                             property="on_time",
	 *                             description="上次启动时间",
	 *                             type="integer",
	 *                         ),
	 *                         @SWG\Property(
	 *                             property="off_time",
	 *                             description="上次停止运行时间",
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
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 *     @SWG\Response(
	 *         response="332",
	 *         description="没有该主机信息",
	 *     ),
	 * )
	 */
	public function actionGetComputerSummary(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		$pc_history_model = new TdComputerHistory();
		$where = "pc_id = {$pc_id}";
		// 获取最长连续运行时长
		$total_info = $pc_history_model->getHistoryInfo($where, "MAX(time_long) as time_long");
		if(empty($total_info['time_long'])){
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取使用时段的合计
		$usually_key = "SUM(run_time_0) as run_time_0, SUM(run_time_1) as run_time_1, SUM(run_time_2) as run_time_2, SUM(run_time_3) as run_time_3, SUM(run_time_4) as run_time_4, 
		SUM(run_time_5) as run_time_5, SUM(run_time_6) as run_time_6, SUM(run_time_7) as run_time_7, SUM(run_time_8) as run_time_8, SUM(run_time_9) as run_time_9,
		SUM(run_time_10) as run_time_10, SUM(run_time_11) as run_time_11, SUM(run_time_12) as run_time_12, SUM(run_time_13) as run_time_13, SUM(run_time_14) as run_time_14, 
		SUM(run_time_15) as run_time_15, SUM(run_time_16) as run_time_16, SUM(run_time_17) as run_time_17, SUM(run_time_18) as run_time_18, SUM(run_time_19) as run_time_19,
		SUM(run_time_20) as run_time_20, SUM(run_time_21) as run_time_21, SUM(run_time_22) as run_time_22, SUM(run_time_23) as run_time_23";
		$usually_info = $pc_history_model->getHistoryInfo($where, $usually_key);
		// 初始化变量
		$section_arr = array(
			'0-2' => 0, '1-3' => 0, '2-4' => 0, '3-5' => 0, '4-6' => 0,
			'5-7' => 0, '6-8' => 0, '7-9' => 0, '8-10' => 0, '9-11' => 0,
			'10-12' => 0, '11-13' => 0, '12-14' => 0, '13-15' => 0, '14-16' => 0,
			'15-17' => 0, '16-18' => 0, '17-19' => 0, '18-20' => 0, '19-21' => 0,
			'20-22' => 0, '21-23' => 0, '22-24' => 0, '23-1' => 0,
		);
		// 计算常用时段
		$use_section = array();
		foreach($section_arr as $key => $value){
			$section = explode("-", $key);
			$section1 = intval($section[0]);
			$section2 = intval($section[1]) - 1;
			$use_section[$key] = $usually_info['run_time_'.$section1] + $usually_info['run_time_'.$section2];
		}
		$section_key = array_search(max($use_section), $use_section);
		$run_section_arr = explode("-", $section_key);

		// 上一次运行时间
		$last_info = $pc_history_model->getHistoryInfo($where, "power_on, power_off");

		$result = array(
			'ret_num' => 0,
			'ret_msg' => 'ok',
			'total_run_time' => $total_info['time_long'],					// 最长连续运行时长(秒)
			'usually_info' => array(
				'begin_section' => $run_section_arr[0],						// 最多启动时段
				'end_section' => $run_section_arr[1],						// 最多结束时段
			),
			'last_info' => array(
				'on_time' => $last_info['power_on'],						// 上次启动时间
				'off_time' => $last_info['power_off'],						// 上次停止运行时间
			)
		);

		echo json_encode ( $result );
	}


	/**
	 * @SWG\Delete(
	 *     path="/app_api/website/api.php/v2_2/computer/clearComputerHistory",
	 *     summary="103.清除主机运行历史",
	 *     tags={"Computer"},
	 *     description="根据主机标识对运行历史进行逻辑删除",
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
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	public function actionClearComputerHistory(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 逻辑删除该主机的历史记录
		$pc_history_model = new TdComputerHistory();
		$attributes = array(
			"update_time" => time(),
			"is_delete" => 1
		);
		$update = $pc_history_model->updateAll($attributes, "pc_id = :pc_id AND is_delete = :is_delete", array(":pc_id" => $pc_id, ":is_delete" => 0));
		if(!empty($update)){
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
			);
		}else{
			$result = array(
				'ret_num' => 905,
				'ret_msg' => $this->language->get('save_fail'),
			);
		}

		echo json_encode ( $result );
	}


	/**
	 * 获取主机信息
	 * @param $mac_address
	 * @return array|mixed|null
	 */
	private function __getComputerInfo($mac_address){
		$computer_model = new TdComputer();
		$computer_info = $computer_model->getComputerInfo("mac_address = '{$mac_address}'");
		if(empty($computer_info)){
			$result ['ret_num'] = 332;
			$result ['ret_msg'] = '没有该主机信息';
			echo json_encode ( $result );
			die ();
		}
		return $computer_info;
	}


	/**
	 * @SWG\Post(
	 *     path="/app_api/website/api.php/v2_2/computer/addGameHistory",
	 *     summary="104.新增游戏记录",
	 *     tags={"Computer"},
	 *     description="根据主机标识和游戏模式id新增该游戏的记录",
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
	 *         name="time_long",
	 *         in="query",
	 *         description="游戏时长(秒)",
	 *         required=true,
	 *         type="integer",
	 *         @SWG\Items(type="integer"),
	 *         collectionFormat="multi"
	 *     ),
	 *     @SWG\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	 *         description="操作失败",
	 *     ),
	 * )
	 */
	public function actionAddGameHistory(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		$gm_id = Frame::getStringFromRequest("gm_id");
		$time_long = Frame::getStringFromRequest("time_long");
		if(empty($mac_address) || empty($gm_id) || empty($time_long)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 获取游戏模式信息
		$game_mode_model = new TdGameMode();
		$where = "gm_id = {$gm_id} AND pc_id = {$pc_id}";
		$game_mode_info = $game_mode_model->getGameModeInfo($where);
		if(!empty($game_mode_info)){
			// 新增游戏记录
			$game_history_model = new TdGameHistory();
			$game_history_model->pc_id = $pc_id;
			$game_history_model->gm_id = $gm_id;
			$game_history_model->time_long = $time_long;
			$game_history_model->created_time = time();
			if($game_history_model->save()){
				$result = array(
					'ret_num' => 0,
					'ret_msg' => 'ok',
				);
			}else{
				$result = array(
					'ret_num' => 905,
					'ret_msg' => $this->language->get('save_fail'),
				);
			}
		}else{
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
		}

		echo json_encode($result);
	}


	/**
	 * @SWG\Delete(
	 *     path="/app_api/website/api.php/v2_2/computer/clearGameHistory",
	 *     summary="105.清除游戏记录",
	 *     tags={"Computer"},
	 *     description="逻辑删除指定主机下的所有游戏记录",
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
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
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
	public function actionClearGameHistory(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 逻辑删除该主机的游戏历史记录
		$game_history_model = new TdGameHistory();
		$attributes = array(
			"update_time" => time(),
			"is_delete" => 1
		);
		$update = $game_history_model->updateAll($attributes, "pc_id = :pc_id AND is_delete = :is_delete", array(":pc_id" => $pc_id, ":is_delete" => 0));
		if(!empty($update)){
			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
			);
		}else{
			$result = array(
				'ret_num' => 905,
				'ret_msg' => $this->language->get('save_fail'),
			);
		}

		echo json_encode ( $result );
	}


	/**
	 * @SWG\Get(
	 *     path="/app_api/website/api.php/v2_2/computer/getGameHistoryList",
	 *     summary="106.获取游戏记录列表",
	 *     tags={"Computer"},
	 *     description="根据主机标识获取该主机的游戏历史记录",
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
	 *     @SWG\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         description="语言",
	 *         type="string",
	 *         @SWG\Items(type="string"),
	 *         collectionFormat="multi",
	 *         default="ZH",
	 *     ),
	 *     @SWG\Response(
	 *         response="200",
	 *         description="成功时返回游戏历史记录列表",
	 *         @SWG\Schema(
	 *             @SWG\Property(
	 *                 property="game_history_list",
	 *                 description="游戏历史记录列表",
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
	 *                         @SWG\Property(
	 *                             property="time_long",
	 *                             description="合计游戏时长(秒)",
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
	 *         response="309",
	 *         description="没有该记录",
	 *     ),
	 *     @SWG\Response(
	 *         response="332",
	 *         description="没有该主机信息",
	 *     ),
	 * )
	 */
	public function actionGetGameHistoryList(){
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$mac_address = Frame::getStringFromRequest("mac_address");
		if(empty($mac_address)){
			$result = array(
				'ret_num' => 201,
				'ret_msg' => $this->language->get('miss_param'),
			);
			echo json_encode ( $result );
			die ();
		}

		$page = Frame::getIntFromRequest('page');
		$page_size = Frame::getIntFromRequest('pageSize');
		// 设置默认页数和页码
		if(empty($page) || $page <= 0){
			$page = 0;
		}
		if(empty($page_size)){
			$page_size = 10;
		}

		// 获取主机信息
		$pc_info = $this->__getComputerInfo($mac_address);
		$pc_id = $pc_info['pc_id'];

		// 获取游戏历史记录
		$game_history_model = new TdGameHistory();
		$game_history_where = "pc_id = {$pc_id}";
		$data_list = $game_history_model->getGameHistoryList($game_history_where, "gm_id, SUM(time_long) AS time_long", $page_size, $page, "time_long DESC, gm_id ASC", "gm_id");
		if(!empty($data_list)){
			// 获取游戏模式名称
			$game_mode_model = new TdGameMode();
			$gm_id_arr = array();
			foreach($data_list as $k => $game_history){
				$gm_id_arr[] = $game_history['gm_id'];
			}
			$gm_id_str = implode(",", $gm_id_arr);
			$game_mode_where = "gm_id IN ({$gm_id_str})";
			$game_mode_list = $game_mode_model->getGameModeName($game_mode_where);
			$gm_name_arr = array();
			foreach($game_mode_list as $k => $game_mode){
				$gm_name_arr[$game_mode['gm_id']] = $game_mode['gm_name'];
			}

			// 记录游戏模式名称用来返回
			$game_history_list = array();
			foreach($data_list as $k => $game_history){
				$game_history_list[$k]['gm_id'] = $game_history['gm_id'];							// 游戏模式id
				$game_history_list[$k]['gm_name'] = $gm_name_arr[$game_history['gm_id']];			// 游戏模式名称
				$game_history_list[$k]['time_long'] = $game_history['time_long'];					// 合计游戏时长(秒)
			}

			$result = array(
				'ret_num' => 0,
				'ret_msg' => 'ok',
				'game_history_list' => $game_history_list											// 游戏历史记录列表
			);
		}else{
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
		}

		echo json_encode ( $result );
	}


}