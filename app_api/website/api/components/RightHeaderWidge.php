<?php
class RightHeaderWidge extends CWidget {
	function init() {
		// 此方法会被 CController::beginWidget() 调用
	}
	function run() {
		// 此方法会被 CController::endWidget() 调用
		$this->render ( 'rightHeaderView' );
	}
}