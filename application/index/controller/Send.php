<?php
namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;

class Send
{
    public function index()
    {
        // = request()->get('phone_num',0,'intval');
        $phoneNum = intval($_GET['phone_num']);
        if(empty($phoneNum)){
            //status 0 1 message data
            return Util::show(config('code.error'), 'error');
        }

        $code = rand(1000,9999);

        $taskData = [
            'method' => 'sendSms',
            'data' => [
                'phone' => $phoneNum,
                'code' => $code,
            ]
        ];
        $_POST['http_server']->task($taskData);
        return Util::show(config('code.success'), '发送成功');
       /* try{
            //$res = Sms::sendSms($phoneNum, $code);
        }catch (\Exception $e){
            return Util::show(config('code.error'), '短信内部错误');
        }*/

        //if($res->Code === 'OK'){
        /*if(true){
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'),config('redis.port'));
            $redis->set(Redis::smsKey($phoneNum), $code, config('redis.out_time'));
            //return Util::show($code, 'success');
            return Util::show(config('code.success'), '发送成功');
        }else{
            return Util::show(config('code.error'), '发送失败');
        }*/
    }



}
