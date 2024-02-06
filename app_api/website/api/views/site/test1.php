<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>test</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link type="text/css" rel="stylesheet" href="/skin/inesa/css/style.css" />
    <link rel="stylesheet" href="/skin/inesa/colorbox.css" />
    <script type="text/javascript" src="/skin/inesa/js/jquery.js"></script>
    <script src="/skin/inesa/js/jquery.colorbox.js"></script>
    <script src="/skin/inesa/js/js.js"></script>
    <base target="_blank" />
</head>

<body>


<h1>登录</h1>
<form action="/app_api/website/api.php/v3_1/contest/GetContestModeList" method="post">

    <input type="text" name="open_id" value="6f5572dd880e4fed25b7f8d1ae971ad3" />

    <input type="submit" value="submit" />
</form>



<h1>登录</h1>
<form action="/app_api/website/api.php/v3_1/user/login" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="phone"/>
    <input type="password" name="password"/>

    <input type="submit" value="submit" />
</form>


<body>
<h1>1.登录(新)</h1>
<form action="/app_api/website/api.php/v3_1/user/login" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="member_name"/>
    <input type="password" name="member_passwd"/>
    <input type="text" name="terminal_code"  value="3213123"  />

    <select name="app_key">
        <option value="448321">电竞精灵PC</option>
        <option value="953033">电竞精灵APP</option>
        <option value="760267">电脑助手</option>
    </select>
    <input type="submit" value="submit" />
</form>


<h1>生成验证码</h1>
<form action="/app_api/website/api.php/v2_2/user/verify" method="post">
    <input type="hidden" name="type" value="2" />
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="phone"/>
    <input type="submit" value="submit" />
</form>


<h1>校验验证码</h1>
<form action="/app_api/website/api.php/v3_1/option/CheckVerificationCode" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="phone" placeholder="phone"/><br>
    <input type="text" name="verify" placeholder="verify"/><br>
    <input type="text" name="type" placeholder="type"/><br>
    <input type="submit" value="submit" />
</form>


<h1>注册</h1>
<form action="/app_api/website/api.php/v2_2/user/register" method="post">
    <input type="hidden" name="key" value="iphone" /><br>
    <input type="text" name="phone" placeholder="phone"/><br>
    <input type="password" name="password" placeholder="password"/><br>
    <input type="password" name="repassword" placeholder="repassword"/><br>
    <input type="text" name="birthday" placeholder="birthday"/><br>
    <input type="text" name="sex" placeholder="sex"/><br>
    <input type="text" name="username" placeholder="username"/><br>
    <input type="text" name="code" placeholder="code"/><br>
    <input type="text" name="address" placeholder="address"/><br>
    <input type="text" name="profession" placeholder="profession"/><br>
    <input type="text" name="marriedType" placeholder="marriedType"/>
    <input type="submit" value="submit" />
</form>


<h1>内部注册（无需验证）</h1>
<form action="/app_api/website/api.php/v2_2/user/registertestuser" method="post">
    <input type="hidden" name="key" value="iphone" /><br>
    <input type="text" name="phone" placeholder="phone"/><br>
    <input type="password" name="password" placeholder="password"/><br>
    <input type="password" name="repassword" placeholder="repassword"/><br>
    <input type="text" name="birthday" placeholder="birthday"/><br>
    <input type="text" name="sex" placeholder="sex"/><br>
    <input type="text" name="username" placeholder="username"/><br>
    <input type="text" name="code" placeholder="code"/><br>
    <input type="text" name="address" placeholder="address"/><br>
    <input type="text" name="profession" placeholder="profession"/><br>
    <input type="text" name="marriedType" placeholder="marriedType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>注销</h1>
<form action="/app_api/website/api.php/v3_1/user/logout" method="post">
    <input type="hidden" name="key" value="iphone" /><br>
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得个人信息</h1>
<form action="/app_api/website/api.php/v3_1/user/GetUserInfo" method="post">
    <input type="hidden" name="key" value="iphone" /><br>
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>forgot password</h1>
<form action="/app_api/website/api.php/v3_1/option/fogpwd" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="phone"/><br>
    <input type="password" name="password"/><br>
    <input type="password" name="repassword"/><br>
    <input type="text" name="code" placeholder="code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>update password</h1>
