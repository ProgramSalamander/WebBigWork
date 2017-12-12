<?php
require_once '../php/user.php';
require_once '../php/util.php';
session_start();
checkSignIn();

$headPicUrl = getHeadPicURL($_SESSION['user_info']['head_pic_url']);
$myUsername = $_COOKIE['username'];
$myUserId = $_SESSION['user_info']['user_id'];

$activityList = array();
try {
    $db = getDB();

    //获取活动基本信息
    $ret = $db->query("SELECT * FROM activity ORDER BY activity_create_time DESC");
    while ($row = $ret->fetchArray()) {
        $activity = array();
        $activity['id'] = $row['activity_id'];
        $activity['name'] = $row['activity_name'];
        $activity['startTime'] = $row['activity_start_time'];
        $activity['endTime'] = $row['activity_end_time'];
        $activity['location'] = $row['activity_location'];
        $activity['introduction'] = $row['activity_introduction'];
        $activity['isNew'] = (date('Y-m-d') == date('Y-m-d', strtotime($row['activity_create_time'])));

        $activityId = $activity['id'];
        //获取活动是否参加
        $subret = $db->query("SELECT * FROM activity_join_record WHERE activity_id = '$activityId' AND user_id = '$myUserId'");
        if ($subret->fetchArray()) {
            $activity['isJoin'] = true;
        } else {
            $activity['isJoin'] = false;
        }
        array_push($activityList, $activity);
    }

} catch (Exception $e) {
    header("location: error.php");
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

        <script src="../js/lib/jquery-3.2.1.min.js"></script>
        <script src="../js/lib/uikit.min.js"></script>
        <script src="../js/lib/uikit-icons.min.js"></script>
        <script src="../js/lib/URI.min.js"></script>
        <script src="../js/util/notification.js"></script>
        <script src="../js/util/imageHelper.js"></script>
        <script src="../js/util/labels.js"></script>
        <script src="../js/util/waterfall.js"></script>
        <script src="../js/component/myHeadPic.js"></script>
        <script src="../js/component/photoCard.js"></script>
        <script src="../js/component/searchBox.js"></script>

        <script>

            $('document').ready(function () {
                new SearchBox($('#searchBoxContainer')).init();

                let activityList = <?php echo json_encode($activityList)?>;

                $.each(activityList, function (index, element) {
                    let activity = $(`<li id="id${element.id}" class="uk-card uk-card-default uk-padding uk-open">
                                            <h3 class="uk-accordion-title">
                                                ${element.name}
                                            </h3>
                                            <div class="uk-accordion-content">
                                                <div>
                                                    <p>开始日期：<span class="uk-text-meta">${element.startTime}</span></p>
                                                    <p>结束日期：<span class="uk-text-meta">${element.endTime}</span></p>
                                                    <p>活动地点：<span class="uk-text-meta">${element.location}</span></p>
                                                    <p>活动简介：<span class="uk-text-meta">${element.introduction}</span></p>
                                                </div>
                                            </div>
                                        </li>`);
                    if (element.isNew) {
                        activity.prepend($('<img style="width: 40px;height: 40px" class="uk-position-top-left" src="../imgs/icon/new.svg"/>'));
                    }
                    if (element.isJoin) {
                        activity.find('.uk-accordion-content').append($('<button disabled class="uk-align-right uk-button uk-button-primary">已加入</button>'));
                    }
                    else {
                        let joinBtn = $('<button class="uk-align-right uk-button uk-button-primary">加入</button>');
                        joinBtn.click(function (ev) {
                            ev.preventDefault();

                            $.ajax({
                                type: 'POST',
                                url: '../php/joinActivity.php',
                                data: {
                                    activityId: element.id,
                                    userId: '<?php echo $myUserId?>'
                                },
                                dataType: 'json',
                                success: function (data) {
                                    if (data.code === 200){
                                        notification(data.msg, 'success');
                                        joinBtn.attr('disabled',true).text('已加入');
                                    }
                                    else {
                                        notification(data.msg,'danger');
                                    }
                                },
                                error: function (error) {
                                    notification('网络异常，请稍候再试', 'warning');
                                }
                            });
                        });
                        activity.find('.uk-accordion-content').append(joinBtn);
                    }
                    $('#activityList').append(activity);
                });

                let uri = new URI();
                if (uri.search(true).ai){
                    $('#scroll').attr('href','#id'+uri.search(true).ai).get(0).click();
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
                    <div id="searchBoxContainer"></div>
                    <ul class="uk-navbar-nav">
                        <li><a href="today.php">今日推荐</a></li>
                        <li class="uk-active"><a href="">一起拍</a></li>
                        <li><a href="ground.php">四处逛逛</a></li>
                        <li><a href="friendsNews.php">朋友圈</a></li>
                        <li>
                            <a id="myHeadPic" href="homepage.php?username=<?php echo $myUsername ?>">
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
            <section class="uk-section">
                <a id="scroll" class="uk-hidden" uk-scroll>test</a>
                <ul class="uk-padding-large uk-padding-remove-vertical" id="activityList" uk-accordion="multiple:true"></ul>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>