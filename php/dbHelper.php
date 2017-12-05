<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/10/20
 * Time: 上午10:19
 */
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('../database/ipaipai.db');
    }

    function __destruct()
    {
        $this->close();
    }
}

function getDB(){
    $db = new MyDB();

    if (!$db){
        throw new Exception($db->lastErrorMsg());
    }
    return $db;
}
