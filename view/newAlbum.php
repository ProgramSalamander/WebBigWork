<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/7
 * Time: 下午4:39
 */
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myHeadPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];
?>

<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
        <link rel="stylesheet" href="../css/uikit.min.css"/>
        <link rel="stylesheet" type="text/css" href="../css/today.css">
        <link rel="stylesheet" type="text/css" href="../css/photo.css">
        <link rel="stylesheet" type="text/css" href="../css/label.css">

        <style>
            form {
                width: 600px;
            }

            h3 {
                margin-bottom: 50px;
            }

            .uk-form-label {
                text-align: center;
                font-size: 1rem;
            }

            #cover {
                width: 100%;
            }
        </style>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/checkInput.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/topProgressBar.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();
            topProgressBar.init();

            $('document').ready(function () {
                //标签加载
                $.each(getLabelChiArray(), function (index, element) {
                    $('#albumLabel').append($(`<option>${element}</option>`));
                });

                $('#coverUpload').on('change', function () {
                    let cover = $('#cover');
                    showPreview($('#coverUpload'), cover, topProgressBar);
                    adapt(cover);
                });

                $("#submit").click(function () {
                    if (checkAlbumName($('#albumName').val())){
                        let formData = new FormData();
                        formData.append('file', $('#coverUpload')[0].files[0]);
                        formData.append('callFunc', 'addAlbum');
                        formData.append('albumName', $('#albumName').val());
                        formData.append('albumLabel', $('#albumLabel').val());
                        $.ajax({
                            type: 'POST',
                            url: '../php/album.php',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200) {
                                    topProgressBar.end(function () {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 500)
                                    });
                                    notification(data.msg, 'success');
                                    // setTimeout(function () {
                                    //     window.location.href = '../view/album.php';
                                    // }, 1000);
                                }
                                else {
                                    topProgressBar.end();
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function (code) {
                                console.log(code);
                                topProgressBar.end();
                                notification('网络异常，请稍候再试。', 'warning');
                            }
                        });
                    }

                    return false;
                });
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
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $myHeadPicUrl?>', 50).render());
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
        <main class="uk-padding-large uk-padding-remove-top uk-padding-remove-bottom">
            <section class="uk-flex uk-flex-center">
                <form class="uk-form-horizontal">
                    <input type="hidden" name="callFunc" value="addAlbum"/>
                    <h3>新建相册</h3>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="albumName">相册名称</label>
                        <div class="uk-form-controls">
                            <input id="albumName" class="uk-input" maxlength="10" type="text" name="albumName" placeholder="不超过10个字符"/>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="albumLabel">相册标签</label>
                        <div class="uk-form-controls">
                            <select class="uk-select" id="albumLabel" name="albumLabel">
                            </select>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label">相册封面</label>
                        <div class="uk-form-controls">
                            <div style="height:250px;" class="uk-inline-clip uk-transition-toggle uk-light" uk-form-custom>
                                <input id="coverUpload" name="file" type="file" accept="image/jpeg"/>
                                <img id="cover" class="photo-long" alt="默认封面" src="../database/imgs/albums/default_cover.jpg"/>
                                <div class="uk-position-center">
                                    <span class="uk-transition-fade">更换封面</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-margin uk-text-center">
                        <button id="submit" class="uk-button uk-button-primary uk-form-width-medium">新建</button>
                    </div>
                </form>
            </section>
        </main>
        <footer>
            <div class="uk-background-secondary uk-light uk-text-center uk-position-bottom">
                <p class="uk-position-center">©2017 Xyc. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>