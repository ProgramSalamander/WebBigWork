/**
 * 校验用户名
 * @param username 输入的用户名
 * @returns {boolean} 是否正确
 */
function checkUsername(username) {
    if (username === '') {
        notification("用户名不能为空!", 'danger');
        return false;
    }
    if (username.length < 6 || username.length > 14) {
        notification("用户名长度需在6~14个字符之间!", 'danger');
        return false;
    }
    return true;
}

/**
 * 校验手机号
 * @param phone 输入的手机号
 * @returns {boolean} 是否合格
 */
function checkPhone(phone) {
    if (phone === '') {
        notification("手机号不能为空!", 'danger');
        return false;
    }
    if (phone.length !== 11) {
        notification("无效的手机号!", 'danger');
        return false;
    }
    return true;
}

/**
 * 校验昵称
 * @param nickname 输入的昵称
 * @returns {boolean} 是否合格
 */
function checkNickname(nickname) {
    if (nickname === '') {
        notification("昵称不能为空!", 'danger');
        return false;
    }
    if (nickname.length < 1 || nickname.length > 12) {
        notification("昵称长度需在1~12个字符之间!", 'danger');
        return false;
    }
    return true;
}

/**
 * 校验密码
 * @param password 输入的密码
 * @param passwordEnsure 输入的确认密码
 * @returns {boolean} 是否合格
 */
function checkPassword(password, passwordEnsure) {
    if (password === '') {
        notification("密码不能为空!", 'danger');
        return false;
    }
    if (!(password === passwordEnsure)) {
        notification("两次输入密码不一致!", 'danger');
        return false;
    }
    return true;
}

/**
 * 校验签名
 * @param sign 输入的签名
 * @returns {boolean} 是否合格
 */
function checkSign(sign) {
    if (sign === '') {
        notification("签名不能为空!", 'danger');
        return false;
    }
    if (sign.length > 30) {
        notification("签名不能超过30个字符!", 'danger');
        return false;
    }
    return true;
}

/**
 * 校验相册名
 * @param albumName 输入的相册名
 * @returns {boolean} 是否合格
 */
function checkAlbumName(albumName) {
    if (albumName === ''){
        notification("相册名不能为空！",'danger');
        return false;
    }
    return true;
}

