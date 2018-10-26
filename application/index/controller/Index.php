<?php
namespace app\index\controller;

use app\common\lib\ali\Sms;

class Index
{
    public function index()
    {
       return '';
    }

    public function  hello(){

        return time();
    }

    public function sms(){
        try{
            $re = Sms::sendSms(15002821257,123456);
            var_dump($re);
        }catch (\Exception $e){

        }

    }

}
