<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 16:20
 */
class UserController extends PublicController{

    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/user/login",
     *     summary="1.用户登录",
     *     tags={"User"},
     *     description="用户登录,返回用户信息",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="key",
     *         in="query",
     *         description="终端类型,iphone,android,pc",
     *         required=true,
     *         type="string",
     *		   default="pc",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="member_name",
     *         in="query",
     *         description="用户名",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="member_passwd",
     *         in="query",
     *         description="用户密码",
     *         required=true,
     *         type="string",
     *		   format="password",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="terminal_code",
     *         in="query",
     *         description="终端标识",
     *         required=true,
     *         type="string",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="app_key",
     *         in="query",
     *         description="软件标识",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *         default="448321",
     *         enum={"448321", "953033", "760267"}
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
     *         description="成功时返回用户信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     * 			       property="user",
     *                 description="用户信息",
     *                 type="array",
     *				   ref="#/definitions/User"
     *			   ),
     *		   )
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="参数输入不完整",
     *     ),
     *     @SWG\Response(
     *         response="208",
     *         description="密码不符合",
     *     ),
     *     @SWG\Response(
     *         response="302",
     *         description="用户名或密码错误",
     *     ),
     *     @SWG\Response(
     *         response="340",
     *         description="没有该应用软件信息",
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
    public function actionLogin() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $member_name = Frame::getStringFromRequest('member_name');
        $member_passwd = Frame::getStringFromRequest('member_passwd');
        $terminal_code = Frame::getStringFromRequest('terminal_code');
        $app_key = Frame::getStringFromRequest('app_key');
        if(empty($member_name) || empty($member_passwd) || empty($terminal_code) || empty($app_key)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        if (!$this->checkpwd($member_passwd)){
            $result['ret_num'] = 208;
            $result['ret_msg'] = $this->language->get('incorrect_pwd');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_model = new TaidushopMember();
        $password = md5($member_passwd);
        $member_where = "(member_name = '{$member_name}' OR member_mobile = '{$member_name}' OR member_email = '{$member_name}') AND member_passwd = '{$password}'" ;
        $member_info = $member_model->getMemberInfo($member_where);
        if(empty($member_info)){
            $result['ret_num'] = 302;
            $result['ret_msg'] = $this->language->get('login_fail');
            echo json_encode($result);
            die();
        }

        // 获取应用软件信息
        $app_model = new TdApps();
        $app_where = "app_key = '{$app_key}'";
        $app_info = $app_model->getAppInfo($app_where);
        if(empty($app_info)){
            $result['ret_num'] = 340;
            $result['ret_msg'] = $this->language->get('wrong_app_info');
            echo json_encode($result);
            die();
        }

        $member_id = $member_info['member_id'];
        $app_id = $app_info['app_id'];
        // 计算登录时间和登录失效时间
        $login_time = time();
        $invalid_time = $login_time + 3600 * 24 * $app_info['limit_days'];

        // 检查是否存有该用户在此终端的情报
        $terminal_model = new TdUserTerminals();
        $terminal_where = "terminal_code = '{$terminal_code}' AND member_id = {$member_id}";
        $terminal_info = $terminal_model->getUserTerminalInfo($terminal_where);
        if(empty($terminal_info)){
            // 保存用户在此终端的情报用于快捷登录
            $key = Frame::getStringFromRequest('key');
            switch ($key){
                case "iphone":
                    $terminal_type = 3;
                    break;
                case "android":
                    $terminal_type = 2;
                    break;
                default:
                    $terminal_type = 1;
            }

            $terminal_model->terminal_code = $terminal_code;
            $terminal_model->member_id = $member_id;
            $terminal_model->terminal_type = $terminal_type;
            $terminal_model->created_time = $login_time;
            if(!$terminal_model->save()){
                $result['ret_num'] = 905;
                $result['ret_msg'] = $this->language->get('save_fail');
                echo json_encode($result);
                die();
            }
        }

        // 检查是否存在该用户在该应用上的情报
        $app_user_model = new TdAppUsers();
        $app_user_where = "app_id = {$app_id} AND member_id = {$member_id}";
        $app_user_info = $app_user_model->getAppUserInfo($app_user_where);
        // 生成登录标识
        $open_id = md5( uniqid( $member_id.'_'.time(), true) );
        if(!empty($app_user_info)){
            // 更新数据
            $app_user_info->open_id = $open_id;
            $app_user_info->login_time = $login_time;
            $app_user_info->invalid_time = $invalid_time;
            if(!$app_user_info->save()){
                $result['ret_num'] = 906;
                $result['ret_msg'] = $this->language->get('update_fail');
                echo json_encode($result);
                die();
            }
        }else{
            // 新增数据
            $app_user_model->app_id = $app_id;
            $app_user_model->member_id = $member_id;
            $app_user_model->open_id = $open_id;
            $app_user_model->login_time = $login_time;
            $app_user_model->invalid_time = $invalid_time;
            if(!$app_user_model->save()){
                $result['ret_num'] = 905;
                $result['ret_msg'] = $this->language->get('save_fail');
                echo json_encode($result);
                die();
            }
        }

        // 返回用户情报
        $member_avatar = '';
        if ($member_info->member_avatar) {
            if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
                $member_avatar = $member_info->member_avatar;
            } else {
                $member_avatar = $this->webroot () . $member_info->member_avatar;
            }
        }
        $result = array(
            'ret_num' => 0,
            'ret_msg' => 'ok',
            'user' => array(
                "memberid" => $member_info->member_id,
                "username" => $member_info->member_name,
                "realname" => $member_info->member_truename,
                'mobile' => $member_info->member_mobile,
                'address' => $this->eraseNull ( $member_info->address ),
                'email' => $this->eraseNull ( $member_info->member_email ),
                'usertype' => $member_info->usertype,
                'sex' => $member_info->member_sex,
                'birthday' => $this->eraseNull ( $member_info->member_birthday ),
                'regtime' => $member_info->member_time,
                'header' => $member_avatar,
                'game' => $member_info->game,
                'regip' => $_SERVER ['SERVER_ADDR'],
                'open_id' => $open_id,
                'updateTime' => $login_time,
                'level' => 2
            )
        );

        echo json_encode($result);
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/user/getUserTerminalList",
     *     summary="107.获取终端用户列表",
     *     tags={"User"},
     *     description="根据终端标识,获取使用该终端登录过的用户列表",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="key",
     *         in="query",
     *         description="终端类型,iphone,android,pc",
     *         required=true,
     *         type="string",
     *		   default="pc",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *     ),
     *     @SWG\Parameter(
     *         name="terminal_code",
     *         in="query",
     *         description="终端标识",
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
     *         description="成功时返回用户列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     * 			       property="member_list",
     *                 description="用户列表",
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
     *                             property="member_avatar",
     *                             description="用户头像",
     *                             type="string",
     *                         ),
     *                     ),
     *                 }
     *			   ),
     *		   )
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
    public function actionGetUserTerminalList(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $terminal_code = Frame::getStringFromRequest('terminal_code');
        if(empty($terminal_code)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        $key = Frame::getStringFromRequest('key');
        switch ($key){
            case "iphone":
                $terminal_type = 3;
                break;
            case "android":
                $terminal_type = 2;
                break;
            default:
                $terminal_type = 1;
        }

        // 获取该终端下的用户列表
        $user_terminal_model = new TdUserTerminals();
        $user_terminal_where = "terminal_code = '{$terminal_code}' and terminal_type = {$terminal_type}";
        $user_terminal_list = $user_terminal_model->getUserTerminalList($user_terminal_where);
        if(!empty($user_terminal_list)){
            // 获取用户id集合
            $member_id_arr = array();
            foreach($user_terminal_list as $k => $user_terminal_info){
                $member_id_arr[] = $user_terminal_info['member_id'];
            }
            $member_id_str = implode(",", $member_id_arr);

            // 根据用户列表获取用户信息
            $member_model = new TaidushopMember();
            $member_where = "member_id in ({$member_id_str})";
            $data_list = $member_model->getMemberList($member_where, "member_id, member_name, member_avatar");

            if(!empty($data_list)){
                $member_list = array();
                foreach($data_list as $k => $member_info){
                    // 获取用户头像路径地址
                    $member_avatar = "";
                    if (!empty($member_info['member_avatar'])) {
                        if (strpos ( $member_info['member_avatar'], "http://" ) === 0) {
                            $member_avatar = $member_info['member_avatar'];
                        } else {
                            $member_avatar = $this->webroot () . $member_info['member_avatar'];
                        }
                    }

                    $member_list[$k]['member_id'] = $member_info['member_id'];              // 用户id
                    $member_list[$k]['member_name'] = $member_info['member_name'];          // 用户名
                    $member_list[$k]['member_avatar'] = $member_avatar;                     // 用户头像
                }

                $result = array(
                    'ret_num' => 0,
                    'ret_msg' => 'ok',
                    'member_list' => $member_list
                );
            }else{
                $result['ret_num'] = 309;
                $result['ret_msg'] = $this->language->get('no_data');
            }
        }else{
            $result['ret_num'] = 309;
            $result['ret_msg'] = $this->language->get('no_data');
        }

        echo json_encode($result);
    }


    /**
     * @SWG\Get(
     *     path="/app_api/website/api.php/v3_1/user/quickLogin",
     *     summary="108.快捷登录",
     *     tags={"User"},
     *     description="根据用户id进行快捷登录,返回用户信息",
     *     operationId="",
     *     consumes={"application/xml", "application/json"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="member_id",
     *         in="query",
     *         description="用户id",
     *         required=true,
     *         type="integer",
     *         @SWG\Items(type="integer"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Parameter(
     *         name="app_key",
     *         in="query",
     *         description="软件标识",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi",
     *         default="448321",
     *         enum={"448321", "953033", "760267"}
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
     *         description="成功时返回用户信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     * 			       property="user",
     *                 description="用户信息",
     *                 type="array",
     *				   ref="#/definitions/User"
     *			   ),
     *		   )
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
     *         response="340",
     *         description="没有该应用软件信息",
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
    public function actionQuickLogin(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $member_id = Frame::getIntFromRequest('member_id');
        $app_key = Frame::getStringFromRequest('app_key');
        if(empty($member_id) || empty($app_key)){
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode($result);
            die();
        }

        // 获取用户信息
        $member_model = new TaidushopMember();
        $member_where = "member_id = {$member_id}" ;
        $member_info = $member_model->getMemberInfo($member_where);
        if(empty($member_info)){
            $result['ret_num'] = 309;
            $result['ret_msg'] = $this->language->get('no_data');
            echo json_encode($result);
            die();
        }

        // 获取应用软件信息
        $app_model = new TdApps();
        $app_where = "app_key = '{$app_key}'";
        $app_info = $app_model->getAppInfo($app_where);
        if(empty($app_info)){
            $result['ret_num'] = 340;
            $result['ret_msg'] = $this->language->get('wrong_app_info');
            echo json_encode($result);
            die();
        }

        $app_id = $app_info['app_id'];
        // 计算登录时间和登录失效时间
        $login_time = time();
        $invalid_time = $login_time + 3600 * 24 * $app_info['limit_days'];

        // 检查是否存在该用户在该应用上的情报
        $app_user_model = new TdAppUsers();
        $app_user_where = "app_id = {$app_id} AND member_id = {$member_id}";
        $app_user_info = $app_user_model->getAppUserInfo($app_user_where);
        // 生成登录标识
        $open_id = md5( uniqid( $member_id.'_'.time(), true) );
        if(!empty($app_user_info)){
            // 更新数据
            $app_user_info->open_id = $open_id;
            $app_user_info->login_time = $login_time;
            $app_user_info->invalid_time = $invalid_time;
            if(!$app_user_info->save()){
                $result['ret_num'] = 906;
                $result['ret_msg'] = $this->language->get('update_fail');
                echo json_encode($result);
                die();
            }
        }else{
            // 新增数据
            $app_user_model->app_id = $app_id;
            $app_user_model->member_id = $member_id;
            $app_user_model->open_id = $open_id;
            $app_user_model->login_time = $login_time;
            $app_user_model->invalid_time = $invalid_time;
            if(!$app_user_model->save()){
                $result['ret_num'] = 905;
                $result['ret_msg'] = $this->language->get('save_fail');
                echo json_encode($result);
                die();
            }
        }

        // 返回用户情报
        $member_avatar = '';
        if ($member_info->member_avatar) {
            if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
                $member_avatar = $member_info->member_avatar;
            } else {
                $member_avatar = $this->webroot () . $member_info->member_avatar;
            }
        }
        $result = array(
            'ret_num' => 0,
            'ret_msg' => 'ok',
            'user' => array(
                "memberid" => $member_info->member_id,
                "username" => $member_info->member_name,
                "realname" => $member_info->member_truename,
                'mobile' => $member_info->member_mobile,
                'address' => $this->eraseNull ( $member_info->address ),
                'email' => $this->eraseNull ( $member_info->member_email ),
                'usertype' => $member_info->usertype,
                'sex' => $member_info->member_sex,
                'birthday' => $this->eraseNull ( $member_info->member_birthday ),
                'regtime' => $member_info->member_time,
                'header' => $member_avatar,
                'game' => $member_info->game,
                'regip' => $_SERVER ['SERVER_ADDR'],
                'open_id' => $open_id,
                'updateTime' => $login_time,
                'level' => 2
            )
        );

        echo json_encode($result);
    }


    /*
     * 退出登录
     */
    public function actionLogout() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);

        include_once '../../includes/bbs/config/config_ucenter.php';
        include_once '../../includes/bbs/uc_client/client.php';

        $time = time () - 3600;
        setcookie ( "username", '', $time, '/' );
        setcookie ( "user_id", '', $time, '/' );
        setcookie ( "td_bbs_login", '', $time, '/' );

        $ret = uc_user_synlogout ();

        $_SESSION ['td_bbs_logout'] = $ret;

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';

        echo json_encode ( $result );
    }


    /**
     * 取得用户信息
     */
    public function actionGetUserInfo() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 获取游戏信息
        $userGames = TdUserGame::model ()->findAll ( "userid='{$member_id}'" );
        $arr = array();
        if ($userGames) {
            $games = TdGame::model ()->findAll ();
            foreach ( $userGames as $key => $value ) {
                foreach ( $games as $key1 => $value1 ) {
                    if ($value1->id == $value->game_id) {
                        $arr [] = array (
                            "game_type" => $value1['game_type'],
                            "game_name" => (!empty($lang) && $lang != "ZH") ? $value1["game_name{$lang}"] : $value1["game_name"],
                            "proficiency" => $value['proficiency'],
                        );

                        break;
                    }
                }
            }
        }

        if ($member_info->member_avatar) {
            if (strpos ( $member_info->member_avatar, "http://" ) === 0) {
                $headImg = $member_info->member_avatar;
            } else {
                $headImg = $this->webroot () . $member_info->member_avatar;
            }
        }else{
            $headImg = "";
        }

        // 获取职业信息
        $profession = "";
        $professions = TdProfession::model ()->findAll ();
        if ($professions) {
            foreach ( $professions as $key => $value ) {
                if ($value->id == $member_info->profession_id) {
                    // 根据语言参数不同保存对应语种内容 2017/05/12
                    $profession = (!empty($lang) && $lang != "ZH") ? $value['profession'.$lang] : $value['profession'];
                    break;
                }
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['user'] = array (
            "memberid" => $member_id,
            "username" => $member_info->member_name,
            "realname" => $member_info->member_truename,
            'mobile' => $member_info->member_mobile,
            'address' => $this->eraseNull ( $member_info->address ),
            'email' => $this->eraseNull ( $member_info->member_email ),
            'usertype' => $member_info->usertype,
            'sex' => $member_info->member_sex,
            'birthday' => $this->eraseNull ( $member_info->member_birthday ),
            'header' => $headImg,
            'height' => $member_info->height,
            'weight' => $member_info->weight,
            'gameInfo' => $arr,
            'profession' => $profession,
            'married_type' => $member_info->married_type,
            'interest' => $member_info->interest,
            'vision' => $member_info->vision,
            'regtime' => $member_info->member_time
        );

        echo json_encode ( $result );
    }


    /**
     * 取得所有职业
     */
    public function actionGetProfessions() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $arr = array();
        $professions = TdProfession::model ()->findAll ();
        if ($professions) {
            foreach ( $professions as $key => $value ) {
                // 根据语言参数不同保存对应语种内容 2017/05/12
                $arr [] = array (
                    "id" => $value->id,
                    "profession" => (!empty($lang) && $lang != "ZH") ? $value['profession'.$lang] : $value['profession']
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['professions'] = $arr;

        echo json_encode ( $result );
    }


    /**
     * 取得所有问题列表
     */
    public function actionGetFeedbackList() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $arr = array();
        $feedbacks = TdFeedback::model ()->findAll ( "userid={$member_id} " );
        if ($feedbacks) {
            foreach ( $feedbacks as $key => $value ) {
                if ($value->image1) {
                    if (strpos ( $value->image1, "http://" ) === 0)
                        $questionImg1 = $value->image1;
                    else
                        $questionImg1 = $this->webroot () . $value->image1;
                } else {
                    $questionImg1 = "";
                }
                if ($value->image2) {
                    if (strpos ( $value->image2, "http://" ) === 0)
                        $questionImg2 = $value->image2;
                    else
                        $questionImg2 = $this->webroot () . $value->image2;
                } else {
                    $questionImg2 = "";
                }
                if ($value->image3) {
                    if (strpos ( $value->image3, "http://" ) === 0)
                        $questionImg3 = $value->image3;
                    else
                        $questionImg3 = $this->webroot () . $value->image3;
                } else {
                    $questionImg3 = "";
                }

                $arr [] = array (
                    "id" => $value->id,
                    "question" => $value->question,
                    "question_type" => $value->question_type,
                    "image1" => $questionImg1,
                    "image2" => $questionImg2,
                    "image3" => $questionImg3,
                    "created_time" => $value->created_time
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['feedbacks'] = $arr;

        echo json_encode ( $result );
    }

    /**
     * 问题反馈
     */
    public function actionQuestionFeedback() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $questionType = Frame::getStringFromRequest ( 'questionType' );
        $question = Frame::getStringFromRequest ( 'question' );
        $image1 = CUploadedFile::getInstanceByName ( 'image1' );
        $image2 = CUploadedFile::getInstanceByName ( 'image2' );
        $image3 = CUploadedFile::getInstanceByName ( 'image3' );
        if (empty ( $questionType ) || empty ( $question )) {
            $result['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 校验图片后缀名
        if ($image1) {
            $arr_img = array (
                "bmp",
                "jpg",
                "jpeg",
                "gif",
                "png"
            );
            if (! in_array ( strtolower ( $image1->getExtensionName () ), $arr_img )) {
                $result ['ret_num'] = 210;
                $result['ret_msg'] = $this->language->get('wrong_format');
                echo json_encode ( $result );
                die ();
            }
        }
        if ($image2) {
            $arr_img = array (
                "bmp",
                "jpg",
                "jpeg",
                "gif",
                "png"
            );
            if (! in_array ( strtolower ( $image2->getExtensionName () ), $arr_img )) {
                $result ['ret_num'] = 210;
                $result['ret_msg'] = $this->language->get('wrong_format');
                echo json_encode ( $result );
                die ();
            }
        }
        if ($image3) {
            $arr_img = array (
                "bmp",
                "jpg",
                "jpeg",
                "gif",
                "png"
            );
            if (! in_array ( strtolower ( $image3->getExtensionName () ), $arr_img )) {
                $result ['ret_num'] = 210;
                $result['ret_msg'] = $this->language->get('wrong_format');
                echo json_encode ( $result );
                die ();
            }
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        $path1 = $path2 = $path3 ="";
        if ($image1) {
            // 上传图片
            $path1 = $this->uploadPic ( $image1 );
            if (! $path1) {
                $result ['ret_num'] = 908;
                $result['ret_msg'] = $this->language->get('upload_fail');
                echo json_encode ( $result );
                die ();
            }
        }
        if ($image2) {
            // 上传图片
            $path2 = $this->uploadPic ( $image2 );
            if (! $path2) {
                $result ['ret_num'] = 908;
                $result['ret_msg'] = $this->language->get('upload_fail');
                echo json_encode ( $result );
                die ();
            }
        }
        if ($image3) {
            // 上传图片
            $path3 = $this->uploadPic ( $image3 );
            if (! $path3) {
                $result ['ret_num'] = 908;
                $result['ret_msg'] = $this->language->get('upload_fail');
                echo json_encode ( $result );
                die ();
            }
        }

        $model = new TdFeedback ();
        $model->userid = $member_id;
        $model->question_type = $questionType;
        $model->question = $question;
        $model->image1 = $path1;
        $model->image2 = $path2;
        $model->image3 = $path3;
        $model->created_time = time ();
        if ($model->save ()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = 'ok';
        } else {
            $result ['ret_num'] = 905;
            $result['ret_msg'] = $this->language->get('save_fail');
        }

        echo json_encode ( $result );
    }

    /**
     * 取得所有消息
     */
    public function actionGetMessageList() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 取得当前月份所有测试记录
        $arr = array();
        $db = Yii::app ()->db;
        $sql = "select m.id, m.title, m.message, m.message_type,m.send_userid,m.send_username,m.is_read,m.created_time,
				 (select member_avatar from taidushop_member where member_id=m.userid) as header
		from td_message m
		where userid={$member_id} 
		order by created_time desc";
        $command = $db->createCommand ( $sql );
        $messages = $command->queryAll ();
        if ($messages) {
            foreach ( $messages as $key => $value ) {
                $arr [] = array (
                    "message_id" => $value ['id'],
                    "title" => $value ['title'],
                    "message" => $value ['message'],
                    "message_type" => $value ['message_type'],
                    "send_userid" => $value ['send_userid'],
                    "send_username" => $value ['send_username'],
                    "is_read" => $value ['is_read'],
                    "created_time" => $value ['created_time']
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['messages'] = $arr;

        echo json_encode ( $result );
    }

    /**
     * 取得消息详情
     */
    public function actionGetMessageDetail() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $messageId = Frame::getIntFromRequest ( 'messageId' );
        if (empty ( $messageId )) {
            $result ['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }

        // 获取用户信息
        $member_info = $this->check_user($lang);

        // 取得当前月份所有测试记录
        $arr = array();
        $db = Yii::app ()->db;
        $sql = "select m.title, m.message, m.message_type,m.send_userid,m.send_username,m.is_read,m.created_time,
		(select member_avatar from taidushop_member where member_id=m.userid) as header
		from td_message m
		where m.id={$messageId}
		order by m.created_time desc";
        $command = $db->createCommand ( $sql );
        $message = $command->query ();
        if ($message) {
            foreach ( $message as $row ) {
                $arr = array (
                    "title" => $row ['title'],
                    "message" => $row ['message'],
                    "message_type" => $row ['message_type'],
                    "send_userid" => $row ['send_userid'],
                    "send_username" => $row ['send_username'],
                    "created_time" => $row ['created_time']
                );

                // 更新消息为已读
                $row = Yii::app ()->getDb ()->createCommand ()->update ( 'td_message', array (
                    'is_read' => 1
                ), "id=:id", array (
                    ':id' => $messageId
                ) );
            }
        } else {
            $arr = null;
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['message'] = $arr;

        echo json_encode ( $result );
    }

    /**
     * 取得未读消息数
     */
    public function actionGetNonReadMessagesCount() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        // 获取用户信息
        $member_info = $this->check_user($lang);
        $member_id = $member_info['member_id'];

        // 取得当前月份所有测试记录
        $count = TdMessage::model ()->count ( "userid={$member_id} and is_read=0" );
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'ok';
        $result ['message_count'] = $count;

        echo json_encode ( $result );
    }



    /**
    获取邮箱验证码
     */
    public function actionEMailVerify(){
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        $email = Frame::getStringFromRequest('email');
        $type = Frame::getStringFromRequest('type');
        //判断是否为空
        if (empty ( $email ) || empty ( $type )) {
            $result ['ret_num'] = 201;
            $result['ret_msg'] = $this->language->get('miss_param');
            echo json_encode ( $result );
            die ();
        }
        
        // 判断邮箱是否存在
        $email_exist = TaidushopMember::model ()->find("member_email='{$email}'");
        if ($email_exist == null){
            // 验证码
            $code = rand(100000, 999999);

            // 成功生成验证码后加入数据库
            $verify_model = new TdVerify ();
            $verify_model->phone = $email;
            $verify_model->verify = $code;
            $verify_model->created_time = time ();
            $verify_model->type = $type;
            $verify_model->save ();

            $message = new YiiMailMessage();
            // 邮件发送人
            $sendUser = Yii::app()->components['mail']->transportOptions['username'];
            $message->setFrom(array($sendUser));
            // 邮件接收人
            $message->setTo(array($email));
            // 邮件标题
            $message->setSubject($this->language->get('verify_mail_subject'));
            // 邮件正文
            $time = date("Y-m-d H:i:s");
            $msg = $this->language->get('verify_mail_content');
            $msg = str_replace('{$time}', $time, $msg);
            $msg = str_replace('{$code}', $code, $msg);
            $message->setBody($msg);
            // 发送邮件
            $sendmail = Yii::app()->mail->send($message);
            if ($sendmail) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = 'ok';
            } else {
                $result['ret_msg'] = $this->language->get('send_mail_fail');
            }
        } else {
            $result ['ret_num'] = 303;
            $result['ret_msg'] = $this->language->get('email_used');
        }

        echo json_encode ( $result );
    }



    /**
     * 用户注册
     */
    public function actionRegister() {
        $lang = Frame::getStringFromRequest('lang');
        // 获取语言文件
        $this->language->read($lang);

        if ((Yii::app ()->request->isPostRequest)) {
            $this->check_key ();
            $key = Frame::getStringFromRequest ( 'key' );
            $username = Frame::getStringFromRequest ( 'username' );
            $email = Frame::getStringFromRequest ( 'email' );
            $phone = Frame::getStringFromRequest ( 'phone' );
            $birthday = Frame::getStringFromRequest ( 'birthday' );
            $sex = Frame::getIntFromRequest ( 'sex' );
            $pwd = Frame::getStringFromRequest ( 'password' );
            $repwd = Frame::getStringFromRequest ( 'repassword' );
            // 头像
            $image = CUploadedFile::getInstanceByName ( 'header' );
            // 职业id
            $professionId = Frame::getIntFromRequest ( 'professionId' );
            // 婚姻状况
            $marriedType = Frame::getIntFromRequest ( 'marriedType' );
            // 所在地
            $address = Frame::getStringFromRequest ( 'address' );

            // 判断是否填写完整
            if (empty ( $username ) || empty ( $pwd ) || empty ( $repwd ) || (empty ( $phone ) && empty($email)) ) {
                $result ['ret_num'] = 201;
                $result['ret_msg'] = $this->language->get('miss_param');
                echo json_encode ( $result );
                die ();
            }
            // 判断俩次密码输入是否一致
            if ($pwd != $repwd) {
                $result ['ret_num'] = 202;
                $result['ret_msg'] = $this->language->get('not_identical');
                echo json_encode ( $result );
                die ();
            }
            if ( !empty ( $phone ) && ! $this->checkphone ( $phone )) {
                $result ['ret_num'] = 207;
                $result['ret_msg'] = $this->language->get('incorrect_mobile');
                echo json_encode ( $result );
                die ();
            }
            if (! $this->checkpwd ( $pwd )) {
                $result ['ret_num'] = 208;
                $result['ret_msg'] = $this->language->get('incorrect_pwd');
                echo json_encode ( $result );
                die ();
            }

            // 校验图片后缀名
            if ($image) {
                $arr_img = array (
                    "bmp",
                    "jpg",
                    "jpeg",
                    "gif",
                    "png"
                );
                if (! in_array ( strtolower ( $image->getExtensionName () ), $arr_img )) {
                    $result ['ret_num'] = 210;
                    $result['ret_msg'] = $this->language->get('wrong_format');
                    echo json_encode ( $result );
                    die ();
                }
            }

            // 判断手机号有没有注册过
            if(!empty($phone)){
                $re = TaidushopMember::model ()->find ( "member_mobile = {$phone}" );
                if ($re) {
                    $result ['ret_num'] = 301;
                    $result['ret_msg'] = $this->language->get('wrong_format');
                    echo json_encode ( $result );
                    die ();
                }
            }
            if(!empty($email)){
                // 判断该邮箱有没有注册过
                $re = TaidushopMember::model ()->find ( "member_email = '{$email}'" );
                if ($re) {
                    $result ['ret_num'] = 303;
                    $result['ret_msg'] = $this->language->get('email_used');
                    echo json_encode ( $result );
                    die ();
                }
            }
            // 判断用户名有没有注册过
            $re = TaidushopMember::model ()->find ( "member_name = '{$username}'" );
            if ($re) {
                $result ['ret_num'] = 315;
                $result['ret_msg'] = $this->language->get('member_exist');
                echo json_encode ( $result );
                die ();
            }

            // 开启事物
            $transaction = Yii::app ()->db->beginTransaction (); // 创建事务

            $user = new TaidushopMember ();
            $user->member_name = $username;
            if(!empty($phone)) {
                $user->member_mobile = $phone;
            }
            if(!empty($email)) {
                $user->member_email = $email;
            }
            $user->member_birthday = $birthday;
            $user->member_sex = $sex;
            $user->member_passwd = md5 ( $pwd );
            $user->address = $address;
            $user->profession_id = $professionId;
            $user->married_type = $marriedType;
            $user->member_time = time ();
            $user->member_login_time = time ();
            $user->member_login_ip = $_SERVER ['SERVER_ADDR'];
            $user->member_mobile_bind = 1;
            if ($image) {
                Yii::log ( "开始上传头像：", "info", "jeff.test" );
                // 上传头像
                $path = $this->uploadPic ( $image );
                if (! $path) {
                    Yii::log ( "上传头像失败：", "info", "jeff.test" );
                    $result ['ret_num'] = 908;
                    $result['ret_msg'] = $this->language->get('upload_fail');
                    echo json_encode ( $result );
                    die ();
                }

                $user->member_avatar = $path;
                $headImg = $user->member_avatar;
            }else{
                $headImg = '';
            }

            if ($user->save ()) {
                // 注册成功后插入member_common表
                $insert_id = Yii::app ()->db->getLastInsertID();

                $member_common_sql = "INSERT INTO taidushop_member_common (member_id) VALUES (:member_id)";
                $member_common_result = Yii::app ()->db->createCommand($member_common_sql)->query(array(
                    ':member_id' => $insert_id,
                ));

                if(empty($member_common_result)){
                    $result ['ret_num'] = 901;
                    $result['ret_msg'] = $this->language->get('add_fail');
                    $transaction->rollback(); //回滚事务
                }

                // 生成欢迎消息
                $message = new TdMessage ();
                $message->userid = $user->member_id;
                $message->title = "";
                $message->message = "亲爱的" . $user->member_name . "，我是钛斯基，欢迎加入钛度车队。
先简单介绍一下新版钛度电竞的功能
工具功能，需要钛度系装备绑定联合使用，想要在游戏中提高致命伤害，还不快快用起来？
发现功能，关注钛度新动态，陆续还会有更多好玩的功能等着大家哦~
我的功能，查看自己的相关信息。
好啦，就酱，赶紧去尝鲜一下吧！
最后钛斯基希望能把你培养成电竞大神中的一员，让你在任何类型的电竞游戏中脱颖而出，驰骋电竞场！";
                $message->message_type = 1;
                $message->send_userid = 2;
                $message->send_username = "钛斯基";
                $message->is_read = 0;
                $message->created_time = time ();
                $message->save ();

                // 注册成功后进行登录
                // 更改openid的生成规则 bug#316 2016/11/14
                $openid = md5( uniqid( $insert_id.'_'.time(), true) );
                $user->openid = $openid;
                $user->update ();
                // 写session
                Yii::app ()->session ['openid'] = $openid;

                // 返回用户信息
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = 'ok';
                $result ['user'] = array (
                    "memberid" => $user->member_id,
                    "username" => $user->member_name,
                    "realname" => $user->member_truename,
                    'mobile' => $user->member_mobile,
                    'address' => $this->eraseNull ( $user->address ),
                    'email' => $this->eraseNull ( $user->member_email ),
                    'usertype' => $user->usertype,
                    'sex' => $user->member_sex,
                    'birthday' => $this->eraseNull ( $user->member_birthday ),
                    'header' => $headImg,
                    'openid' => $user->openid,
                    'regip' => $user->member_login_ip,
                    'regtime' => $user->member_time,
                    'updateTime' => $user->member_login_time
                );

                $email_array = array (
                    'yourdomain.com',
                    'yourdomain.cn'
                );
                $rand_email = substr ( md5 ( $username ), 8, 16 ) . "@" . $email_array [rand ( 0, 1 )];
                $email = !empty ( $email ) ? $email : $rand_email;

                Yii::log ( "开始同步uccenter", "info", "jeff.test" );

                include_once '../../includes/bbs/config/config_ucenter.php';
                include_once '../../includes/bbs/uc_client/client.php';
                // 论坛注册

                $uid = uc_user_register ( $username, $pwd, $email );
                if ($uid < 1) {
                    Yii::log ( "论坛注册失败,uid:" . $uid, "info", "jeff.test" );
                    if ($uid == - 1) {
                        // echo '用户名不合法';
                        $transaction->rollback (); // 回滚事务
                        $result ['ret_num'] = 316;
                        $result['ret_msg'] = $this->language->get('member_illegal');
                    } elseif ($uid == - 2) {
                        // echo '包含要允许注册的词语';
                        $transaction->rollback (); // 回滚事务
                        $result ['ret_num'] = 317;
                        $result['ret_msg'] = $this->language->get('illegal_character');
                    } elseif ($uid == - 3) {
                        // echo '用户名已经存在';
                        $transaction->rollback (); // 回滚事务
                        $result ['ret_num'] = 315;
                        $result['ret_msg'] = $this->language->get('member_exist');
                    } else {
                        // echo '未定义';
                        $transaction->rollback (); // 回滚事务
                        $result ['ret_num'] = 318;
                        $result['ret_msg'] = $this->language->get('unknown_fail');
                    }
                    // Yii::log("论坛注册失败,uid:".$uid, "info", "jeff.test");
                    // $result ['ret_num'] = 301;
                    // $result ['ret_msg'] = '论坛注册失败';
                } else {

                    $transaction->commit (); // 提交事务
                    Yii::log ( "论坛注册成功,uid:" . $uid, "info", "jeff.test" );
                    $lifetime = 31536000;
                    session_set_cookie_params ( $lifetime );
                    if (! isset ( $_SESSION )) {
                        session_start ();
                    }
                    session_regenerate_id ( true );
                    $_SESSION ['user_id'] = $user->member_id;
                    $_SESSION ['user_name'] = $username;
                    $_SESSION ['email'] = $email;

                    Yii::log ( "开始uc_user_login", "info", "jeff.test" );
                    // 登录
                    list ( $uid, $username, $pwd, $email ) = uc_user_login ( $username, $pwd );
                    if ($uid > 0) {
                        Yii::log ( "uid >0,开始uc_user_synlogin", "info", "jeff.test" );
                        // $ucsynlogin = uc_user_synlogin ( $uid );
                        // $_SESSION ['td_bbs_login'] = $ucsynlogin;
                        // // 新增的start
                        // $time = time () + $lifetime;
                        // setcookie ( "username", $username, $time, "/", "");
                        // setcookie ( "user_id", $user->member_id, $time, "/", "");
                        // setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );

                        $ucsynlogin = uc_user_synlogin ( $uid );
                        $_SESSION ['td_bbs_login'] = $ucsynlogin;

                        $_SESSION ['user_id'] = $user->member_id;
                        $_SESSION ['user_name'] = $user->member_name;
                        $_SESSION ['email'] = $this->eraseNull ( $user->member_email );
                        $time = time () + $lifetime;
                        setcookie ( "username", $user->member_name, $time, "/", "" );
                        setcookie ( "user_id", $user->member_id, $time, "/", "" );
                        setcookie ( "td_bbs_login", $ucsynlogin, $time, "/", "" );

                        $time = time () + 3600 * 24 * 15;
                        setcookie ( "ECS[username]", $username, $time, "/", "" );
                        setcookie ( "ECS[user_id]", $user->member_id, $time, "/", "" );
                        setcookie ( "ECS[password]", $pwd, $time, "/", "" );
                    }

                    // 登陆后注销cookie
                    setcookie ( 'mobile', '', time () - 3600 );
                    setcookie ( 'code', '', time () - 3600 );
                    unset ( $_SESSION ['code'] );
                    unset ( $_SESSION ['captcha_word'] );

                    Yii::log ( "uccenter同步完成", "info", "jeff.test" );
                }
                mysql_query ( "END" );
            } else {
                $result ['ret_num'] = 901;
                $result['ret_msg'] = $this->language->get('add_fail');

                $transaction->rollback(); //回滚事务
            }
            echo json_encode ( $result );
        }
    }

}