<?php
/**
 * 所以的task异步任务都走这里
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/5
 * Time: 11:05
 */
namespace app\common\lib\task;
use app\common\lib\ali\Sms;
use app\common\lib\redis\Predis;
use app\common\lib\Redis;
class Task{

    public function sendSms($data,$serv){
        try{
            $res = Sms::sendSms($data['phone'], $data['code']);
        }catch (\Exception $e){
            return false;
        }
        //如果发生成功记录到redis里面
        if($res->Code === 'OK'){
            Predis::getInstance()->set(Redis::smsKey($data['phone']),$data['code'],config('redis.out_time'));
        }else{
            return false;
        }
        return true;
    }

    public function pushLive($data,$serv){
        $clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));

        foreach ($clients as $fd){
            $serv->push($fd, json_encode($data));
        }
    }

}