<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/11
 * Time: 上午9:06
 */

require_once 'dbHelper.php';

if (isset($_POST['myId']) && isset($_POST['followId']) && isset($_POST['state'])) {

    $myId = $_POST['myId'];
    $followId = $_POST['followId'];
    $isAlreadyFollow = $_POST['state'];

    try {
        $db = getDB();
        if ($isAlreadyFollow === 'false') {
            $ret = $db->exec("INSERT INTO follow(user_id, follow_id) VALUES ('$myId','$followId')");
            if ($ret) {
                echo json_encode(array('code' => 200, 'msg' => '关注成功！', 'data'=> true));
            } else {
                echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
            }
        } else {
            $ret = $db->exec("DELETE FROM follow WHERE user_id = '$myId' AND follow_id = '$followId'");
            if ($ret) {
                echo json_encode(array('code' => 200, 'msg' => '取消关注成功！', 'data'=> false));
            } else {
                echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
}