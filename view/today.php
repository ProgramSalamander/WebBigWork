<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];

$todayHotPhotos = array();
$todayStars = array();
try {
    $db = getDB();

    //获取今日热门作品
    $ret = $db->query("SELECT p.photo_url,a.album_name,u.username,u.nick_name,l.label_eng_name,l.label_chi_name FROM photo AS p, album AS a, user AS u, label AS l WHERE date(p.add_time) = date() AND p.album_id = a.album_id AND a.user_id = u.user_id AND p.label_id = l.label_id ORDER BY p.photo_likes, p.photo_comments DESC LIMIT 10");
    while ($row = $ret->fetchArray()) {
        $photo = array();
        $photo['photoUrl'] = getPhotoURL($row['username'], $row['album_name'], $row['photo_url']);
        $photo['author'] = $row['nick_name'];
        $photo['label_chi'] = $row['label_chi_name'];
        $photo['label_eng'] = $row['label_eng_name'];
        array_push($todayHotPhotos, $photo);
    }

    //获取今日明星
    $ret = $db->query("SELECT u.head_pic_url, u.username, u.nick_name, sum(l.record_id) AS today_likes FROM like_comment_record AS l, user AS u, album AS a, photo AS p WHERE l.type = 'l' AND date(l.record_time) = date() AND l.photo_id = p.photo_id AND p.album_id = a.album_id AND a.user_id = u.user_id GROUP BY u.user_id ORDER BY today_likes DESC LIMIT 3");
    while ($row = $ret->fetchArray()) {
        $star = array();
        $star['headPicUrl'] = $row['head_pic_url'];
        $star['username'] = $row['username'];
        $star['nickname'] = $row['nick_name'];
        $star['todayLikes'] = $row['today_likes'];
        array_push($todayStars, $star);
    }
} catch (Exception $e) {
    header('location: error.php');
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

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/waterfall.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>


        <script>
            $('document').ready(function () {
                loadTodayHot();

                function loadTodayHot() {
                    let hotData = [
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '人像',
                            photoLabelClass: 'label-people',
                            photoWHRate: 1.5
                        },
                        {
                            photoUrl: '../../imgs/girl.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery',
                            photoWHRate: 0.66675
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '人像',
                            photoLabelClass: 'label-people',
                            photoWHRate: 1.5
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery',
                            photoWHRate: 1.5
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '动物',
                            photoLabelClass: 'label-animal',
                            photoWHRate: 1.5
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery',
                            photoWHRate: 1.5
                        },
                        {
                            photoUrl: '../../imgs/girl.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery',
                            photoWHRate: 0.66675
                        }
                    ];

                    $.each(hotData, function (index, item) {
                        let photoCard = new PhotoCard(item, $('#column1').width());
                        getShortestColumn($('#column1'), $('#column2'), $('#column3'), $('#column4')).append(photoCard.render());
                    });
                }


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
                    <div>
                        <a class="uk-navbar-toggle" uk-search-icon href="#"></a>
                        <div class="uk-drop" uk-drop="mode: click; pos: left-center; offset: 0">
                            <form class="uk-search uk-search-navbar uk-width-1-1">
                                <input class="uk-search-input" type="search" placeholder="搜索用户/标签..." autofocus>
                            </form>
                        </div>
                    </div>
                    <ul class="uk-navbar-nav">
                        <li class="uk-active"><a href="">今日推荐</a></li>
                        <li><a href="activity.php">约拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
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
            <section id="todayHot" class="uk-panel uk-position-relative uk-padding uk-clearfix">
                <h2 class="uk-position-relative uk-position-top-center">热门作品</h2>
                <ul style="list-style: none" class="uk-child-width-1-4">
                    <li id="column1" class="uk-float-left uk-padding-small"></li>
                    <li id="column2" class="uk-float-left uk-padding-small"></li>
                    <li id="column3" class="uk-float-left uk-padding-small"></li>
                    <li id="column4" class="uk-float-left uk-padding-small"></li>
                </ul>
            </section>
            <hr class="uk-divider-icon"/>
            <section id="todayStar" class="uk-padding uk-padding-remove-top uk-position-relative">
                <h2 class="uk-position-relative uk-position-top-center">今日明星</h2>
                <div class="uk-padding uk-grid uk-child-width-1-3" uk-grid>
                    <div id="second">
                        <div class="uk-position-relative uk-position-center uk-text-center">
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>徐梓航</p>
                            <p>今日获得喜欢总数：<span class="uk-badge like-badge">512</span></p>
                            <img class="top3-icon" src="../imgs/today/star_second.png"/>
                        </div>
                    </div>
                    <div id="first">
                        <div class="uk-position-relative uk-position-center uk-text-center">
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>徐杨晨</p>
                            <p>今日获得喜欢总数：<span class="uk-badge like-badge">1024</span></p>
                            <img class="top3-icon" src="../imgs/today/star_first.png"/>
                        </div>
                    </div>
                    <div id="third">
                        <div class="uk-position-relative uk-position-center uk-text-center">
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>薛恺丰</p>
                            <p>今日获得喜欢总数：<span class="uk-badge like-badge">256</span></p>
                            <img class="top3-icon" src="../imgs/today/star_third.png"/>

                        </div>
                    </div>
                </div>
            </section>
        </main>
        <footer>
            <div class="uk-background-secondary uk-light uk-text-center uk-position-relative">
                <p class="uk-position-center">©2017 Xyc. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>