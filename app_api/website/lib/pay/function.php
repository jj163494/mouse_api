<?php

/* # 处理微信支付异步回调逻辑
 * @ paran data array 	
 * return string
 */

function update_order_pay_status($data, $pay_type) {

	$out_trade_no = $data['out_trade_no']; // 支付流水号
	$total_fee = $data['total_fee'];

	$date = time();

	$sql = "SELECT log_id,order_id,is_paid FROM {$GLOBALS['ecs']->table('pay_log')} WHERE log_id='{$out_trade_no}'";

	$pay_info = $GLOBALS['db']->getRow($sql);

	if ($pay_info and $pay_info['is_paid'] != 1) {
		$order_id = $pay_info['order_id'];

		//更新支付记录
		$sql = "UPDATE {$GLOBALS['ecs']->table('pay_log')} SET is_paid=1 ,transaction_id='{$data['transaction_id']}',money_paid='{$total_fee}' ,update_date='$date' ,pay_type={$pay_type} WHERE log_id=$out_trade_no";
		$result = $GLOBALS['db']->query($sql);

		if ($result) {
			//更新订单状态
			$sql = "SELECT order_status,order_amount,money_paid,type FROM {$GLOBALS['ecs']->table('order_info')} WHERE order_id='{$order_id}'";

			$order_info = $GLOBALS['db']->getRow($sql);
			if (!$order_info) {
				return '订单不存在';
			}

			if (!in_array($order_info['order_status'], array(0, 1))) { // 待付款  及 待付尾款时  可更新支付状态
				return '订单状态不正确';
			}

			if ($order_info['type'] == 1) {//预付订单
				$order_status = $order_info['money_paid'] == 0 ? 2 : 3; // 

				$money_paid = $total_fee + $order_info['money_paid'];
				if ($order_status == 3 and $order_info['order_amount'] < $money_paid) {//付尾款
					return '支付金额错误';
				}
			} else {
				if ($order_info['order_amount'] != $total_fee) {//实际支付金额与订单金额不符
					return '支付金额错误';
				}
				$order_status = 3;
				$money_paid = $total_fee;
			}


			$sql = "UPDATE {$GLOBALS['ecs']->table('order_info')} SET order_status={$order_status} ,money_paid='{$money_paid}' ,pay_status=1 ,pay_time='{$date}'WHERE order_id='{$order_id}'";
			$result = $GLOBALS['db']->query($sql);
			return $result ? 1 : '修改订单状态失败';
		} else {
			return '支付记录不存在';
		}
	} else {
		return $pay_info ? '该笔支付已经处理过' : '支付流水不存在';
	}
}

/*
 * # 写退款日志记录
 */

function insert_order_refund_detail_info($order_id) {
	$time = time();
	$sql = "INSERT INTO {$GLOBALS['ecs']->table('order_refund_detail')}(type,belong,title,content,add_time,update_time,order_id)"
			. " VALUES(1,1,3,'退款成功','{$time}','{$time}',{$order_id})";
	$result = $GLOBALS['db']->query($sql);
	return $result;
}
