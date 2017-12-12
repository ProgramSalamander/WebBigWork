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

$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];
$userAlbums = array();
$defaultAlbumName = null;

try {
    $db = getDB();
    $ret = $db->query("SELECT album_name FROM album WHERE user_id = '$myUserId'");
    while ($row = $ret->fetchArray()) {
        array_push($userAlbums, $row['album_name']);
    }
    if (isset($_GET['nm'])) {
        $defaultAlbumName = $_GET['nm'];
    } else {
        $defaultAlbumName = $userAlbums[0];
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
        <link rel="stylesheet" href="../css/today.css"/>
        <link rel="stylesheet" href="../css/label.css"/>
        <link rel="stylesheet" href="../css/photo.css"/>

        <style>
            .uk-form-label {
                text-align: center;
                font-size: 1rem;
            }

            form {
                border-bottom: 1px solid lightgray;
            }
        </style>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/topProgressBar.js"></script>
        <script src="../js/component/uploadImagePreview.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {

                topProgressBar.init();
                new SearchBox($('#searchBoxContainer')).init();

                //加载用户相册列表
                let userAlbums = <?php echo json_encode($userAlbums)?>;
                $.each(userAlbums, function (index, element) {
                    if (element === '<?php echo $defaultAlbumName?>') {
                        $('#destAlbum').append($(`<option selected="selected">${element}</option>`));
                    }
                    else {
                        $('#destAlbum').append($(`<option>${element}</option>`));
                    }
                });

                //拖拽上传
                UIkit.upload('.js-upload');

                //上传图片预览
                let uploadPreview = new UploadImagePreview($('#photoUpload'), topProgressBar);
                $('#preview').append(uploadPreview.render());

                $('#submit').click(function (ev) {
                    ev.preventDefault();
                    let formData = new FormData();
                    formData.append('callFunc', 'addPhoto');
                    formData.append('albumName', $('#destAlbum').val());

                    //FormData添加多文件的核心
                    $.each(uploadPreview.getImages(),function (index, element) {
                        formData.append('file[]', element);
                    });

                    $.ajax({
                        type: 'POST',
                        url: '../php/photo.php',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                setTimeout(function () {
                                    window.location.href = `../view/albumContent.php?an=${$('#destAlbum').val()}&ui=<?php echo $myUserId?>`;
                                }, 500);
                            }
                            else {
                                notification(data.msg, 'danger');
                            }
                        },
                        error: function (data) {
                            console.log(data);
                            notification('网络异常，请稍候再试。', 'warning');
                        }

                    });
                });
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
                        <li><a href="today.php">今日推荐</a></li>
                        <li><a href="activity.php">一起拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈</a></li>
                        <li>
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo getHeadPicURL($_SESSION['user_info']['head_pic_url']) ?>', 50).render());
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
            <section class="uk-margin uk-margin-remove-bottom uk-flex uk-flex-center">
                <form class="uk-width-2-3 uk-form-horizontal">
                    <input type="hidden" name="callFunc" value="addAlbum"/>
                    <h3>上传图片</h3>
                    <div class="uk-margin uk-flex uk-flex-center">
                        <label class="uk-form-label" for="destAlbum">目标相册</label>
                        <div class="uk-form-controls">
                            <select class="uk-form-width-medium uk-select" id="destAlbum" name="destAlbum"></select>
                        </div>
                    </div>
                    <div class="js-upload uk-margin uk-placeholder uk-text-center">
                        <span uk-icon="icon: cloud-upload"></span>
                        <span class="uk-text-middle">拖动图片至此或者</span>
                        <div uk-form-custom>
                            <input id="photoUpload" type="file" name="file[]" accept="image/jpeg" multiple="multiple">
                            <span class="uk-link">选择图片</span>
                        </div>
                    </div>
                    <div class="uk-margin uk-text-center">
                        <button id="submit" class="uk-button uk-button-primary uk-form-width-medium">开始上传</button>
                    </div>
                </form>
            </section>
            <section id="preview" class="uk-margin uk-margin-remove-top uk-flex uk-flex-center">
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>
