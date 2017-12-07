<?php
require_once 'php/user.php';
session_start();
if (!empty($_COOKIE['username'])) {
    header("location: view/today.php");
}
?>
<html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 专注于约拍的网站</title>
        <link rel="stylesheet" href="css/uikit.min.css"/>
        <link rel="stylesheet" href="css/index.css"/>

        <script src="js/lib/jquery-3.2.1.min.js"></script>
        <script src="js/lib/uikit.min.js"></script>
        <script src="js/lib/uikit-icons.min.js"></script>
        <script src="js/lib/URI.min.js"></script>
        <script src="js/util/notification.js"></script>
        <script src="js/util/checkInput.js"></script>

    </head>
    <body class="uk-background-cover uk-position-relative uk-offcanvas-content">
        <div class="uk-position-center">
            <h1 class="uk-heading-primary uk-text-bold uk-margin-medium-bottom uk-text-center uk-flex-middle uk-animation-slide-top"> 嗨！爱拍吗</h1>
            <div class="uk-width-auto">
                <div class="uk-position-bottom-center uk-position-relative">
                    <button class="uk-button-large uk-button-secondary uk-animation-slide-bottom" uk-toggle="target: #signCanvas">
                        进去看看!
                    </button>
                </div>
            </div>
        </div>
        <div class="uk-light uk-position-bottom-center uk-position-medium">
            <ul class="uk-dotnav">
                <li class="uk-active"><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
            </ul>
        </div>
        <div id="signCanvas" uk-offcanvas="flip: true">
            <div class="uk-offcanvas-bar">

                <button class="uk-offcanvas-close" type="button" uk-close></button>

                <h2 class="uk-margin">欢迎！</h2>

                <form id="signInForm">
                    <fieldset class="uk-fieldset">
                        <legend class="uk-legend">登录</legend>
                        <div class="uk-margin">
                            <input id="signInUsername" class="uk-input" type="text" placeholder="用户名"/>
                        </div>
                        <div class="uk-margin">
                            <input id="signInPassword" class="uk-input" type="password" placeholder="密码"/>
                        </div>
                        <button id="signInBtn" class="uk-form-width-medium uk-button-default uk-button">登录</button>
                    </fieldset>
                    <div class="uk-margin">
                        还没有账号？点<a id="toSignUpLink">这里</a>!
                    </div>
                </form>

                <form id="signUpForm">
                    <fieldset class="uk-fieldset">
                        <legend class="uk-legend">注册</legend>
                        <div class="uk-margin">
                            <input id="signUpUsername" class="uk-input" type="text" placeholder="用户名（6~14个字符）"/>
                        </div>
                        <div class="uk-margin">
                            <input id="signUpPhone" class="uk-input" type="text" placeholder="手机号"/>
                        </div>
                        <div class="uk-margin">
                            <input id="signUpNickname" class="uk-input" type="text" placeholder="昵称（1~12个字符）"/>
                        </div>
                        <div class="uk-margin">
                            <input id="signUpPassword" class="uk-input" type="password" placeholder="密码"/>
                        </div>
                        <div class="uk-margin">
                            <input id="signUpPasswordEnsure" class="uk-input" type="password" placeholder="确认密码"/>
                        </div>
                        <button id="signUpBtn" class="uk-form-width-medium uk-button-default uk-button">注册</button>
                        <div class="uk-margin">
                            回到<a id="toSignInLink">登录</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
    <script>
        $('document').ready(function () {

            let imgPathArr = ['../imgs/index/bg1.jpg',
                '../imgs/index/bg2.jpg',
                '../imgs/index/bg3.jpg',
                '../imgs/index/bg4.jpg',
                '../imgs/index/bg5.jpg',
                '../imgs/index/bg6.jpg'];
            let liArr = $('.uk-dotnav li');

            let counter = 0;

            let timer = setInterval(function () {
                changeBgImg(counter);
            }, 5000);

            $.each($('.uk-dotnav li a'), function (index, element) {
                $(this).mouseover(function () {
                    clearInterval(timer);
                    changeBgImg(index - 1);
                    timer = setInterval(function () {
                        changeBgImg(counter);
                    }, 5000);
                });
            });

            function changeBgImg(index) {
                liArr[counter].className = '';
                counter = index;
                counter++;
                if (counter >= imgPathArr.length) {
                    counter = 0;
                }
                $('body').css('background-image', `url(${imgPathArr[counter]})`);
                liArr[counter].className = 'uk-active';
            }

            $('#signInBtn').click(function () {
                let username = $('#signInUsername').val();
                let password = $('#signInPassword').val();
                if (username === '') {
                    notification('未输入用户名！', 'danger');
                }
                else if (password === '') {
                    notification('未输入密码！', 'danger');
                }
                else {
                    $.ajax({
                        type: 'POST',
                        url: 'php/signIn.php',
                        dataType: 'json',
                        data: {
                            'username': username,
                            'password': password
                        },
                        success: function (data) {
                            if (data.code === 200) {
                                window.location.href = 'view/today.php';
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
                return false;
            });

            $('#signUpBtn').click(function (ev) {
                ev.preventDefault();

                let username = $('#signUpUsername').val();
                let phone = $('#signUpPhone').val();
                let nickname = $('#signUpNickname').val();
                let password = $('#signUpPassword').val();
                let passwordEnsure = $('#signUpPasswordEnsure').val();
                if (checkUsername(username) && checkPhone(phone) && checkNickname(nickname) && checkPassword(password, passwordEnsure)) {
                    $.ajax({
                        type: 'POST',
                        url: 'php/signUp.php',
                        dataType: 'json',
                        data: {
                            'username': username,
                            'password': password,
                            'phone': phone,
                            'nickname': nickname
                        },
                        success: function (data) {
                            if (data.code === 200) {
                                notification(data.msg, 'success');
                                setTimeout(function () {
                                    window.location.href = 'view/today.php';
                                }, 500);
                            }
                            else {
                                notification(data.msg, 'danger');
                            }
                        },
                        error: function (error) {
                            notification('网络异常，请检查网络后重试。', 'warning')
                        }
                    });
                }
            });

            $('#signUpForm').hide();

            $('#toSignUpLink').click(function () {
                $('#signInForm').hide(500);
                $('#signUpForm').show(500);
            });

            $('#toSignInLink').click(function () {
                $('#signInForm').show(500);
                $('#signUpForm').hide(500);
            });


        });

    </script>
</html>