<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/11
 * Time: 下午11:11
 */

require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];

$result = array('user' => array(), 'photo' => array(), 'activity' => array());

if (isset($_GET['keyword']) && $_GET['keyword']) {
    $keyword = $_GET['keyword'];
    try {
        $db = getDB();

        //搜索相关用户
        $ret = $db->query("SELECT * FROM user WHERE username LIKE '%$keyword%' OR nick_name LIKE '%$keyword%' OR user_sign LIKE '%$keyword%'");
        while ($row = $ret->fetchArray()) {
            $userInfo = array();
            $userInfo['userId'] = $row['user_id'];
            $userInfo['username'] = $row['username'];
            $userInfo['headPicUrl'] = getHeadPicURL($row['head_pic_url']);
            $userInfo['nickname'] = $row['nick_name'];
            $userInfo['userSign'] = $row['user_sign'];
            array_push($result['user'], $userInfo);
        }

        //搜索相关照片
        $ret = $db->query("SELECT p.photo_id, p.photo_url, a.album_name, u.user_id, u.username, u.nick_name ,l.label_chi_name, l.label_eng_name FROM photo AS p, album AS a, user AS u,label AS l WHERE l.label_chi_name LIKE '%$keyword%' AND p.label_id = l.label_id AND p.album_id = a.album_id AND a.user_id = u.user_id");
        while ($row = $ret->fetchArray()) {
            $photoInfo = array();
            $photoInfo['photoId'] = $row['photo_id'];
            $photoInfo['photoUrl'] = getPhotoURL($row['username'], $row['album_name'], $row['photo_url']);
            $photoInfo['photoLabel'] = $row['label_chi_name'];
            $photoInfo['photoLabelClass'] = $row['label_eng_name'];
            $photoInfo['authorUserId'] = $row['user_id'];
            $photoInfo['authorUsername'] = $row['username'];
            $photoInfo['authorNickname'] = $row['nick_name'];
            array_push($result['photo'], $photoInfo);
        }

        //搜索相关摄影活动

    } catch (Exception $e) {
        header('location: error.php');
    }
}
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
        <link rel="stylesheet" href="../css/uikit.min.css"/>
        <link rel="stylesheet" href="../css/today.css"/>
        <link rel="stylesheet" href="../css/label.css"/>
        <link rel="stylesheet" href="../css/photo.css"/>

        <style>

            #photoResult{
                border-left: 1px solid lightgray;
                border-right: 1px solid lightgray;
            }

            #userResult,#photoResult,#activityResult{
                height: 600px;
                overflow: scroll;
            }
        </style>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/waterfall.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/topProgressBar.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {
                topProgressBar.init();
                new SearchBox($('#searchBoxContainer')).init();

                loadUserResults();
                loadPhotoResults();
                loadActivityResults();
            });

            function loadUserResults() {
                let userResults = <?php echo json_encode($result['user'])?>;

            }

            function loadPhotoResults() {
                let photoResults = <?php echo json_encode($result['photo'])?>;

            }

            function loadActivityResults() {
                let activityResults = <?php echo json_encode($result['activity'])?>;

            }
        </script>
    </head>
    <body>
        <header>
            <nav class="uk-background-secondary uk-light" uk-navbar uk-sticky>
                <div class="uk-navbar-left">
                    <a class="uk-navbar-item uk-logo" href="today.php"><span class="uk-margin-small-right uk-icon"
                                                                             uk-icon="icon: happy; ratio: 2"></span>iPaiPai</a>
                </div>
                <div class="uk-navbar-right">
                    <div id="searchBoxContainer"></div>
                    <ul class="uk-navbar-nav">
                        <li><a href="today.php">今日推荐</a></li>
                        <li><a href="activity.php">约拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
                        <li>
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo getHeadPicURL($_SESSION['user_info']['head_pic_url']) ?>', 50).render());
                                </script>
                            </a>
                            <div class="uk-width-small uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    <li><a href="photoUpload.php"><span class="uk-icon" uk-icon="icon:image"></span>上传照片</a>
                                    </li>
                                    <li><a href="homepage.php?username=<?php echo $myUsername ?>"><span class="uk-icon" uk-icon="icon:home"></span>我的主页</a>
                                    </li>
                                    <li><a href="../php/signOut.php"><span class="uk-icon" uk-icon="icon:sign-out"></span>登出</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <main>
            <h3 class="uk-padding-small uk-padding-remove-bottom uk-text-center">搜索结果</h3>
            <section class="uk-padding uk-padding-remove-vertical">
                <ul class="uk-list uk-clearfix">
                    <li id="userResult" class="uk-padding-small uk-float-left uk-width-1-3">
                        <h5>相关用户</h5>
                    </li>
                    <li id="photoResult" class="uk-padding-small uk-float-left uk-width-1-3">
                        <h5>相关照片</h5>
                    </li>
                    <li id="activityResult" class="uk-padding-small uk-float-left uk-width-1-3">
                        <h5>相关摄影活动</h5>
                    </li>
                </ul>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>

