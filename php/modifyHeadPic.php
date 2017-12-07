<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/6
 * Time: 下午8:36
 */
require_once 'util.php';
require_once 'dbHelper.php';
session_start();

if ($_FILES['file']['error'] == 0) {
    $userId = $_SESSION['user_info']['user_id'];
    $oldUrl = $_SESSION['user_info']['head_pic_url'];
    $newUrl = $userId.'_'.time().'.jpg';
    if ($oldUrl != 'default.jpg' && file_exists(getHeadPicURL($oldUrl))) {
        unlink(getHeadPicURL($oldUrl));
    }
    $ret = move_uploaded_file($_FILES['file']['tmp_name'], getHeadPicURL($newUrl));
    if ($ret) {
        try {
            $db = getDB();
            $ret = $db->exec("UPDATE user SET head_pic_url = '$newUrl' WHERE user_id = '$userId' ");
            if ($ret) {
                $_SESSION['user_info']['head_pic_url'] = $newUrl;
                echo json_encode(array('code' => 200, 'msg' => '修改头像成功！'));
            } else {
                echo json_encode(array('code' => 404, 'msg' => '修改头像失败,请稍候再试。'));
            }
        } catch (Exception $e) {
            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
        }
    } else {
        echo json_encode(array('code' => 404, 'msg' => '修改头像失败,请稍候再试。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输失败，请重试。'));
}