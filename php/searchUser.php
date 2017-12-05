<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/4
 * Time: 下午9:01
 */
require_once 'dbHelper.php';
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    try {
        $db = getDB();
        $ret = $db->query("SELECT * FROM user WHERE username LIKE '%$username%'");
        $data = array();
        $res = $ret->fetchArray();
        if ($res) {
            while ($res) {
                array_push($data, $res);
                $res = $ret->fetchArray();
            }
            echo json_encode(array('code' => 200, 'data' => $data));
        } else {
            echo json_encode(array('code' => 404, 'msg' => '用户名不存在！'));
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请重试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请重试。'));
}