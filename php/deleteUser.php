<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/4
 * Time: 下午11:09
 */
require_once 'dbHelper.php';
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    try {
        $db = getDB();
        $ret = $db->exec("DELETE FROM user WHERE user_id='$user_id'");
        if ($ret) {
            echo json_encode(array('code' => 200, 'msg' => '用户删除成功！'));
        } else {
            echo json_encode(array('code' => 404, 'msg' => '用户删除失败！'));
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请重试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请重试。'));
}