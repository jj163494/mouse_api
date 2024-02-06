<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 14:38
 */

class Language{
    private static $language_content = array();

    /**
     * 通过语言包文件设置语言内容
     * @param string $lang_type
     * @return bool
     */
    public function read($lang_type){
        if(empty($lang_type)){
            $lang_type = "ZH";
        }
        $tmp_file = BASE_PATH.'/language/'.$lang_type.'.php';
        if (file_exists($tmp_file)){
            require($tmp_file);
            if (!empty($lang) && is_array($lang)){
                self::$language_content = array_merge(self::$language_content,$lang);
            }
            unset($lang);
        }
        return true;

    }

    /**
     * 取指定下标的数组内容
     * @param $key
     * @param string $charset
     * @return mixed|string
     */
    public static function get($key, $charset = ''){
        $result = self::$language_content[$key] ? self::$language_content[$key] : '';
        return $result;
    }

}