<form action="/app_api/website/api.php/v3_1/option/changepwd" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="password" name="oldpassword" placeholder="oldpassword"/><br>
    <input type="password" name="newpassword" placeholder="newpassword"/><br>
    <input type="password" name="newrepassword" placeholder="newrepassword"/><br>
    <input type="submit" value="submit" />
</form>


<h1>save heart</h1>
<form action="/app_api/website/api.php/v3_1/upload/heart" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="distance" placeholder="distance"/><br>
    <input type="text" name="lClickNum" placeholder="lClickNum"/><br>
    <input type="text" name="rClickNum" placeholder="rClickNum"/><br>
    <input type="text" name="isCount" placeholder="isCount"/><br>
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>sync</h1>
<form action="/app_api/website/api.php/v3_1/upload/sync" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="createdTime"/><br>
    <input type="text" name="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>修改个人资料</h1>
<form action="/app_api/website/api.php/v3_1/option/changeuser" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="file" name="header" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="username" placeholder="username"/><br>
    <input type="text" name="sex" placeholder="sex"/><br>
    <input type="text" name="birthday" placeholder="birthday"/><br>
    <input type="text" name="gameInfo" placeholder="gameInfo"/><br>
    <input type="text" name="height" placeholder="height"/><br>
    <input type="text" name="weight" placeholder="weight"/><br>
    <input type="text" name="professionId" placeholder="professionId"/><br>
    <input type="text" name="married_type" placeholder="married_type"/><br>
    <input type="text" name="interest" placeholder="interest"/><br>
    <input type="text" name="vision" placeholder="vision"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得所有游戏</h1>
<form action="/app_api/website/api.php/v3_1/user/getgames" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>uploadTestData</h1>
<form action="/app_api/website/api.php/v3_1/upload/uploadData" method="post" enctype="multipart/form-data">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="clickNum" placeholder="clickNum"/><br>
    <input type="text" name="moveNum" placeholder="moveNum"/><br>
    <input type="text" name="moveNumDetail" placeholder="moveNumDetail"/><br>
    <input type="text" name="moveDistance" placeholder="moveDistance"/><br>
    <input type="text" name="moveDistanceDetail" placeholder="moveDistanceDetail"/><br>
    <input type="text" name="timeLong" placeholder="timeLong"/><br>
    <input type="text" name="heartNum" placeholder="heartNum"/><br>
    <input type="text" name="minHeart" placeholder="minHeart"/><br>
    <input type="text" name="maxHeart" placeholder="maxHeart"/><br>
    <input type="text" name="avgHeart" placeholder="avgHeart"/><br>
    <input type="text" name="heartDetail" placeholder="heartDetail"/><br>
    <input type="text" name="minG" placeholder="minG"/><br>
    <input type="text" name="maxG" placeholder="maxG"/><br>
    <input type="text" name="avgG" placeholder="avgG"/><br>
    <input type="text" name="gDetail" placeholder="gDetail"/><br>
    <input type="text" name="maxApm" placeholder="maxApm"/><br>
    <input type="text" name="avgApm" placeholder="avgApm"/><br>
    <input type="text" name="apmDetail" placeholder="apmDetail"/><br>
    <input type="text" name="finalScore" placeholder="finalScore"/><br>
    <input type="text" name="gameType" placeholder="gameType"/><br>
    <input type="text" name="grade" placeholder="grade"/><br>
    <input type="submit" value="submit" />
</form>


<h1>11.历史记录</h1>
<form action="/app_api/website/api.php/v3_1/count/history" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="userId" placeholder="userId"/><br>
    <input type="text" name="pagesize" placeholder="pagesize"/><br>
    <input type="text" name="gameType" placeholder="gameType"/><br>
    <input type="text" name="page" placeholder="page"/><br>
    <input type="submit" value="submit" />
</form>


<h1>12.查询测试明细数据</h1>
<form action="/app_api/website/api.php/v3_1/count/historydetail" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="testId" placeholder="testId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>89.查询指定天数训练数据详情</h1>
<form action="/app_api/website/api.php/v3_1/upload/getTrainingInfoByDate" method="post" enctype="multipart/form-data">
    <input type="text" name="openId" placeholder="open_id"/><br>
    <input type="text" name="date" placeholder="date"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询历史训练</h1>
