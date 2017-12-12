<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/12
 * Time: 下午3:44
 */

require_once 'dbHelper.php';

if (isset($_POST['activityId']) && isset($_POST['userId'])) {
    $activityId = $_POST['activityId'];
    $userId = $_POST['userId'];

    try {
        $db = getDB();
        $ret = $db->exec("INSERT INTO activity_join_record(activity_id, user_id) VALUES ('$activityId','$userId')");
        if ($ret) {
            echo json_encode(array('code' => 200, 'msg' => '加入活动成功！'));
        } else {
            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
}