<?php
/**
 * 监控服务 ws http 8811
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/27
 * Time: 14:29
 */

class server{
    const PORT = 8811;

    public function port(){

        $shell = "netstat -anp | grep ".self::PORT." | grep LISTEN | wc -l";
        $result = shell_exec($shell);
        if($result != 1){
            //发送报警服务
            echo date("Ymd H:i:s")."error".PHP_EOL;
        }else{
            echo date("Ymd H:i:s")."success".PHP_EOL;
        }
    }

}
// nohup  /usr/local/php/bin/php /wwwroot/tp/script/moito/server.php > /wwwroot/tp/script/moito/a.txt   tail -f a.txt
swoole_timer_tick(2000,function ($time_id){
    (new server())->port();
    echo "time-start".PHP_EOL;
});
