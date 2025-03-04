<?php
ob_start();
require_once( 'load.php' );
setcookie('login_isAdmin','',time()-3600);
setcookie('login_id','',time()-3600);
setcookie('login_name','',time()-3600);

redirect($ABS_PATH.'login.php');
?>