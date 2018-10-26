<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/4
 * Time: 15:38
 */

class Http{

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    public $http = null;
    public function __construct() {
        $this->http = new swoole_http_server("0.0.0.0", 8811);

        $this->http->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/data/wwwroot/tp/public/static",
                'worker_num' => 5,
                'task_worker_num' => 4,
            ]
        );
        $this->http->on("WorkerStart", [$this, 'onWorkerStart']);
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart(swoole_server $server,$worker_id){
        define('APP_PATH', __DIR__ . '/../application/');
        //require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response){
        $_SERVER = [];
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


        $_POST['http_server'] = $this->http;
        //把返回放到一个缓冲区里

        ob_start();
        try {
            think\App::run()->send();
        }catch(\Exception $e){

        }

        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $workerId
     * @param $data
     */
    public function onTask($serv, $taskId, $workerId, $data) {

        //分发task任务机制 不同业务走不同逻辑
        $obj = new app\common\lib\task\Task;
        $method = $data['method'];
        $flag = $obj->$method($data['data']);



        return $flag;
    }

    /**
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function onFinish($serv, $taskId, $data) {

    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {

    }

}

new Http();