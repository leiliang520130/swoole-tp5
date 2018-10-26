<?php
namespace app\index\controller;

use app\common\lib\Util;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Login
{
    public function index()
    {
        //获取验证码
        print_r($_GET);
        $phoneNum = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if(empty($phoneNum) || empty($code)){
            return Util::show(config('code.error'),'phone or code is error');
        }
        //redis code
        $redisCode = Predis::getInstance()->get(Redis::smsKey($phoneNum));

        if($redisCode == $code){
            //登录成功
            //1 用户的信息记录到redis中
            $data = [
                'user' => $phoneNum,
                'srcKey' => md5(Redis::userKey($phoneNum)),
                'time' => time(),
                'isLogin' => true,
            ];
            Predis::getInstance()->set(Redis::userKey($phoneNum),$data);

            return Util::show(config('code.success'),'ok', $data);
        }else{
            return Util::show(config('code.error'),'error');
        }

    }



}
