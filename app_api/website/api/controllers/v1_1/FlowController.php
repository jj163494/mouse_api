<?php
class FlowController extends PublicController {
	/**
	 * 取得最新版本
	 */
	public function actionStat() {
		if ((Yii::app()->request->isPostRequest)) {
			if(!empty($_SERVER["HTTP_CLIENT_IP"])){
				$cip = $_SERVER["HTTP_CLIENT_IP"];
			}
			elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}
			elseif(!empty($_SERVER["REMOTE_ADDR"])){
				$cip = $_SERVER["REMOTE_ADDR"];
			}
			else{
				$cip = "无法获取！";
			}
			$result ['ip'] = $cip;
			$result ['pre_url'] = $_SERVER['HTTP_REFERER'];
			$result ['browser_name'] = $this::getBrowser();
			$result ['browser_version'] = $this::getBrowserVer();
			//$result ['session_id'] = $_COOKIE["PHPSESSID"];
			//session_start('p0cqm5alg9q9obk1ulq853n247');
			//$result ['session'] = $_SESSION;
			//print_r($_SESSION);
			
			//判断是否生成了唯一标识
			if(isset($_COOKIE["UID"])){
				$uid = $_COOKIE["UID"];
				setcookie('UID', $uid, time() + 3600 * 0.5);
			} else {
				$uid = uniqid('', true);
				setcookie('UID',$uid, time() + 3600 * 0.5);
			}
			$result ['uid'] = $uid;
			echo json_encode ( $result );
		}
	}

	function getBrowser(){
	    $agent=$_SERVER["HTTP_USER_AGENT"];
	    if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0')) //ie11判断
	    return "ie";
	    else if(strpos($agent,'Firefox')!==false)
	    return "firefox";
	    else if(strpos($agent,'Chrome')!==false)
	    return "chrome";
	    else if(strpos($agent,'Opera')!==false)
	    return 'opera';
	    else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
	    return 'safari';
	    else
	    return 'unknown';
	}
	 
	function getBrowserVer(){
	    if (empty($_SERVER['HTTP_USER_AGENT'])){    //当浏览器没有发送访问者的信息的时候
	        return 'unknow';
	    }
	    $agent= $_SERVER['HTTP_USER_AGENT'];   
	    if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
	        return $regs[1];
	    elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
	        return $regs[1];
	    elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
	        return $regs[1];
	    elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
	        return $regs[1];
	    elseif ((strpos($agent,'Chrome')==false)&&preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
	        return $regs[1];
	    else
	        return 'unknow';
	}
}
