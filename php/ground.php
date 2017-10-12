<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>iPaiPai - 广场</title>
        <link rel="stylesheet" type="text/css" href="../css/ground.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    </head>
    <body>
        <div class="animated bounceInLeft" id="leftNavigator">
            <div id="personalInfoDiv">
                <ul id="infoList">
                    <li><img src="../imgs/photo3.jpeg" id="headPic"></li>
                    <li><span id="nickName">徐徐徐杨晨</span>，<span id="time">下午</span>好！</li>
                </ul>

                <ul id="entryList">
                    <li class="active"><img src="../imgs/icon_ground.png"/>四处逛逛</li>
                    <li><img src="../imgs/icon_message.png">我的消息</li>
                    <li><img src="../imgs/icon_personalCenter.png">个人主页</li>
                </ul>
                <script>
                    let entryList = document.getElementById('entryList');
                    let entries = entryList.getElementsByTagName('li');

                    let lastEntry = entries[0];

                    for(let i = 0; i < entries.length; i++){
                        entries[i].onclick = function () {
                            lastEntry.className = '';
                            this.className = 'active';
                            lastEntry = this;
                        }
                    }
                </script>
            </div>
        </div>
    </body>
</html>