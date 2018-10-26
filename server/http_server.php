<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/8/31
 * Time: 11:13
 */

//namespace think;

$http = new swoole_http_server("0.0.0.0", 8811);

$http->set(
    [
        'enable_static_handler' => true,
        'document_root' => "/data/wwwroot/tp/public/static",
        'worker_num' => 5,
    ]
);

$http->on('WorkerStart', function($server,$worker_id) {
    //
    //加载框架文件
    require __DIR__ . '/../thinkphp/base.php';

});
$http->on('request', function($request, $response) use ($http){
    $_SERVER = [];
    if(isset($request->server)){
        foreach ($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    if(isset($request->header)){
        foreach ($request->header as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    $_GET = [];
    if(isset($request->get)){
        foreach ($request->get as $k => $v){
            $_GET[$k] = $v;
        }
    }
    //var_dump($request->get);
    $_POST = [];
    if(isset($request->post)){
        foreach ($request->post as $k => $v){
            $_POST[$k] = $v;
        }
    }



    // 执行应用并响应
    ob_start();
    try {
        think\App::run()->send();
    } catch (\Exception $e) {

    }
    $res=ob_get_contents();
    ob_end_clean();
    $response->end($res);

});

$http->start();


