<?php
require_once 'php/dbHelper.php';
session_start();
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    try {
        $db = new SQLite3('database/ipaipai.db');
        $ret = $db->query("SELECT * FROM admin WHERE admin_username = '$username' AND admin_password = '$password'");
        $res = $ret->fetchArray();

        if (!empty($res)) {
            $_SESSION['admin'] = true;
        } else {
            echo "<script>notification('用户名或密码错误，请重新输入！','danger');</script>";
        }
    } catch (Exception $e) {
        echo "<script>notification('服务器异常，请稍候再试。','danger')</script>";
    } finally {
        $db->close();
    }
}
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
        <link rel="stylesheet" href="css/uikit.min.css">
        <script src="js/lib/jquery-3.2.1.min.js"></script>
        <script src="js/lib/uikit.min.js"></script>
        <script src="js/lib/uikit-icons.min.js"></script>
        <script src="js/util/notification.js"></script>

        <style>
            form {
                margin-bottom: 100px;
            }

            #login, #manage {
                display: none;
            }

            #manage {
                width: 1100px;
                height: 600px;
                background: white;
                border: 2px solid #333333;
                position: relative;
                overflow: hidden;
            }

            #manageNav {
                width: 198px;
                height: 100%;
                border-right: 2px solid #333333;
                position: relative;
                float: left;
                text-align: center;
            }

            #manageNav ul li {
                padding: 10px;
                font-size: 16px;
            }

            #manageContent {
                width: 900px;
                height: 100%;
                position: relative;
                float: left;
            }

            #manageContentSearch {
                width: 100%;
                height: 30px;
                padding: 15px;
                border-bottom: 2px solid #333333;
                position: relative;
            }

            #manageContentSearch input {
                font-size: 16px;
            }

            #manageContentList {
                height: 538px;
            }

            #manageContentList ul li {
                border-bottom: 1px solid #333333;
                margin: 0;
                padding: 20px;
            }

            #manageContentList ul li span {
                margin-right: 40px;
            }


        </style>

        <script>
            class UserItem {
                constructor(userInfo) {
                    this.userInfo = userInfo;
                }

                render() {
                    let user_id = this.userInfo.user_id;
                    let username = this.userInfo.username;
                    let phone_number = this.userInfo.phone_number;
                    let nick_name = this.userInfo.nick_name;
                    let item = $(`<li class="uk-grid-small" uk-grid>
                                <div class="uk-width-auto">
                                    <span>ID：${user_id}</span>
                                </div>
                                <div class="uk-width-auto">
                                    <span>用户名：${username}</span>
                                </div>
                                <div class="uk-width-auto">
                                    <span>手机号：${phone_number}</span>
                                </div>
                                <div class="uk-width-expand">
                                    <span>昵称：${nick_name}</span>
                                </div>
                                <div class="uk-width-auto">
                                    <span class="uk-icon" uk-icon="pencil" title="修改用户信息" uk-tooltip></span>
                                </div>
                                <div class="uk-width-auto">
                                    <span class="uk-icon" uk-icon="trash" title="删除此用户" uk-tooltip></span>
                                </div>
                            </li>`);


                    $.each(item.find('.uk-icon'), function (index, element) {
                        if (index === 0) {
                            element.onclick = function () {
                                showEditPanel({
                                    id: user_id,
                                    username: username,
                                    phone: phone_number,
                                    nickname: nick_name
                                });
                            };
                        } else {
                            element.onclick = function () {
                                UIkit.modal.confirm('确认要删除此用户吗？').then(
                                    function () {
                                        $.ajax({
                                            type: 'POST',
                                            url: '/php/deleteUser.php',
                                            dataType: 'json',
                                            data: {user_id: user_id},
                                            success: function (data) {
                                                if (data.code === 200) {

                                                    searchUser();
                                                    notification(data.msg, 'success');
                                                }
                                                else {
                                                    notification(data.msg, 'danger');
                                                }
                                            },
                                            error: function () {
                                                notification('网络异常，请稍候再试。', 'warning');
                                            }
                                        });
                                    }
                                );
                            };
                        }
                    });
                    return item;
                }
            }

            $('document').ready(function () {
                let login = $('#login');
                let manage = $('#manage');
                if ('<?php echo empty($_SESSION['admin'])?>') {
                    login.show();
                    manage.hide();
                }
                else {
                    login.hide();
                    manage.show();

                    $('#btn').click(searchUser);

                    $('#reset').click(function () {
                        $('#search').val('');
                        $('#list').html('');
                    });
                }
            });

            function showEditPanel(data) {
                UIkit.modal.confirm(`<form>
                                <fieldset>
                                    <legend>修改用户信息</legend>
                                    <div class="uk-margin">
                                        <label>用户名：<input class="username-input" type="text" name="username" value="${data.username}"/></label>
                                    </div>
                                    <div class="uk-margin">
                                        <label>手机号：<input class="phone-input" type="text" name="phone" value="${data.phone}"/></label>
                                    </div>
                                    <div class="uk-margin">
                                        <label>昵称：<input class="nickname-input" type="text" name="nickname" value="${data.nickname}"/></label>
                                    </div>
                                </fieldset>
                            </form>`)
                    .then(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'php/modifyUser.php',
                            data: {
                                user_id: data.id,
                                username: $('.username-input').last().val(),
                                phone: $('.phone-input').last().val(),
                                nickname: $('.nickname-input').last().val()
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code === 200){
                                    searchUser();
                                    notification(data.msg, 'success');
                                }
                                else {
                                    notification(data.msg, 'danger');
                                }
                            },
                            error: function () {
                                notification('网络异常，请稍候再试。','warning');
                            }
                        });
                    });
            }

            function refreshList(data) {
                $('#list').html('');
                $.each(data, function (index, element) {
                    let userItem = new UserItem(element);
                    $('#list').append(userItem.render());
                })
            }

            function searchUser() {
                $('#list').html('');
                let searchVal = $('#search').val();
                if (!searchVal) {
                    notification('用户名不能为空！', 'warning');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: 'php/searchUser.php',
                    dataType: 'json',
                    data: {username: searchVal},
                    success: function (data) {
                        if (data.code === 200) {
                            let userData = data.data;
                            refreshList(userData);
                        }
                        else {
                            notification(data.msg, 'danger');
                        }
                    },
                    error: function () {
                        notification('网络异常，请检查网络后重试。', 'warning')
                    }
                });
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
            </nav>
        </header>
        <main>
            <div id="login" class="uk-position-center">
                <form action="admin.php" method="post">
                    <fieldset class="uk-fieldset">
                        <legend class="uk-legend uk-text-center">管理员登录</legend>
                        <div class="uk-margin">
                            <label>管理员账号：<input class="uk-input" name="username" type="text" autofocus="autofocus"/></label>
                        </div>
                        <div class="uk-margin">
                            <label>密码：<input class="uk-input" name="password" type="password"/></label>
                        </div>
                        <div class="uk-margin">
                            <input class="uk-width-1-1 uk-button uk-button-default" type="submit"/>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div id="manage" class="uk-position-center uk-margin-top">
                <div id="manageNav">
                    <ul class="uk-nav uk-nav-default">
                        <li class="uk-active"><a>用户管理</a></li>
                    </ul>
                </div>
                <div id="manageContent">
                    <div id="manageContentSearch">
                        <input id="search" class="uk-search" type="search" autofocus="autofocus" placeholder="输入用户名..."/>
                        <button id="btn">搜索</button>
                        <button id="reset">重置</button>
                    </div>
                    <div id="manageContentList">
                        <ul id="list" class="uk-list">

                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
