<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/7
 * Time: 下午5:16
 */
require_once 'dbHelper.php';
require_once 'encrypter.php';
require_once 'util.php';

//mkdir('../database/imgs/albums/1');

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['phone']) && isset($_POST['nickname'])) {
    $username = $_POST['username'];
    $password = encrypt($_POST['password']);
    $phone = $_POST['phone'];
    $nickname = $_POST['nickname'];
    try {
        $db = getDB();
        $ret = $db->query("SELECT * FROM user WHERE username = '" . $_POST['username']."'");
        if (!empty($ret->fetchArray())) {
            echo json_encode(array('code' => 404, 'msg' => '用户名已注册！'));
        } else {
            $ret = $db->exec("INSERT INTO user (username, password, phone_number, nick_name) VALUES ('$username', '$password', '$phone', '$nickname')");
            if (!$ret) {
                echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试'));
            } else {
                mkdir(getAlbumURL($username));
                mkdir(getAlbumURL($username.'/默认相册'));
                setcookie('username', $username, time() + 7 * 24 * 3600,'/');
                setcookie('password', $password, time() + 7 * 24 * 3600,'/');
                echo json_encode(array('code' => 200, 'msg' => '注册成功！'));
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输失败，请稍候再试'));
}
