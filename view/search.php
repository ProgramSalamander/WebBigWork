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

if (isset($_GET['kw'])) {
    $keyword = $_GET['kw'];
    try {
        $db = getDB();

        //搜索相关用户
        $ret = $db->query("SELECT * FROM user WHERE username LIKE '%$keyword%'
UNION SELECT * FROM user WHERE nick_name LIKE '%$keyword%'
UNION SELECT * FROM user WHERE user_sign LIKE '%$keyword%'");
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
        $ret = $db->query("SELECT p.photo_id, p.photo_url, a.album_name, u.user_id, u.username, u.nick_name ,l.label_chi_name, l.label_eng_name FROM photo AS p, album AS a, user AS u,label AS l WHERE (l.label_chi_name LIKE '%$keyword%') AND p.label_id = l.label_id AND p.album_id = a.album_id AND a.user_id = u.user_id
UNION SELECT p.photo_id, p.photo_url, a.album_name, u.user_id, u.username, u.nick_name ,l.label_chi_name, l.label_eng_name FROM photo AS p, album AS a, user AS u,label AS l WHERE (u.nick_name LIKE '%$keyword%') AND p.label_id = l.label_id AND p.album_id = a.album_id AND a.user_id = u.user_id
UNION SELECT p.photo_id, p.photo_url, a.album_name, u.user_id, u.username, u.nick_name ,l.label_chi_name, l.label_eng_name FROM photo AS p, album AS a, user AS u,label AS l WHERE (a.album_name LIKE '%$keyword%') AND p.label_id = l.label_id AND p.album_id = a.album_id AND a.user_id = u.user_id");
        while ($row = $ret->fetchArray()) {
            $photoInfo = array();
            $photoInfo['photoId'] = $row['p.photo_id'];
            $photoInfo['photoUrl'] = getPhotoURL($row['u.username'], $row['a.album_name'], $row['p.photo_url']);
            $photoInfo['albumName'] = $row['a.album_name'];
            $photoInfo['photoLabel'] = $row['l.label_chi_name'];
            $photoInfo['photoLabelClass'] = $row['l.label_eng_name'];
            $photoInfo['authorUserId'] = $row['u.user_id'];
            $photoInfo['authorUsername'] = $row['u.username'];
            $photoInfo['authorNickname'] = $row['u.nick_name'];
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

            #photoResult {
                border-left: 1px solid lightgray;
                border-right: 1px solid lightgray;
            }

            #userResult, #photoResult, #activityResult {
                height: 600px;
                overflow: scroll;
            }

            .result-list li {
                transition: 1s;
            }

            .result-list li:hover {
                background: #dcdcdc;
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

                let keyword = '<?php echo isset($_GET['kw']) && $_GET['kw']?>';
                if (keyword) {
                    loadUserResults();
                    loadPhotoResults();
                    loadActivityResults();
                }
                else {
                    notification("请输入搜索关键词。", 'warning');
                }

            });

            function loadUserResults() {
                let userResults = <?php echo json_encode($result['user'])?>;
                if (userResults.length > 0) {
                    let list = $('<ul class="uk-list result-list"></ul>');
                    $.each(userResults, function (index, element) {
                        list.append($(`<li class="uk-margin-right">
                                <a href="homepage.php?username=${element.username}" style="text-decoration: none" class="uk-padding-small uk-grid-small" uk-grid>
                                    <div class="uk-width-auto">
                                        <div style="width: 50px; height: 50px; overflow: hidden; position: relative" class="uk-border-circle">
                                            <img src="${element.headPicUrl}" />
                                        </div>
                                    </div>
                                    <div class="uk-width-expand">
                                        <p style="line-height: 30px" class="uk-margin-remove">${element.nickname}</p>
                                        <p style="line-height: 20px" class="uk-margin-remove uk-padding-remove uk-text-meta">${element.userSign}</p>
                                    </div>
                                </a>
                            </li>`));
                    });
                    $('#userResult').append(list);
                }
                else {
                    $('#userResult').append($('<p>暂无结果</p>'));
                }
            }

            function loadPhotoResults() {
                let photoResults = <?php echo json_encode($result['photo'])?>;
                if (photoResults.length > 0) {
                    let list = $('<ul class="uk-list"></ul>');
                    $.each(photoResults, function (index, element) {
                        list.append($(`<li class="uk-margin-right uk-text-center">
                                <div class="uk-inline-clip uk-transition-toggle">
                                    <a href="photoContent.php?id=${element.photoId}" class="uk-padding-small">
                                        <img src="${element.photoUrl}" />
                                    </a>
                                    <a style="text-decoration: none" href="photoContent.php?id=${element.photoId}" class="uk-transition-slide-bottom-medium uk-position-bottom uk-overlay uk-overlay-default">
                                        <p class="uk-h5 uk-margin-remove">作者：${element.authorNickname}</p>
                                        <p class="uk-h5 uk-margin-remove">所属相册：${element.albumName}</p>
                                    </a>
                                </div>

                            </li>`));
                    });
                    $('#photoResult').append(list);
                }
                else{
                    $('#photoResult').append($('<p>暂无结果</p>'));
                }
            }

            function loadActivityResults() {
                let activityResults = <?php echo json_encode($result['activity'])?>;
                if (activityResults.length > 0){

                }
                else {
                    $('#activityResult').append($('<p>暂无结果</p>'));
                }
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
                    <li id="userResult" class="uk-padding-small uk-float-left uk-width-1-4">
                        <h5>相关用户</h5>
                    </li>
                    <li id="photoResult" class="uk-padding-small uk-float-left uk-width-1-2">
                        <h5>相关照片</h5>
                    </li>
                    <li id="activityResult" class="uk-padding-small uk-float-left uk-width-1-4">
                        <h5>相关摄影活动</h5>
                    </li>
                </ul>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>

