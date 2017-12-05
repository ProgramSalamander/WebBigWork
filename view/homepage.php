<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myHeadPicUrl = getHeadPicURI($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];

$homepageUsername = $_GET['username'];
try {
    $db = getDB();
    $ret = $db->query();
} catch (Exception $e) {

}
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
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

        <script>
            $('document').ready(function () {
                let uri = new URI();
                loadAlbum();
                if (uri.search(true)['username'] === encodeURI('<?php echo $_COOKIE['username']?>')){
                    myPageMode();
                }
                else {
                    hisPageMode();
                }
            });

            function myPageMode() {
                $('#homePageHeadPic').attr('src', '<?php echo $myHeadPicUrl ?>');

                initEditInfo($('#editNicknameContainer'),$('#saveNicknameContainer'));
                initEditInfo($('#editSignContainer'),$('#saveSignContainer'));

                $('#albumContainer').append($(`<div></div>`))
            }

            function hisPageMode() {
                $('#editNicknameBtn').hide();
                $('#editSignBtn').hide();
            }

            function initEditInfo(editContainer, saveContainer) {
                let editTxt = editContainer.find('span');
                let editBtn = editContainer.find('a');

                let saveInput = saveContainer.find('input');
                let saveBtn = saveInput.next();
                let cancelBtn = saveBtn.next();

                let temp = editTxt.text();

                editBtn.click(function () {
                    saveInput.val(editTxt.text());

                    temp = editTxt.text();

                    editContainer.toggle();
                    saveContainer.toggle();
                });

                saveBtn.click(function () {
                    editTxt.text(saveInput.val());

                    saveContainer.toggle();
                    editContainer.toggle();

                });

                cancelBtn.click(function () {
                    editTxt.text(temp);

                    saveContainer.toggle();
                    editContainer.toggle();
                });


            }

            function loadAlbum() {
                let data = [
                    {
                        id: 1,
                        name: '童年',
                        username: 'xycxyc',
                        coverUrl: '../imgs/index/bg1.jpg'
                    },
                    {
                        id: 2,
                        name: '20岁',
                        username: 'xycxyc',
                        coverUrl: '../imgs/index/bg1.jpg'
                    },
                    {
                        id: 3,
                        name: '30岁',
                        username: 'xycxyc',
                        coverUrl: '../database/imgs/headPics/default.jpg'
                    },
                    {
                        id: 4,
                        name: '40岁',
                        username: 'xycxyc',
                        coverUrl: '../database/imgs/headPics/default.jpg'
                    },
                ];

                $.each(data, function (index, item) {
                    $('#albumContainer').append(new Album(item).render());
                })
            }

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
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
                        <li>
                            <a id="myHeadPic" href="">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $myHeadPicUrl?>', 50).render());
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
        <main class="uk-padding-large uk-padding-remove-top uk-padding-remove-bottom">
            <section class="uk-padding uk-padding-remove-bottom">
                <div uk-grid>
                    <div class="uk-width-expand">
                        <div style="overflow: hidden; position: relative" class="uk-margin-top uk-align-left uk-width-small uk-height-small uk-border-circle">
                            <img id="homePageHeadPic" class="photo-high" src="<?php  ?>"/>
                        </div>
                        <h2 id="editNicknameContainer" class="uk-article-title"><span>路飞</span>
                            <a id="editNicknameBtn" class="uk-icon" uk-icon="icon:file-edit" title="修改昵称" uk-tooltip></a>
                        </h2>
                        <div style="display: none" id="saveNicknameContainer" class="uk-h2">
                            <input class="uk-input" type="text" name="nickname"/>
                            <a id="saveNicknameConfirmBtn" class="uk-icon" uk-icon="icon:check" title="保存并上传" uk-tooltip></a>
                            <a id="saveNicknameCancelBtn" class="uk-icon" uk-icon="icon:close" title="取消" uk-tooltip></a>
                        </div>
                        <p id="editSignContainer" class="uk-article-meta"><span>要成为海贼王的男人</span>
                            <a id="editSignBtn" class="uk-icon" uk-icon="icon:file-edit" title="修改签名" uk-tooltip></a>
                        </p>
                        <div style="display: none;" id="saveSignContainer">
                            <input class="uk-input uk-article-meta" type="text" name="sign"/>
                            <a id="saveSignConfirmBtn" class="uk-icon" uk-icon="icon:check" title="保存并上传" uk-tooltip></a>
                            <a id="saveSignCancelBtn" class="uk-icon" uk-icon="icon:close" title="取消" uk-tooltip></a>
                        </div>
                    </div>
                    <div class="uk-padding-small uk-align-center uk-margin-large-right">
                        <p>总作品数：<span id="totalWorks" class="uk-badge">12</span></p>
                        <p>获得喜欢总数：<span id="totalLikes" class="uk-badge">12</span></p>
                        <p>获得评论总数：<span id="totalComments" class="uk-badge">12</span></p>
                    </div>
                </div>
                <h3 class="uk-margin">常用标签</h3>
                <div id="labelContainer" class="uk-grid-small uk-padding-small uk-child-width-auto" uk-grid>
                    <div>
                        <span class="uk-label label-scenery">风景</span>
                    </div>
                    <div>
                        <span class="uk-label label-people">人像</span>
                    </div>
                    <div>
                        <span class="uk-label label-animal">动物</span>
                    </div>
                </div>

            </section>
            <section class="uk-padding">
                <h3 class="uk-margin">相册</h3>
                <div id="albumContainer" class="uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l" uk-grid>

                </div>
<!--                <div class="uk-margin-top uk-text-center">-->
<!--                    <button class="uk-button uk-button-text">查看更多相册</button>-->
<!--                </div>-->
            </section>
        </main>
        <footer>
            <div class="uk-background-secondary uk-light uk-text-center uk-position-relative">
                <p class="uk-position-center">©2017 Xyc. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>