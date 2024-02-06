<?php
class VersionController extends PublicController {
	/**
	 * 取得最新版本
	 */
	public function actionGetNewVersion() {
		if ((Yii::app()->request->isPostRequest)) {			
			$this->check_key();
			$key = Frame::getStringFromRequest('key');
			$softType = Frame::getStringFromRequest ( 'softType' );
			$deviceType = 3;
			if ( !is_numeric($softType) ){
				$result ['ret_num'] = 211;
				$result ['ret_msg'] = '输入信息包含非法字符';
				echo json_encode ( $result );
				die ();
			}			
			if ($key == 'android')
			{
				$deviceType = 1;
			} else if ($key == 'iphone'){
				$deviceType = 2;
			}			
			$version = TdSoftVersion::model ()->find ( "device_type={$deviceType} && soft_type={$softType} order by version desc" );
			
			if ($version){
				$result ['ret_num'] = 314;
				$result ['ret_msg'] = '发现新版本';
				$result ['version'] = $version->version;
				$result ['desc'] = $version->desc;
				$result ['download_path'] = $version->download_path;
				$result ['size'] = $version->size;
				$result ['update_time'] = $version->created_time;
			} else {
				$result ['ret_num'] = 0;
				$result ['ret_msg'] = '没有该软件的最新版本信息';
			}

			echo json_encode ( $result );
		}
	}

	
	/**
	 * 取得所有版本
	 */
	public function actionGetAllVersions() {
		$this->check_key();
		$key = Frame::getStringFromRequest('key');
		$softType = Frame::getStringFromRequest ( 'softType' );

		$deviceType = 3;
		if ($key == 'android')
		{
			$deviceType = 1;
		} else if ($key == 'ios'){
			$deviceType = 2;
		}

		$versions = TdSoftVersion::model ()->findAll ( "device_type={$deviceType} && soft_type={$softType} order by version desc" );
		if ($versions){
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
			$result ['ret_num'] = 314;
			$result ['ret_msg'] = '没有该软件的最新版本信息';
		}
		
		echo json_encode ( $result );
	}
}
