<?php
namespace app\index\controller;
use app\common\lib\Util;


class Chart
{
    public function index()
    {

        //return Util::show(config('code.error'),'用户ID不能为空');
        //return $_POST['game_id'];
        //用户需要登录的逻辑处理

        if(empty($_POST['game_id'])){
            return Util::show(config('code.error'),'用户ID不能为空');
        }
        if(empty($_POST['content'])){
            return Util::show(config('code.error'),'内容不能为空');
        }
        //比赛入库
        $data = [
            'user' => "用户".rand(1,200),
            'content' => $_POST['content'],

        ];
        foreach ($_POST['http_server']->ports[1]->connections as $fd){
            $_POST['http_server']->push($fd, json_encode($data));
        }

        return Util::show(config('code.success'),'ok',$data);

    }



}
