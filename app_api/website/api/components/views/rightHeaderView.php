<div class="main_right_header">
	<!-- 	<div class="main_right_header_search"> -->
	<!-- 		<input type="text" placeholder="搜索内容" /> -->
	<!-- 		<div class="main_right_header_search_btn"></div> -->
	<!-- 	</div> -->
	<div class="main_right_header_setting">
		[ <a href="<?php echo Yii::app()->createUrl('/site/logout');?>">退出</a>
		]
	</div>
	<div class="main_right_header_user">欢迎您：<?php echo Yii::app()->user->id;?>！</div>
</div>