<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/7
 * Time: 下午5:16
 */
require_once 'DBHelper.php';
require_once 'Encrypter.php';

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['phone']) && isset($_POST['nickname']) && isset($_POST['role'])) {
    $username = $_POST['username'];
    $password = encrypt($_POST['password']);
    $phone = $_POST['phone'];
    $nickname = $_POST['nickname'];
    $role = $_POST['role'];
    try {
        $db = getDB();
        $ret = $db->query("SELECT * FROM user WHERE username = '" . $_POST['username']."'");
        if (!empty($ret->fetchArray())) {
            echo json_encode(array('code' => 404, 'msg' => '用户名已注册！'));
        } else {
            $ret = $db->exec("INSERT INTO user (username, password, phone_number, nick_name, role) VALUES ('" . $username . "', '" . $password . "', '" . $phone . "', '" . $nickname . "', '" . $role . "')");
            if (!$ret) {
                echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试'));
            } else {
                echo json_encode(array('code' => 200, 'msg' => '注册成功！'));
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输失败，请稍候再试'));
}
