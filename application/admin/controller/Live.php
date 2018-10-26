<?php
namespace app\admin\controller;


use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Live
{
   public function push(){
       //后台是否登录 生产一个token
        if(empty($_GET)){
            return Util::show(config('code.error'),'error');
       }
        //入库
        //此处应该查询数据库
       $teams = [
           1 =>[
               'name' => '马刺',
               'logo' => './imgs/team1.png'
           ],
           4 =>[
               'name' => '火箭',
               'logo' => './imgs/team2.png'
           ],

       ];
        //这里的数据是构建的
        $data = [
            'type' => intval($_GET['type']),
            'title' => !empty($teams[$_GET['team_id']]['name'])?$teams[$_GET['team_id']]['name']:'直播员',
            'logo' => !empty($teams[$_GET['team_id']]['logo'])?$teams[$_GET['team_id']]['logo']:'',
            'content' => !empty($_GET['content'])?$_GET['content']:'',
            'image' => !empty($_GET['image'])?$_GET['image']:'',
        ];
       //推送
       //task推送
       $taskData = [
           'method' => 'pushLive',
           'data' => $data
       ];
       $_POST['http_server']->task($taskData);
       return Util::show(config('code.success'), '推送成功');

       /*$clients = Predis::getInstance()->sMembers(config('redis.live_game_key'));

       foreach ($clients as $fd){
           $_POST['http_server']->push($fd, json_encode($data));
       }*/



   }

}