<form action="/app_api/website/api.php/v3_1/count/historytest" method="post" enctype="multipart/form-data">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="time_type" placeholder="time_type"/><br>
    <input type="submit" value="submit" />
</form>


<h1>dpi</h1>
<form action="/app_api/website/api.php/v3_1/count/dpi" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="dpi" placeholder="dpi"/><br>
    <input type="submit" value="submit" />
</form>


<h1>light</h1>
<form action="/app_api/website/api.php/v3_1/count/light" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="hidden" name="Lstatus" value="1" />
    <input type="hidden" name="Bstatus" value="1" />
    <input type="text" name="colorNum" placeholder="colorNum" /><br>
    <input type="text" name="brightness" placeholder="brightness" /><br>
    <input type="submit" value="submit" />
</form>


<h1>8.查询排行榜</h1>
<form action="/app_api/website/api.php/v3_1/count/rank" method="post" enctype="multipart/form-data">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="orderType" placeholder="orderType"/><br>
    <input type="text" name="gameType" placeholder="gameType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>Statistics</h1>
<form action="/app_api/website/api.php/v3_1/upload/statistics" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="text" name="open_id" placeholder="open_id" /><br>
    <input type="submit" value="submit" />
</form>


<h1>排行榜（每天更新一次）</h1><!--排行榜（每天更新一次）-->
<form action="/app_api/website/api.php/v3_1/record/uploadrank" method="post">
    <input type="hidden" name="key" value="iphone" />
    <input type="submit" value="submit" />
</form>


<h1>allocateTask</h1><!--测试分配任务-->
<form action="/app_api/website/api.php/v3_1/task/allocatetask" method="post">
    <input type="submit" value="submit" />
</form>


<h1>stardpi</h1><!--获取明星dpi-->
<form action="/app_api/website/api.php/v3_1/count/stardpi" method="post">
<input type="text" name="open_id"/><br>
<input type="submit" value="submit" />
</form>


<h1>取得自定义dpi档位</h1><!--获取选中的dpi-->
<form action="/app_api/website/api.php/v3_1/count/getcustomdpis" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="mouseSpec" placeholder="mouseSpec"/><br>
    <input type="submit" value="submit" />
</form>


<h1>replaceStarDpi</h1><!--替换选中的明星dpi-->
<form action="/app_api/website/api.php/v3_1/count/replacestardpi" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="newDpiId" placeholder="newDpiId"/><br>
    <input type="text" name="oldDpiId" placeholder="oldDpiId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>addCustomDpi</h1><!--新增自定义dpi-->
<form action="/app_api/website/api.php/v3_1/count/addcustomdpi" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="dpiIndex" placeholder="dpiIndex"/><br>
    <input type="text" name="dpiName" placeholder="dpiName"/><br>
    <input type="text" name="dpiValue" placeholder="dpiValue"/><br>
    <input type="submit" value="submit" />
</form>


<h1>上传自定义dpi</h1><!--替换自定义dpi-->
<form action="/app_api/website/api.php/v3_1/count/uploadcustomdpi" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="dpiIndex" placeholder="dpiIndex"/><br>
    <input type="text" name="dpiName" placeholder="dpiName"/><br>
    <input type="text" name="dpiValue" placeholder="dpiValue"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得推荐dpi</h1><!--取得推荐dpi-->
<form action="/app_api/website/api.php/v3_1/count/getrecommendeddpi" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="screenType" placeholder="screenType"/><br>
    <input type="text" name="mousePadType" placeholder="mousePadType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>getUserAchievements</h1><!--获取用户的成就-->
<form action="/app_api/website/api.php/v3_1/task/getuserachievements" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>21.绑定设备</h1><!--绑定设备-->
<form action="/app_api/website/api.php/v3_1/device/bindDevice" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="text" name="deviceType" placeholder="deviceType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>88.修改设备名称</h1><!--解除绑定-->
<form action="/app_api/website/api.php/v3_1/device/updateDeviceName" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="text" name="deviceName" placeholder="deviceName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>22.解绑设备</h1><!--解除绑定-->
<form action="/app_api/website/api.php/v3_1/device/removeDevice" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="submit" value="submit" />
</form>


