<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myHeadPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);

$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];
$homepageUsername = $_GET['username'];
$homepageId = 0;
$homepageHeadPicUrl = '';
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
        $homepageId = $row['user_id'];
        $homepageHeadPicUrl = getHeadPicURL($row['head_pic_url']);
        $homepageSign = $row['user_sign'];

        //获取总作品数
        $ret = $db->query("SELECT count(*) AS total_photos FROM photo AS p, album AS a, user AS u WHERE u.user_id = '$homepageId' AND a.user_id = u.user_id AND p.album_id = a.album_id");
        if ($row = $ret->fetchArray()) {
            $homepageTotalPhotos = $row['total_photos'];
        } else {
            $homepageTotalPhotos = 0;
        }

        //获取总喜欢数
        $ret = $db->query("SELECT count(*) AS total_likes FROM photo AS p, album AS a, user AS u, like_comment_record AS l WHERE u.user_id = '$homepageId' AND l.type = 'l' AND a.user_id = u.user_id AND p.album_id = a.album_id AND p.photo_id = l.photo_id");
        if ($row = $ret->fetchArray()) {
            $homepageTotalLikes = $row['total_likes'];
        } else {
            $homepageTotalLikes = 0;
        }


        //获取总评论数
        $ret = $db->query("SELECT count(*) AS total_comments FROM photo AS p, album AS a, user AS u, like_comment_record AS l WHERE u.user_id = '$homepageId' AND l.type = 'c' AND a.user_id = u.user_id AND p.album_id = a.album_id AND p.photo_id = l.photo_id");
        if ($row = $ret->fetchArray()) {
            $homepageTotalComments = $row['total_comments'];
        } else {
            $homepageTotalComments = 0;
        }

        //获取相册信息
        $ret = $db->query("SELECT album.album_id, album.user_id, album.album_name, album.cover_url FROM album, user WHERE user.username = '$homepageUsername' AND user.user_id = album.user_id ");
        while ($row = $ret->fetchArray()) {
            array_push($homepageAlbums, array('id' => $row['album_id'], 'userId' => $row['user_id'], 'name' => $row['album_name'], 'coverUrl' => getAlbumURL($row['cover_url'])));
        }

        //获取常用标签
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
        <title>iPaiPai - 专注于摄影社交的网站</title>
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
        <script src="../js/component/searchBox.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {
                topProgressBar.init();
                new SearchBox($('#searchBoxContainer')).init();

                let uri = new URI();

                loadLabels();
                loadAlbums();

                //判断浏览的是否是当前用户的主页
                if (uri.search(true)['username'] === encodeURI('<?php echo $_COOKIE['username']?>')) {
                    myPageMode();
                }
                else {
                    hisPageMode();
                }

                adapt($('#homePageHeadPic'));
            });

            function myPageMode() {
                //主页头像设置
                $('#homePageHeadPic').attr('src', '<?php echo $myHeadPicUrl ?>');


                //头像修改
                $('#headPicContainer').html(`<a class="js-head-pic-upload">
                                <input id="upload" type="file" accept="image/jpeg">
                                <img id="homePageHeadPic" src="<?php echo $homepageHeadPicUrl?>"/>
                                <div class="uk-position-center">
                                    <span class="uk-transition-fade">修改头像</span>
                                </div>
                            </a>`);

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
                    if (checkSign(newSign)) {
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

                //新建相册
                $('#albumContainer').append($(`<div class="uk-text-center">
                                                    <div class="add-album">
                                                        <div>
                                                             <a href="newAlbum.php" class="uk-icon" uk-icon="icon: plus;ratio:3" title="添加相册" uk-tooltip></a>
                                                        </div>
                                                    </div>
                                                </div>`));
            }

            function hisPageMode() {
                $('#headPicContainer').html(`<img id="homePageHeadPic" src="<?php echo $homepageHeadPicUrl?>" />`);

                $('#editNicknameBtn').hide();
                $('#editSignBtn').hide();

                $('#followContainer').show();

                let isFollowed = ('<?php echo isFollowed($myUserId, $homepageId)?>' === '1');
                toggleFollowButton(isFollowed);

                $('#followButton').click(function (ev) {
                    ev.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: '../php/followUser.php',
                        data: {
                            myId: '<?php echo $myUserId?>',
                            followId: '<?php echo $homepageId?>',
                            state: isFollowed
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                isFollowed = !isFollowed;
                                toggleFollowButton(isFollowed);
                            }
                            else {
                                notification(data.msg, 'danger');
                            }
                        },
                        error: function (data) {
                            notification('网络异常，请稍候再试', 'warning');
                        }
                    });
                });
            }

            function toggleFollowButton(isFollowed) {
                if (isFollowed) {
                    $('#followButton').attr('title', '不再获取Ta的动态').html(`<img src="../imgs/icon/like_active.svg"/>
                            <span>已关注</span>`);
                }
                else {
                    $('#followButton').attr('title', '实时获取Ta的动态！').html(`<img src="../imgs/icon/like.svg"/>
                            <span>关注</span>`);
                }
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
                    <div id="searchBoxContainer"></div>
                    <ul class="uk-navbar-nav">
                        <li><a href="today.php">今日推荐</a></li>
                        <li><a href="activity.php">一起拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈</a></li>
                        <li>
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
                                <script>
                                    $('#myHeadPic').prepend(new MyHeadPic('<?php echo $myHeadPicUrl?>', 50).render());
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
        <main class="uk-padding-large uk-padding-remove-top uk-padding-remove-bottom">
            <section class="uk-padding uk-padding-remove-bottom">
                <div uk-grid>
                    <div class="uk-width-1-2">
                        <div id="headPicContainer" style="overflow: hidden; position: relative"
                             class="uk-margin-top uk-align-left uk-width-small uk-height-small uk-border-circle uk-inline-clip uk-transition-toggle uk-light"
                             uk-form-custom>
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
                    <div class="uk-padding-small uk-align-center">
                        <div>
                            <p>总作品数：<span id="totalWorks" class="uk-badge"><?php echo $homepageTotalPhotos ?></span></p>
                            <p>获得喜欢总数：<span id="totalLikes" class="uk-badge"><?php echo $homepageTotalLikes ?></span></p>
                            <p>获得评论总数：<span id="totalComments" class="uk-badge"><?php echo $homepageTotalComments ?></span></p>
                        </div>
                    </div>
                    <div style="display: none" id="followContainer" class="uk-padding uk-align-center">
                        <button id="followButton" class="uk-button uk-button-default" uk-tooltip>
                            <img src="../imgs/icon/like.svg"/>
                            <span>关注</span>
                        </button>
                    </div>
                </div>
                <h3 class="uk-margin">常用标签</h3>
                <div id="labelContainer" class="uk-grid-small uk-padding-small uk-child-width-auto" uk-grid></div>
            </section>
            <section class="uk-padding">
                <h3 class="uk-margin">相册</h3>
                <div id="albumContainer" class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l"
                     uk-grid></div>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>