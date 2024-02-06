<?php
class DataStatisticController extends PublicController {
	/**
	 * 上传数据统计
	 */
	public function actionUploadDataStatistic() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$date = Frame::getStringFromRequest ( 'date' );
		$time = Frame::getStringFromRequest ( 'time' );
		$mouse_knock = Frame::getStringFromRequest ( 'mouse_knock' );
		$keycap_knock = Frame::getStringFromRequest ( 'keycap_knock' );
		$move_data = Frame::getStringFromRequest ( 'move_data' );
		$move_meter = Frame::getStringFromRequest ( 'move_meter' );
		
		if (empty ( $date ) || ! isset ( $time )) {
			$result ['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 去数据统计表查询 是否有此条数据
		$find_data_statistics = TdDataStatistics::model ()->find ( "userid='{$member_id}' && datetime = '{$date}'" );

		if (empty ( $find_data_statistics )) {
			// 如果无此数据 新增数据
			$set_total_statistics = new TdDataStatistics ();
			// 判断表单传的time 去数据库对应字段添加数据
			switch ($time) {
				case 0 :
					$data = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_0 = $data;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_0_mouse_knock = $mouse_knock;
					$set_total_statistics->time_0_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 2 :
					$data1 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_2 = $data1;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_2_mouse_knock = $mouse_knock;
					$set_total_statistics->time_2_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 4 :
					$data2 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_4 = $data2;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_4_mouse_knock = $mouse_knock;
					$set_total_statistics->time_4_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 6 :
					$data3 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_6 = $data3;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_6_mouse_knock = $mouse_knock;
					$set_total_statistics->time_6_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 8 :
					$data4 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_8 = $data4;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_8_mouse_knock = $mouse_knock;
					$set_total_statistics->time_8_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 10 :
					$data5 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_10 = $data5;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_10_mouse_knock = $mouse_knock;
					$set_total_statistics->time_10_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				case 12 :
					$data6 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_12 = $data6;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_12_mouse_knock = $mouse_knock;
					$set_total_statistics->time_12_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;

					break;
				case 14 :
					$data7 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_14 = $data7;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_14_mouse_knock = $mouse_knock;
					$set_total_statistics->time_14_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;

					break;
				case 16 :
					$data8 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_16 = $data8;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_16_mouse_knock = $mouse_knock;
					$set_total_statistics->time_16_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;

					break;
				case 18 :
					$data9 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_18 = $data9;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_18_mouse_knock = $mouse_knock;
					$set_total_statistics->time_18_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;

					break;
				case 20 :
					$data10 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_20 = $data10;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_20_mouse_knock = $mouse_knock;
					$set_total_statistics->time_20_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;

					break;
				case 22 :
					$data11 = $mouse_knock + $keycap_knock;
					$set_total_statistics->userid = $member_id;
					$set_total_statistics->datetime = $date;
					$set_total_statistics->time_22 = $data11;
					$set_total_statistics->today_total_statistics = $mouse_knock + $keycap_knock;
					$set_total_statistics->time_22_mouse_knock = $mouse_knock;
					$set_total_statistics->time_22_keycap_knock = $keycap_knock;
					$set_total_statistics->mouse_knock = $mouse_knock;
					$set_total_statistics->keycap_knock = $keycap_knock;
					$set_total_statistics->move_data = $move_data;
					$set_total_statistics->move_meter = $move_meter;
					break;
				default :
					$result ['ret_num'] = 201;
					$result['ret_msg'] = $this->language->get('miss_param');
			}
			if ($set_total_statistics->save ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 901;
				$result['ret_msg'] = $this->language->get('add_fail');
			}
		} else {
			// 数据库有数据 根据用户穿的time 更新数据
			$find_data_statistics0 = $find_data_statistics->time_0;
			$find_data_statistics1 = $find_data_statistics->time_2;
			$find_data_statistics2 = $find_data_statistics->time_4;
			$find_data_statistics3 = $find_data_statistics->time_6;
			$find_data_statistics4 = $find_data_statistics->time_8;
			$find_data_statistics5 = $find_data_statistics->time_10;
			$find_data_statistics6 = $find_data_statistics->time_12;
			$find_data_statistics7 = $find_data_statistics->time_14;
			$find_data_statistics8 = $find_data_statistics->time_16;
			$find_data_statistics9 = $find_data_statistics->time_18;
			$find_data_statistics10 = $find_data_statistics->time_20;
			$find_data_statistics11 = $find_data_statistics->time_22;
			$find_data_statistics12 = $find_data_statistics->today_total_statistics;
			$find_data_statistics13 = $find_data_statistics->mouse_knock;
			$find_data_statistics14 = $find_data_statistics->keycap_knock;
			$find_data_statistics15 = $find_data_statistics->move_data;
			$find_data_statistics16 = $find_data_statistics->move_meter;
			$find_data_statistics17 = $find_data_statistics->time_0_mouse_knock;
			$find_data_statistics18 = $find_data_statistics->time_2_mouse_knock;
			$find_data_statistics19 = $find_data_statistics->time_4_mouse_knock;
			$find_data_statistics20 = $find_data_statistics->time_6_mouse_knock;
			$find_data_statistics21 = $find_data_statistics->time_8_mouse_knock;
			$find_data_statistics22 = $find_data_statistics->time_10_mouse_knock;
			$find_data_statistics23 = $find_data_statistics->time_12_mouse_knock;
			$find_data_statistics24 = $find_data_statistics->time_14_mouse_knock;
			$find_data_statistics25 = $find_data_statistics->time_16_mouse_knock;
			$find_data_statistics26 = $find_data_statistics->time_18_mouse_knock;
			$find_data_statistics27 = $find_data_statistics->time_20_mouse_knock;
			$find_data_statistics28 = $find_data_statistics->time_22_mouse_knock;
			$find_data_statistics29 = $find_data_statistics->time_0_keycap_knock;
			$find_data_statistics30 = $find_data_statistics->time_2_keycap_knock;
			$find_data_statistics31 = $find_data_statistics->time_4_keycap_knock;
			$find_data_statistics32 = $find_data_statistics->time_6_keycap_knock;
			$find_data_statistics33 = $find_data_statistics->time_8_keycap_knock;
			$find_data_statistics34 = $find_data_statistics->time_10_keycap_knock;
			$find_data_statistics35 = $find_data_statistics->time_12_keycap_knock;
			$find_data_statistics36 = $find_data_statistics->time_14_keycap_knock;
			$find_data_statistics37 = $find_data_statistics->time_16_keycap_knock;
			$find_data_statistics38 = $find_data_statistics->time_18_keycap_knock;
			$find_data_statistics39 = $find_data_statistics->time_20_keycap_knock;
			$find_data_statistics40 = $find_data_statistics->time_22_keycap_knock;
			switch ($time) {
				case 0 :
					$data = $mouse_knock + $keycap_knock + $find_data_statistics0;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_0 = $data;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_0_mouse_knock = $mouse_knock + $find_data_statistics17;
					$find_data_statistics->time_0_keycap_knock = $keycap_knock + $find_data_statistics29;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 2 :
					$data1 = $mouse_knock + $keycap_knock + $find_data_statistics1;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_2 = $data1;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_2_mouse_knock = $mouse_knock + $find_data_statistics18;
					$find_data_statistics->time_2_keycap_knock = $keycap_knock + $find_data_statistics30;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 4 :
					$data2 = $mouse_knock + $keycap_knock + $find_data_statistics2;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_4 = $data2;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_4_mouse_knock = $mouse_knock + $find_data_statistics19;
					$find_data_statistics->time_4_keycap_knock = $keycap_knock + $find_data_statistics31;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 6 :
					$data3 = $mouse_knock + $keycap_knock + $find_data_statistics3;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_6 = $data3;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_6_mouse_knock = $mouse_knock + $find_data_statistics20;
					$find_data_statistics->time_6_keycap_knock = $keycap_knock + $find_data_statistics32;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 8 :
					$data4 = $mouse_knock + $keycap_knock + $find_data_statistics4;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_8 = $data4;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_8_mouse_knock = $mouse_knock + $find_data_statistics21;
					$find_data_statistics->time_8_keycap_knock = $keycap_knock + $find_data_statistics33;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 10 :
					$data5 = $mouse_knock + $keycap_knock + $find_data_statistics5;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_10 = $data5;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_10_mouse_knock = $mouse_knock + $find_data_statistics22;
					$find_data_statistics->time_10_keycap_knock = $keycap_knock + $find_data_statistics34;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 12 :
					$data6 = $mouse_knock + $keycap_knock + $find_data_statistics6;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_12 = $data6;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_12_mouse_knock = $mouse_knock + $find_data_statistics23;
					$find_data_statistics->time_12_keycap_knock = $keycap_knock + $find_data_statistics35;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 14 :
					$data7 = $mouse_knock + $keycap_knock + $find_data_statistics7;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_14 = $data7;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_14_mouse_knock = $mouse_knock + $find_data_statistics24;
					$find_data_statistics->time_14_keycap_knock = $keycap_knock + $find_data_statistics36;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 16 :
					$data8 = $mouse_knock + $keycap_knock + $find_data_statistics8;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_16 = $data8;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_16_mouse_knock = $mouse_knock + $find_data_statistics25;
					$find_data_statistics->time_16_keycap_knock = $keycap_knock + $find_data_statistics37;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 18 :
					$data9 = $mouse_knock + $keycap_knock + $find_data_statistics9;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_18 = $data9;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_18_mouse_knock = $mouse_knock + $find_data_statistics26;
					$find_data_statistics->time_18_keycap_knock = $keycap_knock + $find_data_statistics38;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 20 :
					$data10 = $mouse_knock + $keycap_knock + $find_data_statistics10;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_20 = $data10;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_20_mouse_knock = $mouse_knock + $find_data_statistics27;
					$find_data_statistics->time_20_keycap_knock = $keycap_knock + $find_data_statistics39;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				case 22 :
					$data11 = $mouse_knock + $keycap_knock + $find_data_statistics11;
					$find_data_statistics->userid = $member_id;
					$find_data_statistics->datetime = $date;
					$find_data_statistics->time_22 = $data11;
					$find_data_statistics->today_total_statistics = $mouse_knock + $keycap_knock + $find_data_statistics12;
					$find_data_statistics->mouse_knock = $mouse_knock + $find_data_statistics13;
					$find_data_statistics->keycap_knock = $keycap_knock + $find_data_statistics14;
					$find_data_statistics->time_22_mouse_knock = $mouse_knock + $find_data_statistics28;
					$find_data_statistics->time_22_keycap_knock = $keycap_knock + $find_data_statistics40;
					$find_data_statistics->move_data = $move_data + $find_data_statistics15;
					$find_data_statistics->move_meter = $move_meter + $find_data_statistics16;
					break;
				default :
					$result ['ret_num'] = 201;
					$result['ret_msg'] = $this->language->get('miss_param');
			}
			if ($find_data_statistics->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 906;
				$result['ret_msg'] = $this->language->get('update_fail');
			}
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询当日数据统计
	 */
	public function actionFindTodayDataStatistic() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$date = Frame::getStringFromRequest ( 'date' );
		if (empty ( $date )) {
			$result ['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 去数据统计表查询 是否有此条数据
		$find_today_datastatistics = TdDataStatistics::model ()->find ( " userid='{$member_id}' && datetime='{$date}'" );
		if (! empty ( $find_today_datastatistics )) {
			// 有数据返回对应内容
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['datetime'] = $find_today_datastatistics->datetime;
			$result ['time_0'] = $find_today_datastatistics->time_0;
			$result ['time_2'] = $find_today_datastatistics->time_2;
			$result ['time_4'] = $find_today_datastatistics->time_4;
			$result ['time_6'] = $find_today_datastatistics->time_6;
			$result ['time_8'] = $find_today_datastatistics->time_8;
			$result ['time_10'] = $find_today_datastatistics->time_10;
			$result ['time_12'] = $find_today_datastatistics->time_12;
			$result ['time_14'] = $find_today_datastatistics->time_14;
			$result ['time_16'] = $find_today_datastatistics->time_16;
			$result ['time_18'] = $find_today_datastatistics->time_18;
			$result ['time_20'] = $find_today_datastatistics->time_20;
			$result ['time_22'] = $find_today_datastatistics->time_22;
			$result ['today_total_statistics'] = $find_today_datastatistics->today_total_statistics;
			$result ['mouse_knock'] = $find_today_datastatistics->mouse_knock;
			$result ['keycap_knock'] = $find_today_datastatistics->keycap_knock;
			$result ['time_0_mouse_knock'] = $find_today_datastatistics->time_0_mouse_knock;
			$result ['time_2_mouse_knock'] = $find_today_datastatistics->time_2_mouse_knock;
			$result ['time_4_mouse_knock'] = $find_today_datastatistics->time_4_mouse_knock;
			$result ['time_6_mouse_knock'] = $find_today_datastatistics->time_6_mouse_knock;
			$result ['time_8_mouse_knock'] = $find_today_datastatistics->time_8_mouse_knock;
			$result ['time_10_mouse_knock'] = $find_today_datastatistics->time_10_mouse_knock;
			$result ['time_12_mouse_knock'] = $find_today_datastatistics->time_12_mouse_knock;
			$result ['time_14_mouse_knock'] = $find_today_datastatistics->time_14_mouse_knock;
			$result ['time_16_mouse_knock'] = $find_today_datastatistics->time_16_mouse_knock;
			$result ['time_18_mouse_knock'] = $find_today_datastatistics->time_18_mouse_knock;
			$result ['time_20_mouse_knock'] = $find_today_datastatistics->time_20_mouse_knock;
			$result ['time_22_mouse_knock'] = $find_today_datastatistics->time_22_mouse_knock;
			$result ['time_0_keycap_knock'] = $find_today_datastatistics->time_0_keycap_knock;
			$result ['time_2_keycap_knock'] = $find_today_datastatistics->time_2_keycap_knock;
			$result ['time_4_keycap_knock'] = $find_today_datastatistics->time_4_keycap_knock;
			$result ['time_6_keycap_knock'] = $find_today_datastatistics->time_6_keycap_knock;
			$result ['time_8_keycap_knock'] = $find_today_datastatistics->time_8_keycap_knock;
			$result ['time_10_keycap_knock'] = $find_today_datastatistics->time_10_keycap_knock;
			$result ['time_12_keycap_knock'] = $find_today_datastatistics->time_12_keycap_knock;
			$result ['time_14_keycap_knock'] = $find_today_datastatistics->time_14_keycap_knock;
			$result ['time_16_keycap_knock'] = $find_today_datastatistics->time_16_keycap_knock;
			$result ['time_18_keycap_knock'] = $find_today_datastatistics->time_18_keycap_knock;
			$result ['time_20_keycap_knock'] = $find_today_datastatistics->time_20_keycap_knock;
			$result ['time_22_keycap_knock'] = $find_today_datastatistics->time_22_keycap_knock;
			$result ['move_data'] = $find_today_datastatistics->move_data;
			$result ['move_meter'] = $find_today_datastatistics->move_meter;
		} else {
			// 没数据返回空
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['datetime'] = $date;
			$result ['time_0'] = 0;
			$result ['time_2'] = 0;
			$result ['time_4'] = 0;
			$result ['time_6'] = 0;
			$result ['time_8'] = 0;
			$result ['time_10'] = 0;
			$result ['time_12'] = 0;
			$result ['time_14'] = 0;
			$result ['time_16'] = 0;
			$result ['time_18'] = 0;
			$result ['time_20'] = 0;
			$result ['time_22'] = 0;
			$result ['time_0_keycap_knock'] = 0;
			$result ['time_2_keycap_knock'] = 0;
			$result ['time_4_keycap_knock'] = 0;
			$result ['time_6_keycap_knock'] = 0;
			$result ['time_8_keycap_knock'] = 0;
			$result ['time_10_keycap_knock'] = 0;
			$result ['time_12_keycap_knock'] = 0;
			$result ['time_14_keycap_knock'] = 0;
			$result ['time_16_keycap_knock'] = 0;
			$result ['time_18_keycap_knock'] = 0;
			$result ['time_20_keycap_knock'] = 0;
			$result ['time_22_keycap_knock'] = 0;
			$result ['time_0_mouse_knock'] = 0;
			$result ['time_2_mouse_knock'] = 0;
			$result ['time_4_mouse_knock'] = 0;
			$result ['time_6_mouse_knock'] = 0;
			$result ['time_8_mouse_knock'] = 0;
			$result ['time_10_mouse_knock'] = 0;
			$result ['time_12_mouse_knock'] = 0;
			$result ['time_14_mouse_knock'] = 0;
			$result ['time_16_mouse_knock'] = 0;
			$result ['time_18_mouse_knock'] = 0;
			$result ['time_20_mouse_knock'] = 0;
			$result ['time_22_mouse_knock'] = 0;
			$result ['today_total_statistics'] = 0;
			$result ['mouse_knock'] = 0;
			$result ['keycap_knock'] = 0;
			$result ['move_data'] = 0;
			$result ['move_meter'] = 0;
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 查询历史数据统计
	 */
	public function actionFindHistoryDataStatistic() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$time_type = Frame::getStringFromRequest ( 'time_type' );

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 获取当前时间
		$a = time ();
		$b = date ( "Y-m-d", $a );
		$now_time1 = str_replace ( '-', '', $b );
		// 查询用户最开始的时间
		$start_time = TdDataStatistics::model ()->find ( " userid='$member_id'" );
		if(empty($start_time)){
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
			echo json_encode ( $result );
			die ();
		}
		
		$start_time1 = $start_time->datetime;
		// 转换成时间戳
		$end3 = strtotime ( $start_time1 );
		// 判断是否查到该时间  查到则使用 没查到使用当前时间
		if (empty ( $start_time1 )) {
			$start_time_new = $now_time1;
		} else {
			$start_time_new = $start_time1;
		}
		if (empty ( $time_type )) {
			// 查询这个时间段所有数据的汇总
			$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$start_time_new}' AND '{$now_time1}'";
			$result = yii::app ()->db->createCommand ( $sql );
			$info = $result->queryAll ();
			// 判断id是否为0（除数不能为0）
			if ($info [0] ['id'] != 0) {
				$pingjun3 = round ( $info [0] ['sum_mouse_knock'] / $info [0] ['id'] );
				$pingjun4 = round ( $info [0] ['sum_keycap_knock'] / $info [0] ['id'] );
			} else {
				$pingjun3 = 0;
				$pingjun4 = 0;
			}
			$total_time = array (
					'mouse_knock' => ! empty ( $info [0] ['sum_mouse_knock'] ) ? $info [0] ['sum_mouse_knock'] : 0,
					'keycap_knock' => ! empty ( $info [0] ['sum_keycap_knock'] ) ? $info [0] ['sum_keycap_knock'] : 0,
					'average_mouse' => $pingjun3,
					'average_keycap' => $pingjun4,
					'move_data' => ! empty ( $info [0] ['sum_move_data'] ) ? $info [0] ['sum_move_data'] : 0,
					'move_meter' => ! empty ( $info [0] ['sum_move_meter'] ) ? $info [0] ['sum_move_meter'] : 0
			);

			$end1 = date ( 'Y-m-d', $a );

			$start2 = strtotime ( $start_time1 . '-01' );
			$end2 = strtotime ( $end1 . '-01' );

			$i = 0;
			$d = array ();
			// 当前时间到1231
			$sub_time = substr ( $now_time1, 4, 4 );
			$sub_time3 = str_replace ( $sub_time, 1231, $now_time1 );
			$sub_time4 = strtotime ( $sub_time3 );
			// 循环开始时间和结束时间 按年拆分
			while ( $start2 <= $sub_time4 ) {
				$d [$i] = trim ( date ( 'Y-01-01', $start2 ), ' ' );
				$start2 += strtotime ( '+1 year', $start2 ) - $start2;
				$now_time = str_replace ( '-', '', $i ++ );
			}
			$time = str_replace ( '-', '', $d );
			$info = array ();
			for($v = 0; $v < count ( $time ); $v ++) {
				// 查询结束时间（12-31）
				$sub_time = substr ( $time [$v], 4, 4 );
				$sub_time1 = str_replace ( $sub_time, '1231', $time );
				// 查询开始时间（01-01）
				$sub_time2 = str_replace ( $sub_time, '0101', $time );
				$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$sub_time2[$v]}' AND '{$sub_time1[$v]}'";
				$result = yii::app ()->db->createCommand ( $sql );
				$info [$v] = $result->queryAll ();
			}
			$test = array ();
			foreach ( $sub_time2 as $key => $value ) {
				$unix_time = strtotime ( $sub_time2 [$key] );
				for($i = 0; $i <= count ( $info ); $i ++) {
					//（判断id  除数不能为0）
					if ($info [$key] [0] ['id'] != 0) {
						$pingjun6 = round ( $info [$key] [0] ['sum_mouse_knock'] / $info [$key] [0] ['id'] );
						$pingjun7 = round ( $info [$key] [0] ['sum_keycap_knock'] / $info [$key] [0] ['id'] );
					} else {
						$pingjun6 = 0;
						$pingjun7 = 0;
					}
					$test [$key] = array (
							'time1' => $unix_time,
							'time' => $sub_time2 [$key],
							'mouse_knock' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
							'keycap_knock' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
							'average_mouse' => $pingjun6,
							'average_keycap' => $pingjun7,
							'move_data' => ! empty ( $info [$key] [0] ['sum_move_data'] ) ? $info [$key] [0] ['sum_move_data'] : 0,
							'move_meter' => ! empty ( $info [$key] [0] ['sum_move_meter'] ) ? $info [$key] [0] ['sum_move_meter'] : 0
					);
				}
			}
			$result = array (
					'total_all' => $total_time,
					'total' => $test
			);
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['start_time'] = $start_time_new;
		} else if ($time_type == 1) {
			// 获取开始时间 当前时间往前推算1年
			$start2 = strtotime ( $b . '-01' );
			$new_start = strtotime ( '-1 year +1 month', $start2 );
			// （转成日期格式）
			$c = date ( "Y-m-d", $new_start );
			// （开始时间从1号开始）
			$c1 = substr_replace ( $c, '01', - 2 );
			$new_start1 = str_replace ( '-', '', $c1 );
			$sub_time5 = strtotime ( $new_start1 );

			$sub_time6 = date ( "Y-m-d", $sub_time5 );
			$sub_time7 = str_replace ( '-', '', $sub_time6 );

			// 获取全部年份的总和
			$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$new_start1}' AND '{$now_time1}'";
			$result = yii::app ()->db->createCommand ( $sql );
			$info = $result->queryAll ();
			// （除数不能为0）
			if ($info [0] ['id'] != 0) {
				$pingjun3 = round ( $info [0] ['sum_mouse_knock'] / $info [0] ['id'] );
				$pingjun4 = round ( $info [0] ['sum_keycap_knock'] / $info [0] ['id'] );
			} else {
				$pingjun3 = 0;
				$pingjun4 = 0;
			}
			// 总时间断数据汇总
			$total_time = array (
					'mouse_knock' => ! empty ( $info [0] ['sum_mouse_knock'] ) ? $info [0] ['sum_mouse_knock'] : 0,
					'keycap_knock' => ! empty ( $info [0] ['sum_keycap_knock'] ) ? $info [0] ['sum_keycap_knock'] : 0,
					'average_mouse' => $pingjun3,
					'average_keycap' => $pingjun4,
					'move_data' => ! empty ( $info [0] ['sum_move_data'] ) ? $info [0] ['sum_move_data'] : 0,
					'move_meter' => ! empty ( $info [0] ['sum_move_meter'] ) ? $info [0] ['sum_move_meter'] : 0
			);
			$end1 = date ( 'Y-m-d', $a );
			$start2 = strtotime ( $sub_time6 . '-01' );
			$end2 = strtotime ( $end1 . '-01' );
			$i = 0;
			$d = array ();
			// ( 1年按月拆分)
			while ( $start2 <= $end2 ) {
				$d [$i] = trim ( date ( 'Y-m-d', $start2 ), ' ' );
				$start2 += strtotime ( '+1 month', $start2 ) - $start2;
				$now_time = str_replace ( '-', '', $i ++ );
			}
			$time = str_replace ( '-', '', $d );
			$info = array ();
			for($v = 0; $v < count ( $time ); $v ++) {
				// 查询结束时间
				$sub_time = substr_replace ( $time [$v], 31, - 2 );
				$sql = "SELECT COUNT(id) count ,SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$time[$v]}' AND '{$sub_time}'";
				$result = yii::app ()->db->createCommand ( $sql );
				$info [$v] = $result->queryAll ();
			}
			$test = array ();
			// 1年中每个月的数据
			foreach ( $time as $key => $value ) {
				$unix_time = strtotime ( $time [$key] );
				for($i = 0; $i <= count ( $info ); $i ++) {
					if ($info [$key] [0] ['count'] != 0) {
						$pingjun6 = round ( $info [$key] [0] ['sum_mouse_knock'] / $info [$key] [0] ['count'] );
						$pingjun7 = round ( $info [$key] [0] ['sum_keycap_knock'] / $info [$key] [0] ['count'] );
					} else {
						$pingjun6 = 0;
						$pingjun7 = 0;
					}
					$test [$key] = array (
							'time1' => $unix_time,
							'time' => $time [$key],
							'mouse_knock' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
							'keycap_knock' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
							'average_mouse' => $pingjun6,
							'average_keycap' => $pingjun7,
							'move_data' => ! empty ( $info [$key] [0] ['sum_move_data'] ) ? $info [$key] [0] ['sum_move_data'] : 0,
							'move_meter' => ! empty ( $info [$key] [0] ['sum_move_meter'] ) ? $info [$key] [0] ['sum_move_meter'] : 0
					);
				}
			}
			$result = array (
					'total_all' => $total_time,
					'total' => $test
			);
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['start_time'] = $sub_time7;
		} else if ($time_type == 2) {
			// 当前时间往前推算3个月
			$start2 = strtotime ( $b . '-01' );
			$new_start = strtotime ( '-3 month', $start2 );
			$c = date ( "Y-m-d", $new_start );
			$new_start1 = str_replace ( '-', '', $c );

			$start = strtotime ( $new_start1 );
			$start1 = strtotime ( $now_time1 );
			$end = date ( 'Y-m-d', $start );
			$end1 = date ( 'Y-m-d', $start1 );
			$start2 = strtotime ( $end . '-01' );
			$end2 = strtotime ( $end1 . '-01' );
			$i = 0;
			$d = array ();
			// 获取周对应的0-6
			$week = date ( 'w', $start2 );
			// 判断对应的0-6只要自然周 周一开始
			switch ($week) {
				case 0 :
					// 开始时间（周一）
					$new_start = strtotime ( '-6 day', $start2 );
					// 结束时间（星期天）
					$new_end = strtotime ( '+6 day', $start2 );
					break;

				case 2 :
					$new_start = strtotime ( '-1 day', $start2 );
					$new_end = strtotime ( '+6 day', $start2 );
					break;

				case 3 :
					$new_start = strtotime ( '-2 day', $start2 );
					$new_end = strtotime ( '+6 day', $start2 );
					break;

				case 4 :
					$new_start = strtotime ( '-3 day', $start2 );
					$new_end = strtotime ( '+6 day', $start2 );
					break;

				case 5 :
					$new_start = strtotime ( '-4 day', $start2 );
					$new_end = strtotime ( '+6 day', $start2 );
					break;

				case 6 :
					$new_start = strtotime ( '-5 day', $start2 );
					$new_end = strtotime ( '+6 day', $start2 );
					break;
			}
			if ($week == 1) {
				// 如果正好是周一 那就查询期间的数据
				while ( $start2 <= $end2 ) {
					$d [$i] = trim ( date ( 'Y-m-d', $start2 ), ' ' );
					$start2 += strtotime ( '+7 day', $start2 ) - $start2;
					$now_time = str_replace ( '-', '', $i ++ );
				}
				$time = str_replace ( '-', '', $d );
				$info = array ();
				for($v = 0; $v < count ( $time ); $v ++) {
					$end_time = strtotime ( $time [$v] );
					$end_time1 = strtotime ( '+6 day', $end_time );
					$end_time2 = date ( "Y-m-d", $end_time1 );
					$end_time3 = str_replace ( '-', '', $end_time2 );
					$sql = "SELECT COUNT(id) count, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$time[$v]}' AND '{$end_time3}'";
					$result = yii::app ()->db->createCommand ( $sql );
					$info [$v] = $result->queryAll ();
				}
				$test = array ();
				foreach ( $info as $key => $value ) {
					$unix_time = strtotime ( $time [$key] );
					if ($info [$key] [0] ['count'] != 0) {
						$pingjun6 = round ( $info [$key] [0] ['sum_mouse_knock'] / $info [$key] [0] ['count'] );
						$pingjun7 = round ( $info [$key] [0] ['sum_keycap_knock'] / $info [$key] [0] ['count'] );
					} else {
						$pingjun6 = 0;
						$pingjun7 = 0;
					}
					$test [$key] = array (
							'time1' => $unix_time,
							'time' => $time [$key],
							'mouse_knock' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
							'keycap_knock' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
							'average_mouse' => $pingjun6,
							'average_keycap' => $pingjun7,
							'move_data' => ! empty ( $info [$key] [0] ['sum_move_data'] ) ? $info [$key] [0] ['sum_move_data'] : 0,
							'move_meter' => ! empty ( $info [$key] [0] ['sum_move_meter'] ) ? $info [$key] [0] ['sum_move_meter'] : 0
					);
				}
				// 查询3个月前到现在的总数据
				$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$time[0]}' AND '{$now_time1}'";
				$result = yii::app ()->db->createCommand ( $sql );
				$info = $result->queryAll ();
				if ($info [0] ['id'] != 0) {
					$pingjun6 = round ( $info [0] ['sum_mouse_knock'] / $info [0] ['id'] );
					$pingjun7 = round ( $info [0] ['sum_keycap_knock'] / $info [0] ['id'] );
				} else {
					$pingjun6 = 0;
					$pingjun7 = 0;
				}
				// 3个月的总数据
				$total_time = array (
						'mouse_knock' => ! empty ( $info [0] ['sum_mouse_knock'] ) ? $info [0] ['sum_mouse_knock'] : 0,
						'keycap_knock' => ! empty ( $info [0] ['sum_keycap_knock'] ) ? $info [0] ['sum_keycap_knock'] : 0,
						'average_mouse' => $pingjun6,
						'average_keycap' => $pingjun7,
						'move_data' => ! empty ( $info [0] ['sum_move_data'] ) ? $info [0] ['sum_move_data'] : 0,
						'move_meter' => ! empty ( $info [0] ['sum_move_meter'] ) ? $info [0] ['sum_move_meter'] : 0
				);
			} else {
				// 循环开始时间周一和结束时间星期天 按7天拆分
				while ( $new_start <= $end2 ) {
					$d [$i] = trim ( date ( 'Y-m-d', $new_start ), ' ' );
					$new_start += strtotime ( '+7 day', $new_start ) - $new_start;
					$now_time = str_replace ( '-', '', $i ++ );
				}
				$time = str_replace ( '-', '', $d );
				$info = array ();
				for($v = 0; $v < count ( $time ); $v ++) {
					$new_end4 = strtotime ( $time [$v] );
					$new_end = strtotime ( '+6 day', $new_end4 );
					$new_end2 = date ( 'Y-m-d', $new_end );
					$new_end3 = str_replace ( '-', '', $new_end2 );
					$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$time[$v]}' AND '{$new_end3}'";
					$result = yii::app ()->db->createCommand ( $sql );
					$info [$v] = $result->queryAll ();
				}
				$test = array ();
				// 循环时间 获取每周的数据汇总
				foreach ( $time as $key => $value ) {
					$unix_time = strtotime ( $time [$key] );
					for($i = 0; $i <= count ( $info ); $i ++) {
						if ($info [$key] [0] ['id'] != 0) {
							$pingjun6 = round ( $info [$key] [0] ['sum_mouse_knock'] / $info [$key] [0] ['id'] );
							$pingjun7 = round ( $info [$key] [0] ['sum_keycap_knock'] / $info [$key] [0] ['id'] );
						} else {
							$pingjun6 = 0;
							$pingjun7 = 0;
						}
						$test [$key] = array (
								'time1' => $unix_time,
								'time' => $time [$key],
								'mouse_knock' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
								'keycap_knock' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
								'average_mouse' => $pingjun6,
								'average_keycap' => $pingjun7,
								'move_data' => ! empty ( $info [$key] [0] ['sum_move_data'] ) ? $info [$key] [0] ['sum_move_data'] : 0,
								'move_meter' => ! empty ( $info [$key] [0] ['sum_move_meter'] ) ? $info [$key] [0] ['sum_move_meter'] : 0
						);
					}
				}
				// 查询3个月前到现在的总数据
				$sql = "SELECT COUNT(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$time[0]}' AND '{$now_time1}'";
				$result = yii::app ()->db->createCommand ( $sql );
				$info = $result->queryAll ();
				$pingjun6 = round ( $info [0] ['sum_mouse_knock'] / $info [0] ['id'] );
				$pingjun7 = round ( $info [0] ['sum_keycap_knock'] / $info [0] ['id'] );
				// 3个月的总数据
				$total_time = array (
						'mouse_knock' => ! empty ( $info [0] ['sum_mouse_knock'] ) ? $info [0] ['sum_mouse_knock'] : 0,
						'keycap_knock' => ! empty ( $info [0] ['sum_keycap_knock'] ) ? $info [0] ['sum_keycap_knock'] : 0,
						'average_mouse' => $pingjun6,
						'average_keycap' => $pingjun7,
						'move_data' => ! empty ( $info [0] ['sum_move_data'] ) ? $info [0] ['sum_move_data'] : 0,
						'move_meter' => ! empty ( $info [0] ['sum_move_meter'] ) ? $info [0] ['sum_move_meter'] : 0
				);
			}
			$result = array (
					'total_all' => $total_time,
					'total' => $test
			);
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		} else if ($time_type == 3) {
			// 当前时间往前推算7天
			$start2 = strtotime ( $b . '-01' );
			$new_start = strtotime ( '-6 day', $start2 );
			$c = date ( "Y-m-d", $new_start );
			$new_start1 = str_replace ( '-', '', $c );
			// 查询7天数据汇总
			$sql = "SELECT count(id) as id, SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND(mouse_knock+keycap_knock+move_data+move_meter)>0 AND datetime BETWEEN '{$new_start1}' AND '{$now_time1}'";
			$result = yii::app ()->db->createCommand ( $sql );
			$info = $result->queryAll ();
			if ($info [0] ['id'] != 0) {
				$pingjun3 = round ( $info [0] ['sum_mouse_knock'] / $info [0] ['id'] );
				$pingjun4 = round ( $info [0] ['sum_keycap_knock'] / $info [0] ['id'] );
			} else {
				$pingjun3 = 0;
				$pingjun4 = 0;
			}
			$total_time = array (
					'mouse_knock' => ! empty ( $info [0] ['sum_mouse_knock'] ) ? $info [0] ['sum_mouse_knock'] : 0,
					'keycap_knock' => ! empty ( $info [0] ['sum_keycap_knock'] ) ? $info [0] ['sum_keycap_knock'] : 0,
					'average_mouse' => $pingjun3,
					'average_keycap' => $pingjun4,
					'move_data' => ! empty ( $info [0] ['sum_move_data'] ) ? $info [0] ['sum_move_data'] : 0,
					'move_meter' => ! empty ( $info [0] ['sum_move_meter'] ) ? $info [0] ['sum_move_meter'] : 0
			);
			$start = strtotime ( $new_start1 );
			$start1 = strtotime ( $now_time1 );

			$end = date ( 'Y-m-d', $start );
			$end1 = date ( 'Y-m-d', $start1 );

			$start2 = strtotime ( $end . '-01' );
			$end2 = strtotime ( $end1 . '-01' );

			$i = 0;
			$d = array ();
			// 开始时间和结束时间 按天拆分
			while ( $start2 <= $end2 ) {
				$d [$i] = trim ( date ( 'Y-m-d', $start2 ), ' ' );
				$start2 += strtotime ( '+1 day', $start2 ) - $start2;
				$now_time = str_replace ( '-', '', $i ++ );
			}
			$time = str_replace ( '-', '', $d );
			$info = array ();
			// 循环7天获取每天数据的汇总
			for($v = 0; $v < count ( $time ); $v ++) {
				$sql = "SELECT SUM(mouse_knock) sum_mouse_knock,SUM(keycap_knock) sum_keycap_knock,SUM(move_data) sum_move_data,SUM(move_meter) sum_move_meter from td_data_statistics where userid='{$member_id}' AND datetime='{$time[$v]}'";
				$result = yii::app ()->db->createCommand ( $sql );
				$info [$v] = $result->queryAll ();
			}
			$test = array ();
			foreach ( $time as $key => $value ) {
				$unix_time = strtotime ( $time [$key] );
				for($i = 0; $i <= count ( $info ); $i ++) {
					$test [$key] = array (
							'time1' => $unix_time,
							'time' => $time [$key],
							'mouse_knock' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
							'keycap_knock' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
							'average_mouse' => ! empty ( $info [$key] [0] ['sum_mouse_knock'] ) ? $info [$key] [0] ['sum_mouse_knock'] : 0,
							'average_keycap' => ! empty ( $info [$key] [0] ['sum_keycap_knock'] ) ? $info [$key] [0] ['sum_keycap_knock'] : 0,
							'move_data' => ! empty ( $info [$key] [0] ['sum_move_data'] ) ? $info [$key] [0] ['sum_move_data'] : 0,
							'move_meter' => ! empty ( $info [$key] [0] ['sum_move_meter'] ) ? $info [$key] [0] ['sum_move_meter'] : 0
					);
				}
			}
			$result = array (
					'total_all' => $total_time,
					'total' => $test
			);
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['start_time'] = $new_start1;
		}

		echo json_encode ( $result );
	}


