window.onload = function () {
    var imgs = document.getElementsByTagName('img');
    for(var i = 0 ; i < imgs.length; i++){
        imgs[i].onclick = function () {
            console.log(i);
        };
    }
};