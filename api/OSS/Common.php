<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
require_once __DIR__ . '/Config.php';

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * Class Common
 *
 * 示例程序【Samples/*.php】 的Common类，用于获取OssClient实例和其他公用方法
 */
class Common
{
    const endpoint = Config::OSS_ENDPOINT;
    const accessKeyId = Config::OSS_ACCESS_ID;
    const accessKeySecret = Config::OSS_ACCESS_KEY;
    const bucket = Config::OSS_TEST_BUCKET;

    /**
     * 根据Config配置，得到一个OssClient实例
     *
     * @return OssClient 一个OssClient实例
     */
    public static function getOssClient()
    {
    	
        try {
            $ossClient = new OssClient(self::accessKeyId, self::accessKeySecret, self::endpoint, false);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }

    public static function getBucketName()
    {
        return self::bucket;
    }

    /**
     * 工具方法，创建一个存储空间，如果发生异常直接exit
     */
    public static function createBucket()
    {
        $ossClient = self::getOssClient();
        if (is_null($ossClient)) exit(1);
        $bucket = self::getBucketName();
        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
        try {
            $ossClient->createBucket($bucket, $acl);
        } catch (OssException $e) {

            $message = $e->getMessage();
            if (\OSS\Core\OssUtil::startsWith($message, 'http status: 403')) {
                echo "Please Check your AccessKeyId and AccessKeySecret" . "\n";
                exit(0);
            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
                echo "Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint. " . "\n";
                exit(0);
            }
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }
    
    /**
     * 创建虚拟目录
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    public static function createObjectDir($ossClient, $bucket)
    {
    	try {
    		$ossClient->createObjectDir($bucket, "dir");
    	} catch (OssException $e) {
    		printf(__FUNCTION__ . ": FAILED\n");
    		printf($e->getMessage() . "\n");
    		return;
    	}
    	print(__FUNCTION__ . ": OK" . "\n");
    }
    
    /**
     * 把本地变量的内容到文件
     *
     * 简单上传,上传指定变量的内存值作为object的内容
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    public static function putObject($ossClient, $bucket, $content)
    {
    	$object = "oss-php-sdk-test/upload-test-object-name.bmp";
    	$options = array();
    	try {
    		$ossClient->putObject($bucket, $object, $content, $options);
    	} catch (OssException $e) {
    		printf(__FUNCTION__ . ": FAILED\n");
    		printf($e->getMessage() . "\n");
    		return;
    	}
    	print(__FUNCTION__ . ": OK" . "\n");
    }
    
    
    /**
     * 上传指定的本地文件内容
     *
     * @param OssClient $ossClient OssClient实例
     * @param string $bucket 存储空间名称
     * @return null
     */
    public static function uploadFile($ossClient, $bucket, $object)
    {
    	$filePath = __FILE__;
    	$options = array();
    
    	try {
    		$ossClient->uploadFile($bucket, $object, $filePath, $options);
    	} catch (OssException $e) {
    		printf(__FUNCTION__ . ": FAILED\n");
    		printf($e->getMessage() . "\n");
    		return false;
    	}
    }
    
    //
    // Function: 获取远程图片并把它保存到本地
    //
    //
    // 确定您有把文件写入本地服务器的权限
    //
    //
    // 变量说明:
    // $url 是远程图片的完整URL地址，不能为空。
    // $filename 是可选变量: 如果为空，本地文件名将基于时间和日期// 自动生成.
    public static function grabImage($url,$filename='') {
    	if($url==''):return false;endif;
    	if($filename=='') {
    		$ext=strrchr($url,'.');
    		$realName = rand ( 100, 999 ) . md5 ( $image->name );
    		if($ext!='.gif' && $ext!='.jpg'&& $ext!='.png'&& $ext!='.jpeg'):return false;endif;$filename='uploads/'.$realName.$ext;
    	}
    	ob_start();
    	readfile($url);
    	$img = ob_get_contents();
    	ob_end_clean();
    	$size = strlen($img);
    	$fp2=@fopen($filename, 'a');
    	fwrite($fp2,$img);
    	fclose($fp2);
    	return $filename;
    }

    public static function getFileUrl($path)
    {
    	$url = "http://".self::bucket.".".self::endpoint."/".$path;
        return $url;
    }
    
    public static function println($message)
    {
    	if (!empty($message)) {
    		echo strval($message) . "\n";
    	}
    }
}


