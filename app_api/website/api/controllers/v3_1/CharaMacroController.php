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
     *     path="/app_api/website/api.php/v3_1/charaMacro/saveGameChara",
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
     *         name="chara_nameEN",
     *         in="query",
     *         description="角色名称(英文)",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="chara_nameZHT",
     *         in="query",
     *         description="角色名称(繁体中文)",
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
     *         name="chara_descEN",
     *         in="query",
     *         description="角色描述(英文)",
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="chara_descZHT",
     *         in="query",
     *         description="角色描述(繁体中文)",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $game_name = Frame::getStringFromRequest('game_name');
        $game_icon = CUploadedFile::getInstanceByName('game_icon');
        $chara_name = Frame::getStringFromRequest('chara_name');
        $chara_nameEN = Frame::getStringFromRequest('chara_nameEN');
        $chara_nameZHT = Frame::getStringFromRequest('chara_nameZHT');
        $chara_desc = Frame::getStringFromRequest('chara_desc');
        $chara_descEN = Frame::getStringFromRequest('chara_descEN');
        $chara_descZHT = Frame::getStringFromRequest('chara_descZHT');
        $chara_image1 = CUploadedFile::getInstanceByName('chara_image1');
        $chara_image2 = CUploadedFile::getInstanceByName('chara_image2');
        if(empty($game_name) || empty($chara_name) || empty($chara_nameEN) || empty($chara_nameZHT)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
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
                        'ret_msg' => $this->language->get('upload_fail'),
                    );
                    echo json_encode($result);
                    die();
                }

                $game_info->game_icon = $game_icon_path;
                if(!$game_info->save()){
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => $this->language->get('update_fail'),
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
                if(!empty($chara_descEN)){
                    $game_chara_info->chara_descEN = $chara_descEN;
                }
                if(!empty($chara_descZHT)){
                    $game_chara_info->chara_descZHT = $chara_descZHT;
                }
                if(!empty($chara_image1)){
                    // 上传角色图像1
                    $chara_image1_path = $this->uploadPic($chara_image1);
                    if(!$chara_image1_path){
                        $result['ret_num'] = 908;
                        $result['ret_msg'] = $this->language->get('upload_fail');
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
                        $result['ret_msg'] = $this->language->get('upload_fail');
                        echo json_encode ( $result );
                        die ();
                    }

                    $game_chara_info->chara_image2 = $chara_image2_path;
                }
                $game_chara_info->update_time = time();
                
                if($game_chara_info->save()){
                    $result = array(
                        'ret_num' => 0,
                        'ret_msg' => 'ok',
                        'chara_id' => $game_chara_info['chara_id']
                    );
                }else{
                    $result = array(
                        'ret_num' => 906,
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
            }else{
                // 新增角色信息
                if(empty($chara_image1) || empty($chara_image2)){
                    // 新增时必须存在角色图像
                    $result['ret_num'] = 201;
                    $result['ret_msg'] = $this->language->get('miss_param');
                    echo json_encode($result);
                    die();
                }

                $game_chara_model->game_id = $game_info['id'];
                $game_chara_model->game_name = $game_info['game_name'];
                $game_chara_model->game_nameEN = $game_info['game_nameEN'];
                $game_chara_model->game_nameZHT = $game_info['game_nameZHT'];
                $game_chara_model->chara_name = $chara_name;
                $game_chara_model->chara_nameEN = $chara_nameEN;
                $game_chara_model->chara_nameZHT = $chara_nameZHT;
                $game_chara_model->chara_desc = $chara_desc;
                $game_chara_model->chara_descEN = $chara_descEN;
                $game_chara_model->chara_descZHT = $chara_descZHT;

                // 上传角色图像
                $chara_image1_path = $this->uploadPic($chara_image1);
                if(!$chara_image1_path){
                    $result = array(
                        'ret_num' => 908,
                        'ret_msg' => $this->language->get('upload_fail'),
                    );
                    echo json_encode($result);
                    die();
                }
                $chara_image2_path = $this->uploadPic($chara_image2);
                if(!$chara_image2_path){
                    $result = array(
                        'ret_num' => 908,
                        'ret_msg' => $this->language->get('upload_fail'),
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
                        'ret_msg' => $this->language->get('save_fail'),
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
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v3_1/charaMacro/deleteGameChara",
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
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionDeleteGameChara(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
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
                    'ret_msg' => $this->language->get('update_fail'),
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
     * @SWG\Post(
     *     path="/app_api/website/api.php/v3_1/charaMacro/saveCharaMacro",
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
     *         name="macro_nameEN",
     *         in="query",
     *         description="宏名称(英文)",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="macro_nameZHT",
     *         in="query",
     *         description="宏名称(繁体中文)",
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
     *         response="210",
     *         description="上传文件格式不正确",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $chara_id = Frame::getIntFromRequest('chara_id');
        $macro_name = Frame::getStringFromRequest('macro_name');
        $macro_nameEN = Frame::getStringFromRequest('macro_nameEN');
        $macro_nameZHT = Frame::getStringFromRequest('macro_nameZHT');
        $macro_content_file = CUploadedFile::getInstanceByName('macro_content');
        if(empty($chara_id) || empty($macro_name) || empty($macro_nameEN) || empty($macro_nameZHT) || empty($macro_content_file)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }
        $file_type = explode('.', $macro_content_file->name);
        if($file_type[1] != "mcr" && $file_type[1] != "cms"){
            $result['ret_num'] = 210;
            $result['ret_msg'] = $this->language->get('wrong_format');
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
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
            }else{
                // 创建事务
                $transaction = Yii::app()->db->beginTransaction();
                $flag = true;

                // 新增宏信息
                $chara_macro_model->chara_id = $chara_id;
                $chara_macro_model->chara_name = $game_chara_info['chara_name'];
                $chara_macro_model->chara_nameEN = $game_chara_info['chara_nameEN'];
                $chara_macro_model->chara_nameZHT = $game_chara_info['chara_nameZHT'];
                $chara_macro_model->macro_name = $macro_name;
                $chara_macro_model->macro_nameEN = $macro_nameEN;
                $chara_macro_model->macro_nameZHT = $macro_nameZHT;
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
                        'ret_msg' => $this->language->get('save_fail'),
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
     * @SWG\Delete(
     *     path="/app_api/website/api.php/v3_1/charaMacro/deleteCharaMacro",
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
     *         response="906",
     *         description="更新信息时发生错误",
     *     ),
     * )
     */
    public function actionDeleteCharaMacro(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $cmacro_id = Frame::getIntFromRequest('cmacro_id');
        if(empty($cmacro_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
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
                if($game_chara_info['macro_count'] > 0){
                    $game_chara_info->macro_count = $game_chara_info['macro_count'] - 1;
                }
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
                        'ret_msg' => $this->language->get('update_fail'),
                    );
                }
            }else{
                $transaction->rollback();

                $result = array(
                    'ret_num' => 309,
                    'ret_msg' => $this->language->get('no_data'),
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
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/charaMacro/getGameList",
     *     summary="120.获取游戏列表",
     *     tags={"CharaMacro"},
     *     description="获取有角色宏的游戏列表",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 查询有宏的游戏列表
        $gc_table_name = TdGameCharas::model()-> tableName();
        $g_table_name = TdGame::model()-> tableName();
        $where = "gc.macro_count > 0 AND gc.is_delete = 0";
        // 根据语言参数获取对应语种的游戏名称 2017/05/12
        if(!empty($lang) && $lang != "ZH"){
            $fields = "g.game_name{$lang} AS game_name, g.game_icon";
        }else{
            $fields = "g.game_name, g.game_icon";
        }
        $sql = "SELECT {$fields} FROM {$gc_table_name} AS gc LEFT JOIN {$g_table_name} AS g ON gc.game_id = g.id WHERE {$where} GROUP BY gc.game_id ORDER BY g.sort ASC";

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

            pr($result);
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
     *     path="/app_api/website/api.php/v3_1/charaMacro/getCharaList",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $game_name = Frame::getStringFromRequest('game_name');
        if(empty($game_name)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        $game_chara_model = new TdGameCharas();
        // 根据语言参数获取对应语种的角色列表 2017/05/12
        if(!empty($lang) && $lang != "ZH"){
            $where = "game_name{$lang} = '{$game_name}' AND is_delete = 0";
            $fields = "chara_id, chara_name{$lang} AS chara_name, chara_image1, download, macro_count, update_time";
        }else{
            $where = "game_name = '{$game_name}' AND is_delete = 0";
            $fields = "chara_id, chara_name, chara_image1, download, macro_count, update_time";
        }
        $data_list = $game_chara_model->getGameCharaList($where, $fields, 1000, 0, 'created_time ASC');
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
                'ret_msg' => $this->language->get('no_data'),
            );
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/charaMacro/getCharaInfo",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
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
                // 根据语言参数获取对应字段 2017/05/12
                if(!empty($lang) && $lang != "ZH"){
                    $fields = "cmacro_id, macro_name{$lang} AS macro_name, macro_content";
                }else{
                    $fields = "cmacro_id, macro_name, macro_content";
                }
                $data_list = $chara_macro_model->getCharaMacroList($where, $fields);
                if(!empty($data_list)){
                    foreach($data_list as $k => $macro_info){
                        $macro_list[$k]['cmacro_id'] = $macro_info['cmacro_id'];                    // 宏id
                        $macro_list[$k]['macro_name'] = $macro_info['macro_name'];                  // 宏名称
                        $macro_list[$k]['macro_content'] = $macro_info['macro_content'];            // 宏内容
                    }
                }
            }

            // 根据语言参数显示对应语种的内容 2017/05/12
            if(!empty($lang) && $lang != "ZH") {
                $chara_name = $data_info['chara_name'.$lang];
                $chara_desc = $data_info['chara_desc'.$lang];
            }else{
                $chara_name = $data_info['chara_name'];
                $chara_desc = $data_info['chara_desc'];
            }
            $result = array(
                'ret_num' => 0,
                'ret_msg' => 'ok',
                'chara_name' => $chara_name,                                                        // 角色名称
                'chara_desc' => $chara_desc,                                                        // 角色描述
                'chara_image' => $data_info['chara_image2'],                                        // 角色图像(小图)
                'macro_list' => $macro_list,                                                        // 宏列表
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
     *     path="/app_api/website/api.php/v3_1/charaMacro/downloadAllMacro",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $chara_id = Frame::getIntFromRequest('chara_id');
        if(empty($chara_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获取角色信息
        $game_chara_model = new TdGameCharas();
        $data_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($data_info) && $data_info['macro_count'] > 0){
            // 获取宏信息
            $chara_macro_model = new TdCharaMacros();
            $where = "chara_id = {$chara_id} AND is_delete = 0";
            // 根据语言参数获取对应字段 2017/05/12
            if(!empty($lang) && $lang != "ZH"){
                $fields = "cmacro_id, macro_name{$lang} AS macro_name, macro_content";
            }else{
                $fields = "cmacro_id, macro_name, macro_content";
            }
            $data_list = $chara_macro_model->getCharaMacroList($where, $fields);
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
                    'ret_msg' => $this->language->get('no_data'),
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
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/charaMacro/downloadMacro",
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
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);
        
        $chara_id = Frame::getIntFromRequest('chara_id');
        $cmacro_id = Frame::getIntFromRequest('cmacro_id');
        if(empty($chara_id) || empty($cmacro_id)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获取角色信息
        $game_chara_model = new TdGameCharas();
        $data_info = $game_chara_model->getGameCharaById($chara_id);
        if(!empty($data_info) && $data_info['macro_count'] > 0){
            // 获取宏信息
            $chara_macro_model = new TdCharaMacros();
            $where = "cmacro_id = {$cmacro_id} AND is_delete = 0";
            // 根据语言参数获取对应字段 2017/05/12
            if(!empty($lang) && $lang != "ZH"){
                $fields = "cmacro_id, macro_name{$lang} AS macro_name, macro_content";
            }else{
                $fields = "cmacro_id, macro_name, macro_content";
            }
            $chara_macro_info = $chara_macro_model->getCharaMacroInfo($where, $fields);
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
                    'ret_msg' => $this->language->get('no_data'),
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

}