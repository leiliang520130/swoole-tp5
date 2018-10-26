<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/4
 * Time: 15:38
 */

class Ws{

    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;
    CONST CHART_PORT = 8812;

    public $ws = null;
    public function __construct() {

        $this->ws = new swoole_websocket_server(self::HOST, self::PORT);
        $this->ws->listen(self::HOST,self::CHART_PORT, SWOOLE_SOCK_TCP);
        $this->ws->set(
            [
                'enable_static_handler' => true,
                'document_root' => "/data/wwwroot/tp/public/static",
                'worker_num' => 5,
                'task_worker_num' => 4,
            ]
        );

        $this->ws->on("start", [$this, 'onStart']);
        $this->ws->on("open", [$this, 'onOpen']);
        $this->ws->on("message", [$this, 'onMessage']);
        $this->ws->on("WorkerStart", [$this, 'onWorkerStart']);
        $this->ws->on("request", [$this, 'onRequest']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this, 'onClose']);

        $this->ws->start();
    }

    public function onStart(){
        swoole_set_process_name("live_mast");
    }
    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart(swoole_server $server,$worker_id){
        define('APP_PATH', __DIR__ . '/../application/');
        //require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';

        //删除redis集合的值
        \app\common\lib\redis\Predis::getInstance()->del(config('redis.live_game_key'));
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

        $_FILES = [];
        if(isset($request->files)){
            foreach ($request->files as $k => $v){
                $_FILES[$k] = $v;
            }
        }
        $_POST = [];
        if(isset($request->post)){
            foreach ($request->post as $k => $v){
                $_POST[$k] = $v;
            }
        }


        $_POST['http_server'] = $this->ws;
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
        $flag = $obj->$method($data['data'],$serv);



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
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request) {
        print_r($request);
        //放入redis里面
        if($request->server['server_port'] == 8811){
            \app\common\lib\redis\Predis::getInstance()->sAdd(config('redis.live_game_key'),$request->fd);
        }
        var_dump($request->fd);
    }
    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame) {
        echo "ser-push-message:{$frame->data}\n";
        // todo 10s
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        //$ws->task($data);

        swoole_timer_after(5000, function() use($ws, $frame) {
            echo "5s-after\n";
            $ws->push($frame->fd, "server-time-after:");
        });
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
    }


    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd) {
        \app\common\lib\redis\Predis::getInstance()->sRem(config('redis.live_game_key'),$fd);
    }

}

new Ws();