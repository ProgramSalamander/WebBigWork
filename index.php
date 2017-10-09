<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai</title>
        <link rel="stylesheet" type="text/css" href="css/index.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    </head>
    <body>

    <h1 class="animated bounceInDown">嗨！爱拍吗</h1>

    <ul>
        <li>
            <button id="signInBtn" class="signBtn animated fadeInUp">登录</button>
        </li>
        <li>
            <button id="signUpBtn" class="signBtn animated fadeInUp">注册</button>
        </li>
    </ul>

    <img id="previousArrow" src="imgs/previous.png"/>

    <div class="signDiv animated bounceOutDown" id="signInDiv">
        <h2>登录</h2>
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

    <div class="signDiv animated bounceOutDown" id="signUpDiv">
        <h2>注册</h2>
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
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        let title = document.getElementsByTagName('h1')[0];
        let signInBtn = document.getElementById('signInBtn');
        let signUpBtn = document.getElementById('signUpBtn');
        let signInDiv = document.getElementById('signInDiv');
        let signUpDiv = document.getElementById('signUpDiv');
        let previousArrow = document.getElementById('previousArrow');

        let currentDiv = null;

        signInBtn.onclick = function () {
            currentDiv = signInDiv;
            showSignDiv(currentDiv);

        };
        signUpBtn.onclick = function () {
            currentDiv = signUpDiv;
            showSignDiv(currentDiv);
        };

        previousArrow.onclick = function () {
            previousArrow.className = 'animated bounceOutDown';
            currentDiv.className = signInDiv.className.replace('bounceInUp', 'bounceOutDown');

            setTimeout(function () {
                title.className = 'animated bounceInDown';
                signInBtn.className = signInBtn.className.replace('bounceOutUp', 'fadeInUp');
                signUpBtn.className = signUpBtn.className.replace('bounceOutUp', 'fadeInUp');
            }, 500);
        };

        function showSignDiv(div) {
            title.className = 'animated bounceOutUp';
            signInBtn.className = signInBtn.className.replace('fadeInUp', 'bounceOutUp');
            signUpBtn.className = signUpBtn.className.replace('fadeInUp', 'bounceOutUp');

            setTimeout(function () {
                previousArrow.style.top = '5%';
                previousArrow.className = 'animated bounceInUp';
                div.style.top = '20%';
                div.className = div.className.replace('bounceOutDown', 'bounceInUp');
            }, 500);
        }

        $('#signInSubmit').click(function () {
            $.ajax({
                type:'POST',
                url:'php/signIn.php',
                dataType:'json',
                data:{'username':$('#signInUsername').val(),'password':$('#signInPassword').val()},
                success:function(data){
                    console.log(data);
                    alert(data);
                }
            });
        });
    </script>
</html>