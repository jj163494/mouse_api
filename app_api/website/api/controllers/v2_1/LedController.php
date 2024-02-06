<?php
class LedController extends PublicController {
	/**
	 * 取得所有led文件
	 */
	public function actionGetLedFileList() {
		// 获取用户信息
		$member_info = $this->check_user();

		$leds = TdLedFile::model()->findAll();
		if ($leds) {
			foreach ($leds as $key=>$value){
				$arr [] = array (
						"group_id"=>$value->group_id,
						"title" => $value->title,
						"led_file_url" => $value->led_file_url,
						"desc"=>$value->desc
				);
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = 'ok';
		$result ['leds'] = $arr;

		echo json_encode ( $result );
	}
}
