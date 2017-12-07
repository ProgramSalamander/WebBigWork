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
                loadTodayHot();

                function loadTodayHot() {
                    let hotData = [
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '人像',
                            photoLabelClass: 'label-people'
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery'
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '人像',
                            photoLabelClass: 'label-people'
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery'
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '动物',
                            photoLabelClass: 'label-animal'
                        },
                        {
                            photoUrl: '../../imgs/index/bg1.jpg',
                            photoAuthor: '徐杨晨',
                            photoLabel: '风景',
                            photoLabelClass: 'label-scenery'
                        }
                    ];
                    $.each(hotData, function (index, item) {
                        getShortestColumn().append(new PhotoCard(item).render());
                    });
                }

                function getShortestColumn() {
                    let list = [$('#column1'), $('#column2'), $('#column3'), $('#column4')];
                    let index = 0;
                    let height = list[index].height();
                    for (let i = 1; i < list.length; i++) {
                        if (list[i].height() < height) {
                            index = i;
                            height = list[i].height();
                        }
                    }
                    return list[index];
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
                                    <li><a href=""><span class="uk-icon" uk-icon="icon:image"></span>上传照片</a>
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
            <section id="todayStar" class="uk-container uk-padding uk-padding-remove-top">
                <h2 class="uk-position-relative uk-position-top-center">今日明星</h2>
                <div class="uk-grid uk-grid-divider uk-padding uk-padding-remove-top uk-padding-remove-bottom uk-padding-remove-left uk-text-center"
                     uk-grid>
                    <div class="uk-grid uk-width-expand uk-child-width-1-3 uk-margin-right" uk-grid>
                        <div class="">
                            <div class="uk-position-relative uk-position-center">
                                <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                    <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                                </div>
                                <p>徐杨晨</p>
                                <p>今日获得喜欢总数：<span class="uk-badge like-badge">1024</span></p>
                                <img class="top3-icon" src="../imgs/today/star_first.png"/>
                            </div>
                        </div>
                        <div class="">
                            <div class="uk-position-relative uk-position-center">
                                <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                    <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                                </div>
                                <p>徐梓航</p>
                                <p>今日获得喜欢总数：<span class="uk-badge like-badge">512</span></p>
                                <img class="top3-icon" src="../imgs/today/star_second.png"/>
                            </div>
                        </div>
                        <div class="">
                            <div class="uk-position-relative uk-position-center">
                                <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                    <img class="uk-transition-scale-up uk-transition-opaque top3-head-pic" src="../imgs/index/bg4.jpg"/>
                                </div>
                                <p>薛恺丰</p>
                                <p>今日获得喜欢总数：<span class="uk-badge like-badge">256</span></p>
                                <img class="top3-icon" src="../imgs/today/star_third.png"/>

                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-3 uk-child-width-1-2" uk-grid>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢一德 <span class="uk-badge like-badge">128</span></p>
                        </div>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢二德 <span class="uk-badge like-badge">128</span></p>
                        </div>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢三德 <span class="uk-badge like-badge">128</span></p>
                        </div>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢四德 <span class="uk-badge like-badge">128</span></p>
                        </div>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢五德 <span class="uk-badge like-badge">128</span></p>
                        </div>
                        <div>
                            <div class="uk-border-circle uk-inline-clip uk-transition-toggle" title="去Ta的主页" uk-tooltip>
                                <img class="uk-transition-scale-up uk-transition-opaque top10-head-pic" src="../imgs/index/bg4.jpg"/>
                            </div>
                            <p>鄢六德 <span class="uk-badge like-badge">128</span></p>
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