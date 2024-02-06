<?php
class BaseController extends Controller {
	public $menuIndex;
	
	protected function beforeAction($action) {
		if (Yii::app ()->user->isGuest && !in_array($action->id, array('login','test'))) {
			// 游客
// 			$this->redirect ( array ('/' ) );
			return true;
		} else {
			// 更新用户信息
			if (isset($userid )){
			$userid = Yii::app ()->user->getState ( "userInfo" )->id;
			$userData = User::model ()->findByPk ( $userid );
			}
			if (! empty ( $userData )) {
				Yii::app ()->user->setState ( "userInfo", $userData );
			}
			return true;
		}
	}
}

?>