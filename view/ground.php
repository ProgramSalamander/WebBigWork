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
        <link rel="stylesheet" type="text/css" href="../css/today.css">
        <link rel="stylesheet" type="text/css" href="../css/ground.css">
        <link rel="stylesheet" type="text/css" href="../css/photo.css">

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/myHeadPic.js"></script>

        <script>
            $('document').ready(function () {
                let labelArr = getLabelEngArray();
                let uri = new URI();
                $.each(labelArr, function (index, element) {
                    if (element === uri.search(true).label) {
                        $('.ground-filter').children('li')[index].className = 'uk-active';
                    }
                });

                let photoData = [
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                        photoLabelClass: 'label-people'
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }, {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    },
                    {
                        photoUrl: '../../imgs/index/bg1.jpg',
                        photoAuthor: '徐杨晨',
                        photoLabel: '人像',
                    }];

                $.each(photoData, function (index, element) {
                    let photo = new PhotoCard(element);
                    $('.ground-container').children('div').get(index % 4).appendChild(photo.render().get(0));
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
                        <li class="uk-active"><a href="">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
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
            <div class="uk-display-inline-block uk-position-fixed uk-card uk-card-default uk-margin-left uk-margin-top">
                <h6 class="uk-card-header uk-text-center uk-margin-remove-bottom">类型</h6>
                <ul class="uk-card-body uk-nav uk-nav-default uk-flex-left uk-child-width-auto ground-filter">
                    <li><a href="?label=people">人像</a></li>
                    <li><a href="?label=scenery">风景</a></li>
                    <li><a href="?label=pet">宠物</a></li>
                    <li><a href="?label=object">物体</a></li>
                    <li><a href="?label=food">美食</a></li>
                    <li><a href="?label=street">街拍</a></li>
                    <li><a href="?label=art">艺术</a></li>
                </ul>
            </div>
            <div class="uk-flex uk-flex-right">
                <div class="uk-padding uk-grid-small uk-child-width-expand ground-container" uk-grid>
                    <div id="column1">

                    </div>
                    <div id="column2">

                    </div>
                    <div id="column3">

                    </div>
                    <div id="column4">

                    </div>
                </div>
            </div>
        </main>
        <footer>

        </footer>
    </body>
</html>