<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/../includes/init.php');

$wxpayPath = "./wxpay/";
require_once $wxpayPath . "lib/WxPay.Api.php";
require_once $wxpayPath . "lib/WxPay.Notify.php";
require_once $wxpayPath . "log.php";
require_once "./function.php";



//		echo 123;exit;
//$callback_data = '{"appid":"wxab9245b9041049b0","attach":"a:1:{s:8:\"order_id\";s:2:\"34\";}","bank_type":"CCB_DEBIT","cash_fee":"1","fee_type":"CNY","is_subscribe":"Y","mch_id":"1243542602","nonce_str":"goeddskg96uybw8kokk3eq9u2gsqf7i7","openid":"oIXdjt8Fq1zWgdHl8xyZwpNIP8Yk","out_trade_no":"47","result_code":"SUCCESS","return_code":"SUCCESS","sign":"3CC50CAAEACF58BB3DC0A0E012ED717D","time_end":"20150702112024","total_fee":"1","trade_type":"NATIVE","transaction_id":"1010000102201507020335019165"}';
//$data = json_decode($callback_data); //$callback_data
//$data = (array) $data;
//echo update_order_pay_status($data);
//初始化日志
$logHandler = new CLogFileHandler($wxpayPath . "logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify {

	public $global = '';
	public $ecs = '';

	//查询订单
	public function Queryorder($transaction_id) {
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg) {
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
//			echo 33333;exit;
		if (!array_key_exists("transaction_id", $data)) {
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if (!$this->Queryorder($data["transaction_id"])) {
			$msg = "订单查询失败";
			return false;
		}

		$data = (array) $data;
		$data['total_fee'] = $data['total_fee'] / 100; // 单位 由 分 转换 为 元 
		//更新订单支付状态
		$result = update_order_pay_status($data, 2); // 2  微信支付
		if ($result != 1) {
			$msg = $result;
		}
		return true;
	}

}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->global = $GLOBALS;
$notify->ecs = $ecs;
$notify->Handle(false);



/* # 处理微信支付异步回调逻辑
 * @ paran data array 	
 * return string
 */
//function update_order_pay_status($data) {
//
//	$out_trade_no = $data['out_trade_no'];
//	$total_fee = $data['total_fee'] / 100; // 单位 由 分 转换 为 元 
////	$total_fee = 3700; //'//-------------------------------------------------- for delete '
//	$date = time();
//
//	$sql = "SELECT log_id,order_id FROM {$GLOBALS['ecs']->table('pay_log')} WHERE log_id='{$out_trade_no}' AND is_paid=0 ";
//
//	$pay_info = $GLOBALS['db']->getRow($sql);
//
//	if ($pay_info) {
//		$order_id = $pay_info['order_id'];
//
//		//更新支付记录
//		$sql = "UPDATE {$GLOBALS['ecs']->table('pay_log')} SET is_paid=1 ,transaction_id='{$data['transaction_id']}',money_paid='{$total_fee}' ,update_date='$date' ,pay_type=2 WHERE log_id=$out_trade_no";
//		$result = $GLOBALS['db']->query($sql);
//
//		if ($result) {
//			//更新订单状态
//			$sql = "SELECT order_status,order_amount,money_paid,type FROM {$GLOBALS['ecs']->table('order_info')} WHERE order_id='{$order_id}'";
//
//			$order_info = $GLOBALS['db']->getRow($sql);
//			if (!$order_info) {
//				return '订单不存在';
//			}
//
//			if (!in_array($order_info['order_status'], array(0, 1))) { // 待付款  及 待付尾款时  可更新支付状态
//				return '订单状态不正确';
//			}
//
//			if ($order_info['type'] == 1) {//预付订单
//				$order_status = $order_info['money_paid'] == 0 ? 2 : 3; // 
//
//				$money_paid = $total_fee + $order_info['money_paid'];
//				if ($order_status == 3 and $order_info['order_amount'] < $money_paid) {//付尾款
//					return '支付金额错误';
//				}
//			} else {
//				if ($order_info['order_amount'] != $total_fee) {//实际支付金额与订单金额不符
//					return '支付金额错误';
//				}
//				$order_status = 3;
//				$money_paid = $total_fee;
//			}
//
//
//			$sql = "UPDATE {$GLOBALS['ecs']->table('order_info')} SET order_status={$order_status} ,money_paid='{$money_paid}' ,pay_status=1 ,pay_time='{$date}'WHERE order_id='{$order_id}'";
//			$result = $GLOBALS['db']->query($sql);
//			return $result ? 1 : '修改订单状态失败';
//		} else {
//			return '支付记录不存在';
//		}
//	} else {
//		return '该笔支付已经处理过';
//	}
//}
