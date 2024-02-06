<?php
class FooterWidge extends CWidget {
	public $index = 1;
	function init() {
		// 此方法会被 CController::beginWidget() 调用
	}
	function run() {
		// 此方法会被 CController::endWidget() 调用
		$this->render ( 'footerView', array (
				"index" => $this->index 
		) );
	}
}