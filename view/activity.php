<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];

?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于摄影社交的网站</title>
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
                        <li><a href="">今日推荐</a></li>
                        <li class="uk-active"><a href="activity.php">一起拍</a></li>
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
            <section class="uk-section">
                <ul class="uk-padding-large uk-padding-remove-vertical" id="activityList" uk-accordion="multiple:true">
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                    <li class="uk-card uk-card-default uk-padding">
                        <img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>
                        <h3 class="uk-accordion-title">
                            南京街拍大赛
                            <span class="uk-margin-left uk-text-meta">参与人数 <span class="uk-badge uk-background-primary">20</span></span>
                        </h3>
                        <div class="uk-accordion-content">
                            <div class="">
                                <p>开始日期：<span class="uk-text-meta">2017-12-10</span></p>
                                <p>结束日期：<span class="uk-text-meta">2017-12-11</span></p>
                                <p>活动地点：<span class="uk-text-meta">南京市</span></p>
                                <p>活动简介：<span class="uk-text-meta">喜爱街拍的朋友们，赶快加入我们吧！</span></p>
                            </div>
                            <button class="uk-align-right uk-button uk-button-primary">加入</button>
                        </div>
                    </li>
                </ul>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>