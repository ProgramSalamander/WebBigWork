<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/11/15
 * Time: 下午8:35
 */
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$albumId = $_GET['id'];
$myUsername = $_COOKIE['username'];
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
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>

        <script>
            $('document').ready(function () {

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
                        <li><a href="">今日推荐</a></li>
                        <li><a href="activity.php">约拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
                        <li>
                            <a id="myHeadPic" href="">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo getHeadPicURI($_SESSION['user_info']['head_pic_url']) ?>', 50).render());
                                </script>
                            </a>
                            <div class="uk-width-small uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    <li><a href="homepage.php?username=<?php echo $myUsername?>"><span class="uk-icon" uk-icon="icon:home"></span>我的主页</a></li>
                                    <li><a href="album.php?username=<?php echo $myUsername?>"><span class="uk-icon" uk-icon="icon:image"></span>我的相册</a></li>
                                    <li><a href="../php/signOut.php"><span class="uk-icon" uk-icon="icon:sign-out"></span>登出</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <main>
            <section class="uk-section uk-padding">
                <h3>相册名</h3>
                <div class="uk-child-width-1-4" uk-grid uk-lightbox="animation: scale">
                    <div>
                        <a class="uk-inline" href="../imgs/index/bg1.jpg" caption="Caption 1">
                            <img src="../imgs/index/bg1.jpg" alt="">
                        </a>
                    </div>
                    <div>
                        <a class="uk-inline" href="../imgs/index/bg2.jpg" caption="Caption 2">
                            <img src="../imgs/index/bg2.jpg" alt="">
                        </a>
                    </div>
                    <div>
                        <a class="uk-inline" href="../imgs/index/bg3.jpg" caption="Caption 3">
                            <img src="../imgs/index/bg3.jpg" alt="">
                        </a>
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
