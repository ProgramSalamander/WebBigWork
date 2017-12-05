<?php
/**
 * Created by PhpStorm.
 * User: xuyangchen
 * Date: 2017/11/13
 * Time: 下午9:18
 */

unset($_SESSION['user_info']);
setcookie("username", null, time()-3600*24*365, '/');
setcookie("password", null, time()-3600*24*365, '/');
header("location: ../index.php");
?>
<html>
</html>
