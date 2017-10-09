<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/7
 * Time: 下午5:16
 */

$username = $_POST['username'];
$password = $_POST['password'];

echo json_encode('signIn!'.$username.'+'.$password);
