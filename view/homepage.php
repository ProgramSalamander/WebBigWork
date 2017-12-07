<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myHeadPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);

$myUsername = $_COOKIE['username'];
$homepageUsername = $_GET['username'];
$homepageNickname = '';
$homepageSign = '';
$homepageTotalPhotos = 0;
$homepageTotalLikes = 0;
$homepageTotalComments = 0;
$homepageAlbums = array();
$homepageLabels = array();

try {
    $db = getDB();
    $ret = $db->query("SELECT * FROM user WHERE username = '$homepageUsername'");
    $row = $ret->fetchArray();
    if ($row) {
        $homepageNickname = $row['nick_name'];
        $homepageSign = $row['user_sign'];
        $homepageTotalPhotos = $row['total_photos'];
        $homepageTotalLikes = $row['total_likes'];
        $homepageTotalComments = $row['total_comments'];

        $ret = $db->query("SELECT album.album_id, album.album_name, album.cover_url FROM album, user WHERE user.username = '$homepageUsername' AND user.user_id = album.user_id ");
        while ($row = $ret->fetchArray()) {
            array_push($homepageAlbums, array('id' => $row['album_id'], 'name' => $row['album_name'], 'coverUrl' => getAlbumURL($row['cover_url'])));
        }

        $sql = <<<EOF
            SELECT DISTINCT label.label_chi_name, label.label_eng_name 
            FROM photo,album,user,label 
            WHERE user.username = '$homepageUsername'
            AND user.user_id = album.user_id 
            AND album.album_id = photo.album_id 
            AND photo.label_id = label.label_id;
EOF;
        $ret = $db->query($sql);
        while ($row = $ret->fetchArray()) {
            array_push($homepageLabels, array('chi' => $row['label_chi_name'], 'eng' => $row['label_eng_name']));
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
        <link rel="stylesheet" type="text/css" href="../css/today.css">
        <link rel="stylesheet" type="text/css" href="../css/homepage.css">
        <link rel="stylesheet" type="text/css" href="../css/photo.css">
        <link rel="stylesheet" type="text/css" href="../css/label.css">

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/checkInput.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/album.js"></script>
        <script src="../js/component/topProgressBar.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {
                let uri = new URI();

                topProgressBar.init();

                adapt($('#homePageHeadPic'));

                loadLabels();
                loadAlbums();

                if (uri.search(true)['username'] === encodeURI('<?php echo $_COOKIE['username']?>')) {
                    myPageMode();
                }
                else {
                    hisPageMode();
                }
            });

            function myPageMode() {
                //主页头像设置
                $('#homePageHeadPic').attr('src', '<?php echo $myHeadPicUrl ?>');

                $('#upload').on('change', function () {
                    let formData = new FormData();
                    formData.append('file', $('#upload')[0].files[0]);
                    $.ajax({
                        type: 'POST',
                        url: '../php/modifyHeadPic.php',
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
                                adapt($('#homePageHeadPic'));
                            }
                            else {
                                topProgressBar.end();
                                notification(data.msg, 'danger');
                            }
                        },
                        error: function (code) {
                            topProgressBar.end();
                            notification('网络异常，请稍候再试。', 'warning');
                        }

                    });
                });
                //头像修改
                UIkit.upload('.js-head-pic-upload', {
                    multiple: false,
                    loadStart: function () {
                        topProgressBar.start();
                    },
                    completeAll: function () {
                        topProgressBar.process();
                    },
                    error: function () {
                        topProgressBar.end();
                        notification('图片上传失败，请重试。', 'warning');
                    }
                });

                //昵称修改
                let tempNickname = $('#editNicknameText').text();

                $('#editNicknameBtn').click(function () {
                    $('#saveNicknameInput').val($('#editNicknameText').text());
                    tempNickname = $('#editNicknameText').text();
                    $('#editNicknameContainer').toggle();
                    $('#saveNicknameContainer').toggle();
                });

                $('#saveNicknameConfirmBtn').click(function () {
                    let newNickname = $('#saveNicknameInput').val();
                    if (tempNickname === newNickname) {
                        notification('昵称未发生改动。', 'warning');
                    }
                    else if (checkNickname(newNickname)) {
                        $.ajax({
                            type: 'POST',
                            url: '../php/modifyPersonalInfo.php',
                            data: {
                                callFunc: 'modifyNickname',
                                username: '<?php echo $myUsername?>',
                                newNickname: newNickname,
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200) {
                                    notification(data.msg, 'success');
                                    $('#editNicknameText').text(newNickname);
                                    $('#editNicknameContainer').toggle();
                                    $('#saveNicknameContainer').toggle();
                                }
                                else {
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function () {
                                notification('网络传输异常，请稍候再试。', 'warning');
                            }
                        });
                    }

                });

                $('#saveNicknameCancelBtn').click(function () {
                    $('#editNicknameText').text(tempNickname);
                    $('#editNicknameContainer').toggle();
                    $('#saveNicknameContainer').toggle();
                });

                //签名修改
                let tempSign = $('#editSignText').text();

                $('#editSignBtn').click(function () {
                    $('#saveSignInput').val($('#editSignText').text());
                    tempSign = $('#editSignText').text();
                    $('#editSignContainer').toggle();
                    $('#saveSignContainer').toggle();
                });

                $('#saveSignConfirmBtn').click(function () {
                    let newSign = $('#saveSignInput').val();
                    if (checkNickname(newSign)) {
                        $.ajax({
                            type: 'POST',
                            url: '../php/modifyPersonalInfo.php',
                            data: {
                                callFunc: 'modifySign',
                                username: '<?php echo $myUsername?>',
                                newSign: newSign,
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200) {
                                    notification(data.msg, 'success');
                                    $('#editSignText').text(newSign);
                                    $('#editSignContainer').toggle();
                                    $('#saveSignContainer').toggle();
                                }
                                else {
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function () {
                                notification('网络传输异常，请稍候再试。', 'warning');
                            }
                        });
                    }

                });

                $('#saveSignCancelBtn').click(function () {
                    $('#editSignText').text(tempSign);
                    $('#editSignContainer').toggle();
                    $('#saveSignContainer').toggle();
                });

                $('#albumContainer').append($(`<div></div>`))
            }

            function hisPageMode() {
                $('#editNicknameBtn').hide();
                $('#editSignBtn').hide();
            }

            function loadAlbums() {
                let albums = <?php echo json_encode($homepageAlbums)?>;
                $.each(albums, function (index, element) {
                    $('#albumContainer').append(new Album(element).render());
                })
            }

            function loadLabels() {
                let labels = <?php echo json_encode($homepageLabels) ?>;
                if (labels.length === 0) {
                    $('#labelContainer').append($('<p>暂无标签</p>'));
                }
                else {
                    $.each(labels, function (index, element) {
                        $('#labelContainer').append(`<div>
                    <span class="uk-label label-${element.eng}">${element.chi}</span>
                        </div>`);
                    });
                }
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
                                    <li><a href="homepage.php?username=<?php echo $myUsername ?>"><span class="uk-icon" uk-icon="icon:home"></span>我的主页</a>
                                    </li>
                                    <li><a href="album.php?username=<?php echo $myUsername ?>"><span class="uk-icon" uk-icon="icon:image"></span>我的相册</a>
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
                <div uk-grid>
                    <div class="uk-width-expand">
                        <div style="overflow: hidden; position: relative"
                             class="uk-margin-top uk-align-left uk-width-small uk-height-small uk-border-circle uk-inline-clip uk-transition-toggle uk-light"
                             uk-form-custom>
                            <a class="js-head-pic-upload">
                                <input id="upload" type="file" accept="image/jpeg">
                                <img id="homePageHeadPic" src=""/>
                                <div class="uk-position-center">
                                    <span class="uk-transition-fade">修改头像</span>
                                </div>
                            </a>
                        </div>
                        <h2 id="editNicknameContainer" class="uk-article-title"><span id="editNicknameText"><?php echo $homepageNickname ?></span>
                            <a id="editNicknameBtn" class="uk-icon" uk-icon="icon:file-edit" title="修改昵称" uk-tooltip></a>
                        </h2>
                        <div style="display: none" id="saveNicknameContainer" class="uk-h2">
                            <input id="saveNicknameInput" class="uk-input" type="text" name="nickname"/>
                            <a id="saveNicknameConfirmBtn" class="uk-icon" uk-icon="icon:check" title="保存并上传" uk-tooltip></a>
                            <a id="saveNicknameCancelBtn" class="uk-icon" uk-icon="icon:close" title="取消" uk-tooltip></a>
                        </div>
                        <p id="editSignContainer" class="uk-article-meta"><span id="editSignText"><?php echo $homepageSign ?></span>
                            <a id="editSignBtn" class="uk-icon" uk-icon="icon:file-edit" title="修改签名" uk-tooltip></a>
                        </p>
                        <div style="display: none;" id="saveSignContainer">
                            <input id="saveSignInput" class="uk-input uk-article-meta" type="text" name="sign"/>
                            <a id="saveSignConfirmBtn" class="uk-icon" uk-icon="icon:check" title="保存并上传" uk-tooltip></a>
                            <a id="saveSignCancelBtn" class="uk-icon" uk-icon="icon:close" title="取消" uk-tooltip></a>
                        </div>
                    </div>
                    <div class="uk-padding-small uk-align-center uk-margin-large-right">
                        <p>总作品数：<span id="totalWorks" class="uk-badge"><?php echo $homepageTotalPhotos ?></span></p>
                        <p>获得喜欢总数：<span id="totalLikes" class="uk-badge"><?php echo $homepageTotalLikes ?></span></p>
                        <p>获得评论总数：<span id="totalComments" class="uk-badge"><?php echo $homepageTotalComments ?></span></p>
                    </div>
                </div>
                <h3 class="uk-margin">常用标签</h3>
                <div id="labelContainer" class="uk-grid-small uk-padding-small uk-child-width-auto" uk-grid></div>
            </section>
            <section class="uk-padding">
                <h3 class="uk-margin">相册</h3>
                <div id="albumContainer" class="uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l" uk-grid></div>
            </section>
        </main>
        <footer>
            <div class="uk-background-secondary uk-light uk-text-center uk-position-relative">
                <p class="uk-position-center">©2017 Xyc. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>