<?php
class LeftWidge extends CWidget {
	public $index = 10;
	function init() {
		// 此方法会被 CController::beginWidget() 调用
	}
	function run() {
		// 此方法会被 CController::endWidget() 调用
		// 加载主菜单
		$this->loadMenu ();
	}
	
	// 加载主菜单
	function loadMenu() {
		$menu = getMenu ();
		$subMenu = getSubMenu ();
		$this->render ( 'leftView', array (
				"index" => $this->index,
				"menu" => $menu,
				"subMenu" => $subMenu 
		) );
	}
}