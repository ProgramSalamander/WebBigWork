<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/24
 * Time: 下午9:08
 */

function encrypt($origin){
    return md5(md5($origin).md5($origin));
}