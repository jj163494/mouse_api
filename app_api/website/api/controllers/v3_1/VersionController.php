<?php
class VersionController extends PublicController {
	/**
	 * 取得最新版本
	 */
	public function actionGetNewVersion() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		if ((Yii::app()->request->isPostRequest)) {
			$this->check_key();
			$key = Frame::getStringFromRequest('key');
			$softType = Frame::getIntFromRequest( 'softType' );
			if ( empty($softType) ){
				$result['ret_num'] = 201;
				$result['ret_msg'] = $this->language->get('miss_param');
				echo json_encode ( $result );
				die ();
			}			
			if ($key == 'android') {
				$deviceType = 1;
			} else if ($key == 'iphone'){
				$deviceType = 2;
			}else{
				$deviceType = 3;
			}
			$version = TdSoftVersion::model ()->find ( "device_type={$deviceType} && soft_type={$softType} order by version desc" );
			if ($version){
				$result ['ret_num'] = 314;
				$result['ret_msg'] = $this->language->get('has_new_version');
				$result ['version'] = $version->version;
				if(!empty($lang) && $lang != 'ZH'){
					$result ['desc'] = $version["desc{$lang}"];
				}else{
					$result ['desc'] = $version["desc"];
				}
				$result ['download_path'] = $version->download_path;
				$result ['size'] = $version->size;
				$result ['update_time'] = $version->created_time;
			} else {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = 'ok';
			}

			echo json_encode ( $result );
		}
	}

	
	/**
	 * 取得所有版本
	 */
	public function actionGetAllVersions() {
		$lang = Frame::getStringFromRequest('lang');
		// 获取语言文件
		$this->language->read($lang);

		$this->check_key();
		$key = Frame::getStringFromRequest('key');
		$softType = Frame::getIntFromRequest ( 'softType' );
		$version = Frame::getStringFromRequest('version');

		if ($key == 'android') {
			$deviceType = 1;
		} else if ($key == 'ios'){
			$deviceType = 2;
		}else{
			$deviceType = 3;
		}

		// 根据语言获得对应字段 #2017/06/20
		if(!empty($lang) && $lang != "ZH") {
			$field = "`version`, desc{$lang} AS `desc`, download_path, size, created_time";
		}else {
			$field = "`version`, `desc`, download_path, size, created_time";
		}
		// 新增版本参数,查询该版本之后的数据 #2017/06/20
		if(!empty($version)){
			$where = "device_type={$deviceType} && soft_type={$softType} && `version` > '{$version}'";
		}else{
			$where = "device_type={$deviceType} && soft_type={$softType}";
		}
		$criteria = array(
			'select' => $field,
			'condition' => $where,
			'order' => "version desc",
		);
		$versions = TdSoftVersion::model ()->findAll ( $criteria );
		if ($versions){
			$arr = array();
			foreach ( $versions as $key => $value ) {
				$arr [] = array (
						"version" => $value->version,
						"desc" => $value->desc,
						"download_path" => $value->download_path,
						"size" => $value->size,
						"update_time" => $value->created_time
				);
			}

			$result ['ret_num'] = 0;
			$result ['ret_msg'] = 'ok';
			$result ['versions'] = $arr;
		} else {
			$result = array(
				'ret_num' => 309,
				'ret_msg' => $this->language->get('no_data'),
			);
		}
		
		echo json_encode ( $result );
	}
}