<h1>23.获取已绑定的设备</h1><!--获取用户所有绑定的设备-->
<form action="/app_api/website/api.php/v3_1/device/getUserDeviceList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得最新版本</h1><!--取得最新版本-->
<form action="/app_api/website/api.php/v3_1/version/getnewversion" method="post">
    <input type="hidden" name="key" value="pc" />
    <input type="text" name="softType" placeholder="softType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得所有版本</h1><!--取得最新版本-->
<form action="/app_api/website/api.php/v3_1/version/getallversions" method="post">
    <input type="hidden" name="key" value="pc" />
    <input type="hidden" name="softType" value="3" />
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>分析用户测试数据</h1><!---->
<form action="/app_api/website/api.php/v3_1/count/testdatacount" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>26.取得设备名称</h1><!--取得鼠标名称-->
<form action="/app_api/website/api.php/v3_1/device/getDeviceName" method="post">
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="submit" value="submit" />
</form>


<h1>记录用户操作</h1>
<form action="/app_api/website/api.php/v3_1/record/userAction" method="post">
    <input type="text" name="userId" placeholder="userId"/><br>
    <input type="text" name="actionType" placeholder="actionType"/><br>
    <input type="text" name="softType" placeholder="softType"/><br>
    <input type="text" name="deviceType" placeholder="deviceType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>记录用户操作</h1>
<form action="/app_api/website/api.php/v3_1/record/log" method="post">
    <input type="text" name="userId" placeholder="userId"/><br>
    <input type="text" name="softVersion" placeholder="softVersion"/><br>
    <input type="text" name="softType" placeholder="softType"/><br>
    <input type="text" name="deviceType" placeholder="deviceType"/><br>
    <input type="text" name="model" placeholder="model"/><br>
    <input type="text" name="os" placeholder="os"/><br>
    <input type="text" name="logType" placeholder="logType"/><br>
    <input type="text" name="section" placeholder="section"/><br>
    <input type="text" name="message" placeholder="message"/><br>
    <input type="submit" value="submit" />
</form>



<h1>问题反馈</h1>
<form action="/app_api/website/api.php/v3_1/user/QuestionFeedback" method="post" enctype="multipart/form-data">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="questionType" placeholder="questionType"/><br>
    <input type="text" name="question" placeholder="question"/><br>
    <input type="file" name="image1" /><br>
    <input type="file" name="image2" /><br>
    <input type="file" name="image3" /><br>
    <input type="submit" value="submit" />
</form>


<h1>所有反馈问题列表</h1>
<form action="/app_api/website/api.php/v3_1/user/GetFeedbackList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>FAQ列表</h1>
<form action="/app_api/website/api.php/v3_1/user/GetQAList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="softType" placeholder="softType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得用户累计数据</h1>
<form action="/app_api/website/api.php/v3_1/count/TabulateData" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="userId" placeholder="userId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得所有led文件</h1>
<form action="/app_api/website/api.php/v3_1/led/GetLedFileList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得活动列表</h1>
<form action="/app_api/website/api.php/v3_1/activity/getActivityList" method="post">
    <input type="text" name="type" placeholder="type"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得活动明细</h1>
<form action="/app_api/website/api.php/v3_1/activity/getActivityDetail" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="activityId" placeholder="activityId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>设置活动提醒</h1>
<form action="/app_api/website/api.php/v3_1/activity/SetActivityAlarm" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="activityId" placeholder="activityId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得消息列表</h1>
<form action="/app_api/website/api.php/v3_1/user/GetMessageList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得消息明细</h1>
<form action="/app_api/website/api.php/v3_1/user/GetMessageDetail" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="messageId" placeholder="messageId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>取得未读消息数</h1>
<form action="/app_api/website/api.php/v3_1/user/GetNonReadMessagesCount" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>量子统计</h1>
<form action="/app_api/website/api.php/v3_1/flow/stat" method="post">
    <input type="text" name="pageId" placeholder="pageId"/><br>
    <input type="submit" value="submit" />
</form>


