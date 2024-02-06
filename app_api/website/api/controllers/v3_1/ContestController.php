<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/22
 * Time: 11:04
 */
class ContestController extends PublicController{

    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/contest/getGameList",
     *     summary="113.获取游戏列表",
     *     tags={"Contest"},
     *     description="获取默认游戏列表",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="game_list",
     *                 description="设备配置列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="game_id",
     *                             description="游戏id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_name",
     *                             description="游戏名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_type",
     *                             description="游戏类型值",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_type_text",
     *                             description="游戏类型名称",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 }
     *             ),
     * 		   ),
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
    public function actionGetGameList(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
//        $member_info = $this->check_user($lang);

        // 获取游戏列表
        $game_model = new TdGame();
        // 根据语言参数获取对应语种的游戏名称 2017/05/12
        if(!empty($lang) && $lang != "ZH"){
            $field = "id, game_name{$lang} AS game_name, game_type";
        }else{
            $field = "id, game_name, game_type";
        }
        $data_list = $game_model->getGameList("", $field, 1000, 0, 'id ASC');
        if(!empty($data_list)){
            $game_list = array();
            foreach($data_list as $k => $game_info){
                $game_list[$k]['game_id'] = $game_info['id'];                                       // 游戏id
                $game_list[$k]['game_name'] = $game_info['game_name'];                              // 游戏名称
                $game_list[$k]['game_type'] = $this->__getGameType($game_info['game_type']);        // 游戏类型值
                $game_list[$k]['game_type_text'] = $game_info['game_type'];                         // 游戏类型名称
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'game_list' => $game_list                                                           // 游戏列表
            );
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
     *     path="/app_api/website/api.php/v3_1/contest/deleteContestMode",
     *     summary="115.删除竞技模式",
     *     tags={"Contest"},
     *     description="删除指定用户的竞技模式和配置",
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
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
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
     *     @SWG\Response(
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionDeleteContestMode(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getIntFromRequest('cm_id');
        if(empty($cm_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获得此竞技模式的信息
        $cm_model = new TdContestMode();
        $cm_where = "cm_id = {$cm_id} AND member_id = {$member_id}";
        $contest_mode_info = $cm_model->getContestModeInfo($cm_where);
        if(!empty($contest_mode_info)){
            if($contest_mode_info['recommend_cm_id'] == 0){
                // 自定义竞技模式进行物理删除
                $cm_delete_result = $cm_model->deleteByPk($cm_id);
                if(!empty($cm_delete_result)){
                    // 物理删除该竞技模式下的配置数据
                    $cc_model = new TdContestConfig();
                    $cc_where = "cm_id = {$cm_id}";
                    $cc_delete_result = $cc_model->deleteAll($cc_where);

                    // 物理删除该竞技模式的自定义设置 2017/06/07
                    $cs_model = new TdContestSetting();
                    $cs_delete_result = $cs_model->deleteAll($cm_where);

                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                    );
                }else{
                    $result = array(
                        'ret_num' => 309,
                        'ret_msg' => $this->language->get('no_data'),
                    );
                }
            }else{
                // 推荐竞技模式进行逻辑删除
                $contest_mode_info->is_delete = 1;
                if($contest_mode_info->save()){
                    // 物理删除该竞技模式的自定义设置 2017/06/07
                    $cs_model = new TdContestSetting();
                    $cs_delete_result = $cs_model->deleteAll($cm_where);

                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                    );
                }else{
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
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
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/contest/getContestModeList",
     *     summary="116.获取用户竞技模式列表",
     *     tags={"Contest"},
     *     description="获取指定用户所有的竞技模式列表,如存在用户没有的推荐竞技模式则进行自动添加",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="contest_mode_list",
     *                 description="竞技模式列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="cm_id",
     *                             description="竞技模式id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="recommend_cm_id",
     *                             description="推荐竞技模式id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_name",
     *                             description="游戏名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_type",
     *                             description="游戏类型",
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
    public function actionGetContestModeList(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $cm_model = new TdContestMode();
        // 获取用户的推荐竞技模式列表
        $member_recommend_where = "member_id = {$member_id} AND recommend_cm_id <> 0";
        $member_recommend_list = $cm_model->getContestModeList($member_recommend_where, "*", 1000, 0, "cm_id ASC");
        // 获取所有推荐竞技模式列表
        $recommend_where = "member_id = 0 AND recommend_cm_id IS NULL AND cm_id <> 0";
        $recommend_list = $cm_model->getContestModeList($recommend_where, "*", 1000, 0, "cm_id ASC");

        // 遍历用户列表,查找是否已添加过该推荐模式
        $add_array = array();
        foreach($recommend_list as $k => $recommend_info){
            $is_exist = false;
            foreach($member_recommend_list as $k => $member_recommend_info){
                if($member_recommend_info['recommend_cm_id'] == $recommend_info['cm_id']){
                    $is_exist = true;
                    break;
                }
            }

            if(!$is_exist){
                $add_array[] = $recommend_info;
            }
        }

        // 添加用户没有的推荐竞技模式及配置信息
        if(!empty($add_array)){
            //开启事物
            $transaction = Yii::app()->db->beginTransaction(); //创建事务
            $flag = true;

            foreach($add_array as $k => $v) {
                $add_cm_model = new TdContestMode();
                $add_cm_model->member_id = $member_id;
                $add_cm_model->recommend_cm_id = $v['cm_id'];
                $add_cm_model->game_name = $v['game_name'];
                $add_cm_model->game_nameEN = $v['game_nameEN'];
                $add_cm_model->game_type = $v['game_type'];
                $add_cm_model->created_time = time();
                if(!$add_cm_model->save()){
                    $flag = false;
                    break;
                }

                // 获取该竞技模式下的推荐配置信息
                $cc_model = new TdContestConfig();
                $cc_list = $cc_model->getContestConfigList("cm_id = {$v['cm_id']}");
                foreach($cc_list as $k => $cc_info){
                    $add_cc_model = new TdContestConfig();
                    $add_cc_model->cm_id = $add_cm_model['cm_id'];
                    $add_cc_model->device_model = $cc_info['device_model'];
                    $add_cc_model->device_type = $cc_info['device_type'];
                    $add_cc_model->config_content = $cc_info['config_content'];
                    $add_cc_model->created_time = time();

                    if(!$add_cc_model->save()){
                        $flag = false;
                        break;
                    }
                }
            }

            if($flag){
                $transaction->commit();
            }else{
                $transaction->rollback();
            }
        }

        // 获取该用户所有的竞技模式列表
        $where = "member_id = {$member_id} AND is_delete = 0";
        // 根据语言参数获取对应语种的游戏名称 2017/05/12
        if(!empty($lang) && $lang != "ZH"){
            $field = "cm_id, recommend_cm_id, game_name{$lang} AS game_name, game_type";
        }else{
            $field = "cm_id, recommend_cm_id, game_name, game_type";
        }
        $data_list = $cm_model->getContestModeList($where, $field, 1000, 0, "recommend_cm_id ASC, created_time DESC");
        if(!empty($data_list)){
            $contest_mode_list = array();
            foreach($data_list as $k => $contest_mode){
                $contest_mode_list[$k]['cm_id'] = $contest_mode['cm_id'];                       // 竞技模式id
                $contest_mode_list[$k]['recommend_cm_id'] = $contest_mode['recommend_cm_id'];   // 推荐竞技模式id
                $contest_mode_list[$k]['game_name'] = $contest_mode['game_name'];               // 游戏名称
                $contest_mode_list[$k]['game_type'] = $contest_mode['game_type'];               // 游戏类型
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'contest_mode_list' => $contest_mode_list                                       // 竞技模式列表
            );
        }else{
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/contest/getContestInfo",
     *     summary="117.获取用户竞技模式的配置明细",
     *     tags={"Contest"},
     *     description="获取指定用户竞技模式下的配置明细",
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
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_device_model",
     *         in="query",
     *         description="鼠标设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_device_model",
     *         in="query",
     *         description="键盘设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_device_model",
     *         in="query",
     *         description="主机设备型号",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="cm_id",
     *                 description="竞技模式id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="game_name",
     *                 description="游戏名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="ms_config_info",
     *                 description="鼠标配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="ms_switch",
     *                             description="鼠标设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="report_rate",
     *                             description="回报率",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="dpi",
     *                             description="DPI",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="dpi_value",
     *                             description="DPI实值",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_light",
     *                             description="灯带灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_id",
     *                             description="灯带自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_name",
     *                             description="灯带自定义方案名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_content",
     *                             description="灯带灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key",
     *                             description="改键灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_id",
     *                             description="改键自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_name",
     *                             description="改键自定义方案名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_content",
     *                             description="改键灯光内容",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="kb_config_info",
     *                 description="键盘配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="kb_switch",
     *                             description="键盘设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="polling_rate",
     *                             description="轮询率",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="repeat_speed",
     *                             description="重复速度",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="conflict_mode",
     *                             description="无冲模式",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="ban_keys",
     *                             description="禁用按键",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light",
     *                             description="键帽灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light_config_id",
     *                             description="键帽灯光自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light_config_name",
     *                             description="键帽灯光自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_content",
     *                             description="键帽灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_light",
     *                             description="灯带灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_id",
     *                             description="灯带自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_name",
     *                             description="灯带自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_content",
     *                             description="灯带灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key",
     *                             description="改键灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_id",
     *                             description="改键自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_name",
     *                             description="改键自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_content",
     *                             description="改键灯光内容",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="pc_config_info",
     *                 description="主机配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="pc_switch",
     *                             description="主机设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_effect",
     *                             description="主机灯光效果",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="assist_url",
     *                 description="网址",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="url1",
     *                             description="网址1",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="url2",
     *                             description="网址2",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="url3",
     *                             description="网址3",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="assist_tool",
     *                 description="设备配置列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="tool1",
     *                             description="程序1",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
     *                         ),
     *                         @SWG\Property(
     *                             property="tool2",
     *                             description="程序2",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
     *                         ),
     *                         @SWG\Property(
     *                             property="tool3",
     *                             description="程序3",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
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
    public function actionGetContestInfo(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getIntFromRequest("cm_id");
        $ms_device_model = Frame::getStringFromRequest("ms_device_model");
        $kb_device_model = Frame::getStringFromRequest("kb_device_model");
        $pc_device_model = Frame::getStringFromRequest("pc_device_model");
        if(empty($cm_id) || (empty($ms_device_model) && empty($kb_device_model) && empty($pc_device_model)) ){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获取竞技模式信息
        $cm_model = new TdContestMode();
        $cm_where = "cm_id = {$cm_id} AND member_id = {$member_id}";
        $contest_mode_info = $cm_model->getContestModeInfo($cm_where);
        if(!empty($contest_mode_info)){
            $ms_config = $kb_config = $pc_config = array();
            // 获取该竞技模式的配置信息
            $ms_where = "device_type = 1";
            // 鼠标暂时全使用同一配置
//            $ms_where = "device_model = '{$ms_device_model}' AND device_type = 1";
            $kb_where = "device_model = '{$kb_device_model}' AND device_type = 2";
            $pc_where = "device_model = '{$pc_device_model}' AND device_type = 3";

            // 获取用户自定义的鼠标配置
            $ms_info = $this->__getContestConfig($ms_where, $contest_mode_info);
            // 解析配置信息用于返回
            if(!empty($ms_info['config_content'])){
                $ms_config = unserialize($ms_info['config_content']);
            }

            // 获取用户自定义的键盘配置
            $kb_info = $this->__getContestConfig($kb_where, $contest_mode_info);
            // 解析配置信息用于返回
            if(!empty($kb_info['config_content'])){
                $kb_config = unserialize($kb_info['config_content']);
            }

            // 获取用户自定义的主机配置 2017/06/06
            $pc_info = $this->__getContestConfig($pc_where, $contest_mode_info);
            // 解析配置信息用于返回
            if(!empty($pc_info['config_content'])){
                $pc_config = unserialize($pc_info['config_content']);
            }

            if(empty($ms_config) && empty($kb_config) && empty($pc_config)){
                // 三边均没有默认配置的情况
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => $this->language->get('no_data'),
                );
                echo json_encode($result);
                die();
            }

            $ms_config_info = array(
                "ms_switch" => (!empty($ms_config["switch"])) ? $ms_config["switch"] : true,                                               // 鼠标设备开关
                "report_rate" => (!empty($ms_config["report_rate"])) ? $ms_config["report_rate"] : 0,                                       // 回报率
                "dpi" => (!empty($ms_config["dpi"])) ? $ms_config["dpi"] : 0,                                                               // DPI
                "dpi_value" => (!empty($ms_config["dpi_value"])) ? $ms_config["dpi_value"] : 0,                                             // DPI实值
                "lamp_bar_light" => (!empty($ms_config["lamp_bar_light"])) ? $ms_config["lamp_bar_light"] : 0,                              // 灯带灯光
                "lamp_bar_config_id" => (!empty($ms_config["lamp_bar_config_id"])) ? $ms_config["lamp_bar_config_id"] : 0,                  // 灯带自定义方案id
                "lamp_bar_config_name" => (!empty($ms_config["lamp_bar_config_name"])) ? $ms_config["lamp_bar_config_name"] : "",           // 灯带自定义方案名称
                "lamp_bar_content" => (!empty($ms_config["lamp_bar_content"])) ? $ms_config["lamp_bar_content"] : "",                       // 灯带灯光内容
                "change_key" => (!empty($ms_config["change_key"])) ? $ms_config["change_key"] : 0,                                          // 改键灯光
                "change_key_config_id" => (!empty($ms_config["change_key_config_id"])) ? $ms_config["change_key_config_id"] : 0,            // 改键自定义方案id
                "change_key_config_name" => (!empty($ms_config["change_key_config_name"])) ? $ms_config["change_key_config_name"] : "",     // 灯带自定义方案名称
                "change_key_content" => (!empty($ms_config["change_key_content"])) ? $ms_config["change_key_content"] : "",                 // 灯带灯光内容
            );

            $kb_config_info = array(
                "kb_switch" => (!empty($kb_config["switch"])) ? $kb_config["switch"] : true,                                                // 键盘设备开关
                "polling_rate" => (!empty($kb_config["polling_rate"])) ? $kb_config["polling_rate"] : 0,                                     // 轮询率
                "repeat_speed" => (!empty($kb_config["repeat_speed"])) ? $kb_config["repeat_speed"] : 0,                                     // 重复速度
                "conflict_mode" => (!empty($kb_config["conflict_mode"])) ? $kb_config["conflict_mode"] : 0,                                  // 无冲模式
                "ban_keys" => (!empty($kb_config["ban_keys"])) ? $kb_config["ban_keys"] : "",                                                // 禁用按键
                "keycap_light" => (!empty($kb_config["keycap_light"])) ? $kb_config["keycap_light"] : 0,                                     // 灯带灯光
                "keycap_light_config_id" => (!empty($kb_config["keycap_light_config_id"])) ? $kb_config["keycap_light_config_id"] : 0,       // 灯带自定义方案id
                "keycap_light_config_name" => (!empty($kb_config["keycap_light_config_name"])) ? $kb_config["keycap_light_config_name"] : "",// 灯带自定义方案名称
                "keycap_content" => (!empty($kb_config["keycap_content"])) ? $kb_config["keycap_content"] : "",                              // 灯带灯光内容
                "lamp_bar_light" => (!empty($kb_config["lamp_bar_light"])) ? $kb_config["lamp_bar_light"] : 0,                               // 灯带灯光
                "lamp_bar_config_id" => (!empty($kb_config["lamp_bar_config_id"])) ? $kb_config["lamp_bar_config_id"] : 0,                   // 灯带自定义方案id
                "lamp_bar_config_name" => (!empty($kb_config["lamp_bar_config_name"])) ? $kb_config["lamp_bar_config_name"] : "",            // 灯带自定义方案名称
                "lamp_bar_content" => (!empty($kb_config["lamp_bar_content"])) ? $kb_config["lamp_bar_content"] : "",                        // 灯带灯光内容
                "change_key" => (!empty($kb_config["change_key"])) ? $kb_config["change_key"] : 0,                                           // 改键灯光
                "change_key_config_id" => (!empty($kb_config["change_key_config_id"])) ? $kb_config["change_key_config_id"] : 0,             // 改键自定义方案id
                "change_key_config_name" => (!empty($kb_config["change_key_config_name"])) ? $kb_config["change_key_config_name"] : "",      // 灯带自定义方案名称
                "change_key_content" => (!empty($kb_config["change_key_content"])) ? $kb_config["change_key_content"] : "",                  // 灯带灯光内容
            );

            $pc_config_info = array(
                "pc_switch" => (!empty($pc_config["switch"])) ? $pc_config["switch"] : true,                                                // 主机设备开关
                "lamp_effect" => (!empty($pc_config["lamp_effect"])) ? $pc_config["lamp_effect"] : 0,                                        // 主机灯光效果
            );

            // 初始化自定义设置数组
            $assist_url = array(
                'url1' => "",
                'url2' => "",
                "url3" => "",
            );
            $assist_tool = array(
                'tool1' => array(
                    'select' => "",
                    'path' => "",
                ),
                'tool2' => array(
                    'select' => "",
                    'path' => "",
                ),
                'tool3' => array(
                    'select' => "",
                    'path' => "",
                )
            );
            // 获取该竞技模式的自定义设置 2017/06/07
            $cs_model = new TdContestSetting();
            $contest_setting_info = $cs_model->getContestSettingInfo($cm_where);
            if(!empty($contest_setting_info)){
                // 解析链接和工具数据
                $url_array = explode(',', $contest_setting_info['assist_url']);
                $assist_url['url1'] = (!empty($url_array[0])) ? $url_array[0] : "";
                $assist_url['url2'] = (!empty($url_array[1])) ? $url_array[1] : "";
                $assist_url['url3'] = (!empty($url_array[2])) ? $url_array[2] : "";

                $tool_array = explode(',', $contest_setting_info['assist_tool']);
                if(!empty($tool_array[0])){
                    $tool1_array = explode('|', $tool_array[0]);
                    $assist_tool['tool1']['select'] = (!empty($tool1_array[0])) ? $tool1_array[0] : "";
                    $assist_tool['tool1']['path'] = (!empty($tool1_array[1])) ? $tool1_array[1] : "";
                }
                if(!empty($tool_array[1])){
                    $tool2_array = explode('|', $tool_array[1]);
                    $assist_tool['tool2']['select'] = (!empty($tool2_array[0])) ? $tool2_array[0] : "";
                    $assist_tool['tool2']['path'] = (!empty($tool2_array[1])) ? $tool2_array[1] : "";
                }
                if(!empty($tool_array[2])){
                    $tool3_array = explode('|', $tool_array[2]);
                    $assist_tool['tool3']['select'] = (!empty($tool3_array[0])) ? $tool3_array[0] : "";
                    $assist_tool['tool3']['path'] = (!empty($tool3_array[1])) ? $tool3_array[1] : "";
                }
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'cm_id' => $contest_mode_info['cm_id'],
                "game_name" => (!empty($lang) && $lang != "ZH") ? $contest_mode_info['game_name'.$lang] : $contest_mode_info['game_name'],    // 竞技模式名称
                'ms_config_info' => $ms_config_info,                                                                                           // 鼠标配置
                'kb_config_info' => $kb_config_info,                                                                                           // 键盘配置
                'pc_config_info' => $pc_config_info,                                                                                           // 主机配置
                'assist_url' => $assist_url,                                                                                                   // 辅助网址
                'assist_tool' => $assist_tool,                                                                                                 // 辅助程序
            );
        }else{
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/contest/saveContestInfo",
     *     summary="118.保存竞技模式",
     *     tags={"Contest"},
     *     description="保存用户的竞技模式信息",
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
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="game_name",
     *         in="query",
     *         description="游戏名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_device_model",
     *         in="query",
     *         description="鼠标设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_switch",
     *         in="query",
     *         description="鼠标设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="report_rate",
     *         in="query",
     *         description="回报率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="dpi",
     *         in="query",
     *         description="DPI",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="ms_lamp_bar_light",
     *         in="query",
     *         description="鼠标灯带灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_lamp_bar_config_id",
     *         in="query",
     *         description="鼠标灯带自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_lamp_bar_config_name",
     *         in="query",
     *         description="鼠标灯带自定义方案名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_change_key",
     *         in="query",
     *         description="鼠标改键配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_change_key_config_id",
     *         in="query",
     *         description="鼠标改键自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_change_key_config_name",
     *         in="query",
     *         description="鼠标改键自定义方案名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_device_model",
     *         in="query",
     *         description="键盘设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_switch",
     *         in="query",
     *         description="键盘设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="polling_rate",
     *         in="query",
     *         description="轮询率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="repeat_speed",
     *         in="query",
     *         description="重复速度",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="conflict_mode",
     *         in="query",
     *         description="无冲模式",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ban_keys",
     *         in="query",
     *         description="禁用按键",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light",
     *         in="query",
     *         description="键盘键帽灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light_config_id",
     *         in="query",
     *         description="键帽灯光自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light_config_name",
     *         in="query",
     *         description="键帽灯光自定义方案名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_lamp_bar_light",
     *         in="query",
     *         description="键盘灯带灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_lamp_bar_config_id",
     *         in="query",
     *         description="键盘灯带自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_lamp_bar_config_name",
     *         in="query",
     *         description="键盘灯带自定义方案名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_change_key",
     *         in="query",
     *         description="键盘改键配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_change_key_config_id",
     *         in="query",
     *         description="键盘改键自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_change_key_config_name",
     *         in="query",
     *         description="键盘改键自定义方案名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_device_model",
     *         in="query",
     *         description="主机设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_switch",
     *         in="query",
     *         description="主机设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="pc_lamp_effect",
     *         in="query",
     *         description="主机灯光效果",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="cm_id",
     *                 description="竞技模式id",
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
    public function actionSaveContestInfo(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getIntFromRequest("cm_id");
        $game_name = Frame::getStringFromRequest('game_name');
        // 鼠标配置
        $ms_device_model = Frame::getStringFromRequest("ms_device_model");
        $ms_switch = Frame::getStringFromRequest("ms_switch");
        $report_rate = Frame::getIntFromRequest("report_rate");
        $dpi = Frame::getIntFromRequest("dpi");
        $ms_lamp_bar_light = Frame::getIntFromRequest("ms_lamp_bar_light");
        $ms_lamp_bar_config_id = Frame::getIntFromRequest("ms_lamp_bar_config_id");
        $ms_lamp_bar_config_name = Frame::getStringFromRequest("ms_lamp_bar_config_name");
        $ms_change_key = Frame::getIntFromRequest("ms_change_key");
        $ms_change_key_config_id = Frame::getIntFromRequest("ms_change_key_config_id");
        $ms_change_key_config_name = Frame::getStringFromRequest("ms_change_key_config_name");
        // 键盘配置
        $kb_device_model = Frame::getStringFromRequest("kb_device_model");
        $kb_switch = Frame::getStringFromRequest("kb_switch");
        $polling_rate = Frame::getIntFromRequest("polling_rate");
        $repeat_speed = Frame::getIntFromRequest("repeat_speed");
        $conflict_mode = Frame::getIntFromRequest("conflict_mode");
        $ban_keys = Frame::getStringFromRequest("ban_keys");
        $keycap_light = Frame::getIntFromRequest("keycap_light");
        $keycap_light_config_id = Frame::getIntFromRequest("keycap_light_config_id");
        $keycap_light_config_name = Frame::getStringFromRequest("keycap_light_config_name");
        $kb_lamp_bar_light = Frame::getIntFromRequest("kb_lamp_bar_light");
        $kb_lamp_bar_config_id = Frame::getIntFromRequest("kb_lamp_bar_config_id");
        $kb_lamp_bar_config_name = Frame::getStringFromRequest("kb_lamp_bar_config_name");
        $kb_change_key = Frame::getIntFromRequest("kb_change_key");
        $kb_change_key_config_id = Frame::getIntFromRequest("kb_change_key_config_id");
        $kb_change_key_config_name = Frame::getStringFromRequest("kb_change_key_config_name");
        // 主机配置
        $pc_device_model = Frame::getStringFromRequest("pc_device_model");
        $pc_switch = Frame::getStringFromRequest("pc_switch");
        $pc_lamp_effect = Frame::getIntFromRequest("pc_lamp_effect");
        if(empty($ms_device_model) && empty($kb_device_model) && empty($pc_device_model)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }
        // 设备型号不为空时自定义方案不能为空
        if(
            (
                !empty($ms_device_model) &&
                ($ms_lamp_bar_light == 1 && empty($ms_lamp_bar_config_name)) ||
                ($ms_change_key == 1 && empty($ms_change_key_config_name))
            ) || (
                !empty($kb_device_model) &&
                ($keycap_light == 1 && empty($keycap_light_config_name)) ||
                ($kb_lamp_bar_light == 1 && empty($kb_lamp_bar_config_name)) ||
                ($kb_change_key == 1 && empty($kb_change_key_config_name))
            )
        ){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $ms_where = "device_type = 1";
        // 鼠标暂时全使用同一配置
//        $ms_where = "device_model = '{$ms_device_model}' AND device_type = 1";
        $kb_where = "device_model = '{$kb_device_model}' AND device_type = 2";
        $pc_where = "device_model = '{$pc_device_model}' AND device_type = 3";

        if(!empty($cm_id)){
            // 存在竞技模式id时修改
            $edit_cm_model = new TdContestMode();
            $cm_where = "cm_id = {$cm_id}";
            $cm_info = $edit_cm_model->getContestModeInfo($cm_where);
            if(!empty($cm_info)){
                $customer_where = " AND cm_id = {$cm_info['cm_id']}";

                //开启事物
                $transaction = Yii::app()->db->beginTransaction(); //创建事务
                $flag = true;

                // 修改竞技模式信息
                $cm_info->game_name = $game_name;
                $cm_info->game_nameEN = $game_name;
                if(!$cm_info->save()){
                    $flag = false;
                }

                // 存在鼠标设备型号时进行鼠标配置的修改
                if(!empty($ms_device_model)){
                    // 获取该模式下的鼠标配置信息
                    $cc_model = new TdContestConfig();
                    $ms_config_info = $cc_model->getContestConfigInfo($ms_where . $customer_where);

                    if(!empty($ms_config_info)){
                        // 修改配置信息
                        $ms_config = unserialize($ms_config_info['config_content']);

                        // 序列化鼠标配置信息
                        $ms_config_array = array(
                            "switch" => (!empty($ms_switch)) ? $ms_switch : true,                                                                                   // 设备开关
                            "report_rate" => (isset($report_rate)) ? $report_rate : $ms_config['report_rate'],                                                      // 回报率
                            "dpi" => (isset($dpi)) ? $dpi : $ms_config['dpi'],                                                                                      // DPI
                            "dpi_value" => $ms_config['dpi_value'],                                                                                                  // DPI实值
                            "lamp_bar_light" => (isset($ms_lamp_bar_light)) ? $ms_lamp_bar_light : $ms_config['lamp_bar_light'],                                    // 灯带灯光
                            "lamp_bar_config_id" => (!empty($ms_lamp_bar_config_id)) ? $ms_lamp_bar_config_id : $ms_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                            "lamp_bar_config_name" => (!empty($ms_lamp_bar_config_name)) ? $ms_lamp_bar_config_name : $ms_config['lamp_bar_config_name'],           // 灯带自定义方案名称
                            "lamp_bar_content" => $ms_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                            "change_key" => (isset($ms_change_key)) ? $ms_change_key : $ms_config['change_key'],                                                    // 改键
                            "change_key_config_id" => (!empty($ms_change_key_config_id)) ? $ms_change_key_config_id : $ms_config['change_key_config_id'],           // 改键自定义方案id
                            "change_key_config_name" => (!empty($ms_change_key_config_name)) ? $ms_change_key_config_name : $ms_config['change_key_config_name'],   // 改键自定义方案id
                            "change_key_content" => $ms_config['change_key_content'],                                                                                // 改键内容
                        );
                        $ms_config_content = serialize($ms_config_array);

                        // 修改竞技模式鼠标配置
                        $ms_config_info->config_content = $ms_config_content;
                        if(!$ms_config_info->save()){
                            $flag = false;
                        }
                    }else{
                        // 获取默认配置信息
                        $ms_config_info = $this->__getContestConfig($ms_where, $cm_info);
                        $ms_config = unserialize($ms_config_info['config_content']);

                        // 序列化鼠标配置信息
                        $ms_config_array = array(
                            "switch" => (!empty($ms_switch)) ? $ms_switch : true,                                                                                   // 设备开关
                            "report_rate" => (isset($report_rate)) ? $report_rate : $ms_config['report_rate'],                                                      // 回报率
                            "dpi" => (isset($dpi)) ? $dpi : $ms_config['dpi'],                                                                                      // DPI
                            "dpi_value" => $ms_config['dpi_value'],                                                                                                  // DPI实值
                            "lamp_bar_light" => (isset($ms_lamp_bar_light)) ? $ms_lamp_bar_light : $ms_config['lamp_bar_light'],                                    // 灯带灯光
                            "lamp_bar_config_id" => (!empty($ms_lamp_bar_config_id)) ? $ms_lamp_bar_config_id : $ms_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                            "lamp_bar_config_name" => (!empty($ms_lamp_bar_config_name)) ? $ms_lamp_bar_config_name : $ms_config['lamp_bar_config_name'],           // 灯带自定义方案名称
                            "lamp_bar_content" => $ms_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                            "change_key" => (isset($ms_change_key)) ? $ms_change_key : $ms_config['change_key'],                                                    // 改键
                            "change_key_config_id" => (!empty($ms_change_key_config_id)) ? $ms_change_key_config_id : $ms_config['change_key_config_id'],           // 改键自定义方案id
                            "change_key_config_name" => (!empty($ms_change_key_config_name)) ? $ms_change_key_config_name : $ms_config['change_key_config_name'],   // 改键自定义方案id
                            "change_key_content" => $ms_config['change_key_content'],                                                                                // 改键内容
                        );
                        $ms_config_content = serialize($ms_config_array);

                        // 新建配置信息
                        $add_ms_cc_model = new TdContestConfig();
                        $add_ms_cc_model->cm_id = $cm_info['cm_id'];
//                $add_ms_cc_model->device_model = $ms_device_model;
                        $add_ms_cc_model->device_type = 1;
                        $add_ms_cc_model->config_content = $ms_config_content;
                        $add_ms_cc_model->created_time = time();
                        if(!$add_ms_cc_model->save()){
                            $flag = false;
                        }
                    }
                }

                // 存在键盘设备型号时进行键盘配置的修改
                if(!empty($kb_device_model)){
                    // 获取该模式下的键盘配置信息
                    $cc_model = new TdContestConfig();
                    $kb_config_info = $cc_model->getContestConfigInfo($kb_where . $customer_where);

                    if(!empty($kb_config_info)){
                        // 修改配置信息
                        $kb_config = unserialize($kb_config_info['config_content']);

                        // 序列化键盘配置信息
                        $kb_config_array = array(
                            "switch" => (!empty($kb_switch)) ? $kb_switch : true,                                                                                   // 设备开关
                            "polling_rate" => (isset($polling_rate)) ? $polling_rate : $kb_config['polling_rate'],                                                  // 轮询率
                            "repeat_speed" => (isset($repeat_speed)) ? $repeat_speed : $kb_config['repeat_speed'],                                                  // 重复速度
                            "conflict_mode" => (!empty($conflict_mode)) ? $conflict_mode : $kb_config['conflict_mode'],                                             // 无冲模式
                            "ban_keys" => (isset($ban_keys)) ? $ban_keys : $kb_config['ban_keys'],                                                                  // 禁用按键
                            "keycap_light" => (isset($keycap_light)) ? $keycap_light : $kb_config['keycap_light'],                                                  // 键帽灯光
                            "keycap_light_config_id" => (!empty($keycap_light_config_id)) ? $keycap_light_config_id : $kb_config['keycap_light_config_id'],         // 键帽灯光自定义方案id
                            "keycap_light_config_name" => (!empty($keycap_light_config_name)) ? $keycap_light_config_name : $kb_config['keycap_light_config_name'], // 键帽灯光自定义方案id
                            "keycap_content" => $kb_config['keycap_content'],                                                                                        // 键帽灯光内容
                            "lamp_bar_light" => (isset($kb_lamp_bar_light)) ? $kb_lamp_bar_light : $kb_config['lamp_bar_light'],                                    // 灯带灯光
                            "lamp_bar_config_id" => (!empty($kb_lamp_bar_config_id)) ? $kb_lamp_bar_config_id : $kb_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                            "lamp_bar_config_name" => (!empty($kb_lamp_bar_config_name)) ? $kb_lamp_bar_config_name : $kb_config['lamp_bar_config_name'],           // 灯带自定义方案id
                            "lamp_bar_content" => $kb_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                            "change_key" => (isset($kb_change_key)) ? $kb_change_key : $kb_config['change_key'],                                                    // 改键
                            "change_key_config_id" => (!empty($kb_change_key_config_id)) ? $kb_change_key_config_id : $kb_config['change_key_config_id'],           // 改键自定义方案id
                            "change_key_config_name" => (!empty($kb_change_key_config_name)) ? $kb_change_key_config_name : $kb_config['change_key_config_name'],   // 改键自定义方案id
                            "change_key_content" => $kb_config['change_key_content'],                                                                                // 改键内容
                        );
                        $kb_config_content = serialize($kb_config_array);

                        // 修改竞技模式键盘配置
                        $kb_config_info->config_content = $kb_config_content;
                        if(!$kb_config_info->save()){
                            $flag = false;
                        }
                    }else{
                        // 获取默认配置信息
                        $kb_config_info = $this->__getContestConfig($kb_where, $cm_info);
                        $kb_config = unserialize($kb_config_info['config_content']);

                        // 序列化键盘配置信息
                        $kb_config_array = array(
                            "switch" => (!empty($kb_switch)) ? $kb_switch : true,                                                                                   // 设备开关
                            "polling_rate" => (isset($polling_rate)) ? $polling_rate : $kb_config['polling_rate'],                                                  // 轮询率
                            "repeat_speed" => (isset($repeat_speed)) ? $repeat_speed : $kb_config['repeat_speed'],                                                  // 重复速度
                            "conflict_mode" => (!empty($conflict_mode)) ? $conflict_mode : $kb_config['conflict_mode'],                                             // 无冲模式
                            "ban_keys" => (isset($ban_keys)) ? $ban_keys : $kb_config['ban_keys'],                                                                  // 禁用按键
                            "keycap_light" => (isset($keycap_light)) ? $keycap_light : $kb_config['keycap_light'],                                                  // 键帽灯光
                            "keycap_light_config_id" => (!empty($keycap_light_config_id)) ? $keycap_light_config_id : $kb_config['keycap_light_config_id'],         // 键帽灯光自定义方案id
                            "keycap_light_config_name" => (!empty($keycap_light_config_name)) ? $keycap_light_config_name : $kb_config['keycap_light_config_name'], // 键帽灯光自定义方案id
                            "keycap_content" => $kb_config['keycap_content'],                                                                                        // 键帽灯光内容
                            "lamp_bar_light" => (isset($kb_lamp_bar_light)) ? $kb_lamp_bar_light : $kb_config['lamp_bar_light'],                                    // 灯带灯光
                            "lamp_bar_config_id" => (!empty($kb_lamp_bar_config_id)) ? $kb_lamp_bar_config_id : $kb_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                            "lamp_bar_config_name" => (!empty($kb_lamp_bar_config_name)) ? $kb_lamp_bar_config_name : $kb_config['lamp_bar_config_name'],           // 灯带自定义方案id
                            "lamp_bar_content" => $kb_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                            "change_key" => (isset($kb_change_key)) ? $kb_change_key : $kb_config['change_key'],                                                    // 改键
                            "change_key_config_id" => (!empty($kb_change_key_config_id)) ? $kb_change_key_config_id : $kb_config['change_key_config_id'],           // 改键自定义方案id
                            "change_key_config_name" => (!empty($kb_change_key_config_name)) ? $kb_change_key_config_name : $kb_config['change_key_config_name'],   // 改键自定义方案id
                            "change_key_content" => $kb_config['change_key_content'],                                                                                // 改键内容
                        );
                        $kb_config_content = serialize($kb_config_array);

                        // 新建配置信息
                        $add_kb_cc_model = new TdContestConfig();
                        $add_kb_cc_model->cm_id = $cm_info['cm_id'];
                        $add_kb_cc_model->device_model = $kb_device_model;
                        $add_kb_cc_model->device_type = 2;
                        $add_kb_cc_model->config_content = $kb_config_content;
                        $add_kb_cc_model->created_time = time();
                        if(!$add_kb_cc_model->save()){
                            $flag = false;
                        }
                    }
                }

                // 存在主机设备型号时进行主机配置的修改 2017/06/06
                if(!empty($pc_device_model)){
                    // 获取该模式下的主机配置信息
                    $cc_model = new TdContestConfig();
                    $pc_config_info = $cc_model->getContestConfigInfo($pc_where . $customer_where);

                    if(!empty($pc_config_info)){
                        // 修改配置信息
                        $pc_config = unserialize($pc_config_info['config_content']);

                        // 序列化主机配置信息
                        $pc_config_array = array(
                            "switch" => (!empty($pc_switch)) ? $pc_switch : true,                                                                                   // 设备开关
                            "lamp_effect" => (isset($pc_lamp_effect)) ? $pc_lamp_effect : $pc_config['lamp_effect'],                                                // 主机灯光效果
                        );
                        $pc_config_content = serialize($pc_config_array);

                        // 修改竞技模式主机配置
                        $pc_config_info->config_content = $pc_config_content;
                        if(!$pc_config_info->save()){
                            $flag = false;
                        }
                    }else{
                        // 获取默认配置信息
                        $pc_config_info = $this->__getContestConfig($pc_where, $cm_info);
                        $pc_config = unserialize($pc_config_info['config_content']);

                        // 序列化主机配置信息
                        $pc_config_array = array(
                            "switch" => (!empty($pc_switch)) ? $pc_switch : true,                                                                                   // 设备开关
                            "lamp_effect" => (isset($pc_lamp_effect)) ? $pc_lamp_effect : $pc_config['lamp_effect'],                                                // 主机灯光效果
                        );
                        $pc_config_content = serialize($pc_config_array);

                        // 新建配置信息
                        $add_pc_cc_model = new TdContestConfig();
                        $add_pc_cc_model->cm_id = $cm_info['cm_id'];
                        $add_pc_cc_model->device_model = $pc_device_model;
                        $add_pc_cc_model->device_type = 3;
                        $add_pc_cc_model->config_content = $pc_config_content;
                        $add_pc_cc_model->created_time = time();
                        if(!$add_pc_cc_model->save()){
                            $flag = false;
                        }
                    }
                }

                if($flag){
                    $transaction->commit();
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'cm_id' => $cm_info['cm_id'],
                    );
                }else{
                    $transaction->rollback();
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
            }else{
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => $this->language->get('no_data'),
                );
            }
        }else{
            // 不存在竞技模式id时新增
            $recommend_cm_model = new TdContestMode();
            $recommend_cm_where = "member_id = 0 AND game_type = 4";
            // 获取其他类型的推荐配置
            $recommend_mode_info = $recommend_cm_model->getContestModeInfo($recommend_cm_where, "*", "cm_id ASC");
            if(!empty($recommend_mode_info)){
                $default_where = " AND cm_id = {$recommend_mode_info['cm_id']}";

                //开启事物
                $transaction = Yii::app()->db->beginTransaction(); //创建事务
                $flag = true;

                // 新增竞技模式信息
                $add_cm_model = new TdContestMode();
                $add_cm_model->member_id = $member_id;
                $add_cm_model->recommend_cm_id = 0;
                $add_cm_model->game_name = $game_name;
                $add_cm_model->game_nameEN = $game_name;
                $add_cm_model->game_type = 4;
                $add_cm_model->created_time = time();
                if(!$add_cm_model->save()){
                    $flag = false;
                }

                // 存在键盘设备型号时新增鼠标模式键盘配置
                if(!empty($ms_device_model)){
                    // 获取默认的鼠标配置
                    $ms_cc_model = new TdContestConfig();
                    $ms_config_info = $ms_cc_model->getContestConfigInfo($ms_where . $default_where);
                    $ms_config = unserialize($ms_config_info['config_content']);

                    // 序列化鼠标配置信息
                    $ms_config_array = array(
                        "switch" => (!empty($ms_switch)) ? $ms_switch : true,                                                                                   // 设备开关
                        "report_rate" => (isset($report_rate)) ? $report_rate : $ms_config['report_rate'],                                                      // 回报率
                        "dpi" => (isset($dpi)) ? $dpi : $ms_config['dpi'],                                                                                      // DPI
                        "dpi_value" => $ms_config['dpi_value'],                                                                                                  // DPI实值
                        "lamp_bar_light" => (isset($ms_lamp_bar_light)) ? $ms_lamp_bar_light : $ms_config['lamp_bar_light'],                                    // 灯带灯光
                        "lamp_bar_config_id" => (!empty($ms_lamp_bar_config_id)) ? $ms_lamp_bar_config_id : $ms_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                        "lamp_bar_config_name" => (!empty($ms_lamp_bar_config_name)) ? $ms_lamp_bar_config_name : $ms_config['lamp_bar_config_name'],           // 灯带自定义方案名称
                        "lamp_bar_content" => $ms_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                        "change_key" => (isset($ms_change_key)) ? $ms_change_key : $ms_config['change_key'],                                                    // 改键
                        "change_key_config_id" => (!empty($ms_change_key_config_id)) ? $ms_change_key_config_id : $ms_config['change_key_config_id'],           // 改键自定义方案id
                        "change_key_config_name" => (!empty($ms_change_key_config_name)) ? $ms_change_key_config_name : $ms_config['change_key_config_name'],   // 改键自定义方案id
                        "change_key_content" => $ms_config['change_key_content'],                                                                                // 改键内容
                    );
                    $ms_config_content = serialize($ms_config_array);

                    $add_ms_cc_model = new TdContestConfig();
                    $add_ms_cc_model->cm_id = $add_cm_model['cm_id'];
//                $add_ms_cc_model->device_model = $ms_device_model;
                    $add_ms_cc_model->device_type = 1;
                    $add_ms_cc_model->config_content = $ms_config_content;
                    $add_ms_cc_model->created_time = time();
                    if(!$add_ms_cc_model->save()){
                        $flag = false;
                    }
                }

                // 存在键盘设备型号时新增竞技模式键盘配置
                if(!empty($ms_device_model)) {
                    // 获取默认的键盘配置
                    $kb_cc_model = new TdContestConfig();
                    $kb_config_info = $kb_cc_model->getContestConfigInfo($kb_where . $default_where);
                    $kb_config = unserialize($kb_config_info['config_content']);

                    // 序列化键盘配置信息
                    $kb_config_array = array(
                        "switch" => (!empty($kb_switch)) ? $kb_switch : true,                                                                                   // 设备开关
                        "polling_rate" => (isset($polling_rate)) ? $polling_rate : $kb_config['polling_rate'],                                                  // 轮询率
                        "repeat_speed" => (isset($repeat_speed)) ? $repeat_speed : $kb_config['repeat_speed'],                                                  // 重复速度
                        "conflict_mode" => (!empty($conflict_mode)) ? $conflict_mode : $kb_config['conflict_mode'],                                             // 无冲模式
                        "ban_keys" => (isset($ban_keys)) ? $ban_keys : $kb_config['ban_keys'],                                                                  // 禁用按键
                        "keycap_light" => (isset($keycap_light)) ? $keycap_light : $kb_config['keycap_light'],                                                  // 键帽灯光
                        "keycap_light_config_id" => (!empty($keycap_light_config_id)) ? $keycap_light_config_id : $kb_config['keycap_light_config_id'],         // 键帽灯光自定义方案id
                        "keycap_light_config_name" => (!empty($keycap_light_config_name)) ? $keycap_light_config_name : $kb_config['keycap_light_config_name'], // 键帽灯光自定义方案id
                        "keycap_content" => $kb_config['keycap_content'],                                                                                        // 键帽灯光内容
                        "lamp_bar_light" => (isset($kb_lamp_bar_light)) ? $kb_lamp_bar_light : $kb_config['lamp_bar_light'],                                    // 灯带灯光
                        "lamp_bar_config_id" => (!empty($kb_lamp_bar_config_id)) ? $kb_lamp_bar_config_id : $kb_config['lamp_bar_config_id'],                   // 灯带自定义方案id
                        "lamp_bar_config_name" => (!empty($kb_lamp_bar_config_name)) ? $kb_lamp_bar_config_name : $kb_config['lamp_bar_config_name'],           // 灯带自定义方案id
                        "lamp_bar_content" => $kb_config['lamp_bar_content'],                                                                                    // 灯带方案内容
                        "change_key" => (isset($kb_change_key)) ? $kb_change_key : $kb_config['change_key'],                                                    // 改键
                        "change_key_config_id" => (!empty($kb_change_key_config_id)) ? $kb_change_key_config_id : $kb_config['change_key_config_id'],           // 改键自定义方案id
                        "change_key_config_name" => (!empty($kb_change_key_config_name)) ? $kb_change_key_config_name : $kb_config['change_key_config_name'],   // 改键自定义方案id
                        "change_key_content" => $kb_config['change_key_content'],                                                                                // 改键内容
                    );
                    $kb_config_content = serialize($kb_config_array);

                    $add_kb_cc_model = new TdContestConfig();
                    $add_kb_cc_model->cm_id = $add_cm_model['cm_id'];
                    $add_kb_cc_model->device_model = $kb_device_model;
                    $add_kb_cc_model->device_type = 2;
                    $add_kb_cc_model->config_content = $kb_config_content;
                    $add_kb_cc_model->created_time = time();
                    if(!$add_kb_cc_model->save()){
                        $flag = false;
                    }
                }

                // 存在主机设备型号时新增竞技模式主机配置 2017/06/06
                if(!empty($pc_device_model)){
                    // 获取默认的主机配置
                    $pc_cc_model = new TdContestConfig();
                    $pc_config_info = $pc_cc_model->getContestConfigInfo($pc_where . $default_where);
                    $pc_config = unserialize($pc_config_info['config_content']);

                    // 序列化鼠标配置信息
                    $pc_config_array = array(
                        "switch" => (!empty($pc_switch)) ? $pc_switch : true,                                                                                   // 设备开关
                        "lamp_effect" => (isset($pc_lamp_effect_)) ? $pc_lamp_effect_ : $pc_config['lamp_effect'],                                              // 主机灯光效果
                    );
                    $pc_config_content = serialize($pc_config_array);

                    $add_pc_cc_model = new TdContestConfig();
                    $add_pc_cc_model->cm_id = $add_cm_model['cm_id'];
                    $add_pc_cc_model->device_model = $pc_device_model;
                    $add_pc_cc_model->device_type = 3;
                    $add_pc_cc_model->config_content = $pc_config_content;
                    $add_pc_cc_model->created_time = time();
                    if(!$add_pc_cc_model->save()){
                        $flag = false;
                    }
                }

                if($flag){
                    $transaction->commit();
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'cm_id' => $add_cm_model['cm_id'],
                    );
                }else{
                    $transaction->rollback();
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
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v3_1/contest/resetContestConfig",
     *     summary="119.重置竞技模式的配置信息",
     *     tags={"Contest"},
     *     description="重置用户竞技模式的配置信息,返回该竞技模式的默认配置信息",
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
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_device_model",
     *         in="query",
     *         description="鼠标设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_device_model",
     *         in="query",
     *         description="键盘设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_device_model",
     *         in="query",
     *         description="主机设备型号",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="cm_id",
     *                 description="竞技模式id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="game_name",
     *                 description="游戏名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="ms_config_info",
     *                 description="鼠标配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="ms_switch",
     *                             description="鼠标设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="report_rate",
     *                             description="回报率",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="dpi",
     *                             description="DPI",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="dpi_value",
     *                             description="DPI实值",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_light",
     *                             description="灯带灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_id",
     *                             description="灯带自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_name",
     *                             description="灯带自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_content",
     *                             description="灯带灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key",
     *                             description="改键灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_id",
     *                             description="改键自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_name",
     *                             description="改键自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_content",
     *                             description="改键灯光内容",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="kb_config_info",
     *                 description="键盘配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="kb_switch",
     *                             description="键盘设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="polling_rate",
     *                             description="轮询率",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="repeat_speed",
     *                             description="重复速度",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="conflict_mode",
     *                             description="无冲模式",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="ban_keys",
     *                             description="禁用按键",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light",
     *                             description="键帽灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light_config_id",
     *                             description="键帽灯光自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_light_config_name",
     *                             description="键帽灯光自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="keycap_content",
     *                             description="键帽灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_light",
     *                             description="灯带灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_id",
     *                             description="灯带自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_config_name",
     *                             description="灯带自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_bar_content",
     *                             description="灯带灯光内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key",
     *                             description="改键灯光",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_id",
     *                             description="改键自定义方案id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_config_name",
     *                             description="改键自定义方案名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="change_key_content",
     *                             description="改键灯光内容",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="pc_config_info",
     *                 description="主机配置信息",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="pc_switch",
     *                             description="主机设备开关",
     *                             type="boolean",
     *                         ),
     *                         @SWG\Property(
     *                             property="lamp_effect",
     *                             description="主机灯光效果",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="assist_url",
     *                 description="网址",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="url1",
     *                             description="网址1",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="url2",
     *                             description="网址2",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="url3",
     *                             description="网址3",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="assist_tool",
     *                 description="设备配置列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="tool1",
     *                             description="程序1",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
     *                         ),
     *                         @SWG\Property(
     *                             property="tool2",
     *                             description="程序2",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
     *                         ),
     *                         @SWG\Property(
     *                             property="tool3",
     *                             description="程序3",
     *                             type="array",
     *                             items={
     *                                  @SWG\Property(
     *                                      property="select",
     *                                      description="开关",
     *                                      type="string",
     *                                 ),
     *                                  @SWG\Property(
     *                                      property="path",
     *                                      description="路径",
     *                                      type="string",
     *                                 ),
     *                             }
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
    public function actionResetContestConfig(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getIntFromRequest("cm_id");
        $ms_device_model = Frame::getStringFromRequest("ms_device_model");
        $kb_device_model = Frame::getStringFromRequest("kb_device_model");
        $pc_device_model = Frame::getStringFromRequest("pc_device_model");
        if(empty($cm_id) || (empty($ms_device_model) && empty($kb_device_model) && empty($pc_device_model)) ){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获取竞技模式信息
        $cm_model = new TdContestMode();
        $cm_where = "cm_id = {$cm_id} AND member_id = {$member_id}";
        $contest_mode_info = $cm_model->getContestModeInfo($cm_where);
        if(!empty($contest_mode_info)){
            $ms_config = $kb_config = $pc_config = array();
            // 获取该竞技模式的配置信息
            if(!empty($ms_device_model)){
                $ms_where = "device_type = 1";
                // 鼠标暂时全使用同一配置
//            $ms_where = "device_model = '{$ms_device_model}' AND device_type = 1";

                // 获取默认的鼠标配置
                $ms_info = $this->__getContestConfigWithReset($ms_where, $contest_mode_info);
                // 解析配置信息用于返回
                if(!empty($ms_info['config_content'])){
                    $ms_config = unserialize($ms_info['config_content']);
                }
            }

            if(!empty($kb_device_model)) {
                $kb_where = "device_model = '{$kb_device_model}' AND device_type = 2";

                // 获取默认的键盘配置
                $kb_info = $this->__getContestConfigWithReset($kb_where, $contest_mode_info);
                // 解析配置信息用于返回
                if(!empty($kb_info['config_content'])){
                    $kb_config = unserialize($kb_info['config_content']);
                }
            }

            // 增加主机配置 2017/06/06
            if(!empty($pc_device_model)){
                $pc_where = "device_model = '{$pc_device_model}' AND device_type = 3";

                // 获取默认的主机配置
                $pc_info = $this->__getContestConfigWithReset($pc_where, $contest_mode_info);
                // 解析配置信息用于返回
                if(!empty($pc_info['config_content'])){
                    $pc_config = unserialize($pc_info['config_content']);
                }
            }

            if(empty($ms_config) && empty($kb_config) && empty($pc_config)){
                // 三者均没有默认配置的情况
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => $this->language->get('no_data'),
                );
                echo json_encode($result);
                die();
            }

            $ms_config_info = array(
                "ms_switch" => (!empty($ms_config["switch"])) ? $ms_config["switch"] : true,                                               // 鼠标设备开关
                "report_rate" => (!empty($ms_config["report_rate"])) ? $ms_config["report_rate"] : 0,                                       // 回报率
                "dpi" => (!empty($ms_config["dpi"])) ? $ms_config["dpi"] : 0,                                                               // DPI
                "dpi_value" => (!empty($ms_config["dpi_value"])) ? $ms_config["dpi_value"] : 0,                                             // DPI实值
                "lamp_bar_light" => (!empty($ms_config["lamp_bar_light"])) ? $ms_config["lamp_bar_light"] : 0,                              // 灯带灯光
                "lamp_bar_config_id" => (!empty($ms_config["lamp_bar_config_id"])) ? $ms_config["lamp_bar_config_id"] : 0,                  // 灯带自定义方案id
                "lamp_bar_config_name" => (!empty($ms_config["lamp_bar_config_name"])) ? $ms_config["lamp_bar_config_name"] : "",           // 灯带自定义方案名称
                "lamp_bar_content" => (!empty($ms_config["lamp_bar_content"])) ? $ms_config["lamp_bar_content"] : "",                       // 灯带灯光内容
                "change_key" => (!empty($ms_config["change_key"])) ? $ms_config["change_key"] : 0,                                          // 改键灯光
                "change_key_config_id" => (!empty($ms_config["change_key_config_id"])) ? $ms_config["change_key_config_id"] : 0,            // 改键自定义方案id
                "change_key_config_name" => (!empty($ms_config["change_key_config_name"])) ? $ms_config["change_key_config_name"] : "",     // 灯带自定义方案名称
                "change_key_content" => (!empty($ms_config["change_key_content"])) ? $ms_config["change_key_content"] : "",                 // 灯带灯光内容
            );

            $kb_config_info = array(
                "kb_switch" => (!empty($kb_config["switch"])) ? $kb_config["switch"] : true,                                                // 键盘设备开关
                "polling_rate" => (!empty($kb_config["polling_rate"])) ? $kb_config["polling_rate"] : 0,                                     // 轮询率
                "repeat_speed" => (!empty($kb_config["repeat_speed"])) ? $kb_config["repeat_speed"] : 0,                                     // 重复速度
                "conflict_mode" => (!empty($kb_config["conflict_mode"])) ? $kb_config["conflict_mode"] : 0,                                  // 无冲模式
                "ban_keys" => (!empty($kb_config["ban_keys"])) ? $kb_config["ban_keys"] : "",                                                // 禁用按键
                "keycap_light" => (!empty($kb_config["keycap_light"])) ? $kb_config["keycap_light"] : 0,                                     // 灯带灯光
                "keycap_light_config_id" => (!empty($kb_config["keycap_light_config_id"])) ? $kb_config["keycap_light_config_id"] : 0,       // 灯带自定义方案id
                "keycap_light_config_name" => (!empty($kb_config["keycap_light_config_name"])) ? $kb_config["keycap_light_config_name"] : "",// 灯带自定义方案名称
                "keycap_content" => (!empty($kb_config["keycap_content"])) ? $kb_config["keycap_content"] : "",                              // 灯带灯光内容
                "lamp_bar_light" => (!empty($kb_config["lamp_bar_light"])) ? $kb_config["lamp_bar_light"] : 0,                               // 灯带灯光
                "lamp_bar_config_id" => (!empty($kb_config["lamp_bar_config_id"])) ? $kb_config["lamp_bar_config_id"] : 0,                   // 灯带自定义方案id
                "lamp_bar_config_name" => (!empty($kb_config["lamp_bar_config_name"])) ? $kb_config["lamp_bar_config_name"] : "",            // 灯带自定义方案名称
                "lamp_bar_content" => (!empty($kb_config["lamp_bar_content"])) ? $kb_config["lamp_bar_content"] : "",                        // 灯带灯光内容
                "change_key" => (!empty($kb_config["change_key"])) ? $kb_config["change_key"] : 0,                                           // 改键灯光
                "change_key_config_id" => (!empty($kb_config["change_key_config_id"])) ? $kb_config["change_key_config_id"] : 0,             // 改键自定义方案id
                "change_key_config_name" => (!empty($kb_config["change_key_config_name"])) ? $kb_config["change_key_config_name"] : "",      // 灯带自定义方案名称
                "change_key_content" => (!empty($kb_config["change_key_content"])) ? $kb_config["change_key_content"] : "",                  // 灯带灯光内容
            );

            $pc_config_info = array(
                "pc_switch" => (!empty($pc_config["switch"])) ? $pc_config["switch"] : true,                                                // 主机设备开关
                "lamp_effect" => (!empty($pc_config["lamp_effect"])) ? $pc_config["lamp_effect"] : 0,                                        // 主机灯光效果
            );

            // 增加初始化此竞技模式的自定义设置 2017/06/06
            $url_array = array(
                'url1' => "",
                'url2' => "",
                "url3" => "",
            );
            $assist_url = implode(",", $url_array);
            $tool1_array = $tool2_array = $tool3_array = array(
                'select' => "",
                'path' => "",
            );
            $tool_array = array(
                'tool1' => implode("|", $tool1_array),
                'tool2' => implode("|", $tool2_array),
                'tool3' => implode("|", $tool3_array),
            );
            $assist_tool = implode(",", $tool_array);

            $cs_model = new TdContestSetting();
            $contest_setting_info = $cs_model->getContestSettingInfo($cm_where);
            if(!empty($contest_setting_info)){
                $contest_setting_info->assist_url = $assist_url;
                $contest_setting_info->assist_tool = $assist_tool;
                $contest_setting_info->save();
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'cm_id' => $contest_mode_info['cm_id'],
                "game_name" => (!empty($lang) && $lang != "ZH") ? $contest_mode_info['game_name'.$lang] : $contest_mode_info['game_name'],    // 竞技模式名称
                'ms_config_info' => $ms_config_info,                                                                                           // 鼠标配置
                'kb_config_info' => $kb_config_info,                                                                                           // 键盘配置
                'pc_config_info' => $pc_config_info,                                                                                           // 主机配置
                'assist_url' => $url_array,                                                                                                    // 辅助网址
                'assist_tool' => array(                                                                                                       // 辅助程序
                    'tool1' => $tool1_array,
                    'tool2' => $tool2_array,
                    'tool3' => $tool3_array,
                ),
            );
        }else{
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode($result);
    }



    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/contest/AddDefaultContest",
     *     summary="新增竞技模式的默认配置(对内部)",
     *     tags={"Contest"},
     *     description="不对外开放接口",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="game_name",
     *         in="query",
     *         description="游戏名称",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="game_nameEN",
     *         in="query",
     *         description="游戏名称(英文)",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="game_type",
     *         in="query",
     *         description="游戏类型",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"0", "1", "2", "3"}
     *     ),
     *     @SWG\Parameter(
     *         name="ms_device_model",
     *         in="query",
     *         description="鼠标设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_switch",
     *         in="query",
     *         description="鼠标设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="report_rate",
     *         in="query",
     *         description="回报率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1000",
     *     ),
     *     @SWG\Parameter(
     *         name="dpi",
     *         in="query",
     *         description="DPI",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="dpi_value",
     *         in="query",
     *         description="DPI实值",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1500",
     *     ),
     *     @SWG\Parameter(
     *         name="ms_lamp_bar_light",
     *         in="query",
     *         description="鼠标灯带灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="ms_lamp_bar_content",
     *         in="query",
     *         description="鼠标灯带灯光配置内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ms_change_key",
     *         in="query",
     *         description="鼠标改键配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="ms_change_key_content",
     *         in="query",
     *         description="鼠标改键配置内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_device_model",
     *         in="query",
     *         description="键盘设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_switch",
     *         in="query",
     *         description="键盘设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="polling_rate",
     *         in="query",
     *         description="轮询率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1000",
     *     ),
     *     @SWG\Parameter(
     *         name="repeat_speed",
     *         in="query",
     *         description="重复速度",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="conflict_mode",
     *         in="query",
     *         description="无冲模式",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="2",
     *     ),
     *     @SWG\Parameter(
     *         name="ban_keys",
     *         in="query",
     *         description="禁用按键",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light",
     *         in="query",
     *         description="键盘键帽灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_content",
     *         in="query",
     *         description="键盘键帽灯光配置内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_lamp_bar_light",
     *         in="query",
     *         description="键盘灯带灯光配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="kb_lamp_bar_content",
     *         in="query",
     *         description="键盘灯带灯光配置内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="kb_change_key",
     *         in="query",
     *         description="键盘改键配置",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="kb_change_key_content",
     *         in="query",
     *         description="键盘改键配置内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_device_model",
     *         in="query",
     *         description="主机设备型号",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_switch",
     *         in="query",
     *         description="主机设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="pc_lamp_effect",
     *         in="query",
     *         description="主机灯光效果",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
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
    public function actionAddDefaultContest(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $game_name = Frame::getStringFromRequest('game_name');
        $game_nameEN = Frame::getStringFromRequest('game_nameEN');
        $game_type = Frame::getIntFromRequest('game_type');

        // 鼠标配置
        $ms_device_model = Frame::getStringFromRequest("ms_device_model");
        $ms_switch = Frame::getStringFromRequest("ms_switch");
        $report_rate = Frame::getIntFromRequest("report_rate");
        $dpi = Frame::getIntFromRequest("dpi");
        $dpi_value = Frame::getIntFromRequest("dpi_value");
        $ms_lamp_bar_light = Frame::getIntFromRequest("ms_lamp_bar_light");
        $ms_lamp_bar_content = Frame::getStringFromRequest("ms_lamp_bar_content");
        $ms_change_key = Frame::getIntFromRequest("ms_change_key");
        $ms_change_key_content = Frame::getStringFromRequest("ms_change_key_content");
        // 键盘配置
        $kb_device_model = Frame::getStringFromRequest("kb_device_model");
        $kb_switch = Frame::getStringFromRequest("kb_switch");
        $polling_rate = Frame::getIntFromRequest("polling_rate");
        $repeat_speed = Frame::getIntFromRequest("repeat_speed");
        $conflict_mode = Frame::getIntFromRequest("conflict_mode");
        $ban_keys = Frame::getStringFromRequest("ban_keys");
        $keycap_light = Frame::getIntFromRequest("keycap_light");
        $keycap_content = Frame::getStringFromRequest("keycap_content");
        $kb_lamp_bar_light = Frame::getIntFromRequest("kb_lamp_bar_light");
        $kb_lamp_bar_content = Frame::getStringFromRequest("kb_lamp_bar_content");
        $kb_change_key = Frame::getIntFromRequest("kb_change_key");
        $kb_change_key_content = Frame::getStringFromRequest("kb_change_key_content");
        // 主机配置
        $pc_device_model = Frame::getStringFromRequest("pc_device_model");
        $pc_switch = Frame::getStringFromRequest("pc_switch");
        $pc_lamp_effect = Frame::getIntFromRequest("pc_lamp_effect");
        if(empty($game_name) || empty($game_nameEN)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 增加竞技模式
        $cm_model = new TdContestMode();
        $cm_model->game_name = $game_name;
        $cm_model->game_nameEN = $game_nameEN;
        $cm_model->game_type = $game_type;
        $cm_model->created_time = time();
        if(!$cm_model->save()){
            echo 'fail';
            exit;
        }

        $cm_id = $cm_model['cm_id'];

        // 序列化鼠标配置信息
        $ms_config_array = array(
            "switch" => (!empty($ms_switch)) ? $ms_switch  : true,            // 设备开关
            "report_rate" => $report_rate,                                      // 回报率
            "dpi" => $dpi,                                                      // DPI
            "dpi_value" => $dpi_value,                                          // DPI实值
            "lamp_bar_light" => $ms_lamp_bar_light,                             // 灯带灯光
            "lamp_bar_config_id" => 0,                                          // 灯带自定义方案id
            "lamp_bar_config_name" => "推荐",                                   // 灯带自定义方案名称
            "lamp_bar_content" => $ms_lamp_bar_content,                         // 灯带方案内容
            "change_key" => $ms_change_key,                                     // 改键
            "change_key_config_id" => 0,                                        // 改键自定义方案id
            "change_key_config_name" => "推荐",                                 // 改键自定义方案名称
            "change_key_content" => $ms_change_key_content,                     // 改键内容
        );
        $ms_config_content = serialize($ms_config_array);

        // 序列化键盘配置信息
        $kb_config_array = array(
            "switch" => (!empty($kb_switch)) ? $kb_switch  : true,            // 设备开关
            "polling_rate" => $polling_rate,                                    // 轮询率
            "repeat_speed" => $repeat_speed,                                    // 重复速度
            "conflict_mode" => $conflict_mode,                                  // 无冲模式
            "ban_keys" => $ban_keys,                                            // 禁用按键
            "keycap_light" => $keycap_light,                                    // 键帽灯光
            "keycap_light_config_id" => 0,                                      // 键帽灯光自定义方案id
            "keycap_light_config_name" => "推荐",                               // 键帽灯光自定义方案名称
            "keycap_content" => $keycap_content,                                // 键帽灯光内容
            "lamp_bar_light" => $kb_lamp_bar_light,                             // 灯带灯光
            "lamp_bar_config_id" => 0,                                          // 灯带自定义方案id
            "lamp_bar_config_name" => "推荐",                                   // 灯带自定义方案名称
            "lamp_bar_content" => $kb_lamp_bar_content,                         // 灯带方案内容
            "change_key" => $kb_change_key,                                     // 改键
            "change_key_config_id" => 0,                                        // 改键自定义方案id
            "change_key_config_name" => "推荐",                                 // 改键自定义方案名称
            "change_key_content" => $kb_change_key_content,                     // 改键内容
        );
        $kb_config_content = serialize($kb_config_array);

        // 序列化主机配置信息 2017/06/06
        $pc_config_array = array(
            "switch" => (!empty($pc_switch)) ? $pc_switch  : true,            // 设备开关
            "lamp_effect" => $pc_lamp_effect,                                   // 主机灯光效果
        );
        $pc_config_content = serialize($pc_config_array);

        // 新增默认配置
        $ms_cc_model = new TdContestConfig();
        $ms_cc_model->cm_id = $cm_id;
//        $ms_cc_model->device_model = $ms_device_model;
        $ms_cc_model->device_type = 1;
        $ms_cc_model->config_content = $ms_config_content;
        $ms_cc_model->created_time = time();
        if(!$ms_cc_model->save()){
            echo 'fail';
            exit;
        }

        $kb_cc_model = new TdContestConfig();
        $kb_cc_model->cm_id = $cm_id;
        $kb_cc_model->device_model = $kb_device_model;
        $kb_cc_model->device_type = 2;
        $kb_cc_model->config_content = $kb_config_content;
        $kb_cc_model->created_time = time();
        if(!$kb_cc_model->save()){
            echo 'fail';
            exit;
        }

        $kb_cc_model = new TdContestConfig();
        $kb_cc_model->cm_id = $cm_id;
        $kb_cc_model->device_model = $pc_device_model;
        $kb_cc_model->device_type = 3;
        $kb_cc_model->config_content = $pc_config_content;
        $kb_cc_model->created_time = time();
        if(!$kb_cc_model->save()){
            echo 'fail';
            exit;
        }
        
        echo 'ok';
    }


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/contest/AddContestConfig",
     *     summary="新增竞技模式的推荐默认配置(对内部)",
     *     tags={"Contest"},
     *     description="不对外开放接口",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_model",
     *         in="query",
     *         description="设备型号",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="设备型号",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1",
     *         enum={"1", "2", "3"}
     *     ),
     *     @SWG\Parameter(
     *         name="switch",
     *         in="query",
     *         description="设备开关",
     *         type="boolean",
     *         @SWG\Items(type="boolean"),
     *         collectionFormat="multi",
     *         default="true",
     *     ),
     *     @SWG\Parameter(
     *         name="lamp_bar_light",
     *         in="query",
     *         description="灯带灯光",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="lamp_bar_config_id",
     *         in="query",
     *         description="灯带自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="lamp_bar_content",
     *         in="query",
     *         description="灯带方案内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="change_key",
     *         in="query",
     *         description="改键",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="change_key_config_id",
     *         in="query",
     *         description="改键自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="change_key_content",
     *         in="query",
     *         description="改键方案内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="report_rate",
     *         in="query",
     *         description="回报率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="dpi",
     *         in="query",
     *         description="DPI",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="dpi_value",
     *         in="query",
     *         description="DPI实值",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="polling_rate",
     *         in="query",
     *         description="轮询率",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="1000",
     *     ),
     *     @SWG\Parameter(
     *         name="repeat_speed",
     *         in="query",
     *         description="重复速度",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="conflict_mode",
     *         in="query",
     *         description="无冲模式",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="ban_keys",
     *         in="query",
     *         description="禁用按键",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light",
     *         in="query",
     *         description="键帽灯光",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_light_config_id",
     *         in="query",
     *         description="键帽灯光自定义方案id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi",
     *         default="0",
     *     ),
     *     @SWG\Parameter(
     *         name="keycap_content",
     *         in="query",
     *         description="键帽灯光内容",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="pc_lamp_effect",
     *         in="query",
     *         description="主机灯光效果",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="cm_id",
     *                 description="竞技模式id",
     *                 type="integer",
     *             ),
     * 		   ),
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
    public function actionAddContestConfig(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getStringFromRequest('cm_id');
        $device_model = Frame::getStringFromRequest('device_model');
        $device_type = Frame::getIntFromRequest('device_type');

        $switch = Frame::getStringFromRequest("switch");
        $lamp_bar_light = Frame::getIntFromRequest("lamp_bar_light");
        $lamp_bar_config_id = Frame::getIntFromRequest("lamp_bar_config_id");
        $lamp_bar_content = Frame::getStringFromRequest("lamp_bar_content");
        $change_key = Frame::getIntFromRequest("change_key");
        $change_key_config_id = Frame::getIntFromRequest("change_key_config_id");
        $change_key_content = Frame::getStringFromRequest("change_key_content");

        // 鼠标配置
        $report_rate = Frame::getIntFromRequest("report_rate");
        $dpi = Frame::getIntFromRequest("dpi");
        $dpi_value = Frame::getIntFromRequest("dpi_value");

        // 键盘配置
        $polling_rate = Frame::getIntFromRequest("polling_rate");
        $repeat_speed = Frame::getIntFromRequest("repeat_speed");
        $conflict_mode = Frame::getIntFromRequest("conflict_mode");
        $ban_keys = Frame::getStringFromRequest("ban_keys");
        $keycap_light = Frame::getIntFromRequest("keycap_light");
        $keycap_light_config_id = Frame::getIntFromRequest("keycap_light_config_id");
        $keycap_content = Frame::getStringFromRequest("keycap_content");

        // 主机配置
        $pc_lamp_effect = Frame::getIntFromRequest("pc_lamp_effect");
        if(empty($cm_id) || empty($device_type) || empty($device_model)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        $config_array = array();
        if($device_type == 1){
            // 序列化鼠标配置信息
            $config_array = array(
                "switch" => (!empty($switch)) ? $switch : true,                   // 设备开关
                "report_rate" => $report_rate,                                      // 回报率
                "dpi" => $dpi,                                                      // DPI
                "dpi_value" => $dpi_value,                                          // DPI实值
                "lamp_bar_light" => $lamp_bar_light,                                // 灯带灯光
                "lamp_bar_config_id" => $lamp_bar_config_id,                        // 灯带自定义方案id
                "lamp_bar_config_name" => "推荐",                                   // 灯带自定义方案名称
                "lamp_bar_content" => $lamp_bar_content,                            // 灯带方案内容
                "change_key" => $change_key,                                        // 改键
                "change_key_config_id" => $change_key_config_id,                    // 改键自定义方案id
                "change_key_config_name" => "推荐",                                 // 改键自定义方案名称
                "change_key_content" => $change_key_content,                        // 改键内容
            );
        }elseif($device_type == 2){
            // 序列化键盘配置信息
            $config_array = array(
                "switch" => (!empty($switch)) ? $switch : true,                   // 设备开关
                "polling_rate" => $polling_rate,                                    // 轮询率
                "repeat_speed" => $repeat_speed,                                    // 重复速度
                "conflict_mode" => $conflict_mode,                                  // 无冲模式
                "ban_keys" => $ban_keys,                                            // 禁用按键
                "keycap_light" => $keycap_light,                                    // 键帽灯光
                "keycap_light_config_id" => $keycap_light_config_id,                // 键帽灯光自定义方案id
                "keycap_light_config_name" => "推荐",                               // 键帽灯光自定义方案名称
                "keycap_content" => $keycap_content,                                // 键帽灯光内容
                "lamp_bar_light" => $lamp_bar_light,                                // 灯带灯光
                "lamp_bar_config_id" => $lamp_bar_config_id,                        // 灯带自定义方案id
                "lamp_bar_config_name" => "推荐",                                   // 灯带自定义方案名称
                "lamp_bar_content" => $lamp_bar_content,                            // 灯带方案内容
                "change_key" => $change_key,                                        // 改键
                "change_key_config_id" => $change_key_config_id,                    // 改键自定义方案id
                "change_key_config_name" => "推荐",                                 // 改键自定义方案名称
                "change_key_content" => $change_key_content,                        // 改键内容
            );
        }elseif($device_type == 3){
            $config_array = array(
                "switch" => (!empty($switch)) ? $switch : true,                   // 设备开关
                "lamp_effect" => $pc_lamp_effect,                                      // 主机灯光效果
            );
        }
        $config_content = serialize($config_array);

        $kb_cc_model = new TdContestConfig();
        $kb_cc_model->cm_id = $cm_id;
        $kb_cc_model->device_model = $device_model;
        $kb_cc_model->device_type = $device_type;
        $kb_cc_model->config_content = $config_content;
        $kb_cc_model->created_time = time();
        if(!$kb_cc_model->save()){
            $result['ret_num'] = 905;
            $result['ret_msg'] = $this->language->get('save_fail');
            echo json_encode($result);
            die();
        }

        echo 'ok';
    }


    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/contest/saveContestSetting",
     *     summary="125.保存竞技模式设置",
     *     tags={"Contest"},
     *     description="保存用户对竞技模式的自定义设置信息",
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
     *         name="cm_id",
     *         in="query",
     *         description="竞技模式id",
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="game_name",
     *         in="query",
     *         description="游戏名称",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="url1",
     *         in="query",
     *         description="网址1",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="url2",
     *         in="query",
     *         description="网址2",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="url3",
     *         in="query",
     *         description="网址3",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="tool1_select",
     *         in="query",
     *         description="程序1选择",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="tool1_path",
     *         in="query",
     *         description="程序1路径",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="tool2_select",
     *         in="query",
     *         description="程序2选择",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="tool2_path",
     *         in="query",
     *         description="程序2路径",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="tool3_select",
     *         in="query",
     *         description="程序3选择",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="tool3_path",
     *         in="query",
     *         description="程序3路径",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
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
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="cm_id",
     *                 description="竞技模式id",
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
    public function actionSaveContestSetting(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cm_id = Frame::getStringFromRequest('cm_id');
        $game_name = Frame::getStringFromRequest('game_name');
        $url1 = Frame::getStringFromRequest('url1');
        $url2 = Frame::getStringFromRequest('url2');
        $url3 = Frame::getStringFromRequest('url3');
        $tool1_select = Frame::getStringFromRequest('tool1_select');
        $tool1_path = Yii::app ()->request->getParam ( "tool1_path" );
        $tool2_select = Frame::getStringFromRequest('tool2_select');
        $tool2_path = Yii::app ()->request->getParam ( "tool2_path" );
        $tool3_select = Frame::getStringFromRequest('tool3_select');
        $tool3_path = Yii::app ()->request->getParam ( "tool3_path" );

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 拼接网址字符串用于保存
        $url_array = array(
            'url1' => $url1,
            'url2' => $url2,
            "url3" => $url3,
        );
        $assist_url = implode(",", $url_array);

        // 拼接工具字符串用于保存
        $tool1_array = array(
            'select' => $tool1_select,
            'path' => $tool1_path,
        );
        $tool2_array = array(
            'select' => $tool2_select,
            'path' => $tool2_path,
        );
        $tool3_array = array(
            'select' => $tool3_select,
            'path' => $tool3_path,
        );
        $tool_array = array(
            'tool1' => implode("|", $tool1_array),
            'tool2' => implode("|", $tool2_array),
            'tool3' => implode("|", $tool3_array),
        );
        $assist_tool = implode(",", $tool_array);

        if(!empty($cm_id)){
            // 存在竞技模式id时修改
            $edit_cm_model = new TdContestMode();
            $cm_where = "cm_id = {$cm_id}";
            $cm_info = $edit_cm_model->getContestModeInfo($cm_where);
            if(!empty($cm_info)){
                //开启事物
                $transaction = Yii::app()->db->beginTransaction(); //创建事务
                $flag = true;

                // 修改竞技模式信息
                $cm_info->game_name = $game_name;
                $cm_info->game_nameEN = $game_name;
                if(!$cm_info->save()){
                    $flag = false;
                }

                // 获取该竞技模式的自定义配置信息
                $cs_model = new TdContestSetting();
                $cs_where = "cm_id = {$cm_id} AND member_id = {$member_id}";
                $cs_info = $cs_model->getContestSettingInfo($cs_where);
                if(!empty($cs_info)){
                    // 修改自定义设置信息
                    $cs_info->assist_url = $assist_url;
                    $cs_info->assist_tool = $assist_tool;
                    if(!$cs_info->save()){
                        $flag = false;
                    }
                }else{
                    // 新增竞技模式自定义设置
                    $cs_model = new TdContestSetting();
                    $cs_model->member_id = $member_id;
                    $cs_model->cm_id = $cm_id;
                    $cs_model->assist_url = $assist_url;
                    $cs_model->assist_tool = $assist_tool;
                    $cs_model->created_time = time();
                    if(!$cs_model->save()) {
                        $flag = false;
                    }
                }

                if($flag){
                    $transaction->commit();
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'cm_id' => $cm_info['cm_id'],
                    );
                }else{
                    $transaction->rollback();
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
            }else{
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => $this->language->get('no_data'),
                );
            }
        }else{
            // 不存在竞技模式id时新增
            $recommend_cm_model = new TdContestMode();
            $recommend_cm_where = "member_id = 0 AND game_type = 4";
            // 获取其他类型的推荐配置
            $recommend_mode_info = $recommend_cm_model->getContestModeInfo($recommend_cm_where, "*", "cm_id ASC");
            if(!empty($recommend_mode_info)){
                //开启事物
                $transaction = Yii::app()->db->beginTransaction(); //创建事务
                $flag = true;

                // 新增竞技模式信息
                $add_cm_model = new TdContestMode();
                $add_cm_model->member_id = $member_id;
                $add_cm_model->recommend_cm_id = 0;
                $add_cm_model->game_name = $game_name;
                $add_cm_model->game_nameEN = $game_name;
                $add_cm_model->game_type = 4;
                $add_cm_model->created_time = time();
                if(!$add_cm_model->save()){
                    $flag = false;
                }

                // 新增竞技模式自定义设置
                $cs_model = new TdContestSetting();
                $cs_model->member_id = $member_id;
                $cs_model->cm_id = $add_cm_model['cm_id'];
                $cs_model->assist_url = $assist_url;
                $cs_model->assist_tool = $assist_tool;
                $cs_model->created_time = time();
                if(!$cs_model->save()) {
                    $flag = false;
                }

                if($flag){
                    $transaction->commit();
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'cm_id' => $add_cm_model['cm_id'],
                    );
                }else{
                    $transaction->rollback();
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
        }

        echo json_encode($result);
    }


    /**
     * 获取游戏类型
     * @param $game_type_text
     * @return int
     */
    private function __getGameType($game_type_text){
        switch($game_type_text){
            case "ETC":
                $game_type = 0;
            case "RTS":
                $game_type = 1;
                break;
            case "MOBA":
                $game_type = 2;
                break;
            case "FPS":
                $game_type = 3;
                break;
            default:
                $game_type = 4;
        }

        return $game_type;
    }


    /**
     * 获取竞技模式配置
     * @param $where
     * @param $contest_mode_info
     * @return array|mixed|null
     */
    private function __getContestConfig($where, $contest_mode_info){
        $cc_model = new TdContestConfig();
        $customer_where = " AND cm_id = {$contest_mode_info['cm_id']}";
        $ms_config_info = $cc_model->getContestConfigInfo($where . $customer_where);
        if(empty($ms_config_info)){
            // 没有自定义配置的情况读取默认配置
            $default_where = " AND cm_id = {$contest_mode_info['recommend_cm_id']}";
            $default_config_info = $cc_model->getContestConfigInfo($where . $default_where);

            return $default_config_info;
        }else{
            return $ms_config_info;
        }
    }


    /**
     * 重置竞技模式配置
     * @param $where
     * @param $contest_mode_info
     * @return array|mixed|null
     */
    private function __getContestConfigWithReset($where, $contest_mode_info){
        $cc_model = new TdContestConfig();
        $customer_where = $where . " AND cm_id = {$contest_mode_info['cm_id']}";
        // 删除用户自定义配置
        $cc_model->deleteAll($customer_where);

        // 获取此竞技模式对应该设备的默认配置信息
        $default_where = $where . " AND cm_id = {$contest_mode_info['recommend_cm_id']}";
        $default_config_info = $cc_model->getContestConfigInfo($default_where);
        if(empty($default_config_info)){
            // 没有默认配置的情况
            $result = array(
                'ret_num' => 309,
                'ret_msg' => $this->language->get('no_data'),
            );
            echo json_encode($result);
            die();
        }

        return $default_config_info;
    }


    /**
     * 获取设备自定义配置的配置名
     * @param $dc_id
     * @param $member_id
     * @return string
     */
    private function __getDeviceConfigName($dc_id, $member_id){
        $config_name = "";
        if(!empty($dc_id)){
            $model = new TdDeviceConfig();
            $device_config_info = $model->getDeviceConfigInfo("dc_id = {$dc_id} AND member_id = {$member_id}", "config_name");
            if(!empty($device_config_info)){
                $config_name = $device_config_info['config_name'];
            }
        }

        return $config_name;
    }


}