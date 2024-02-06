<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10
 * Time: 16:35
 */
class CharaMacroController extends PublicController{

    /**
     * @SWG\Post(
     *     path="/app_api/website/api.php/v2_2/charaMacro/saveGameChara",
     *     summary="保存游戏角色(对内接口)",
     *     tags={"CharaMacro"},
     *     description="保存角色宏对应的游戏角色",
     *     operationId="",
     *     consumes={"application/x-www-form-urlencoded"},
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
     *         name="game_icon",
     *         in="formData",
     *         description="游戏图标",
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="chara_name",
     *         in="query",
     *         description="角色名称",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="chara_desc",
     *         in="query",
     *         description="角色描述",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="chara_image1",
     *         in="formData",
     *         description="角色图像(大图)",
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="chara_image2",
     *         in="formData",
     *         description="角色图像(小图)",
     *         type="file",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="操作成功",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="chara_id",
     *                 description="角色id",
     *                 type="integer",
     *             ),
     * 		   ),
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
     *         response="905",
     *         description="保存信息时发生错误",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     *     @SWG\Response(
     *         response="908",
     *         description="图片上传失败",
     *     ),
     * )
     */
    public function actionSaveGameChara(){
        $game_name = Frame::getStringFromRequest('game_name');
        $game_icon = CUploadedFile::getInstanceByName('game_icon');
        $chara_name = Frame::getStringFromRequest('chara_name');
        $chara_desc = Frame::getStringFromRequest('chara_desc');
        $chara_image1 = CUploadedFile::getInstanceByName('chara_image1');
        $chara_image2 = CUploadedFile::getInstanceByName('chara_image2');
        if(empty($game_name) || empty($chara_name)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 查询游戏是否存在
        $game_model = new TdGame();
        $game_where = "game_name = '{$game_name}'";
        $game_info = $game_model->getGameInfo($game_where);
        if(!empty($game_info)){
            // 保存上传的游戏图标
            if(!empty($game_icon)){
                $game_icon_path = $this->uploadPic($game_icon);
                if(!$game_icon_path){
                    $result = array(
                        'ret_num' => 908,
                        'ret_msg' => '图片上传失败',
                    );
                    echo json_encode($result);
                    die();
                }

                $game_info->game_icon = $game_icon_path;
                if(!$game_info->save()){
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => '更新信息时发生错误',
                    );
                    echo json_encode($result);
                    die();
                }
            }

            // 根据游戏和角色名称获取信息
            $game_chara_model = new TdGameCharas();
            $gc_where = "game_id = '{$game_info['id']}' AND chara_name = '{$chara_name}'";
            $game_chara_info = $game_chara_model->getGameCharaInfo($gc_where);
            if(!empty($game_chara_info)){
                // 修改角色信息
                if(!empty($chara_desc)){
                    $game_chara_info->chara_desc = $chara_desc;
                }
                if(!empty($chara_image1)){
                    // 上传角色图像1
                    $chara_image1_path = $this->uploadPic($chara_image1);
                    if(!$chara_image1_path){
                        $result['ret_num'] = 908;
                        $result['ret_msg'] = '图片上传失败';
                        echo json_encode ( $result );
                        die ();
                    }

                    $game_chara_info->chara_image1 = $chara_image1_path;
                }
                if(!empty($chara_image2)){
                    // 上传角色图像2
                    $chara_image2_path = $this->uploadPic($chara_image2);
                    if(!$chara_image2_path){
                        $result['ret_num'] = 908;
                        $result['ret_msg'] = '图片上传失败';
                        echo json_encode ( $result );
                        die ();
                    }

                    $game_chara_info->chara_image2 = $chara_image2_path;
                }
                $game_chara_model->update_time = time();
                
                if($game_chara_info->save()){
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'chara_id' => $game_chara_info['chara_id']
                    );
                }else{
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => '更新信息时发生错误',
                    );
                }
            }else{
                // 新增角色信息
                if(empty($chara_image1) || empty($chara_image2)){
                    // 新增时必须存在角色图像
                    $result['ret_num'] = 201;
                    $result['ret_msg'] = '参数输入不完整';
                    echo json_encode($result);
                    die();
                }

                $game_chara_model->game_id = $game_info['id'];
                $game_chara_model->game_name = $game_info['game_name'];
                $game_chara_model->chara_name = $chara_name;
                $game_chara_model->chara_desc = $chara_desc;
                // 上传角色图像
                $chara_image1_path = $this->uploadPic($chara_image1);
                if(!$chara_image1_path){
                    $result = array(
                        'ret_num' => 908,
                        'ret_msg' => '图片上传失败',
                    );
                    echo json_encode($result);
                    die();
                }
                $chara_image2_path = $this->uploadPic($chara_image2);
                if(!$chara_image2_path){
                    $result = array(
                        'ret_num' => 908,
                        'ret_msg' => '图片上传失败',
                    );
                    echo json_encode($result);
                    die();
                }
                $game_chara_model->chara_image1 = $chara_image1_path;
                $game_chara_model->chara_image2 = $chara_image2_path;
                $game_chara_model->created_time = time();

                if($game_chara_model->save()){
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'chara_id' => $game_chara_model['chara_id']
                    );
                }else{
                    $result = array(
                        'ret_num' => 905,
                        'ret_msg' => '保存信息时发生错误',
                    );
                }
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/deleteGameChara",
     *     summary="删除游戏角色(对内接口)",
     *     tags={"CharaMacro"},
     *     description="根据游戏角色id删除游戏角色",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="chara_id",
     *         in="query",
     *         description="角色id",
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
     *         response="309",
     *         description="没有该记录",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionDeleteGameChara(){
        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 逻辑删除角色信息
        $game_chara_model = new TdGameCharas();
        $game_chara_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($game_chara_info)){
            $game_chara_info->update_time = time();
            $game_chara_info->is_delete = 1;
            if($game_chara_info->save()){
                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                );
            }else{
                $result = array(
                    'ret_num' => 906,
                    'ret_msg' => '更新信息时发生错误',
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
     * @SWG\Post(
     *     path="/app_api/website/api.php/v2_2/charaMacro/saveCharaMacro",
     *     summary="保存角色宏(对内接口)",
     *     tags={"CharaMacro"},
     *     description="保存指定游戏角色下的角色宏",
     *     operationId="",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="chara_id",
     *         in="query",
     *         description="角色id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="macro_name",
     *         in="query",
     *         description="宏名称",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="macro_content",
     *         in="formData",
     *         description="宏内容",
     *         type="file",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="操作成功",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="chara_id",
     *                 description="角色id",
     *                 type="integer",
     *             ),
     * 		   ),
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
     *         response="905",
     *         description="保存信息时发生错误",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionSaveCharaMacro(){
        $chara_id = Frame::getIntFromRequest('chara_id');
        $macro_name = Frame::getStringFromRequest('macro_name');
        $macro_content_file = CUploadedFile::getInstanceByName('macro_content');
        if(empty($chara_id) || empty($macro_name) || empty($macro_content_file)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }
        $file_type = explode('.', $macro_content_file->name);
        if($file_type[1] != "mcr"){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '宏文件类型不正确';
            echo json_encode($result);
            die();
        }
        // 读取上传的宏文件
        $macro_content = file_get_contents($macro_content_file->tempName);

        // 获取角色信息
        $game_chara_model = new TdGameCharas();
        $game_chara_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($game_chara_info)){
            $chara_macro_model = new TdCharaMacros();
            $where = "chara_id = {$chara_id} AND macro_name = '{$macro_name}' AND is_delete = 0";
            $chara_macro_info = $chara_macro_model->getCharaMacroInfo($where);
            if(!empty($chara_macro_info)){
                // 修改宏信息
                $chara_macro_info->macro_content = $macro_content;
                $chara_macro_info->update_time = time();
                if($chara_macro_info->save()){
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'chara_id' => $chara_macro_info['cmacro_id']
                    );
                }else{
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => '更新信息时发生错误',
                    );
                }
            }else{
                // 创建事务
                $transaction = Yii::app()->db->beginTransaction();
                $flag = true;

                // 新增宏信息
                $chara_macro_model->chara_id = $chara_id;
                $chara_macro_model->chara_name = $game_chara_info['chara_name'];
                $chara_macro_model->macro_name = $macro_name;
                $chara_macro_model->macro_content = $macro_content;
                $chara_macro_model->created_time = time();
                if(!$chara_macro_model->save()){
                    $flag = false;
                }

                // 游戏角色表的宏数量增加
                $game_chara_info->macro_count = $game_chara_info['macro_count'] + 1;
                if(!$game_chara_info->save()){
                    $flag = false;
                }

                if($flag){
                    $transaction->commit();

                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'chara_id' => $chara_macro_model['cmacro_id']
                    );
                }else{
                    $transaction->rollback();

                    $result = array(
                        'ret_num' => 905,
                        'ret_msg' => '保存信息时发生错误',
                    );
                }
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/deleteCharaMacro",
     *     summary="删除角色宏(对内接口)",
     *     tags={"CharaMacro"},
     *     description="根据角色宏id删除游戏角色宏",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="cmacro_id",
     *         in="query",
     *         description="宏id",
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
     *         response="309",
     *         description="没有该记录",
     *     ),
     *     @SWG\Response(
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionDeleteCharaMacro(){
        $cmacro_id = Frame::getIntFromRequest('cmacro_id');
        if(empty($cmacro_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 逻辑删除角色宏信息
        $chara_macro_model = new TdCharaMacros();
        $where = "cmacro_id = {$cmacro_id} AND is_delete = 0";
        $chara_macro_info = $chara_macro_model->getCharaMacroInfo($where);
        if(!empty($chara_macro_info)){
            // 创建事务
            $transaction = Yii::app()->db->beginTransaction();
            $flag = true;

            $chara_macro_info->update_time = time();
            $chara_macro_info->is_delete = 1;
            if(!$chara_macro_info->save()){
                $flag = false;
            }

            // 获取角色信息
            $game_chara_model = new TdGameCharas();
            $gc_where = "chara_id = {$chara_macro_info['chara_id']}";
            $game_chara_info = $game_chara_model->getGameCharaInfo($gc_where);
            if(!empty($game_chara_info)){
                // 游戏角色表的宏数量减少
                $game_chara_info->macro_count = $game_chara_info['macro_count'] - 1;
                $game_chara_info->update_time = time();
                if(!$game_chara_info->save()){
                    $flag = false;
                }

                if($flag){
                    $transaction->commit();

                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                    );
                }else{
                    $transaction->rollback();

                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => '更新信息时发生错误',
                    );
                }
            }else{
                $transaction->rollback();

                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => '没有该记录',
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/getGameList",
     *     summary="120.获取游戏列表",
     *     tags={"CharaMacro"},
     *     description="获取有角色宏的游戏列表",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Response(
     *         response="200",
     *         description="成功时返回游戏列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="game_list",
     *                 description="游戏列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="game_name",
     *                             description="游戏名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="game_icon",
     *                             description="游戏图标",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="309",
     *         description="没有该记录",
     *     ),
     * )
     */
    public function actionGetGameList(){
        // 查询有宏的游戏列表
        $gc_table_name = TdGameCharas::model()-> tableName();
        $g_table_name = TdGame::model()-> tableName();
        $where = "gc.macro_count > 0 AND gc.is_delete = 0";
        $sql = "SELECT g.game_name, g.game_icon FROM {$gc_table_name} AS gc LEFT JOIN {$g_table_name} AS g ON gc.game_id = g.id WHERE {$where} GROUP BY gc.game_id ORDER BY gc.game_id DESC";

        $command = Yii::app ()->db->createCommand($sql);
        $data_list = $command->queryAll();
        if(!empty($data_list)){
            $game_list = array();
            foreach($data_list as $k => $game_info){
                $game_list[$k]['game_name'] = $game_info['game_name'];                          // 游戏名称
                $game_list[$k]['game_icon'] = $game_info['game_icon'];                          // 游戏图标
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'game_list' => $game_list                                                       // 游戏列表
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/getCharaList",
     *     summary="121.获取游戏角色列表",
     *     tags={"CharaMacro"},
     *     description="获取指定游戏下所有角色信息",
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
     *     @SWG\Response(
     *         response="200",
     *         description="成功时返回游戏角色列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="chara_list",
     *                 description="角色列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="chara_id",
     *                             description="角色id",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="chara_name",
     *                             description="角色名称",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="chara_image",
     *                             description="角色图像(大图)",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="download",
     *                             description="下载量",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="macro_count",
     *                             description="宏数量",
     *                             type="integer",
     *                         ),
     *                     ),
     *                 }
     *             ),
     *             @SWG\Property(
     *                 property="last_update",
     *                 description="最后更新时间",
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
    public function actionGetCharaList(){
        $game_name = Frame::getStringFromRequest('game_name');
        if(empty($game_name)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        $game_chara_model = new TdGameCharas();
        $where = "game_name = '{$game_name}' AND is_delete = 0";
        $data_list = $game_chara_model->getGameCharaList($where);
        if(!empty($data_list)){
            $chara_list = array();
            $update_time = array();
            foreach($data_list as $k => $chara_info){
                $chara_list[$k]['chara_id'] = $chara_info['chara_id'];                              // 角色id
                $chara_list[$k]['chara_name'] = $chara_info['chara_name'];                          // 角色名称
                $chara_list[$k]['chara_image'] = $chara_info['chara_image1'];                       // 角色图像(大图)
                $chara_list[$k]['download'] = $chara_info['download'];                              // 下载量
                $chara_list[$k]['macro_count'] = $chara_info['macro_count'];                        // 宏数量

                $update_time[] = $chara_info['update_time'];                                        // 更新时间
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'chara_list' => $chara_list,                                                        // 角色列表
                'last_update' => max($update_time),                                                 // 最后更新时间
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/getCharaInfo",
     *     summary="122.获取游戏角色信息",
     *     tags={"CharaMacro"},
     *     description="获取指定角色的信息和宏列表",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="chara_id",
     *         in="query",
     *         description="角色id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="成功时返回角色信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="chara_name",
     *                 description="角色名称",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="chara_desc",
     *                 description="角色描述",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="chara_image",
     *                 description="角色图像(小图)",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="macro_list",
     *                 description="宏列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="cmacro_id",
     *                             description="宏id",
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
    public function actionGetCharaInfo(){
        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        $game_chara_model = new TdGameCharas();
        $data_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($data_info)){
            $macro_list = array();
            if($data_info['macro_count'] > 0){
                // 获取宏信息
                $chara_macro_model = new TdCharaMacros();
                $where = "chara_id = {$chara_id} AND is_delete = 0";
                $data_list = $chara_macro_model->getCharaMacroList($where);
                if(!empty($data_list)){
                    foreach($data_list as $k => $macro_info){
                        $macro_list[$k]['cmacro_id'] = $macro_info['cmacro_id'];                    // 宏id
                        $macro_list[$k]['macro_name'] = $macro_info['macro_name'];                  // 宏名称
                        $macro_list[$k]['macro_content'] = $macro_info['macro_content'];            // 宏内容
                    }
                }
            }

            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'chara_name' => $data_info['chara_name'],                                           // 角色名称
                'chara_desc' => $data_info['chara_desc'],                                           // 角色描述
                'chara_image' => $data_info['chara_image2'],                                        // 角色图像(小图)
                'macro_list' => $macro_list,                                                        // 宏列表
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/downloadAllMacro",
     *     summary="123.下载全部角色宏",
     *     tags={"CharaMacro"},
     *     description="下载指定角色下的所有宏",
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
     *         name="chara_id",
     *         in="query",
     *         description="角色id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="成功时返回指定宏的列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="macro_list",
     *                 description="宏列表",
     *                 type="array",
     *                 items={
     *                     @SWG\Schema(
     *                         @SWG\Property(
     *                             property="macro_name",
     *                             description="宏名称",
     *                             type="integer",
     *                         ),
     *                         @SWG\Property(
     *                             property="macro_content",
     *                             description="宏内容",
     *                             type="string",
     *                         ),
     *                         @SWG\Property(
     *                             property="chara_image",
     *                             description="角色图像(小图)",
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
    public function actionDownloadAllMacro(){
        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 获取角色信息
        $game_chara_model = new TdGameCharas();
        $data_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($data_info) && $data_info['macro_count'] > 0){
            // 获取宏信息
            $chara_macro_model = new TdCharaMacros();
            $where = "chara_id = {$chara_id} AND is_delete = 0";
            $data_list = $chara_macro_model->getCharaMacroList($where);
            if(!empty($data_list)){
                // 创建事务
                $transaction = Yii::app()->db->beginTransaction();
                $flag = true;

                $macro_list = array();
                foreach($data_list as $k => $chara_macro_info){
                    $macro_list[$k]['macro_name'] = $chara_macro_info['macro_name'];                  // 宏名称
                    $macro_list[$k]['macro_content'] = $chara_macro_info['macro_content'];            // 宏内容
                    $macro_list[$k]['chara_image'] = $data_info['chara_image2'];                      // 角色图像(小图)

                    // 查询用户是否有下载过该角色的宏
                    $macro_model = new TdMacros();
                    $macro_where = "member_id = {$member_id} AND cmacro_id = {$chara_macro_info['cmacro_id']}";
                    $macro_info = $macro_model->getMacroInfo($macro_where);
                    if(!empty($macro_info)){
                        // 下载过该宏时进行数据覆盖
                        $macro_info->macro_name = $chara_macro_info['macro_name'];
                        $macro_info->macro_content = $chara_macro_info['macro_content'];
                        $macro_info->chara_image = $data_info['chara_image2'];
                        $macro_info->update_time = time();
                        if(!$macro_info->save()){
                            $flag = false;
                        }
                    }else{
                        // 保存用户下载的宏信息
                        $macro_model->member_id = $member_id;
                        $macro_model->cmacro_id = $chara_macro_info['cmacro_id'];
                        $macro_model->macro_name = $chara_macro_info['macro_name'];
                        $macro_model->macro_content = $chara_macro_info['macro_content'];
                        $macro_model->chara_image = $data_info['chara_image2'];
                        $macro_model->created_time = time();
                        if(!$macro_model->save()){
                            $flag = false;
                        }
                    }
                }

                // 下载量增加
                $data_info->download = $data_info['download'] + $data_info['macro_count'];
                if(!$data_info->save()){
                    $flag = false;
                }

                if($flag){
                    $transaction->commit();
                }else{
                    $transaction->rollback();
                }

                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'macro_list' => $macro_list,                                                // 宏列表
                );
            }else{
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => '没有该记录',
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
     *     path="/app_api/website/api.php/v2_2/charaMacro/downloadMacro",
     *     summary="124.下载角色宏",
     *     tags={"CharaMacro"},
     *     description="下载指定角色下的指定宏",
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
     *         name="chara_id",
     *         in="query",
     *         description="角色id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="cmacro_id",
     *         in="query",
     *         description="角色宏id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="成功时返回指定宏的信息",
     *         @SWG\Schema(
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
     *                 description="角色图像(小图)",
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
     *         response="309",
     *         description="没有该记录",
     *     ),
     * )
     */
    public function actionDownloadMacro(){
        $chara_id = Frame::getIntFromRequest('chara_id');
        $cmacro_id = Frame::getIntFromRequest('cmacro_id');
        if(empty($chara_id) || empty($cmacro_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = '参数输入不完整';
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user();
        $member_id = $member_info['member_id'];

        // 获取角色信息
        $game_chara_model = new TdGameCharas();
        $data_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($data_info) && $data_info['macro_count'] > 0){
            // 获取宏信息
            $chara_macro_model = new TdCharaMacros();
            $where = "cmacro_id = {$cmacro_id} AND is_delete = 0";
            $chara_macro_info = $chara_macro_model->getCharaMacroInfo($where);
            if(!empty($chara_macro_info)){
                // 创建事务
                $transaction = Yii::app()->db->beginTransaction();
                $flag = true;

                // 查询用户是否有下载过此角色宏
                $macro_model = new TdMacros();
                $macro_where = "member_id = {$member_id} AND cmacro_id = {$chara_macro_info['cmacro_id']}";
                $macro_info = $macro_model->getMacroInfo($macro_where);
                if(!empty($macro_info)){
                    // 下载过该宏时进行数据覆盖
                    $macro_info->macro_name = $chara_macro_info['macro_name'];
                    $macro_info->macro_content = $chara_macro_info['macro_content'];
                    $macro_info->chara_image = $data_info['chara_image2'];
                    $macro_info->update_time = time();
                    if(!$macro_info->save()){
                        $flag = false;
                    }
                }else{
                    // 保存当前下载的宏信息
                    $macro_model->member_id = $member_id;
                    $macro_model->cmacro_id = $chara_macro_info['cmacro_id'];
                    $macro_model->macro_name = $chara_macro_info['macro_name'];
                    $macro_model->macro_content = $chara_macro_info['macro_content'];
                    $macro_model->chara_image = $data_info['chara_image2'];
                    $macro_model->created_time = time();
                    if(!$macro_model->save()){
                        $flag = false;
                    }
                }

                // 下载量增加
                $data_info->download = $data_info['download'] + 1;
                if(!$data_info->save()){
                    $flag = false;
                }

                if($flag){
                    $transaction->commit();
                }else{
                    $transaction->rollback();
                }

                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'macro_name' => $chara_macro_info['macro_name'],                          // 宏名称
                    'macro_content' => $chara_macro_info['macro_content'],                    // 宏内容
                    'chara_image' => $data_info['chara_image2'],                              // 角色图像(小图)
                );
            }else{
                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => '没有该记录',
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

}