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

$albumInfo = null;

try {
    $db = getDB();
    $ret = $db->query("SELECT a.album_name, a.user_id, a.cover_url,l.label_chi_name, l.label_eng_name, u.nick_name  FROM album AS a, label AS l, user AS u WHERE a.album_id = '$albumId' AND a.label_id = l.label_id AND a.user_id = u.user_id;");
    if ($row = $ret->fetchArray()) {
        $albumInfo = array('name' => $row['album_name'], 'userId' => $row['user_id'], 'coverUrl' => getAlbumURL($row['cover_url']), 'label_chi' => $row['label_chi_name'], 'label_eng' => $row['label_eng_name'], 'author' => $row['nick_name']);

        $albumPhotos = array();
        $ret = $db->query("SELECT photo_id, photo_name, photo_url AS album_comments FROM photo WHERE album_id = '$albumId'");
        while ($row = $ret->fetchArray()) {
            array_push($albumPhotos, array('photoId' => $row['photo_id'], 'photoName' => $row['photo_name'], 'photoUrl' => $row['photo_url']));
        }
        $albumInfo['photos'] = $albumPhotos;

        $ret = $db->query("SELECT sum(photo_likes) AS album_likes, sum(photo_comments) AS album_comments FROM photo WHERE album_id = '$albumId'");
        if ($row = $ret->fetchArray()) {
            $albumInfo['likes'] = isset($row['album_likes']) ? $row['album_likes'] : 0;
            $albumInfo['comments'] = isset($row['album_comments']) ? $row['album_comments'] : 0;
        } else {
            header('location:error.php');
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
        <title>iPaiPai - 专注于约拍的网站</title>
        <link rel="stylesheet" href="../css/uikit.min.css"/>
        <link rel="stylesheet" href="../css/today.css"/>
        <link rel="stylesheet" href="../css/label.css"/>
        <link rel="stylesheet" href="../css/photo.css"/>

        <style>
            .uk-label {
                font-size: 1rem;
            }

            .uk-grid-margin {
                margin-top: 30px !important;
            }

            .left-part {

            }

            .right-part {
                border-left: 1px solid lightgray;
            }

            #addPhoto {
                display: none;
                vertical-align: text-top;
            }

            #addPhoto input {
                display: none;
            }

            #addPhoto img {
                width: 24px;
                margin-left: 10px;
                margin-right: 10px;
                vertical-align: middle;
            }

            #coverContainer div {
                color: white;
            }

            #editBtn, #confirmBtn, #cancelBtn {
                width: 50px;
                height: 25px;
                font-size: small;
                padding: 0;
                line-height: 25px;
                vertical-align: text-top;
                display: none;
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

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {
                topProgressBar.init();

                //判断相册是否属于当前用户
                if ('<?php echo $albumInfo['userId'] == $_SESSION['user_info']['user_id']?>' === '1') {
                    myAlbumMode();
                }
                else {
                    hisAlbumMode();
                }

                adapt($('#cover'));

            });

            function myAlbumMode() {
                //可上传图片
                $('#addPhoto').removeClass().addClass('uk-display-inline-block').click(function () {
                    $('#addPhoto').find('input')[0].click();
                });

                //封面可更换
                $('#coverContainer').html(`<input id="coverChange" type="file" accept="image/jpeg"/>
                                            <div style="height:250px" class="uk-overflow-hidden uk-position-relative">
                                                <img id="cover" src="<?php echo $albumInfo['coverUrl'].'?t='.time()?>"/>
                                                <div class="uk-position-center">
                                                    <span class="uk-transition-fade">更换封面</span>
                                                </div>
                                            </div>`);
                $('#coverChange').on('change', function () {
                    topProgressBar.start();
                    let formData = new FormData();
                    formData.append('file', $('#coverChange')[0].files[0]);
                    formData.append('callFunc', 'modifyCover');
                    formData.append('albumName', '<?php echo $albumInfo['name']?>');
                    topProgressBar.process();
                    $.ajax({
                        type: 'POST',
                        url: '../php/album.php',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                topProgressBar.end(function () {
                                    window.location.reload();
                                });
                            }
                            else {
                                notification(data.msg, 'danger');
                                topProgressBar.end();
                            }
                        },
                        error: function (error) {
                            console.log(error);
                            notification('网络异常，请稍候再试。', 'warning');
                            topProgressBar.end();
                        }
                    });
                });

                //信息可更改
                let editBtn = $('#editBtn');
                let confirmBtn = $('#confirmBtn');
                let cancelBtn = $('#cancelBtn');

                let name = $('#albumName');
                let label = $('#albumLabel');

                let originName = name.html();
                let originLabel = label.html();

                editBtn.show();

                editBtn.click(function () {
                    topProgressBar.start();
                    editBtn.hide();
                    name.html('<input id="newName" class="uk-input" type="text" value="<?php echo $albumInfo['name'] ?>"/>');
                    label.html(`<select id="newLabel" class="uk-select">
                                                <option>人像</option>
                                                <option>风景</option>
                                                <option>动物</option>
                                                <option>植物</option>
                                                <option>美食</option>
                                                <option>街拍</option>
                                                <option>艺术</option>
                                           </select>`);
                    confirmBtn.show();
                    cancelBtn.show();
                });

                confirmBtn.click(function () {
                    topProgressBar.end();

                    $.ajax({
                        type: 'POST',
                        url: '../php/album.php',
                        data: {
                            callFunc: 'modifyInfo',
                            id: '<?php echo $albumId?>',
                            oldName: '<?php echo $albumInfo['name']?>',
                            newName: $('#newName').val(),
                            newLabel: $('#newLabel').val()
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                topProgressBar.end(function () {
                                    window.location.reload();
                                });
                            } else {
                                notification(data.msg, 'danger');
                                name.html(originName);
                                label.html(originLabel);
                                cancelBtn.hide();
                                confirmBtn.hide();
                                editBtn.show();
                            }
                        },
                        error: function (error) {
                            console.log(error);
                            notification('网络传输异常，请重试。','warning');
                            name.html(originName);
                            label.html(originLabel);
                            cancelBtn.hide();
                            confirmBtn.hide();
                            editBtn.show();
                        }
                    })
                });

                cancelBtn.click(function () {
                    name.html(originName);
                    label.html(originLabel);
                    cancelBtn.hide();
                    confirmBtn.hide();
                    editBtn.show();
                })
            }

            function hisAlbumMode() {
                //封面不可更换
                $('#coverContainer').html(`<div style="height:250px" class="uk-overflow-hidden uk-position-relative" uk-lightbox>
                                                <a href="<?php echo $albumInfo['coverUrl']?>">
                                                    <img id="cover" src="<?php echo $albumInfo['coverUrl']?>"/>
                                                </a>
                                            </div>`)
            }

            function CreatePhoto(photoData) {
                return $(`<div style="height: 150px" class="uk-text-center">
                                <a class="uk-inline-clip uk-transition-toggle" href="photoContent.php?id=${photoData.id}">
                                    <img src="${photoData.url+'?t='+new Date().getTime()}"/>
                                    <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                                        <p class="uk-text-muted uk-margin-remove">${photoData.name}</p>
                                    </div>
                                </a>
                            </div>`);
            }
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
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈<span class="uk-badge">8</span></a></li>
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
        <main class="uk-grid uk-padding" uk-grid>
            <section class="uk-width-1-3 left-part">
                <h4>相册信息
                    <button id="editBtn" class="uk-margin-left uk-button uk-button-primary">修改</button>
                    <button id="confirmBtn" class="uk-margin-left uk-button badge-work">保存</button>
                    <button id="cancelBtn" class="uk-margin-left uk-button uk-button-default">取消</button>
                </h4>
                <div id="infoContainer" class="uk-grid uk-padding-small" uk-grid>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>作者</span>
                    </div>
                    <div class="uk-width-3-4 uk-text-center">
                        <span><?php echo $albumInfo['author'] ?></span>
                    </div>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>名称</span>
                    </div>
                    <div id="albumName" class="uk-width-3-4 uk-text-center">
                        <span><?php echo $albumInfo['name'] ?></span>
                    </div>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>标签</span>
                    </div>
                    <div id="albumLabel" class="uk-width-3-4 uk-text-center">
                        <span class="uk-label label-<?php echo $albumInfo['label_eng'] ?>"><?php echo $albumInfo['label_chi'] ?></span>
                    </div>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>喜欢数</span>
                    </div>
                    <div class="uk-width-3-4 uk-text-center">
                        <span class="uk-badge badge-like"><?php echo $albumInfo['likes'] ?></span>
                    </div>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>评论数</span>
                    </div>
                    <div class="uk-width-3-4 uk-text-center">
                        <span class="uk-badge badge-comment"><?php echo $albumInfo['comments'] ?></span>
                    </div>
                    <div class="uk-width-1-4 uk-text-center">
                        <span>封面</span>
                    </div>
                    <div id="coverContainer" class="uk-width-1-1 uk-inline-clip uk-transition-toggle uk-light uk-position-relative" uk-form-custom>
                    </div>
                </div>
            </section>
            <section class="uk-width-expand right-part">
                <h4>
                    相册照片
                    <a id="addPhoto" class="uk-hidden" title="添加图片" uk-form-custom uk-tooltip>
                        <img src="../imgs/icon/add.png"/>
                        <input type="file" name="file" accept="image/jpeg">
                    </a>
                </h4>
                <div id="photoContainer" class="uk-grid-small uk-child-width-1-4" uk-grid>
                    <div class="uk-text-center">
                        <a class="uk-inline-clip uk-transition-toggle">
                            <img src="../imgs/index/bg1.jpg"/>
                            <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                                <p class="uk-text-muted uk-margin-remove">照片一</p>
                            </div>
                        </a>
                    </div>
                    <div class="uk-text-center">
                        <a class="uk-inline-clip uk-transition-toggle">
                            <img src="../imgs/index/bg2.jpg"/>
                            <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                                <p class="uk-text-muted uk-margin-remove">照片二</p>
                            </div>
                        </a>
                    </div>
                    <div class="uk-text-center">
                        <a class="uk-inline-clip uk-transition-toggle">
                            <img src="../imgs/index/bg3.jpg"/>
                            <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                                <p class="uk-text-muted uk-margin-remove">照片三</p>
                            </div>
                        </a>
                    </div>
                    <div class="uk-text-center">
                        <a class="uk-inline-clip uk-transition-toggle">
                            <img src="../imgs/index/bg4.jpg"/>
                            <div class="uk-transition-slide-bottom uk-position-bottom uk-overlay uk-overlay-default">
                                <p class="uk-text-muted uk-margin-remove">照片四</p>
                            </div>
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