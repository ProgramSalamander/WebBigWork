<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/11/13
 * Time: 下午9:32
 */

function getHeadPicURL($url){
    return '../database/imgs/headPics/'.$url;
}

function getAlbumURL($url){
    return '../database/imgs/albums/'.$url;
}

function getPhotoURL($username, $albumName, $url){
    return '../database/imgs/albums/'.$username.'/'.$albumName.'/'.$url;
}