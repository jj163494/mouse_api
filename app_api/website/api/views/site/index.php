<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理后台</title>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/public.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/login.css" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/login.js"></script>
</head>
<body>
	<div class="container container_large" id="container">
			<div class="main_login" id="main_contain">
				<div class="main_login_logo">
					管理后台
				</div>
				<div class="main_login_name">
					<i class="username_icon"></i>
					<input id="login_username" type="text" placeholder="登录名" />
				</div>
				<div class="main_login_pwd">
					<i class="password_icon"></i>
					<input id="login_pwd" type="password" placeholder="密码" />
				</div>
				<div class="line_icon"></div>
				<div id="dologin" class="main_login_btn">登 录</div>
				<input id ="baseUrl" type ="hidden" value="<?php echo Yii::app()->request->baseUrl; ?>"/>
<!-- 				<div class="main_login_msg">忘记密码？</div> -->
			</div>
	</div>
	<script>
	$(function(){
		var column_height =   $(window).height(); 
        $("#container").css("height",column_height + 'px');
        var mar =  (column_height - 460)/2;
        if(mar < 0) mar = 0;
        $('#main_contain').css('margin-top',mar + 'px');
	});
</script>
</body>
</html>