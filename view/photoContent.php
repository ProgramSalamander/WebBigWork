<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/12/8
 * Time: 下午3:05
 */
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];

if (!isset($_GET['id'])) {
    header('location: error.php');
}

$photoId = $_GET['id'];

$albumName = '';
$albumUserId = '';

$photoInfo = array();
$photoComments = array();
try {
    $db = getDB();

    //获取照片基本信息数据
    $ret = $db->query("SELECT p.photo_url, u.user_id, u.username, u.nick_name, a.album_name, l.label_chi_name,l.label_eng_name FROM photo AS p,album AS a, label AS l, user AS u WHERE p.photo_id = '$photoId' AND p.album_id = a.album_id AND a.user_id = u.user_id AND p.label_id = l.label_id;");
    if ($row = $ret->fetchArray()) {
        $photoInfo['photoUrl'] = getPhotoURL($row['username'], $row['album_name'], $row['photo_url']);
        $photoInfo['authorId'] = $row['user_id'];
        $photoInfo['authorName'] = $row['nick_name'];
        $photoInfo['authorUsername'] = $row['username'];
        $photoInfo['photoAlbumName'] = $row['album_name'];
        $photoInfo['photoWHRate'] = getPhotoWHRate($photoInfo['photoUrl']);
        $photoInfo['photoLabel'] = $row['label_chi_name'];
        $photoInfo['photoLabelClass'] = $row['label_eng_name'];

        //获取照片喜欢数
        $ret = $db->query("SELECT count(*) AS likes FROM like_comment_record WHERE photo_id = '$photoId' AND type = 'l'");
        if ($row = $ret->fetchArray()) {
            $photoInfo['photoLikes'] = $row['likes'];
        } else {
            $photoInfo['photoLikes'] = 0;
        }

        //获取照片评论数
        $ret = $db->query("SELECT count(*) AS comments FROM like_comment_record WHERE photo_id = '$photoId' AND type = 'c'");
        if ($row = $ret->fetchArray()) {
            $photoInfo['photoComments'] = $row['comments'];
        } else {
            $photoInfo['photoComments'] = 0;
        }

        //获取照片是否已喜欢
        $ret = $db->query("SELECT * FROM like_comment_record WHERE type='l' AND photo_id = '$photoId' AND user_id = '$myUserId'");
        if ($ret->fetchArray()) {
            $photoInfo['isAlreadyLike'] = true;
        } else {
            $photoInfo['isAlreadyLike'] = false;
        }

        //获取照片评论
        $ret = $db->query("SELECT r.record_content,r.record_time, u.username, u.nick_name, u.head_pic_url FROM like_comment_record AS r, user AS u WHERE r.photo_id = '$photoId' AND r.type = 'c' AND r.user_id = u.user_id ORDER BY r.record_time");
        while ($row = $ret->fetchArray()) {
            $comment = array();
            $comment['content'] = $row['record_content'];
            $comment['time'] = $row['record_time'];
            $comment['username'] = $row['username'];
            $comment['userNickname'] = $row['nick_name'];
            $comment['userHeadPicUrl'] = getHeadPicURL($row['head_pic_url']);
            array_push($photoComments, $comment);
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

            #editBtn, #confirmBtn, #cancelBtn, #deleteBtn {
                width: 50px;
                height: 25px;
                font-size: small;
                padding: 0;
                line-height: 25px;
                vertical-align: text-top;
                display: none;
            }

            #likeButton {
                height: 40px;
                display: none;
            }

            #addComment img {
                width: 24px;
                margin-left: 10px;
                margin-right: 10px;
                vertical-align: text-top;
            }

        </style>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/waterfall.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/topProgressBar.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>
            let topProgressBar = new TopProgressBar();

            $('document').ready(function () {
                topProgressBar.init();
                new SearchBox($('#searchBoxContainer')).init();

                adaptPhoto();

                //判断照片是否属于当前用户
                if ('<?php echo $photoInfo['authorId'] == $_SESSION['user_info']['user_id']?>' === '1') {
                    myPhotoMode();
                }
                else {
                    hisPhotoMode();
                }

                //评论照片
                adapt($('#addCommentContainer').find('img'));
                $('#addComment').click(function (ev) {
                    ev.preventDefault();
                    topProgressBar.start();
                    $.ajax({
                        type: 'POST',
                        url: '../php/photo.php',
                        data: {
                            callFunc: 'addComment',
                            id: '<?php echo $photoId?>',
                            content: $('#content').val()
                        },
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
                            }
                        },
                        error: function (error) {
                            console.log(error);
                            notification("网络异常，请稍候再试。", 'warning');
                        }
                    });
                });

                loadComments();
            });

            function adaptPhoto() {
                if (parseFloat('<?php echo $photoInfo['photoWHRate']?>') > 1) {
                    $('#photoContainer').addClass('uk-width-1-1');
                }
                else {
                    $('#photoContainer').addClass('uk-width-1-2');
                }
            }

            function myPhotoMode() {
                //信息可更改
                let editBtn = $('#editBtn');
                let confirmBtn = $('#confirmBtn');
                let cancelBtn = $('#cancelBtn');
                let deleteBtn = $('#deleteBtn');
                let label = $('#photoLabel');
                let originLabel = label.html();

                editBtn.show().click(function () {
                    topProgressBar.start();
                    editBtn.hide();
                    deleteBtn.hide();
                    label.html(`<select id="newLabel" class="uk-select uk-form-width-medium">
                                                <option>人像</option>
                                                <option>风景</option>
                                                <option>动物</option>
                                                <option>植物</option>
                                                <option>美食</option>
                                                <option>街拍</option>
                                                <option>艺术</option>
                                           </select>`);
                    $('#newLabel').val('<?php echo $photoInfo['photoLabel']?>');
                    confirmBtn.show();
                    cancelBtn.show();
                });

                confirmBtn.click(function () {
                    topProgressBar.end();

                    $.ajax({
                        type: 'POST',
                        url: '../php/photo.php',
                        data: {
                            callFunc: 'modifyPhoto',
                            id: '<?php echo $photoId?>',
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
                                label.html(originLabel);
                                cancelBtn.hide();
                                confirmBtn.hide();
                                editBtn.show();
                                deleteBtn.show();
                            }
                        },
                        error: function (error) {
                            notification('网络传输异常，请重试。', 'warning');
                            label.html(originLabel);
                            cancelBtn.hide();
                            confirmBtn.hide();
                            editBtn.show();
                            deleteBtn.show();
                        }
                    })
                });

                cancelBtn.click(function () {
                    label.html(originLabel);
                    cancelBtn.hide();
                    confirmBtn.hide();
                    editBtn.show();
                    deleteBtn.show();
                });

                //照片可删除
                deleteBtn.show().click(function () {
                    UIkit.modal.confirm('确定要删除这张照片吗？').then(function () {
                        $.ajax({
                            type: 'POST',
                            url: '../php/photo.php',
                            data: {
                                callFunc: 'deletePhoto',
                                id: '<?php echo $photoId?>',
                                url: '<?php echo $photoInfo['photoUrl']?>'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200) {
                                    notification(data.msg, 'success');
                                    topProgressBar.end(function () {
                                        window.location.href = 'albumContent.php?an=<?php echo $photoInfo['photoAlbumName']?>&ui=<?php echo $photoInfo['authorId']?>';
                                    });
                                }
                                else {
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function (error) {
                                console.log(error);
                                notification("网络异常，请稍候再试", 'warning');
                            }
                        });
                    });
                });
            }

            function hisPhotoMode() {

                let isLike = '<?php echo $photoInfo['isAlreadyLike']?>' === '1';
                toggleLikeButton(isLike);

                //可以喜欢他人的照片
                $('#likeButton').show().click(function () {
                    $.ajax({
                        type: 'POST',
                        url: '../php/photo.php',
                        data: {
                            callFunc: 'likePhoto',
                            id: '<?php echo $photoId?>',
                            isAlreadyLike: isLike
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                isLike = !isLike;
                                toggleLikeButton(isLike);
                            } else {
                                notification(data.msg, 'danger');
                            }
                        },
                        error: function (error) {
                            notification('网络传输异常，请重试。', 'warning');
                        }
                    });
                });
            }

            function loadComments() {
                let comments = <?php echo json_encode($photoComments)?>;
                let commentContainer = $('#commentContainer');
                $.each(comments, function (index, element) {
                    let comment = $(`<li class="uk-padding-small">
                                                <article class="uk-card uk-card-default uk-padding-small uk-padding-remove-left" uk-grid>
                                                    <div class="uk-grid uk-grid-medium" uk-grid>
                                                        <div class="uk-width-auto">
                                                            <div style="width: 60px;height: 60px;overflow: hidden" class="uk-border-circle">
                                                                <img src="${element.userHeadPicUrl}" alt="">
                                                            </div>
                                                        </div>
                                                        <div class="uk-width-1-2">
                                                            <h4 class="uk-h4 uk-margin-remove"><a href="homepage.php?username=${element.username}" style="text-decoration: none" class="uk-button-text">${element.userNickname}</a></h4>
                                                            <div class="uk-comment-meta uk-subnav uk-margin-remove-top">
                                                                <span class="uk-width-large">${element.time}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="uk-margin uk-margin-remove-bottom uk-width-1-1">
                                                        ${element.content}
                                                    </div>
                                                </article>
                                            </li>`);
                    adapt(comment.find('img'));
                    commentContainer.append(comment);
                });
            }

            function toggleLikeButton(isLike) {
                if (isLike) {
                    $('#likeButton').html('<img src="../imgs/icon/like_active.svg"/>');
                }
                else {
                    $('#likeButton').html('<img src="../imgs/icon/like.svg"/>');
                }
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
                    <div id="searchBoxContainer"></div>
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
            <section class="uk-position-fixed uk-padding-large uk-padding-remove-top uk-width-2-3">
                <div class="uk-flex uk-flex-center uk-margin-top uk-margin-bottom">
                    <button id="likeButton" class="uk-button uk-button-default" title="喜欢" uk-tooltip>
                        <img src="../imgs/icon/like.svg"/>
                    </button>
                </div>
                <div class="uk-flex uk-flex-center">
                    <div id="photoContainer" uk-lightbox>
                        <a href="<?php echo $photoInfo['photoUrl'] ?>">
                            <img src="<?php echo $photoInfo['photoUrl'] ?>"/>
                        </a>
                    </div>
                </div>
            </section>
            <section class="uk-margin-left uk-padding uk-padding-remove-right uk-flex uk-flex-right">
                <div class="uk-width-1-3 ">
                    <h4>图片信息
                        <button id="editBtn" class="uk-margin-left uk-button uk-button-primary">修改</button>
                        <button id="deleteBtn" class="uk-margin-left uk-button badge-like">删除</button>
                        <button id="confirmBtn" class="uk-margin-left uk-button badge-work">保存</button>
                        <button id="cancelBtn" class="uk-margin-left uk-button uk-button-default">取消</button>
                    </h4>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-3 uk-text-center">
                            作者
                        </div>
                        <div class="uk-width-2-3 uk-text-center">
                            <?php echo $photoInfo['authorName'] ?>
                        </div>
                        <div class="uk-width-1-3 uk-text-center">
                            所属相册
                        </div>
                        <div class="uk-width-2-3 uk-text-center">
                            <a href="albumContent.php?an=<?php echo urldecode($photoInfo['photoAlbumName']) ?>&ui=<?php echo $photoInfo['authorId'] ?>"
                               class="uk-link"><?php echo $photoInfo['photoAlbumName'] ?></a>
                        </div>
                        <div class="uk-width-1-3 uk-text-center">
                            标签
                        </div>
                        <div id="photoLabel" class="uk-width-2-3 uk-text-center">
                            <span class="uk-label label-<?php echo $photoInfo['photoLabelClass'] ?>"><?php echo $photoInfo['photoLabel'] ?></span>
                        </div>
                        <div class="uk-width-1-3 uk-text-center">
                            喜欢
                        </div>
                        <div class="uk-width-2-3 uk-text-center">
                            <span class="uk-badge badge-like"><?php echo $photoInfo['photoLikes'] ?></span>
                        </div>
                        <div class="uk-width-1-3 uk-text-center">
                            评论
                        </div>
                        <div class="uk-width-2-3 uk-text-center">
                            <span class="uk-badge badge-comment"><?php echo $photoInfo['photoComments'] ?></span>
                        </div>
                    </div>
                    <h4>照片评论</h4>
                    <ul class="uk-list" id="commentContainer">
                        <li id="addCommentContainer" class="uk-padding-small">
                            <article class="uk-card uk-card-default uk-padding-small uk-padding-remove-left" uk-grid>
                                <div class="uk-grid uk-grid-medium" uk-grid>
                                    <div class="uk-width-auto">
                                        <div style="width: 60px;height: 60px;overflow: hidden;" class="uk-border-circle">
                                            <img src="<?php echo getHeadPicURL($_SESSION['user_info']['head_pic_url']) ?>" alt="">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-2">
                                        <h4 class="uk-h4 uk-margin-remove"><a style="text-decoration: none" class="uk-button-text"><?php echo $_SESSION['user_info']['nick_name']?></a></h4>
                                        <div class="uk-comment-meta uk-subnav uk-margin-remove-top">
                                            <span class="uk-width-large">现在</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="uk-margin uk-margin-remove-bottom uk-width-1-1">
                                    <textarea id="content" class="uk-textarea" autofocus="autofocus" maxlength="50" placeholder="长度在50字以内"></textarea>
                                    <button id="addComment" class="uk-align-right uk-margin uk-button uk-button-primary">发表</button>
                                </div>
                            </article>
                        </li>
                    </ul>
                </div>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>