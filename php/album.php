<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/7
 * Time: 下午8:54
 */
require_once 'dbHelper.php';
require_once 'util.php';
session_start();

$userId = $_SESSION['user_info']['user_id'];
$username = $_SESSION['user_info']['username'];

if (isset($_POST['callFunc'])) {
    $callFunc = $_POST['callFunc'];
    switch ($callFunc) {
        case 'addAlbum':
            if (isset($_POST['albumName']) && isset($_POST['albumLabel'])) {
                $albumName = $_POST['albumName'];
                $albumLabel = $_POST['albumLabel'];

                $albumDirPath = getAlbumURL($username.'/'.$albumName);
                if (mkdir($albumDirPath)) {
                    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                        move_uploaded_file($_FILES['file']['tmp_name'], $albumDirPath.'/cover.jpg');
                        $albumCoverUrl = $username.'/'.$albumName.'/cover.jpg';
                    } else {
                        $albumCoverUrl = 'default_cover.jpg';
                    }
                    try {
                        $db = getDB();
                        $ret = $db->exec("INSERT INTO album(album_name, user_id, cover_url, create_time, label_id) VALUES ('$albumName','$userId','$albumCoverUrl',datetime(),(SELECT label_id FROM label WHERE label_chi_name = '$albumLabel'))");
                        if ($ret) {
                            echo json_encode(array('code' => 200, 'msg' => '新建相册成功！'));
                        } else {
                            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                        }
                    } catch (Exception $e) {
                        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                    }
                } else {
                    echo json_encode(array('code' => 404, 'msg' => '相册名称已存在，请修改相册名称。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输失败，请稍候再试。'));
            }
            break;
        case 'modifyInfo':
            if (isset($_POST['id']) && isset($_POST['oldName']) && isset($_POST['newName']) && isset($_POST['newLabel'])){
                $albumId = $_POST['id'];
                $oldName = $_POST['oldName'];
                $newName = $_POST['newName'];
                $newLabel = $_POST['newLabel'];
                $newCoverUrl = $username.'/'.$newName.'/cover.jpg';
                try {
                    $db = getDB();
                    $ret = $db->exec("UPDATE album SET album_name = '$newName', label_id = (SELECT label_id FROM label WHERE label_chi_name = '$newLabel'), cover_url = '$newCoverUrl' WHERE album_id = '$albumId'");
                    if ($ret){
                        rename(getAlbumURL($username.'/'.$oldName), getAlbumURL($username.'/'.$newName));
                        echo json_encode(array('code' => 200, 'msg' => '相册信息修改成功！'));
                    }
                    else {
                        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                    }
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            }
            break;
        case 'modifyCover':
            if (isset($_POST['albumName'])) {
                $albumName = $_POST['albumName'];
                $coverUrl = $username.'/'.$albumName.'/cover.jpg';
                if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                    move_uploaded_file($_FILES['file']['tmp_name'], getAlbumURL($coverUrl));
                    try {
                        $db = getDB();
                        $ret = $db->exec("UPDATE album SET cover_url = '$coverUrl' WHERE album_name = '$albumName' AND user_id = '$userId'");
                        if ($ret){
                            echo json_encode(array('code' => 200, 'msg' => '修改封面成功！'));
                        }
                        else{
                            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                        }
                    } catch (Exception $e) {
                        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                    }
                } else {
                    echo json_encode(array('code' => 404, 'msg' => '图片传输失败，请稍候再试。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输失败，请稍候再试。'));
            }
            break;
        default:
            echo json_encode(array('code' => 404, 'msg' => '不合法的请求。'));
    }
} else {
    echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
}