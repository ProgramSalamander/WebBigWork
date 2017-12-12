<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];

$photos = array();
try {
    $db = getDB();
    $ret = null;

    //是否指定标签
    if (!isset($_GET['label'])) {
        $ret = $db->query("SELECT p.photo_id, p.photo_url, u.username, u.nick_name,a.album_name, l.label_chi_name, l.label_eng_name FROM photo AS p,album AS a,user AS u, label AS l WHERE p.album_id = a.album_id AND a.user_id = u.user_id AND p.label_id = l.label_id ORDER BY p.add_time DESC ");
    } else {
        $label = $_GET['label'];
        $ret = $db->query("SELECT p.photo_id, p.photo_url, u.username, u.nick_name,a.album_name, l.label_chi_name, l.label_eng_name FROM photo AS p,album AS a,user AS u, label AS l WHERE l.label_eng_name = '$label' AND p.label_id = l.label_id AND p.album_id = a.album_id AND a.user_id = u.user_id  ORDER BY p.add_time DESC ");
    }

    if ($ret) {
        while ($row = $ret->fetchArray()) {
            $photo = array();
            $photo['photoId'] = $row['photo_id'];
            $photo['photoUrl'] = getPhotoURL($row['username'], $row['album_name'], $row['photo_url']);
            $photo['photoAuthor'] = $row['nick_name'];
            $photo['photoLabel'] = $row['label_chi_name'];
            $photo['photoLabelClass'] = 'label-'.$row['label_eng_name'];
            $photo['photoWHRate'] = getPhotoWHRate($photo['photoUrl']);
            array_push($photos, $photo);
        }
    } else {
        header('location:error.php');
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
        <link rel="stylesheet" type="text/css" href="../css/today.css">
        <link rel="stylesheet" type="text/css" href="../css/ground.css">
        <link rel="stylesheet" type="text/css" href="../css/label.css">
        <link rel="stylesheet" type="text/css" href="../css/photo.css">

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/waterfall.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>

            $('document').ready(function () {
                new SearchBox($('#searchBoxContainer')).init();

                let labelArr = getLabelEngArray();

                let uri = new URI();

                if (!uri.search(true).label) {
                    $('.ground-filter').children('li')[0].className = 'uk-active';
                }

                $.each(labelArr, function (index, element) {
                    if (element === uri.search(true).label) {
                        $('.ground-filter').children('li')[index + 1].className = 'uk-active';
                    }


                });

                let photoData = <?php echo json_encode($photos)?>;

                $.each(photoData, function (index, element) {
                    let photo = new PhotoCard(element, $('#column1').width());
                    getShortestColumn($('#column1'), $('#column2'), $('#column3'), $('#column4')).append(photo.render());
                });
            })
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
                        <li class="uk-active"><a href="">四处逛逛</a></li>
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
            <div class="uk-display-inline-block uk-position-fixed uk-card uk-card-default uk-margin-left uk-margin-top">
                <h6 class="uk-card-header uk-text-center uk-margin-remove-bottom">类型</h6>
                <ul class="uk-card-body uk-nav uk-nav-default uk-flex-left uk-child-width-auto ground-filter">
                    <li><a href="?">综合</a></li>
                    <li><a href="?label=people">人像</a></li>
                    <li><a href="?label=scenery">风景</a></li>
                    <li><a href="?label=animal">动物</a></li>
                    <li><a href="?label=plant">植物</a></li>
                    <li><a href="?label=food">美食</a></li>
                    <li><a href="?label=street">街拍</a></li>
                    <li><a href="?label=art">艺术</a></li>
                </ul>
            </div>
            <div class="uk-flex uk-flex-right">
                <ul style="list-style: none" class="uk-child-width-1-4 ground-container">
                    <li id="column1" class="uk-float-left uk-padding-small"></li>
                    <li id="column2" class="uk-float-left uk-padding-small"></li>
                    <li id="column3" class="uk-float-left uk-padding-small"></li>
                    <li id="column4" class="uk-float-left uk-padding-small"></li>
                </ul>
            </div>
        </main>
        <footer>
        </footer>
    </body>
</html>