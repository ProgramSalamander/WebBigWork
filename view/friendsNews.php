<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURI($_SESSION['user_info']['head_pic_url']);
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
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
                        <li><a href="today.php">今日推荐</a></li>
                        <li><a href="activity.php">约拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li class="uk-active"><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
                        <li>
                            <a id="myHeadPic" href="">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $headPicUrl?>', 50).render());
                                </script>
                            </a>
                            <div class="uk-width-small uk-navbar-dropdown">
                                <ul class="uk-nav uk-navbar-dropdown-nav">
                                    <li><a href="homepage.php"><span class="uk-icon" uk-icon="icon:home"></span>我的主页</a></li>
                                    <li><a href=""><span class="uk-icon" uk-icon="icon:image"></span>我的相册</a></li>
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
                        <a href="">好友动态</a>
                    </li>
                    <li>
                        <a href="myNews.php">我的动态</a>
                    </li>
                    <li>
                        <a href="friendsManage.php">好友管理</a>
                    </li>
                </ul>
            </section>
            <section>
                <div id="friendsNewsContainer" class="uk-flex uk-flex-center">
                </div>
            </section>
            <section class="uk-padding friends-list-container">
                <h5>看看Ta的动态</h5>
                <div class="uk-grid-small uk-margin-remove uk-child-width-1-3 friends-list" uk-grid>
                </div>
            </section>
        </main>
        <footer style="display: none">
            <div class="uk-background-secondary uk-light uk-text-center uk-position-relative">
                <p class="uk-position-center">©2017 Xyc. All rights reserved.</p>
            </div>
        </footer>
    </body>
    <script src="../js/component/friendNews.js"></script>
    <script>
        $('document').ready(function () {

            let newsData = [{
                friendHeadPicUrl: '../imgs/index/bg4.jpg',
                friendHomePageUrl: 'hisPage.html',
                friendName: '徐杨晨',
                friendNewsContent: '这张照片好看吗？',
                friendNewsTime: '20:19',
                friendNewsPhotos: ['../imgs/index/bg1.jpg',
                    '../imgs/index/bg2.jpg',
                    '../imgs/index/bg3.jpg',
                    '../imgs/index/bg4.jpg'],
                friendNewsLikes: '10',
                friendNewsIsLiked: false,
                commentData: [{
                    commentHeadPicUrl: '../imgs/index/bg3.jpg',
                    commentUsername: '徐梓航',
                    commentContent: '好看！',
                    commentTime: '20:30'
                }, {
                    commentHeadPicUrl: '../imgs/index/bg2.jpg',
                    commentUsername: '鄢巴德',
                    commentContent: '不好看！',
                    commentTime: '20:40'
                }]
            }, {
                friendHeadPicUrl: '../imgs/index/bg4.jpg',
                friendHomePageUrl: 'hisPage.html',
                friendName: '徐杨晨',
                friendNewsContent: '这张照片好看吗？',
                friendNewsTime: '20:19',
                friendNewsPhotos: ['../imgs/index/bg1.jpg',
                    '../imgs/index/bg2.jpg',
                    '../imgs/index/bg3.jpg',
                    '../imgs/index/bg4.jpg'],
                friendNewsLikes: '10',
                friendNewsIsLiked: false,
                commentData: [{
                    commentHeadPicUrl: '../imgs/index/bg3.jpg',
                    commentUsername: '徐梓航',
                    commentContent: '好看！',
                    commentTime: '20:30'
                }, {
                    commentHeadPicUrl: '../imgs/index/bg2.jpg',
                    commentUsername: '鄢巴德',
                    commentContent: '不好看！',
                    commentTime: '20:40'
                }]
            }, {
                friendHeadPicUrl: '../imgs/index/bg4.jpg',
                friendHomePageUrl: 'hisPage.html',
                friendName: '徐杨晨',
                friendNewsContent: '这张照片好看吗？',
                friendNewsTime: '20:19',
                friendNewsPhotos: ['../imgs/index/bg1.jpg'],
                friendNewsLikes: '10',
                friendNewsIsLiked: false,
                commentData: [{
                    commentHeadPicUrl: '../imgs/index/bg3.jpg',
                    commentUsername: '徐梓航',
                    commentContent: '好看！',
                    commentTime: '20:30'
                }, {
                    commentHeadPicUrl: '../imgs/index/bg2.jpg',
                    commentUsername: '鄢巴德',
                    commentContent: '不好看！',
                    commentTime: '20:40'
                }]
            }];
            let friendNews = new FriendNews(newsData);
            $('#friendsNewsContainer').append(friendNews.render());


            let friendsData = [
                {
                    name: '徐杨晨',
                    headPicURL: '../imgs/index/bg2.jpg'
                },
                {
                    name: '徐梓航',
                    headPicURL: '../imgs/index/bg3.jpg'
                },
                {
                    name: '徐杨晨',
                    headPicURL: '../imgs/index/bg4.jpg'
                }];
            $.each(friendsData, function (index, val) {
                $('.friends-list').append($(`<div><a href="${new URI(window.location.href).query({friendName: encodeURI(val.name)})}"><img class="uk-border-circle" src="${val.headPicURL}" title="${val.name}" uk-tooltip/></a></div>`));
            });

            $('footer').show();
        });
    </script>
</html>