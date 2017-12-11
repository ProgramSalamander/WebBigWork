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

                            //注：图片标签默认为所属的相册标签
                            $db->exec("INSERT INTO photo(album_id, add_time, photo_url, label_id) VALUES ((SELECT album_id FROM album WHERE user_id = '$userId' AND album_name = '$destAlbumName'),'$date','$photoUrl',(SELECT album.label_id FROM album WHERE user_id = '$userId' AND album_name = '$destAlbumName'))");
                        }
                    }
                    $ret = $db->query("SELECT photo_id FROM photo WHERE album_id = (SELECT album_id FROM album WHERE album_name = '$destAlbumName') AND add_time = '$date'");
                    while ($row = $ret->fetchArray()) {
                        $photoId = $row['photo_id'];
                        $db->exec("INSERT INTO photos_of_news(news_id, photo_id) VALUES ('$newsId','$photoId')");
                    }

                    echo json_encode(array('code' => 200, 'msg' => '照片上传成功！'));
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }

            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        case 'modifyPhoto':
            if (isset($_POST['id']) && isset($_POST['newLabel'])) {
                $photoId = $_POST['id'];
                $newLabel = $_POST['newLabel'];

                try {
                    $db = getDB();
                    $ret = $db->exec("UPDATE photo SET label_id = (SELECT label_id FROM label WHERE label_chi_name = '$newLabel') WHERE photo_id = '$photoId'");
                    if ($ret) {
                        echo json_encode(array('code' => 200, 'msg' => '照片信息修改成功！'));
                    } else {
                        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                    }
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        case 'deletePhoto':
            if (isset($_POST['id']) && isset($_POST['url'])) {
                $photoId = $_POST['id'];
                $photoUrl = $_POST['url'];
                try {
                    $db = getDB();

                    //删除照片相关的评论
                    $db->exec("DELETE FROM like_comment_record WHERE photo_id = '$photoId'");

                    //删除相关动态中的照片
                    $db->exec("DELETE FROM photos_of_news WHERE photo_id = '$photoId'");

                    //删除照片数据
                    $db->exec("DELETE FROM photo WHERE photo_id = '$photoId'");

                    //删除图片
                    unlink($photoUrl);

                    echo json_encode(array('code' => 200, 'msg' => '照片删除成功！'));
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            } else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        case 'likePhoto':
            if (isset($_POST['id']) && isset($_POST['isAlreadyLike'])) {
                $photoId = $_POST['id'];
                $isAlreadyLike = $_POST['isAlreadyLike'];
                try {
                    $db = getDB();
                    //未喜欢
                    if ($isAlreadyLike === 'false') {
                        $ret = $db->exec("INSERT INTO like_comment_record(type, record_time, photo_id, user_id) VALUES ('l',datetime(),'$photoId','$userId')");
                        if ($ret) {
                            echo json_encode(array('code' => 200, 'msg' => '喜欢成功！'));
                        } else {
                            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                        }
                    } //已喜欢
                    else {
                        $ret = $db->exec("DELETE FROM like_comment_record WHERE type = 'l' AND user_id = '$userId' AND photo_id = '$photoId'");
                        if ($ret) {
                            echo json_encode(array('code' => 200, 'msg' => '已取消喜欢！'));
                        } else {
                            echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                        }
                    }
                } catch (Exception $e) {
                    echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
                }
            }else {
                echo json_encode(array('code' => 404, 'msg' => '数据传输异常，请稍候再试。'));
            }
            break;
        case 'addComment':
            if (isset($_POST['id']) && isset($_POST['content'])) {
                $photoId = $_POST['id'];
                $commentContent = $_POST['content'];
                try {
                    $db = getDB();
                    $ret = $db->exec("INSERT INTO like_comment_record(type, record_time, photo_id, user_id, record_content) VALUES ('c',datetime(),'$photoId','$userId','$commentContent')");
                    if ($ret){
                        echo json_encode(array('code' => 200, 'msg' => '评论成功！'));
                    }
                    else{
                        echo json_encode(array('code' => 404, 'msg' => '服务器异常，请稍候再试。'));
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