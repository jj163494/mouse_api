<?php
class SiteController extends BaseController {
	/**
	 * Declares class-based actions.
	 */
	public $layout = false;
	public function actions() {
		return array (
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha' => array (
						'class' => 'CCaptchaAction',
						'backColor' => 0xFFFFFF 
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page' => array (
						'class' => 'CViewAction' 
				) 
		);
	}
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex() {
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		// $this->render('index');
		if (Yii::app ()->user->isGuest) {
			// 游客
			$role = getRole ();	
			$this->render ( 'index', array (
					'role' => $role 
			) );
		} else {
			// 已登录用户
			$this->redirect ( array (
					'/index' 
			) );
		}
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError() {
		if ($error = Yii::app ()->errorHandler->error) {
			if (Yii::app ()->request->isAjaxRequest)
				echo $error ['message'];
			else
				$this->render ( 'error', $error );
		}
	}
	
	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		$model = new LoginForm ();
		
		$username = $_POST ['username'];
		$password = $_POST ['password'];
		
		$result ['status'] = 0;
		if (empty ( $username )) {
			$result ['message'] = '请输入登陆名称';
			echo json_encode ( $result );
			die ();
		}
		if (empty ( $password )) {
			$result ['message'] = '请输入登陆密码';
			echo json_encode ( $result );
			die ();
		}
		$identity = new UserIdentity ( $username, $password );
		if ($identity->authenticate ()) {
			$duration = 3600 * 24 * 30; // 30 days
			Yii::app ()->user->login ( $identity, $duration );
			$result ['status'] = 1;
		} else {
			$result ['message'] = '登录名或密码错误';
		}
		echo json_encode ( $result );
		die ();
		
		/*
		 * // if it is ajax validation request if(isset($_POST['ajax']) && $_POST['ajax']==='login-form') { echo CActiveForm::validate($model); Yii::app()->end(); } // collect user input data if(isset($_POST['LoginForm'])) { $model->attributes=$_POST['LoginForm']; // validate user input and redirect to the previous page if valid if($model->validate() && $model->login()) $this->redirect(Yii::app()->user->returnUrl); } // display the login form $this->render('login',array('model'=>$model));
		 */
	}
	
	public function actionTest()
	{
		$this->menuIndex = 21;
		$this->layout = "admin";
		$this->render("error");
	}
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout() {
		Yii::app ()->user->logout ( false );
		$this->redirect ( Yii::app ()->homeUrl );
	}

	/**
	 * 表单提交页面
	 */
	public function actionTest1(){
		$open_id = Yii::app()->session['openid'];
		$this->render("test1", array('open_id' => $open_id));
	}
}