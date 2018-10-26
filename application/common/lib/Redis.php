<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/3
 * Time: 17:16
 */

namespace app\common\lib;

class Redis{
    /**
     * 验证码 redis
     * @var string
     */
    public static $pre = "sms_";
    /**
     * 用户的key
     * @var string
     */
    public static $userpre = "user_";

    /**
     * 存储验证码
     * @param $phone
     * @return string
     */
    public static function smsKey($phone){

        return self::$pre.$phone;
    }

    /**
     * 用户的key
     * @param $phone
     * @return string
     */
    public static function userKey($phone){
        return self::$userpre.$phone;
    }

}