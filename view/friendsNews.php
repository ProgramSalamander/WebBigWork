<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];

try {
    $db = getDB();

    //获取关注用户的列表
    $friendList = array();

    $ret = $db->query("SELECT f.follow_id, u.nick_name, u.head_pic_url FROM follow AS f, user AS u WHERE f.user_id = '$myUserId' AND f.follow_id = u.user_id");
    while ($row = $ret->fetchArray()) {
        $friend = array();
        $friend['id'] = $row['follow_id'];
        $friend['nickname'] = $row['nick_name'];
        $friend['headPicUrl'] = getHeadPicURL($row['head_pic_url']);
        array_push($friendList, $friend);
    }

    //获取关注动态列表
    $newsList = array();
    $ret = $db->query("SELECT news_id,news_time FROM news WHERE news_user_id IN (SELECT follow_id FROM follow WHERE user_id = '$myUserId') ORDER BY news_time DESC ");
    while ($row = $ret->fetchArray()) {
        $news = array();
        $newsId = $row['news_id'];
        $news['newsId'] = $newsId;
        $news['newsTime'] = $row['news_time'];

        $news['photos'] = array();
        $subret = $db->query("SELECT p.photo_id,p.photo_url FROM photos_of_news AS pn, photo AS p WHERE pn.news_id = '$newsId' AND pn.photo_id = p.photo_id");
        while ($subrow = $subret->fetchArray()) {
            array_push($news['photos'], array('photoId' => $subrow['photo_id'], 'photoUrl' => $subrow['photo_url']));
        }

        if (!empty($news['photos'])) {
            $tempPhotoId = $news['photos'][0]['photoId'];
            $subret = $db->query("SELECT a.album_name,u.user_id ,u.username, u.nick_name, u.head_pic_url FROM photo AS p, album AS a, user AS u WHERE p.photo_id = '$tempPhotoId' AND p.album_id = a.album_id AND a.user_id = u.user_id");
            if ($subrow = $subret->fetchArray()) {
                $news['newsAlbumName'] = $subrow['album_name'];
                $news['newsAuthorId'] = $subrow['user_id'];
                $news['newsAuthorUsername'] = $subrow['username'];
                $news['newsAuthorNickname'] = $subrow['nick_name'];
                $news['newsAuthorHeadPicUrl'] = getHeadPicURL($subrow['head_pic_url']);
                array_push($newsList, $news);
            }
        }
    }
} catch (Exception $e) {
    header('location:error.php');
}
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于摄影社交的网站</title>
        <link rel="stylesheet" href="../css/uikit.min.css"/>
        <link rel="stylesheet" href="../css/today.css"/>
        <link rel="stylesheet" href="../css/friendsNews.css"/>
        <link rel="stylesheet" href="../css/photo.css"/>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>

            $('document').ready(function () {
                new SearchBox($('#searchBoxContainer')).init();
            });
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
                        <li><a href="activity.php">一起拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li class="uk-active"><a href="friendsNews.php">朋友圈</a></li>
                        <li>
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $headPicUrl?>', 50).render());
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
            <section class="uk-section side-bar uk-visible@l">
                <ul class="uk-nav uk-nav-default">
                    <li class="uk-active">
                        <a href="friendsNews.php">关注动态</a>
                    </li>
                    <li>
                        <a href="myNews.php">我的动态</a>
                    </li>
                    <li>
                        <a href="friendsManage.php">关注管理</a>
                    </li>
                </ul>
            </section>
            <section>
                <div id="friendsNewsContainer" class="uk-flex uk-flex-center">
                    <ul class="uk-list uk-list-divider friend-news-list">
                        <?php
                        if (isset($_GET['fi']) && !empty($_GET['fi'])) {
                            foreach ($newsList as $news) {
                                if ($news['newsAuthorId'] == $_GET['fi']) {
                                    $photos = '';
                                    foreach ($news['photos'] as $photo) {
                                        $photos = $photos.'<li><a class="friend-news-photo" href="photoContent.php?id='.$photo['photoId'].'" target="_blank"><img src="'.getPhotoURL($news['newsAuthorUsername'], $news['newsAlbumName'], $photo['photoUrl']).'"/></a></li>';
                                    }

                                    $arrow = '';
                                    if (count($news['photos']) > 1) {
                                        $arrow = '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a><a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
                                    }
                                    echo '<li class="friend-news">
                                    <div class="uk-grid-small friend-news-header" uk-grid>
                                        <div class="uk-width-auto uk-align-center">
                                            <div style="width: 70px;height: 70px;overflow: hidden;position: relative" class="uk-border-circle">
                                                 <img class="'.getPhotoClass($news['newsAuthorHeadPicUrl']).'" src="'.$news['newsAuthorHeadPicUrl'].'"/>
                                            </div>
                                        </div>
                                        <div class="uk-width-expand uk-align-center">
                                            <p style="line-height: 10px" class="uk-width-large">
                                                <a href="homepage.php?username='.$news['newsAuthorUsername'].'" class="uk-text-success" title="去Ta的主页" uk-tooltip>'.$news['newsAuthorNickname'].'</a> 添加了新照片
                                            </p>
                                        </div>
                                        <div class="uk-width-auto uk-align-center">
                                            <span style="line-height: 10px" class="uk-text-muted uk-text-small">'.$news['newsTime'].'</span>
                                        </div>
                                    </div>
                                    <div class="uk-card uk-card-default uk-padding-small uk-position-relative uk-visible-toggle uk-light friend-news-body" uk-slideshow>
                                        <ul class="friend-news-photo-slide uk-slideshow-items">'.$photos.'</ul>
                                        '.$arrow.'
                                    </div>
                                  </li>';
                                }
                            }
                        } else {
                            foreach ($newsList as $news) {
                                $photos = '';
                                foreach ($news['photos'] as $photo) {
                                    $photos = $photos.'<li><a class="friend-news-photo" href="photoContent.php?id='.$photo['photoId'].'" target="_blank"><img src="'.getPhotoURL($news['newsAuthorUsername'], $news['newsAlbumName'], $photo['photoUrl']).'"/></a></li>';
                                }

                                $arrow = '';
                                if (count($news['photos']) > 1) {
                                    $arrow = '<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a><a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>';
                                }
                                echo '<li class="friend-news">
                                    <div class="uk-grid-small friend-news-header" uk-grid>
                                        <div class="uk-width-auto uk-align-center">
                                            <div style="width: 70px;height: 70px;overflow: hidden;position: relative" class="uk-border-circle">
                                                 <img class="'.getPhotoClass($news['newsAuthorHeadPicUrl']).'" src="'.$news['newsAuthorHeadPicUrl'].'"/>
                                            </div>
                                        </div>
                                        <div class="uk-width-expand uk-align-center">
                                            <p style="line-height: 10px" class="uk-width-large">
                                                <a href="homepage.php?username='.$news['newsAuthorUsername'].'" class="uk-text-success" title="去Ta的主页" uk-tooltip>'.$news['newsAuthorNickname'].'</a> 添加了新照片
                                            </p>
                                        </div>
                                        <div class="uk-width-auto uk-align-center">
                                            <span style="line-height: 10px" class="uk-text-muted uk-text-small">'.$news['newsTime'].'</span>
                                        </div>
                                    </div>
                                    <div class="uk-card uk-card-default uk-padding-small uk-position-relative uk-visible-toggle uk-light friend-news-body" uk-slideshow>
                                        <ul class="friend-news-photo-slide uk-slideshow-items">'.$photos.'</ul>
                                        '.$arrow.'
                                    </div>
                                  </li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </section>
            <section class="uk-padding friends-list-container">
                <h5>看看Ta的动态</h5>
                <div class="uk-grid uk-margin-remove uk-child-width-1-3 friends-list" uk-grid>
                    <?php
                    foreach ($friendList as $friend) {
                        $photoClass = getPhotoWHRate($friend['headPicUrl']) > 1 ? 'photo-long' : 'photo-high';
                        echo '<div>
                            <div style="width: 60px; height: 60px; overflow: hidden; position: relative" class="uk-border-circle">
                                <a href="friendsNews.php?fi='.$friend['id'].'">
                                   <img class="'.$photoClass.'" src="'.$friend['headPicUrl'].'" title="'.$friend['nickname'].'" uk-tooltip/>
                                </a>
                            </div>
                            </div>';
                    }
                    ?>
                </div>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>