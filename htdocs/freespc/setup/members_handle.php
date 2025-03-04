<?php
$needAuthenticate = true;
require_once('../load.php');
$_COOKIE['login_isAdmin'] == 2 && redirect($ABS_PATH."login.php");
require_once('../includes/sendmail.php');

$members = $_POST['members'];
if(!empty($_POST['delete']))
 $action = 'delete';
if(!empty($_POST['resend']))
 $action = 'resend';

if(empty($members))
	tx_die("请选择要操作的Email<br><br><br><br><input type=button value='返回'  class='bt' onclick='history.go(-1)'/>");

if($action == 'delete'){
	global $wpdb;
	require_db();
	foreach($members as $member_id){
		$wpdb->query( "DELETE FROM members WHERE id=".$member_id );
	}
	echo "<script>location.href= 'members.php';</script>";
	die;
}

if( $action == 'resend'){
	$email_list = "";
	foreach($members as $member_id)
		$email_list .= $member_id . "|";
?>
<form  id="resend" action="invite.php?resend=resend" method="post" style="display:none">
<input type="text" name="emails" value="<?php echo $email_list?>" />
<input type="submit" />
</form>
<script>
	document.getElementById("resend").submit();
</script>
<?php
}
?>