<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/10
 * Time: 下午5:55
 */
require_once 'dbHelper.php';
require_once 'util.php';
session_start();

$userId = $_SESSION['user_info']['user_id'];
$username = $_SESSION['user_info']['username'];

if (isset($_POST['callFunc'])) {
    $callFunc = $_POST['callFunc'];
    switch ($callFunc) {
        case 'addPhoto':
            if (isset($_POST['albumName']) && isset($_FILES)) {
                $destAlbumName = $_POST['albumName'];

                try {
                    $db = getDB();

                    //计算本次上传的文件数量
                    $count = count($_FILES['file']['name']);

                    //创建添加图片动态
                    $db->exec("INSERT INTO news(news_user_id, news_time) VALUES ('$userId',datetime())");

                    $ret = $db->query("SELECT news_id FROM news WHERE news_user_id = '$userId' ORDER BY news_time DESC LIMIT 1");
                    $newsId = $ret->fetchArray()['news_id'];

                    $date = date('Y-m-d H:i:s');
                    for ($i = 0; $i < $count; $i++) {
                        $photoUrl = $_FILES['file']['name'][$i];
                        //不再添加同名文件
                        if (!file_exists(getPhotoURL($username, $destAlbumName, $photoUrl))) {
                            move_uploaded_file($_FILES['file']['tmp_name'][$i], getPhotoURL($username, $destAlbumName, $photoUrl));
                            $db->exec("INSERT INTO photo(album_id, add_time, photo_url) VALUES ((SELECT album_id FROM album WHERE album_name = '$destAlbumName'),'$date','$photoUrl')");
                        }
                    }
                    $ret = $db->query("SELECT photo_id FROM photo WHERE album_id = (SELECT album_id FROM album WHERE album_name = '$destAlbumName') AND add_time = '$date'");
                    while ($row = $ret->fetchArray()){
                        $photoId = $row['photo_id'];
                        $db->exec("INSERT INTO photos_of_news(news_id, photo_id) VALUES ('$newsId','$photoId')");
                    }

                    echo json_encode(array('code' => 200, 'msg' => '图片上传成功！'));
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