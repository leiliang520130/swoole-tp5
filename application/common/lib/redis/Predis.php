<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/3
 * Time: 17:16
 */

namespace app\common\lib\redis;

class Predis{
    public $redis = "";
    /**
     * 定义一个单例模式
     * @var null
     */
   private static  $_instance = null;

   public static function getInstance(){
        if(empty(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;

   }

   private  function  __construct()
   {
       $this->redis = new \Redis();
       $result = $this->redis->connect(config('redis.host'),config('redis.port'),config('redis.timeOut'));
       if($result === false){
            throw new \Exception('redis connect error');
       }
   }

    /**
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     *
     */
   public function set($key, $value, $time = 0){
        if(!$key){
            return '';
        }
        if(is_array($value)){
            $value = json_encode($value);
        }
        if(!$time){
            return $this->redis->set($key, $value);
        }

        return $this->redis->setex($key,$time,$value);
   }

    /**
     * @param $key
     * @return bool|string
     *
     */
   public function get($key){
       if(!$key){
           return '';
       }
       return $this->redis->get($key);
   }

    /**
     * @param $key
     * @param $value
     * @return int
     */
   public function sAdd($key, $value){
        return $this->redis->sAdd($key, $value);
   }

    /**
     * @param $key
     * @param $value
     * @return int
     */
   public function sRem($key, $value){
       return $this->redis->sRem($key, $value);
   }

    /**
     * @param $key
     * @return array
     */
   public function sMembers($key){
       return $this->redis->sMembers($key);
   }

   public function del($key){
       return $this->redis->del($key);

   }

    /**
     * 不存在的方法用call来处理非常方便
     * @param $name
     * @param $arguments
     * @return mixed
     */
   public function __call($name, $arguments)
   {
       // TODO: Implement __call() method.
       if(count($arguments) != 2){
           return $this->redis->$name($arguments[0], $arguments[1]);
       }
   }

}