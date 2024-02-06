<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>管理后台</title>
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/bootstrap/bootstrap.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/public.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/themes/css/index.css" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/public.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/lib/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="<?php echo Yii::app()->request->baseUrl; ?>/themes/js/respond.min.js"></script>
	<![endif]-->
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<div class="container-fluid">
		<div class="main">
			<div class="main_content">
			<?php $this->widget('LeftWidge', array('index' => $this->menuIndex)) ;?>		
			<div class="main_content_right col-lg-10 col-md-10 col-sm-12 col-xs-12">
			<?php $this->widget('RightHeaderWidge');?>	
			<?php echo $content; ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>