<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];

try {
    $db = getDB();

    //获取关注用户的列表
    $friendList = array();

    $ret = $db->query("SELECT f.follow_id, u.username, u.nick_name, u.head_pic_url, u.user_sign FROM follow AS f, user AS u WHERE f.user_id = '$myUserId' AND f.follow_id = u.user_id");
    while ($row = $ret->fetchArray()) {
        $friend = array();
        $friend['id'] = $row['follow_id'];
        $friend['username'] = $row['username'];
        $friend['nickname'] = $row['nick_name'];
        $friend['headPicUrl'] = getHeadPicURL($row['head_pic_url']);
        $friend['sign'] = $row['user_sign'];
        array_push($friendList, $friend);
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
        <link rel="stylesheet" href="../css/friendsNews.css"/>
        <link rel="stylesheet" href="../css/photo.css"/>

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>
            $('document').ready(function () {
                new SearchBox($('#searchBoxContainer')).init();

                let friendData = <?php echo json_encode($friendList)?>;
                $.each(friendData, function (index, element) {
                    let friend = $(`<li>
                                    <div style="text-decoration: none" class="uk-padding-small uk-grid-small uk-position-relative" uk-grid>
                                        <div class="uk-width-auto">
                                            <div style="width: 50px; height: 50px; overflow: hidden; position: relative" class="uk-border-circle">
                                                <img src="${element.headPicUrl}" />
                                            </div>
                                        </div>
                                        <div class="uk-width-expand uk-margin-left">
                                            <p style="line-height: 30px" class="uk-margin-remove"><a href="homepage.php?username=${element.username}">${element.nickname}</a></p>
                                            <p style="line-height: 20px" class="uk-margin-remove uk-padding-remove uk-text-meta">${element.sign}</p>
                                        </div>
                                        <div class="uk-width-auto uk-position-center-right">
                                            <button class="uk-button uk-button-default">取消关注</button>
                                        </div>
                                    </div>
                                  </li>`);
                    adapt(friend.find('img'));
                    friend.find('button').click(function (e) {
                        e.preventDefault();
                        $.ajax({
                            type: 'POST',
                            url: '../php/followUser.php',
                            data: {
                                myId: '<?php echo $myUserId?>',
                                followId: element.id,
                                state: 'true'
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200) {
                                    notification(data.msg, 'success');
                                    friend.remove();
                                }
                                else {
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function (error) {
                                notification("网络异常，请稍候再试。", 'warning');
                            }
                        })
                    });

                    $('#friendsList').append(friend);
                })
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
            <section class="uk-section side-bar uk-visible@l">
                <ul class="uk-nav uk-nav-default">
                    <li>
                        <a href="friendsNews.php">关注动态</a>
                    </li>
                    <li>
                        <a href="myNews.php">我的动态</a>
                    </li>
                    <li class="uk-active">
                        <a>关注管理</a>
                    </li>
                </ul>
            </section>
            <section>
                <div class="uk-flex uk-flex-center uk-padding">
                    <ul id="friendsList" class="uk-list uk-list-divider uk-width-1-3"></ul>
                </div>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>