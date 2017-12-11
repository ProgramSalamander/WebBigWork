<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/5
 * Time: 下午8:36
 */
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myUsername = $_COOKIE['username'];
$myHeadPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于摄影社交的网站</title>
        <link rel="stylesheet" href="../css/uikit.min.css"/>
        <link rel="stylesheet" type="text/css" href="../css/today.css">
        <link rel="stylesheet" type="text/css" href="../css/homepage.css">
        <link rel="stylesheet" type="text/css" href="../css/photo.css">
        <link rel="stylesheet" type="text/css" href="../css/label.css">

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/album.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>
            $('document').ready(function () {
                new SearchBox($('#searchBoxContainer')).init();
            });
        </script>
    </head>
    <body>
        <header>
            <nav class="uk-background-secondary uk-light " uk-navbar uk-sticky>
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
                            <a id="myHeadPic" href="">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $myHeadPicUrl?>', 50).render());
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
        <main class="uk-padding-large uk-padding-remove-top uk-padding-remove-bottom">
            <section class="uk-padding uk-padding-remove-bottom">
                <img class="uk-position-center" src="../imgs/error.png" />
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>