	/**
	 * 查看设备数据详情
	 */
	public function actionfindDeviceDeatil() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$device_id = Frame::getStringFromRequest ( 'device_id' );
		if (empty ( $device_id )) {
			$result ['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 判断数据库是否有该条数据 如果有则返回该条数据详情
		$find_device_detail = TdEquipmentData::model ()->find ( "userid='{$member_id}' && device_id='{$device_id}'" );
		// 鼠标左键+右键汇总
		$mouse_total = $find_device_detail ['mouse_knock_left'] + $find_device_detail ['mouse_knock_right'];

		if ($find_device_detail) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['id'] = $find_device_detail->id;
			$result ['userid'] = $member_id;
			$result ['device_type'] = $find_device_detail->device_type;
			$result ['device_name'] = $find_device_detail->device_name;
			$result ['device_id'] = $find_device_detail->device_id;
			$result ['keycap_knock'] = $find_device_detail->keycap_knock;
			$result ['mouse_knock_left'] = $find_device_detail->mouse_knock_left;
			$result ['mouse_knock_right'] = $find_device_detail->mouse_knock_right;
			$result ['mouse_knock_total'] = $mouse_total;
			$result ['move_meter'] = $find_device_detail->move_meter;
			$result ['old_mouse_knock_left'] = $find_device_detail->old_mouse_knock_left;
			$result ['old_mouse_knock_right'] = $find_device_detail->old_mouse_knock_right;
			$result ['old_move_meter'] = $find_device_detail->old_move_meter;
			$result ['update_time'] = $find_device_detail->update_time;
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = $this->language->get('no_data');
		}

		echo json_encode ( $result );
	}
	
	
	/**
	 * 查询7天分页记录
	 */
	public function actionSevenHistroy() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		// 页码从0开始
		$page = Frame::getStringFromRequest ( 'page' );

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 7条数据
		$offset = 7 * $page;
		// 查询数据按7条返回  （分页）
		$find_page = TdDataStatistics::model ()->findAll ( "userid='{$member_id}' order by id desc limit $offset,7" );
		if ($find_page) {
			foreach ( $find_page as $key => $value ) {
				$arr [] = array (
						"datetime" => strtotime ( $value->datetime ),
						"today_total_statistics" => $value->today_total_statistics,
						"mouse_knock" => $value->mouse_knock,
						"keycap_knock" => $value->keycap_knock,
						"move_data" => $value->move_data,
						"move_meter" => $value->move_meter
				);
			}

			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['seven_history'] = $arr;
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = $this->language->get('no_data');
		}

