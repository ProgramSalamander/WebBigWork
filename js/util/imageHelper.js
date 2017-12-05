function adapt(img) {
    // img.get(0).onload = function () {
    if (img.get(0).naturalWidth > img.get(0).naturalHeight) {
        img.addClass('photo-long');
    }
    else {
        img.addClass('photo-high');
    }
    // }

}