<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/11/13
 * Time: 下午8:11
 */
require_once 'dbHelper.php';
require_once 'encrypter.php';

function checkSignIn()
{
    if (empty($_SESSION['user_info'])) {
        if (empty($_COOKIE['username']) || empty($_COOKIE['password'])) {
            header("location: ../index.php");
        } else {
            $user_info = getUserInfo($_COOKIE['username'], $_COOKIE['password']);
            if (empty($user_info)) {
                header("location: ../index.php");
            } else {
                $_SESSION['user_info'] = $user_info;
            }
        }
    }
}

function getUserInfo($username,
    $password)
{
    $db = getDB();
    $ret = $db->query("SELECT * FROM user WHERE username = '$username' AND password = '$password'");
    return $ret->fetchArray();
}

