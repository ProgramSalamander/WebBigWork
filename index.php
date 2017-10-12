<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai</title>
        <link rel="stylesheet" href="css/uikit.min.css"/>
        <link rel="stylesheet" href="css/index.css"/>

        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/uikit.min.js"></script>
        <script src="js/uikit-icons.min.js"></script>
    </head>
    <body class="uk-background-cover uk-light">
        <div class="uk-position-center">
            <h1 class="uk-text-bold uk-margin-medium-bottom uk-text-center uk-flex-middle uk-animation-slide-top">嗨！爱拍吗</h1>
            <!--            <ul>-->
            <!--                <li>-->
            <!--                    <button id="signInBtn" class="uk-button-primary uk-animation-fade signBtn">登录</button>-->
            <!--                </li>-->
            <!--                <li>-->
            <!--                    <button id="signUpBtn" class="uk-button-primary uk-animation-fade signBtn">注册</button>-->
            <!--                </li>-->
            <!--            </ul>-->
        </div>
        <div class="uk-position-bottom-center uk-position-medium">
            <ul class="uk-dotnav">
                <li class="uk-active"><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
            </ul>
        </div>

        <a id="previousArrow" uk-icon="icon:triangle-up; ratio:3"></a>

        <div class="uk-position-center signDiv" id="signInDiv">
            <h2>登录</h2>
            <hr/>
            <form>
                <ul>
                    <li><label for="signInUsername">用户名</label><input id="signInUsername" type="text"/></li>
                    <li><label for="signInPassword">密码</label><input id="signInPassword" type="password"/></li>
                    <li>
                        <button class="signBtn" id="signInSubmit" type="button">提交</button>
                    </li>
                </ul>
            </form>
        </div>


        <div class="uk-position-center signDiv" id="signUpDiv">
            <h2>注册</h2>
            <hr/>
            <form>
                <ul>
                    <li><label for="signUpUsername">用户名</label><input id="signUpUsername" type="text"/></li>
                    <li id="likePart"><label>喜欢</label><label><input name="signUpRole" id="signUpRoleActive" type="radio"/>拍人</label><label><input
                                    name="signUpRole" id="signUpRolePassive" type="radio"/>被拍</label>
                    </li>
                    <li><label for="signUpPassword">密码</label><input id="signUpPassword" type="password"/></li>
                    <li><label for="signUpEnsurePassword">确认密码</label><input id="signUpEnsurePassword" type="password"/></li>
                    <li>
                        <button class="signBtn" id="signUpSubmit" type="button">提交</button>
                    </li>
                </ul>
            </form>
        </div>

    </body>
    <script src="js/cookie.js"></script>
    <script>
        $('document').ready(function () {
            let imgPathArr = ['../imgs/index/bg1.jpg', '../imgs/index/bg2.jpg', '../imgs/index/bg3.jpg', '../imgs/index/bg4.jpg'];
            let dotNavArr = $('.uk-dotnav li');
            let counter = 1;
            setInterval(function () {
                $('body').css('background-image', `url(${imgPathArr[counter]})`);
                dotNavArr[counter - 1 < 0 ? dotNavArr.length - 1 : counter - 1].className = '';
                dotNavArr[counter].className = 'uk-active';
                if (counter === imgPathArr.length - 1) {
                    counter = 0;
                }
                else {
                    counter++;
                }
            }, 5000);

            $('#signInSubmit').click(function () {
                $.ajax({
                    type: 'POST',
                    url: 'php/signIn.php',
                    dataType: 'json',
                    data: {'username': $('#signInUsername').val(), 'password': $('#signInPassword').val()},
                    success: function (data) {

                    }
                });
            });
        });

    </script>
</html>