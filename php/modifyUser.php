<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/5
 * Time: 上午11:16
 */

require_once 'dbHelper.php';
if (isset($_POST['user_id']) && isset($_POST['username']) && isset($_POST['phone']) && isset($_POST['nickname'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $nickname = $_POST['nickname'];
    try {
        $db = getDB();
        $ret = $db->exec("UPDATE user SET username = '$username', phone_number = '$phone', nick_name = '$nickname' WHERE user_id = $user_id");
        if ($ret) {
            echo json_encode(array('code' => 200, 'msg' => '用户更新成功！'));
        } else {
            echo json_encode(array('code' => 404, 'msg' => '用户更新失败！'));
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请重试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请重试。'));
}