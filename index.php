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
        <script src="js/cookie.js"></script>
    </head>
    <body class="uk-background-cover uk-position-relative">
        <div class="uk-position-center">
            <h1 class="uk-heading-primary uk-text-bold uk-margin-medium-bottom uk-text-center uk-flex-middle uk-animation-slide-top"> 嗨！爱拍吗</h1>
            <div class="uk-width-auto">
                <div class="uk-position-bottom-center uk-position-relative" style="text-align: center">
                    <button class="uk-button-large uk-button-secondary"> 加入约拍!</button>
                </div>
            </div>
        </div>
        <div class="uk-light uk-position-bottom-center uk-position-medium">
            <ul class="uk-dotnav">
                <li class="uk-active"><a href=""></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
                <li><a></a></li>
            </ul>
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

            let timer = setInterval(changeBgImg, 5000);

            $.each($('.uk-dotnav li a'), function (index, element) {
                $(this).bind('click', function () {
                    clearInterval(timer);
                    changeBgImg(index);
                    timer = setInterval(changeBgImg, 5000);
                });
            });

            function changeBgImg(index) {
                liArr[counter].className = '';
                if(index) {
                    counter = index;
                }
                else{
                    if (counter >= imgPathArr.length - 1) {
                        counter = 0;
                    }
                    else {
                        counter++;
                    }
                }
                $('body').css('background-image', `url(${imgPathArr[counter]})`);
                liArr[counter].className = 'uk-active';

            }


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