		echo json_encode ( $result );
	}
	
	/**
	 * 修改设备名称
	 */
	public function actionUpdateDeviceName() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$device_id = Frame::getStringFromRequest ( 'device_id' );
		$device_name = Frame::getStringFromRequest ( 'device_name' );
		if (empty ( $device_id ) || empty ( $device_name )) {
			$result ['ret_num'] = 201;
			$result['ret_msg'] = $this->language->get('miss_param');
			echo json_encode ( $result );
			die ();
		}

		// 获取用户信息
		$member_info = $this->check_user($lang);
		$member_id = $member_info['member_id'];

		// 查询数据库是否有该条记录 查到修改
		$find_device_name = TdEquipmentData::model ()->find ( " userid='{$member_id}' && device_id='{$device_id}'" );
		if ($find_device_name) {

			$find_device_name->device_id = $device_id;
			$find_device_name->device_name = $device_name;
			if ($find_device_name->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			} else {
				$result ['ret_num'] = 906;
				$result ['ret_msg'] = $this->language->get('update_fail');
			}
		} else {
			$result ['ret_num'] = 309;
			$result ['ret_msg'] = $this->language->get('no_data');
		}
		// 去设备表查询是否有该条数据如果有则修改 如果没有也返回成功
		$find_devicenickname = TdUserDevice::model ()->find ( " userid='{$member_id}' && device_code='{$device_id}'" );
		if ($find_devicenickname) {
			$find_devicenickname->device_nickname = $device_name;
			if ($find_devicenickname->update ()) {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			}
		} else {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
		}

		echo json_encode ( $result );
	}
}