function adapt(img) {
    img.get(0).onload = function () {
        if (img.get(0).naturalWidth > img.get(0).naturalHeight) {
            img.removeClass().addClass('photo-long');
        }
        else {
            img.removeClass().addClass('photo-high');
        }
    }

}

function showPreview(source, img, progressBar) {
    let file = source.get(0).files[0];
    if (window.FileReader){
        let fr = new FileReader();
        fr.onloadstart = function (ev) {
            progressBar.start();
        };
        fr.onprogress = function (ev) {
            progressBar.process();
        };
        fr.onloadend = function (ev) {
            progressBar.end();
            img.attr('src',ev.target.result);
        };
        fr.readAsDataURL(file);
    }
}