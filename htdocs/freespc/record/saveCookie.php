<?php
if($_COOKIE['login_isAdmin'] == 2){
	$cookieName = 'recordOrder_'.$_COOKIE['login_id'];
}elseif($_COOKIE['login_isAdmin'] == 1){
	$cookieName = 'recordOrder_admin';
}
if(!empty($cookieName) && !empty($_POST['order']))
	setcookie($cookieName,$_POST['order'],time()+3600*24*30);
	
if(!empty($_POST['manual']))
	setcookie("manual",$_POST['manual'],time()+3600*24*30);

?>