<h1>氛围灯设置</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/SetAtmosphereLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="text" name="customContent" placeholder="customContent"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询氛围灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindAtmosphereLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询全部氛围灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindAtmosphereLights" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询全部默认氛围灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindAllAtmosphereLights" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>删除氛围灯方案</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/DeleteSetAtmosphereLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>键帽灯设置</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/SetKeyCapLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="text" name="customContent" placeholder="customContent"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询键帽灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindKeyCapLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询全部键帽灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindKeyCapLights" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询全部默认键帽灯设置详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindAllKeyCapLights" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>删除键帽灯方案</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/DeleteSetKeyCapLight" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="customName" placeholder="customName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>上传键盘改键内容</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/UploadResconstructProject" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="projectName" placeholder="projectName"/><br>
    <input type="text" name="projectContent" placeholder="projectContent"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询键盘改键内容详情</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindResconstructproject" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="projectName" placeholder="projectName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>删除键盘改键方案</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/DeleteResconstructproject" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="Id" placeholder="Id"/><br>
    <input type="text" name="projectName" placeholder="projectName"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询键盘全部改键内容</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindAllResconstructproject" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="submit" value="submit" />
</form>


<h1>用户游戏模式创建(包括推荐)</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/AddUserGameMode" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="recommended_game_id" placeholder="recommended_game_id"/><br>
    <input type="text" name="customer_game_id" placeholder="customer_game_id"/><br>
    <input type="text" name="mode_name" placeholder="mode_name"/><br>
    <input type="text" name="game_type_name" placeholder="game_type_name"/><br>
    <input type="text" name="polling_rate" placeholder="polling_rate"/><br>
    <input type="text" name="repeat_speed" placeholder="repeat_speed"/><br>
    <input type="text" name="no_rush_mode" placeholder="no_rush_mode"/><br>
    <input type="text" name="kill_keys" placeholder="kill_keys"/><br>
    <input type="text" name="lamp_light_type" placeholder="lamp_light_type"/><br>
    <input type="text" name="lamp_light_name" placeholder="lamp_light_name"/><br>
    <input type="text" name="lamp_light_content" placeholder="lamp_light_content"/><br>
    <input type="text" name="keycap_light_type" placeholder="keycap_light_type"/><br>
    <input type="text" name="keycap_light_name" placeholder="keycap_light_name"/><br>
    <input type="text" name="keycap_light_content" placeholder="keycap_light_content"/><br>
    <input type="text" name="resconstruct_project_type" placeholder="resconstruct_project_type"/><br>
    <input type="text" name="resconstruct_project_name" placeholder="resconstruct_project_name"/><br>
    <input type="submit" value="submit" />
</form>


<h1>删除用户自定义模式</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/DeleteUserGameMode" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="customer_game_id" placeholder="customer_game_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询自定义用户游戏类型</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/FindGameType" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询游戏类型下面的游戏模式</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/GetGameModeByType" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="device_code" placeholder="device_code"/><br>
    <input type="text" name="game_type_id" placeholder="game_type_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询游戏模式详情(包含推荐)</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/GetGameModeDetail" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="customer_game_id" placeholder="customer_game_id"/><br>
    <input type="text" name="recommended_game_id" placeholder="recommended_game_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>推荐游戏模式一键恢复</h1>
<form action="/app_api/website/api.php/v3_1/keyboard/ResetRecommendedGameMode" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="recommended_id" placeholder="recommended_id"/><br>
    <input type="submit" value="submit" />
</form>


<h1>上传数据统计</h1>
<form action="/app_api/website/api.php/v3_1/DataStatistic/UploadDataStatistic" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="date" placeholder="date"/><br>
    <input type="text" name="time" placeholder="time"/><br>
    <input type="text" name="mouse_knock" placeholder="mouse_knock"/><br>
    <input type="text" name="keycap_knock" placeholder="keycap_knock"/><br>
    <input type="text" name="move_data" placeholder="move_data"/><br>
    <input type="text" name="move_meter" placeholder="move_meter"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询当日数据统计</h1>
<form action="/app_api/website/api.php/v3_1/DataStatistic/findtodaydatastatistic" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="date" placeholder="date"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询历史数据统计</h1>
<form action="/app_api/website/api.php/v3_1/DataStatistic/findhistorydatastatistic" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="time_type" placeholder="time_type"/><br>
    <input type="submit" value="submit" />
