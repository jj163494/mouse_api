<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/15
 * Time: 10:23
 */
class DeviceConfigController extends PublicController{


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v2_2/deviceConfig/uploadDeviceConfig",
     *     summary="109.上传设备配置",
     *     tags={"DeviceConfig"},
     *     description="根据设备类型和配置类型,上传指定的用户设备配置",
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
     *         name="device_model",
     *         in="query",
     *         description="设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="设备类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2"}
     *     ),
     *     @SWG\Parameter(
     *         name="config_type",
     *         in="query",
     *         description="配置类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2", "3"}
     *     ),
     *     @SWG\Parameter(
     *         name="config_name",
     *         in="query",
     *         description="配置名称",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="config_content",
     *         in="query",
     *         description="配置内容",
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
     *                 property="dc_id",
     *                 description="设备配置id",
     *                 type="integer",
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
    public function actionUploadDeviceConfig(){
        $device_model = Frame::getStringFromRequest('device_model');
        $device_type = Frame::getIntFromRequest('device_type');
        $config_type = Frame::getIntFromRequest('config_type');
        $config_name = Frame::getStringFromRequest('config_name');
        $config_content = Frame::getStringFromRequest('config_content');
        if(empty($device_type) || empty($config_type) || empty($config_name) || empty($config_content)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 查询用户是否已经上传过该设备配置
        $device_config_model = new TdDeviceConfig();
        $where = "member_id = {$member_id} AND device_type = {$device_type} AND config_type = {$config_type} AND config_name = '{$config_name}'";
        if($device_type == 2){
            // 设备为键盘时区分设备型号 2017/02/13
            $where .= " AND device_model = '{$device_model}'";
        }
        $device_config_info = $device_config_model->getDeviceConfigInfo($where);
        if(!empty($device_config_info)){
            // 有数据的情况进行更新
            $device_config_info->device_model = $device_model;
            $device_config_info->config_name = $config_name;
            $device_config_info->config_content = $config_content;
            if($device_config_info->save()){
                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'dc_id' => $device_config_info['dc_id']
                );
            }else{
                $result = array(
                    'ret_num' => 906,
                    'ret_msg' => '更新信息时发生错误',
                );
            }
        }else{
            // 没数据的情况进行新增
            $device_config_model->member_id = $member_id;
            if($device_type == 2){
                // 设备为键盘时才记录设备型号 2017/02/13
                $device_config_model->device_model = $device_model;
            }
            $device_config_model->device_type = $device_type;
            $device_config_model->config_type = $config_type;
            $device_config_model->config_name = $config_name;
            $device_config_model->config_content = $config_content;
            $device_config_model->created_time = time();
            if($device_config_model->save()){
                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'dc_id' => $device_config_model['dc_id']
                );
            }else{
                $result = array(
                    'ret_num' => 905,
                    'ret_msg' => '保存信息时发生错误',
                );
            }
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v2_2/deviceConfig/deleteDeviceConfig",
     *     summary="110.删除设备配置",
     *     tags={"DeviceConfig"},
     *     description="根据设备类型和配置类型,删除指定的用户设备配置",
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
     *         name="dc_id",
     *         in="query",
     *         description="配置id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_model",
     *         in="query",
     *         description="设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="config_name",
     *         in="query",
     *         description="配置名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="设备类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2"}
     *     ),
     *     @SWG\Parameter(
     *         name="config_type",
     *         in="query",
     *         description="配置类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2", "3"}
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
     *         description="没有该记录",
     *     ),
     * )
     */
    public function actionDeleteDeviceConfig(){
        $dc_id = Frame::getIntFromRequest('dc_id');
        $device_model = Frame::getStringFromRequest('device_model');
        $config_name = Frame::getStringFromRequest('config_name');
        $device_type = Frame::getIntFromRequest('device_type');
        $config_type = Frame::getIntFromRequest('config_type');
        if((empty($dc_id) && empty($config_name)) || empty($device_type) || empty($config_type)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 删除指定设备配置
        $device_config_model = new TdDeviceConfig();
        $where = "member_id = {$member_id} AND device_type = {$device_type} AND config_type = {$config_type}";
        if(!empty($dc_id)){
            $where .= " AND dc_id = {$dc_id}";
        }else if(!empty($config_name)){
            $where .= " AND config_name = '{$config_name}'";
        }
        if($device_type == 2){
            // 设备为键盘时区分设备型号 2017/02/13
            $where .= " AND device_model = '{$device_model}'";
        }
        $delete_result = $device_config_model->deleteAll($where);
        if(!empty($delete_result)){
            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
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
     * @SWG\Get(
     *     path="/app_api/website/api.php/v2_2/deviceConfig/getDeviceConfigInfo",
     *     summary="111.获取用户设备配置明细",
     *     tags={"DeviceConfig"},
     *     description="根据设备类型和配置类型,获取指定的用户设备配置明细",
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
     *         name="dc_id",
     *         in="query",
     *         description="配置id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_model",
     *         in="query",
     *         description="设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="config_name",
     *         in="query",
     *         description="配置名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="设备类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2"}
     *     ),
     *     @SWG\Parameter(
     *         name="config_type",
     *         in="query",
     *         description="配置类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2", "3"}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="操作成功",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="dc_id",
     *                 description="设备配置id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="device_model",
     *                 description="设备型号",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="config_name",
     *                 description="配置名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="config_content",
     *                 description="配置内容",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="created_time",
     *                 description="创建时间",
     *                 type="integer",
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
    public function actionGetDeviceConfigInfo(){
        $dc_id = Frame::getIntFromRequest('dc_id');
        $device_model = Frame::getStringFromRequest('device_model');
        $config_name = Frame::getStringFromRequest('config_name');
        $device_type = Frame::getIntFromRequest('device_type');
        $config_type = Frame::getIntFromRequest('config_type');
        if((empty($dc_id) && empty($config_name)) || empty($device_type) || empty($config_type)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 获取设备配置信息
        $device_config_model = new TdDeviceConfig();
        $where = "member_id = {$member_id} AND device_type = {$device_type} AND config_type = {$config_type}";
        if(!empty($dc_id)){
            $where .= " AND dc_id = {$dc_id}";
        }else if(!empty($config_name)){
            $where .= " AND config_name = '{$config_name}'";
        }
        if($device_type == 2){
            // 设备为键盘时区分设备型号 2017/02/13
            $where .= " AND device_model = '{$device_model}'";
        }
        $device_config_info = $device_config_model->getDeviceConfigInfo($where);
        if(!empty($device_config_info)){
            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'dc_id' => $device_config_info['dc_id'],                                            // 配置id
                'device_model' => $device_config_info['device_model'],                              // 设备型号
                'config_name' => $device_config_info['config_name'],                                // 配置名称
                'config_content' => $device_config_info['config_content'],                          // 配置内容
                'created_time' => $device_config_info['created_time'],                              // 创建时间
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
     * @SWG\Get(
     *     path="/app_api/website/api.php/v2_2/deviceConfig/getDeviceConfigList",
     *     summary="112.获取用户设备配置列表",
     *     tags={"DeviceConfig"},
     *     description="根据设备类型和配置类型,获取用户所有的设备配置列表",
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
     *         name="device_model",
     *         in="query",
     *         description="设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="设备类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2"}
     *     ),
     *     @SWG\Parameter(
     *         name="config_type",
     *         in="query",
     *         description="配置类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2", "3"}
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="操作成功",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="device_config_list",
     *                 description="设备配置列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="dc_id",
     *                             description="宏id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="device_model",
     *                             description="设备型号",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="config_name",
     *                             description="配置名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="config_content",
     *                             description="配置内容",
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
    public function actionGetDeviceConfigList(){
        $device_model = Frame::getStringFromRequest('device_model');
        $device_type = Frame::getIntFromRequest('device_type');
        $config_type = Frame::getIntFromRequest('config_type');
        if(empty($device_type) || empty($config_type)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 获取设备配置列表
        $device_config_model = new TdDeviceConfig();
        $where = "member_id = {$member_id} AND device_type = {$device_type} AND config_type = {$config_type}";
        if($device_type == 2){
            // 设备为键盘时区分设备型号 2017/02/13
            $where .= " AND device_model = '{$device_model}'";
        }
        $data_list = $device_config_model->getDeviceConfigList($where);
        if(!empty($data_list)){
            $device_config_list = array();
            foreach($data_list as $k => $device_config_info){
                $device_config_list[$k]['dc_id'] = $device_config_info['dc_id'];                    // 配置id
                $device_config_list[$k]['device_model'] = $device_config_info['device_model'];      // 设备型号
                $device_config_list[$k]['config_name'] = $device_config_info['config_name'];        // 配置名称
                $device_config_list[$k]['config_content'] = $device_config_info['config_content'];  // 配置内容
                $device_config_list[$k]['created_time'] = $device_config_info['created_time'];      // 创建时间
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'device_config_list' => $device_config_list                                         // 设备配置列表
            );
        }else{
            $result = array(
                'ret_num' => 309,
                'ret_msg' => '没有该记录',
            );
        }

        echo json_encode($result);
    }

}