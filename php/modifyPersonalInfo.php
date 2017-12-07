<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/5
 * Time: 下午9:20
 */
require_once 'dbHelper.php';
if (isset($_POST['callFunc'])) {
    $callFunc = $_POST['callFunc'];
    switch ($callFunc) {
        case 'modifyNickname':
            if (isset($_POST['username']) && isset($_POST['newNickname'])) {
                $username = $_POST['username'];
                $newNickname = $_POST['newNickname'];
                try {
                    $db = getDB();
                    $ret = $db->exec("UPDATE user SET nick_name = '$newNickname' WHERE username = '$username'");
                    if ($ret) {
                        echo json_encode(array('code' => 200, 'msg' => '修改昵称成功！'));
                    } else {
                        echo json_encode(array('code' => 404, 'msg' => '修改昵称失败，请稍候再试。'));
                    }
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        case 'modifySign':
            if (isset($_POST['username']) && isset($_POST['newSign'])) {
                $username = $_POST['username'];
                $newSign = $_POST['newSign'];
                try {
                    $db = getDB();
                    $ret = $db->exec("UPDATE user SET user_sign = '$newSign' WHERE username = '$username'");
                    if ($ret) {
                        echo json_encode(array('code' => 200, 'msg' => '修改签名成功！'));
                    } else {
                        echo json_encode(array('code' => 404, 'msg' => '修改签名失败，请稍候再试。'));
                    }
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        default:
            echo json_encode(array('code' => 404, 'msg' => '不合法的请求。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
}