</form>


<h1>82.保存装备数据</h1>
<form action="/app_api/website/api.php/v3_1/device/saveUserEquipment" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceType" placeholder="deviceType"/><br>
    <input type="text" name="deviceModel" placeholder="deviceModel"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="text" name="keyKnockDetail" placeholder="keyKnockDetail"/><br>
    <input type="text" name="clickNumLeft" placeholder="clickNumLeft"/><br>
    <input type="text" name="clickNumRight" placeholder="clickNumRight"/><br>
    <input type="text" name="moveDistance" placeholder="moveDistance"/><br>
    <input type="submit" value="submit" />
</form>


<h1>83.查看用户所有装备</h1>
<form action="/app_api/website/api.php/v3_1/device/getUserEquipmentList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceType" placeholder="deviceType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>84.获取用户装备详情</h1>
<form action="/app_api/website/api.php/v3_1/device/getUserEquipmentInfo" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="submit" value="submit" />
</form>


<h1>85.删除用户装备数据</h1>
<form action="/app_api/website/api.php/v3_1/device/removeUserEquipment" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="deviceCode" placeholder="deviceCode"/><br>
    <input type="submit" value="submit" />
</form>


<h1>查询7天历史记录</h1>
<form action="/app_api/website/api.php/v3_1/DataStatistic/sevenhistroy" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="page" placeholder="page"/><br>
    <input type="submit" value="submit" />
</form>


<h1>90.测试数据计算3.4</h1>
<form action="/app_api/website/api.php/v3_1/upload/getTrainingResult" method="post" enctype="multipart/form-data">
    <input type="text" name="open_id" placeholder="open_id" /><br>
    <input type="text" name="game_name" placeholder="game_name" /><br>
    <input type="text" name="clickNum" placeholder="clickNum"/><br>
    <input type="text" name="moveNum" placeholder="moveNum"/><br>
    <input type="text" name="maxMoveCount" placeholder="maxMoveCount"/><br>
    <input type="text" name="moveNumDetail" placeholder="moveNumDetail"/><br>
    <input type="text" name="moveDistance" placeholder="moveDistance"/><br>
    <input type="text" name="maxMoveDistance" placeholder="maxMoveDistance"/><br>
    <input type="text" name="moveDistanceDetail" placeholder="moveDistanceDetail"/><br>
    <input type="text" name="timeLong" placeholder="timeLong"/><br>
    <input type="text" name="heartNum" placeholder="heartNum"/><br>
    <input type="text" name="minHeart" placeholder="minHeart"/><br>
    <input type="text" name="maxHeart" placeholder="maxHeart"/><br>
    <input type="text" name="avgHeart" placeholder="avgHeart"/><br>
    <input type="text" name="heartDetail" placeholder="heartDetail"/><br>
    <input type="text" name="minG" placeholder="minG"/><br>
    <input type="text" name="maxG" placeholder="maxG"/><br>
    <input type="text" name="avgG" placeholder="avgG"/><br>
    <input type="text" name="gDetail" placeholder="gDetail"/><br>
    <input type="text" name="maxApm" placeholder="maxApm"/><br>
    <input type="text" name="avgApm" placeholder="avgApm"/><br>
    <input type="text" name="apmDetail" placeholder="apmDetail"/><br>
    <input type="text" name="gameType" placeholder="gameType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>91.获取键盘点击次数</h1>
<form action="/app_api/website/api.php/v3_1/upload/getKeyboardCounters" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="knockCounters" placeholder="knockCounters"/><br>
    <input type="submit" value="submit" />
</form>


<h1>92.查询指定时间段训练数据摘要</h1>
<form action="/app_api/website/api.php/v3_1/upload/getTrainingSummary" method="post" enctype="multipart/form-data">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="dateType" placeholder="dateType"/><br>
    <input type="submit" value="submit" />
</form>


<h1>93.查询指定时间段训练数据列表</h1>
<form action="/app_api/website/api.php/v3_1/upload/getTrainingList" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>
    <input type="text" name="userId" placeholder="userId"/><br>
    <input type="text" name="dateType" placeholder="dateType"/><br>
    <input type="text" name="gameType" placeholder="gameType"/><br>
    <input type="text" name="page" placeholder="page"/><br>
    <input type="text" name="pageSize" placeholder="pageSize"/><br>
    <input type="submit" value="submit" />
