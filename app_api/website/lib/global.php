<?php
// 配置文件
//商品分类
function getGoodsCat() {
	$GoodsCat = array (
			-1 => '--请选择--',
			1 => '鼠标',
			2 => '鼠标垫',
			3 => '鼠标线'
	);
	return $GoodsCat;
}
//商品分类属性
function getGoodsCatAttr() {
	$GoodsCatAttr = array (
			1 => array(
				1=>'颜色'
			),
			2 =>array(
				1=>'颜色',
				2=>'型号'
			),
// 			3 => array(
// 				1=>'',
// 				2=>''
// 			)
	);
	return $GoodsCatAttr;
}
//商品分类属性值
function getGoodsCatAttrVal() {
	$GoodsCatAttrVal = array (
			1 => array(
					1=>array(
						'name'=>'白色',
						'rgb'=>'rgb(255, 255, 255)',
						'img'=>''
					),
					2=>array(
						'name'=>'黑色',
						'rgb'=>'rgb(0, 0, 0)',
						'img'=>''
					),
					3=>array(
							'name'=>'红色',
							'rgb'=>'rgb(255, 0, 0)',
							'img'=>''
					),
			),
			2 =>array(
					1=>array(
						'name'=>'001'
					),
					2=>array(
						'name'=>'002'
					),
					3=>array(
							'name'=>'003'
					),
			)
	);
	return $GoodsCatAttrVal;
}
//退货状态
function getRefundStatus() {
	$refundStatus = array (
			-1 => '--请选择--',
			0 => '等待审核',
			3 => '审核通过',
			5 => '审核不通过',
			7 => '货物退回中',
			9 => '已收货（仓库收到货物）',
			11 => '成功退款'


	);
	return $refundStatus;
}
//退货方式
function getRefundWay() {
	$refundWay = array (
			-1 => '--请选择--',
			1 => '上门取件',
			2 => '客户发货'
	);
	return $refundWay;
}
//退货原因
function getRefundReason() {
	$refundReason = array (
			1 => '商品质量问题',
			2 => '商品破损问题',
			3 => '与描述不符',
			4 => '效果不好',
			5 => '其他',
	);
	return $refundReason;
}
//会员管理--用户类型
function getRole() {
	$role = array (
			1 => '管理员',
			2 => '编辑' 
	);
	return $role;
}
function getMenu() {
	$menu = array (
			1 => array (
					'name' => '订单管理',
					'uri' => '/order'
			) ,
			2 => array (
					'name' => '商品管理',
					'uri' => '/goods'
			),
			3=>array(
					'name'=>'报表管理',
					'uri'=>'/report'
					
			),
			4=>array(
					'name'=>'会员管理',
					'uri'=>'/member'
			
			),
			5=>array(
					'name'=>'首页管理',
					'uri'=>'/homepage'
			),
			6 => array (
					'name' => '系统管理',
					'uri' => '/system' 
			) ,
			
		

	);
	return $menu;
}
function getSubMenu() {

	$subMenu = array (
			1=>array(
					1 => array (
							'name' => '订单管理',
							'uri' => '/order/index'
					),
					2 => array (
							'name' => '订单统计',
							'uri' => '/ordercount/index'
					),
					3 => array (
							'name' => '售后管理',
							'uri' => '/refund/index'
					)
			
			),
			2 => array (
					1 => array (
							'name' => '商品管理',
							'uri' => '/goods/index'
					) ,
					2 => array (
							'name' => '赠品管理',
							'uri' => '/gifts/index'
					) ,
					9=>array(
							'name'=>'套餐管理',
							'uri'=>'/package/index'
					)
			),
			3=>array(
					1 => array (
							'name' => '订单报表',
							'uri' => '/report/index'
					)
						
			),
			4=>array(
					1 => array (
							'name' => '会员管理',
							'uri' => '/member/index'
					),
					2 => array (
							'name' => '分组管理',
							'uri' => '/group/index'
					)
			),
			5=>array(
					1=>array(
							'name'=>'首页管理',
							'uri'=>'/homepage/index'
		
					),
					2=>array(
							'name'=>'商城首页',
							'uri'=>'/shoppage/index'
					),
					3=>array(
							'name'=>'帮助管理',
							'uri'=>'/pagehelp/index'
					)
			),
			6 => array (
					1 => array (
							'name' => '用户管理',
							'uri' => '/system/index' 
					),
					2 => array (
							'name' => '角色管理',
							'uri' => '/role/index' 
					) 
			) ,
	);
	return $subMenu;
}

function pr($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function vd($data){
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}


/**
 * 稳定指数评价转换表
 * @param $heart_score
 * @return string
 */
function get_heart_rank($heart_score){
	if($heart_score > 30000){
		$heart_rank = 'S';
	}else if($heart_score > 10000 && $heart_score <= 30000){
		$heart_rank = 'A';
	}else if($heart_score > 3200 && $heart_score <= 10000){
		$heart_rank = 'B';
	}else if($heart_score > 1200 && $heart_score <= 3200){
		$heart_rank = 'C';
	}else if($heart_score > 300 && $heart_score <= 1200){
		$heart_rank = 'D';
	}else{
		$heart_rank = 'E';
	}

	return $heart_rank;
}


/**
 * 获取敏捷得分评价
 * @param $agi_score
 * @return string
 */
function get_agi_rank($agi_score){
	if($agi_score > 3200){
		$agi_rank = 'S';
	}else if($agi_score > 2800 && $agi_score <= 3200){
		$agi_rank = 'A';
	}else if($agi_score > 2400 && $agi_score <= 2800){
		$agi_rank = 'B';
	}else if($agi_score > 1800 && $agi_score <= 2400){
		$agi_rank = 'C';
	}else if($agi_score > 900 && $agi_score <= 1800){
		$agi_rank = 'D';
	}else{
		$agi_rank = 'E';
	}

	return $agi_rank;
}


/**
 * 获取时间系数
 * @param $time_long
 * @return float|int
 */
function get_time_config($time_long){
	if($time_long > 3600) {
		$time_conf = 0.73;
	}else if($time_long > 2400 && $time_long <= 3600){
		$time_conf = 0.72;
	}else if($time_long > 1200 && $time_long <= 2400){
		$time_conf = 0.71;
	}else if($time_long > 600 && $time_long <= 1200){
		$time_conf = 0.7;
	}else if($time_long >= 180 && $time_long <= 600){
		$time_conf = 0.63;
	}else{
		$time_conf = 0;
	}

	return $time_conf;
}

/**
 * 获取职业综合评价
 * @param $pro_score
 * @return string
 */
function get_pro_rank($pro_score){
	if($pro_score > 6500){
		$pro_rank = 'S';
	}else if($pro_score > 5400 && $pro_score <= 6500){
		$pro_rank = 'A';
	}else if($pro_score > 4200 && $pro_score <= 5400){
		$pro_rank = 'B';
	}else if($pro_score > 3000 && $pro_score <= 4200){
		$pro_rank = 'C';
	}else if($pro_score > 1500 && $pro_score <= 3000){
		$pro_rank = 'D';
	}else{
		$pro_rank = 'E';
	}

	return $pro_rank;
}