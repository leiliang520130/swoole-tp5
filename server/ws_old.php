<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/3
 * Time: 8:57
 */
$http = new swoole_http_server("0.0.0.0", 8811);
//worker_num设置启动的worker进程数
$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/data/wwwroot/tp/public/static",
        'worker_num' => 5,
    ]
);

$http->on('WorkerStart',function(swoole_server $server,$worker_id){
    define('APP_PATH', __DIR__ . '/../application/');
    // 这里 引入 base.php  而不引入start.php  是因为
    // start.php 的话 就会执行thinkphp 的相应的控制器方法了
    require __DIR__ . '/../thinkphp/base.php';
});



$http->on('request', function ($request, $response) use($http){
    if(isset($request->server)){
        foreach($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    if(isset($request->server)){
        foreach($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    //swoole对于超全局数组并不会释放，所以要先清空一次
    $_GET = [];
    if(isset($request->get)){
        foreach ($request->get as $k => $v){
            $_GET[$k] = $v;
        }
    }

    $_POST = [];
    if(isset($request->post)){
        foreach ($request->post as $k => $v){
            $_POST[$k] = $v;
        }
    }
    //把返回放到一个缓冲区里
    ob_start();
    try {
        think\App::run()->send();
    }catch(\Exception $e){

    }
    //echo request()->action();
    $res = ob_get_contents();
    ob_end_clean();
    $response->end($res);

});

$http->start();