</form>


<h1>95</h1>
<form action="/app_api/website/api.php/v3_1/computer/UploadComputerConfig" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>

    <input type="text" name="mac_address" placeholder="mac_address"/><br>
    <input type="text" name="config" placeholder="config"/><br>
    <input type="submit" value="submit" />
</form>

<h1>96</h1>
<form action="/app_api/website/api.php/v3_1/computer/DownloadComputerConfig" method="post">
    <input type="text" name="open_id" placeholder="open_id"/><br>

    <input type="text" name="mac_address" placeholder="mac_address"/><br>
    <input type="submit" value="submit" />
</form>


<h1>101</h1>
<form action="/app_api/website/api.php/v3_1/computer/SaveComputerHistory" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>
    <input type="text" name="power_on" placeholder="power_on"/><br>
    <input type="text" name="power_off" placeholder="power_off"/><br>

    <input type="submit" value="submit" />
</form>

<h1>102</h1>
<form action="/app_api/website/api.php/v3_1/computer/GetComputerSummary" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>

    <input type="submit" value="submit" />
</form>

<h1>102</h1>
<form action="/app_api/website/api.php/v3_1/computer/ClearComputerHistory" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>

    <input type="submit" value="submit" />
</form>


<h1>97.增加游戏模式</h1>
<form action="/app_api/website/api.php/v3_1/gameMode/addGameMode" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>
    <input type="text" name="gm_name" placeholder="gm_name"/><br>
    <input type="submit" value="submit" />
</form>


<h1>98</h1>
<form action="/app_api/website/api.php/v3_1/gameMode/GetGameModeList" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>

    <input type="submit" value="submit" />
</form>



<h1>106</h1>
<form action="/app_api/website/api.php/v3_1/computer/GetGameHistoryList" method="post">
    <input type="text" name="mac_address" placeholder="mac_address"/><br>

    <input type="submit" value="submit" />
</form>



<h1>新增竞技模式默认配置</h1>
<form action="/app_api/website/api.php/v3_1/contest/AddDefaultContest" method="post">
    <input type="hidden" name="key" value="iphone" />

    <input type="text" name="game_name" placeholder="game_name" /><br>
    <select name = "game_type">
        <option value="1">RTS</option>
        <option value="2">MOBA</option>
        <option value="3">FPS</option>
    </select><br>
    
    <input type="text" name="ms_device_model" placeholder="ms_device_model" /><br>
    <input type="text" name="report_rate" value="" placeholder="report_rate" /><br>
    <input type="text" name="dpi" value="" placeholder="dpi" /><br>
    <input type="text" name="ms_lamp_bar_light" value="" placeholder="ms_lamp_bar_light" /><br>
    <input type="text" name="ms_lamp_bar_content" value="" placeholder="ms_lamp_bar_content" /><br>
    <input type="text" name="ms_change_key" value="" placeholder="ms_change_key" /><br>
    <input type="text" name="ms_change_key_content" value="" placeholder="ms_change_key_content" /><br>

    <input type="text" name="kb_device_model" placeholder="kb_device_model" /><br>
    <input type="text" name="kb_lamp_bar_light" value="" placeholder="kb_lamp_bar_light" /><br>
    <input type="text" name="kb_lamp_bar_content" value="" placeholder="kb_lamp_bar_content" /><br>
    <input type="text" name="kb_change_key" value="" placeholder="kb_change_key" /><br>
    <input type="text" name="kb_change_key_content" value="" placeholder="kb_change_key_content" /><br>
    <input type="text" name="polling_rate" value="" placeholder="polling_rate" /><br>
    <input type="text" name="repeat_speed" value="" placeholder="repeat_speed" /><br>
    <input type="text" name="conflict_mode" value="" placeholder="conflict_mode" /><br>
    <input type="text" name="ban_keys" value="" placeholder="ban_keys" /><br>
    <input type="text" name="keycap_light" value="" placeholder="keycap_light" /><br>
    <input type="text" name="keycap_content" value="" placeholder="keycap_content" /><br>

    <input type="submit" value="submit" />
</form>



</body>
</html>