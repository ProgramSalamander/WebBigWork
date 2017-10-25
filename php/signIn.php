<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/24
 * Time: 下午9:18
 */

require_once 'DBHelper.php';
require_once 'Encrypter.php';

if (isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = encrypt($_POST['password']);

    try{
        $db = getDB();

        $ret = $db->query("SELECT password FROM user WHERE username = '".$username."'");
        $res = $ret -> fetchArray();
        if ($password == $res['password']){
            echo json_encode(array('code' => 200, 'msg' => '登录成功！'));
        }
        else{
            echo json_encode(array('code' => 404, 'msg' => '用户名或密码错误！'));
        }
    }catch (Exception $e){
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试'));
    }
}
else{
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍后再试'));
}