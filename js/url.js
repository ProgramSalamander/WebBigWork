const mainUrl = window.location.href.split('/')[0]
    + '/' + window.location.href.split('/')[1]
    + '/' + window.location.href.split('/')[2];

function getDestUrl(dest) {
    return mainUrl + '/' + dest;
}