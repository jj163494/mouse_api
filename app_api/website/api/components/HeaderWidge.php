<?php
class HeaderWidge extends CWidget {
	public $cssArray = array ();
	function init() {
		// 此方法会被 CController::beginWidget() 调用
	}
	function run() {
		// 此方法会被 CController::endWidget() 调用
		$this->render ( 'headerView', array (
				"css" => $this->cssArray 
		) );
	}
}