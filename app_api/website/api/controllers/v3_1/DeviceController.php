<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 9:56
 */
class DeviceController extends PublicController{

    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/device/bindDevice",
     *     summary="21.绑定设备",
     *     tags={"Device"},
     *     description="绑定用户设备,返回设备所属人信息",
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
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="deviceType",
     *         in="query",
     *         description="设备类型",
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
     *         description="成功时返回结果",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="auth_info",
     *                 description="所属者信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="member_id",
     *                             description="用户id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="member_name",
     *                             description="用户名",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="member_mobile",
     *                             description="用户手机",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="member_email",
     *                             description="用户邮箱",
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
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或已在其他设备上登录",
     *     ),
     *     @SWG\Response(
     *         response="903",
     *         description="绑定失败",
     *     ),
     *     @SWG\Response(
     *         response="912",
     *         description="此设备已被绑定",
     *     ),
     * )
     */
    public function actionBindDevice() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        $device_type = Frame::getIntFromRequest ( 'deviceType' );
        if (empty ( $device_code ) || empty ( $device_type )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 查找该用户有没有绑定过该设备
        $device_info = TdUserDevice::model ()->find ( "device_type={$device_type} && device_code='{$device_code}'" );
        if(!empty($device_info)){
            // 获取该设备的绑定人信息 bug#455 2016/11/30
            $member_model = new TaidushopMember();
            $author_id = $device_info['userId'];
            $author_info = $member_model->find("member_id = {$author_id}");
            if($author_id != $member_id){
                // 其他人已绑定该设备
                $member_name = $author_info['member_name'];
                $msg = $this->language->get('has_bound');
                if(!empty($author_info['member_mobile'])){
                    // 隐藏显示的手机号
                    $member_mobile = substr_replace($author_info['member_mobile'], "****", 3, -4);
                    $msg = str_replace('{$member_name}', $member_name."（{$member_mobile}）", $msg);
                }else{
                    $msg = str_replace('{$member_name}', $member_name, $msg);
                }

                $result = array(
                    'ret_num' => 912,
                    'ret_msg' => $msg,
                    'auth_info' => array(
                        'member_id' => $author_info['member_id'],                                                           // 用户id
                        'member_name' => $member_name,                                                                      // 用户名
                        'member_mobile' => $author_info['member_mobile'],                                                   // 用户手机
                        'member_email' => (!empty($author_info['member_email'])) ? $author_info['member_email'] : "",      // 用户邮箱
                    ),
                );
            }else{
                // 已绑定该设备
                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'auth_info' => array(
                        'member_id' => $member_info['member_id'],                                                           // 用户id
                        'member_name' => $member_info['member_name'],                                                       // 用户名
                        'member_mobile' => $member_info['member_mobile'],                                                   // 用户手机
                        'member_email' => (!empty($member_info['member_email'])) ? $member_info['member_email'] : "",      // 用户邮箱
                    ),
                );
            }

            echo json_encode ( $result );
            die();
        }

        // 未绑定的情况进行绑定操作
        $model = new TdUserDevice ();
        $model->userId = $member_id;
        $model->device_code = $device_code;
        $model->device_type = $device_type;
        $model->created_time = time ();
        if ($model->save ()) {
            // 查找该设备是否为新设备
            $device = TdDevice::model ()->find ( "device_code='{$device_code}'" );
            if (! $device) {
                // 插入
                $modelDevice = new TdDevice ();
                // 设备类型不统一 TODO
                if ($device_type == 1) {
                    $modelDevice->device_name = $member_info['member_name'] . "的鼠标";
                } elseif ($device_type == 2) {
                    $modelDevice->device_name = $member_info['member_name'] . "的键盘";
                } elseif ($device_type == 3) {
                    $modelDevice->device_name = $member_info['member_name'] . "的300鼠标";
                } elseif ($device_type == 4) {
                    $modelDevice->device_name = $member_info['member_name'] . "的600鼠标";
                }
                $modelDevice->device_code = $device_code;
                $modelDevice->device_type = $device_type;
                $modelDevice->created_time = time ();
                $modelDevice->save ();
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'auth_info' => array(
                    'member_id' => $member_info['member_id'],                                                           // 用户id
                    'member_name' => $member_info['member_name'],                                                       // 用户名
                    'member_mobile' => $member_info['member_mobile'],                                                   // 用户手机
                    'member_email' => (!empty($member_info['member_email'])) ? $member_info['member_email'] : "",      // 用户邮箱
                ),
            );
        } else {
            $result['ret_num'] = 905;
            $result['ret_msg'] = $this->language->get('save_fail');
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v3_1/device/removeDevice",
     *     summary="22.解除设备",
     *     tags={"Device"},
     *     description="解绑用户设备,返回结果",
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
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
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
     *         description="成功时返回结果",
     *         @SWG\Schema(ref="#/definitions/ApiResponse")
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="参数输入不完整",
     *     ),
     *     @SWG\Response(
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或已在其他设备上登录",
     *     ),
     *     @SWG\Response(
     *         response="904",
     *         description="解绑失败",
     *     ),
     * )
     */
    public function actionRemoveDevice() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        if (empty ( $device_code )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $count = TdUserDevice::model ()->deleteAll ( "userId={$member_id} && device_code='{$device_code}'" );
        if ($count > 0) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = 'ok';
        } else {
            $result['ret_num'] = 904;
            $result['ret_msg'] = $this->language->get('delete_fail');
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/device/getUserDeviceList",
     *     summary="23.获取已绑定的设备",
     *     tags={"Device"},
     *     description="取得用户绑定的所有设备,返回用户设备列表",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="open_id",
     *         in="query",
     *         description="用户登录时生成的唯一值",
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
     *                 property="user_device_list",
     *                 description="用户设备列表",
     *                 type="array",
     *                 ref="#/definitions/Device"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="222",
     *         description="用户标识不能为空",
     *     ),
     *     @SWG\Response(
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或已在其他设备上登录",
     *     ),
     *     @SWG\Response(
     *         response="309",
     *         description="没有该记录",
     *     ),
     * )
     */
    public function actionGetUserDeviceList() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $user_device_list = array();
        // 获取设备数据
        $ud_table_name = TdUserDevice::model()-> tableName();
        $d_table_name = TdDevice::model()-> tableName();
        $where = "userId = {$member_id}";
        $sql = "SELECT ud.*, d.* FROM {$ud_table_name} AS ud LEFT JOIN {$d_table_name} AS d ON ud.device_code = d.device_code WHERE {$where} ORDER BY ud.id DESC";

        $command = Yii::app ()->db->createCommand($sql);
        $data_list = $command->queryAll();
        if ($data_list) {
            foreach ( $data_list as $key => $user_device_info ) {
                if(!empty($user_device_info['device_name'])){
                    // 设备名称不存在的话不显示
                    $user_device_list[] = array (
                        "device_code" => $user_device_info['device_code'],					// 设备编号
                        "device_type" => $user_device_info['device_type'],					// 设备类型
                        "device_name" => $user_device_info['device_name'],					// 设备名称
                        "device_state" => 0													// 设备状态
                    );
                }
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = 'ok';
            $result['user_device_list'] = $user_device_list;
        }else{
            // 没有数据的情况返回错误信息
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/device/getDeviceInfo",
     *     summary="26.取得设备信息",
     *     tags={"Device"},
     *     description="根据设备号获取设备信息",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
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
     *                 property="device_code",
     *                 description="设备号",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_name",
     *                 description="设备名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_type",
     *                 description="设备类型",
     *                 type="integer",
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
    public function actionGetDeviceInfo() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        if (empty ( $device_code )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        $device_info = TdDevice::model ()->find ( "device_code='{$device_code}'" );
        if ($device_info) {
            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'device_code' => $device_info['device_code'],           // 设备号
                'device_name' => $device_info['device_name'],           // 设备名称
                'device_type' => $device_info['device_type']            // 设备类型
            );
        } else {
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/device/updateDeviceName",
     *     summary="88.修改设备名称",
     *     tags={"Device"},
     *     description="根据设备号修改设备名称",
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
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="deviceName",
     *         in="query",
     *         description="设备名称",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="deviceType",
     *         in="query",
     *         description="设备类型",
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
     *         description="成功时返回结果",
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
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或在其他设备登录",
     *     ),
     *     @SWG\Response(
     *         response="905",
     *         description="保存失败",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="保存失败",
     *     ),
     * )
     */
    public function actionUpdateDeviceName(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        $device_name = Frame::getStringFromRequest ( 'deviceName' );
        $device_type = Frame::getIntFromRequest('deviceType');

        if (empty ( $device_code ) || empty ( $device_name )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 查找设备
        $where = "device_code='{$device_code}'";
        if(!empty($device_type)){
            $where .= "AND device_type = {$device_type}";
        }
        $device_info = TdDevice::model()->find($where);
        if ($device_info){
            // 更改设备名
            $device_info->device_name = $device_name;
            if(!$device_info->update()){
                $result['ret_num'] = 906;
                $result['ret_msg'] = $this->language->get('update_fail');
                echo json_encode ( $result );
                die();
            }
        }else{
            // 新增设备 2017/04/17
            $device_model = new TdDevice();
            $device_model->device_code = $device_code;
            $device_model->device_name = $device_name;
            $device_model->device_type = $device_type;
            $device_model->created_time = time();
            if(!$device_model->save()){
                $result['ret_num'] = 905;
                $result['ret_msg'] = $this->language->get('save_fail');
                echo json_encode ( $result );
                die();
            }
        }

        // 更改装备名
        $equipment_info = TdEquipmentData::model()->find(" userid='{$member_id}' && device_code='{$device_code}'");
        if ($equipment_info){
            $equipment_info->device_name = $device_name;
            if(!$equipment_info->update()){
                $result['ret_num'] = 906;
                $result['ret_msg'] = $this->language->get('update_fail');
                echo json_encode ( $result );
                die();
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';

        echo json_encode ( $result );
    }


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/device/saveUserEquipment",
     *     summary="82.保存装备数据",
     *     tags={"Device"},
     *     description="上传装备数据并保存",
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
     *         name="deviceType",
     *         in="query",
     *         description="设备类型",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="deviceModel",
     *         in="query",
     *         description="设备型号",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="keyKnockDetail",
     *         in="query",
     *         description="键盘按键详情",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="clickNumLeft",
     *         in="query",
     *         description="鼠标左键点击次数",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="clickNumRight",
     *         in="query",
     *         description="鼠标右键点击次数",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="moveDistance",
     *         in="query",
     *         description="移动距离(单位:毫米)",
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
     *         description="成功时返回结果",
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
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或在其他设备登录",
     *     ),
     *     @SWG\Response(
     *         response="901",
     *         description="新增数据失败",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="编辑数据失败",
     *     ),
     * )
     */
    public function actionSaveUserEquipment() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_type = Frame::getIntFromRequest ( 'deviceType' );
        $device_model = Frame::getStringFromRequest ( 'deviceModel' );
        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        $key_knock_detail = Frame::getStringFromRequest ( 'keyKnockDetail' );
        $click_num_left = Frame::getIntFromRequest ( 'clickNumLeft' );
        $click_num_right = Frame::getIntFromRequest ( 'clickNumRight' );
        $move_distance = Frame::getIntFromRequest ( 'moveDistance' );
        if (empty ( $device_type ) || empty ( $device_model ) || empty ( $device_code )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $equipment_model = new TdEquipmentData ();
        // 查询数据库是否有该数据
        $equipment_info = $equipment_model->find ( " userid='{$member_id}' && device_code='{$device_code}'" );
        if (! empty ( $equipment_info )) {
            // 装备表有数据的情况更新数据
            $equipment_info->userid = $member_id;                              // 用户id
            $equipment_info->device_type = $device_type;                       // 设备类型
            $equipment_info->device_model = $device_model;                     // 设备型号
            $equipment_info->device_code = $device_code;                       // 设备号
            $equipment_info->key_knock_detail = $key_knock_detail;             // 键盘按键点击次数
            $equipment_info->click_num_left = $click_num_left;                 // 鼠标左键点击次数
            $equipment_info->click_num_right = $click_num_right;               // 鼠标右键点击次数
            $equipment_info->move_distance = $move_distance;                   // 移动距离(毫米)
            $equipment_info->update_time = time ();
            if ($equipment_info->update ()) {
                $result['ret_num'] = 0;
                $result['ret_msg'] = 'ok';
            } else {
                $result['ret_num'] = 906;
                $result['ret_msg'] = $this->language->get('update_fail');
            }
        } else {
            // 新增装备
            $old_move_distance = $old_click_num_left = $old_click_num_right = 0;

            if ($device_type == 1) {
                // 设备类型为1是鼠标  先去老表找对应id 的数据累加添加到新表中
                // 新表没数据 查询老表数据统计 用户的汇总（老表数据每次新增新表数据时都要检查）
                $sql = "SELECT SUM(distance) sum_distance,SUM(left_click_num) sum_left_click_num,SUM(right_click_num) sum_right_click_num from td_heart where userid='{$member_id}'";
                $result1 = yii::app ()->db->createCommand ( $sql );
                $info = $result1->queryAll ();

                $old_move_distance = ! empty ( $info [0] ['sum_distance'] ) ? $info [0] ['sum_distance'] : 0;
                $old_click_num_left = ! empty ( $info [0] ['sum_left_click_num'] ) ? $info [0] ['sum_left_click_num'] : 0;
                $old_click_num_right = ! empty ( $info [0] ['sum_right_click_num'] ) ? $info [0] ['sum_right_click_num'] : 0;
            }

            // 根据设备表获得设备名称,没有设备名称时保存设备型号 #2017/04/28
            $device_info = TdDevice::model ()->find("device_code = '{$device_code}'");
            if(!empty($device_info)){
                $device_name = $device_info['device_name'];
            }else{
                $device_name = $device_model;
            }

            $equipment_model->userid = $member_id;                              // 用户id
            $equipment_model->device_type = $device_type;                       // 设备类型
            $equipment_model->device_model = $device_model;                     // 设备型号
            $equipment_model->device_code = $device_code;                       // 设备号
            $equipment_model->device_name = $device_name;                       // 设备名称
            $equipment_model->key_knock_detail = $key_knock_detail;             // 键盘按键点击次数
            $equipment_model->click_num_left = $click_num_left;                 // 鼠标左键点击次数
            $equipment_model->click_num_right = $click_num_right;               // 鼠标右键点击次数
            $equipment_model->move_distance = $move_distance;                   // 移动距离(毫米)
            $equipment_model->old_click_num_left = $old_click_num_left;         // 老表鼠标左键点击次数
            $equipment_model->old_click_num_right = $old_click_num_right;       // 老表鼠标右键点击次数
            $equipment_model->old_move_distance = $old_move_distance;           // 老表移动距离(毫米)
            $equipment_model->update_time = time ();
            if ($equipment_model->save ()) {
                $result['ret_num'] = 0;
                $result['ret_msg'] = 'ok';
            } else {
                $result['ret_num'] = 901;
                $result['ret_msg'] = $this->language->get('add_fail');
            }
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/device/getUserEquipmentList",
     *     summary="83.查看用户所有装备",
     *     tags={"Device"},
     *     description="根据用户标识和装备类型获取用户的装备列表",
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
     *         name="deviceType",
     *         in="query",
     *         description="设备类型",
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
     *         description="成功时返回用户设备列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="total_click_num",
     *                 description="所有鼠标合计点击次数",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="total_move_distance",
     *                 description="所有鼠标合计移动距离(毫米)",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="equipment_list",
     *                 description="装备列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="id",
     *                             description="主键id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="device_type",
     *                             description="设备类型",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="device_model",
     *                             description="设备型号",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="device_name",
     *                             description="设备名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="device_code",
     *                             description="设备号",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="key_knock_detail",
     *                             description="键盘敲击详情",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="click_num_left",
     *                             description="鼠标左键点击次数",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="click_num_right",
     *                             description="鼠标右键点击次数",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="click_num",
     *                             description="该鼠标合计点击次数",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="move_distance",
     *                             description="移动距离(毫米)",
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
     *         response="222",
     *         description="用户标识不能为空",
     *     ),
     *     @SWG\Response(
     *         response="304",
     *         description="登录信息过期",
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
    public function actionGetUserEquipmentList() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_type = Frame::getStringFromRequest ( 'deviceType' );

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 查询符合条件的数据
        $data_list = TdEquipmentData::model ()->findAll ( " userid='{$member_id}' && device_type='{$device_type}'" );
        if ($data_list) {
            $equipment_list = array();
            $total_click_num = $click_num = 0;
            $click_num_left = $click_num_right = $total_move_distance = $move_distance = 0;
            foreach ( $data_list as $key => $equipment_info ) {
                if($device_type == 1){
                    // 计算鼠标左键点击次数
                    $click_num_left = $equipment_info['click_num_left'] + $equipment_info['old_click_num_left'];
                    // 计算鼠标右键点击次数
                    $click_num_right = $equipment_info['click_num_right'] + $equipment_info['old_click_num_right'];
                    // 计算所有鼠标合计点击次数
                    $click_num = $click_num_left + $click_num_right;
                    // 计算所有鼠标合计移动距离
                    $move_distance = $equipment_info['move_distance'] + $equipment_info['old_move_distance'];

                    $total_click_num += $click_num;
                    $total_move_distance += $move_distance;
                }else{
                    // 键盘敲击次数无法计算
                }

                $equipment_list [$key] = array (
                    "id" => $equipment_info['id'],                                          // 主键id
                    "device_type" => $equipment_info['device_type'],                        // 设备类型
                    "device_model" => $equipment_info['device_model'],                      // 设备型号
                    "device_name" => $equipment_info['device_name'],                        // 设备名称
                    "device_code" => $equipment_info['device_code'],                        // 设备号
                    "key_knock_detail" => $equipment_info['key_knock_detail'],              // 当前键盘敲击详情
                    "click_num_left" => $click_num_left,                                    // 当前鼠标左键点击次数
                    "click_num_right" => $click_num_right,                                  // 当前鼠标右键点击次数
                    'click_num' => $click_num,                                              // 当前鼠标合计点击次数
                    'move_distance' => $move_distance,                                      // 移动距离(毫米)
                );
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'total_click_num' => $total_click_num,                                      // 所有鼠标合计点击次数
                'total_move_distance' => $total_move_distance,                              // 所有鼠标合计移动距离
                'equipment_list' => $equipment_list,                                        // 装备列表
            );
        } else {
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/device/getUserEquipmentInfo",
     *     summary="84.获取用户装备详情",
     *     tags={"Device"},
     *     description="根据用户标识和设备号获取用户的装备数据",
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
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
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
     *                 property="id",
     *                 description="装备id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="userid",
     *                 description="用户id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="device_type",
     *                 description="设备类型",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="device_model",
     *                 description="设备型号",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_name",
     *                 description="设备名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_code",
     *                 description="设备号",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="key_knock_detail",
     *                 description="键盘敲击详情",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="click_num_left",
     *                 description="鼠标左键点击次数",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="click_num_right",
     *                 description="鼠标右键点击次数",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="click_num",
     *                 description="鼠标合计点击次数",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="move_distance",
     *                 description="移动距离(毫米)",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="update_time",
     *                 description="编辑时间",
     *                 type="integer",
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
     *         response="304",
     *         description="登录信息过期",
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
    public function actionGetUserEquipmentInfo() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        if (empty ( $device_code )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 查询符合条件的数据
        $equipment_info = TdEquipmentData::model ()->find ( "userid='{$member_id}' && device_code='{$device_code}'" );
        if ($equipment_info) {
            // 计算鼠标左键点击次数
            $click_num_left = $equipment_info['click_num_left'] + $equipment_info['old_click_num_left'];
            // 计算鼠标右键点击次数
            $click_num_right = $equipment_info['click_num_right'] + $equipment_info['old_click_num_right'];
            // 计算所有鼠标合计点击次数
            $click_num = $click_num_left + $click_num_right;
            // 计算所有鼠标合计移动距离
            $move_distance = $equipment_info['move_distance'] + $equipment_info['old_move_distance'];

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'id' => $equipment_info['id'],                                                      // 装备id
                'userid' => $member_id,                                                             // 用户id
                'device_type' => $equipment_info['device_type'],                                    // 设备类型
                'device_model' => $equipment_info['device_model'],                                  // 设备型号
                'device_name' => $equipment_info['device_name'],                                    // 设备名称
                'device_code' => $equipment_info['device_code'],                                    // 设备号
                'key_knock_detail' => $equipment_info['key_knock_detail'],                          // 键盘敲击详情
                "click_num_left" => $click_num_left,                                                // 鼠标左键点击次数
                "click_num_right" => $click_num_right,                                              // 鼠标右键点击次数
                'click_num' => $click_num,                                                          // 该鼠标合计点击次数
                'move_distance' => $move_distance,                                                  // 移动距离(毫米)
                'update_time' => $equipment_info['update_time'],
            );
        } else {
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode ( $result );
    }


    /**
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v3_1/device/removeUserEquipment",
     *     summary="85.删除用户装备数据",
     *     tags={"Device"},
     *     description="删除用户装备,返回结果",
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
     *         name="deviceCode",
     *         in="query",
     *         description="设备号",
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
     *         description="成功时返回结果",
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
     *         response="304",
     *         description="登录信息过期",
     *     ),
     *     @SWG\Response(
     *         response="306",
     *         description="用户不存在或已在其他设备上登录",
     *     ),
     *     @SWG\Response(
     *         response="309",
     *         description="没有该记录",
     *     ),
     * )
     */
    public function actionRemoveUserEquipment() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $device_code = Frame::getStringFromRequest ( 'deviceCode' );
        if (empty ( $device_code )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 查询该条数据是否存在 存在删除
        $del_device = TdEquipmentData::model ()->deleteAll ( "userid='{$member_id}' && device_code='{$device_code}'" );
        if ($del_device) {
            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
            );
        } else {
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode ( $result );
    }

}