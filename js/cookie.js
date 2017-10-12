/**
 * 添加Cookie
 * @param key 键
 * @param value 值
 * @param t 过期时间
 */
function setCookie(key,value,t) {
    let oDate = new Date();
    oDate.setDate(oDate.getDate() + t);
    document.cookie = key + '=' + encodeURI(value) + ';expires=' + oDate.toUTCString();
}

/**
 * 获取Cookie
 * @param key 键
 * @returns {string} 值
 */
function getCookie(key) {
    let arr1 = document.cookie.split('; ');
    for (let i = 0; i < arr1.length; i++){
        let arr2 = arr1[1].split('=');
        if(arr2[0] === key){
            return decodeURI(arr2[1]);
        }
    }
}

/**
 * 删除Cookie
 * @param key 键
 */
function removeCookie(key) {
    setCookie(key, '', -1);
}