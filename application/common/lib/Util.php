<?php
/**
 * Created by PhpStorm.
 * User: ASUSH110
 * Date: 2018/9/3
 * Time: 15:31
 */

namespace app\common\lib;

class Util{


    public static function show($status, $message = '', $data = []){
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];

        echo json_encode($result);
    }
}