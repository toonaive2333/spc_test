<?php
define( 'ABSPATH', dirname(__FILE__) . '/' );
$destination_path = dirname(ABSPATH)."\\";

$result = 0;
$oName = "0";
$linkName = "0";
$oName = basename( $_FILES['uploader']['name']);
$ext = substr($oName,strrpos($oName,'.')+1);
$linkName = date("ymdHis").".".$ext;
echo $destination_path;
$target_path = $destination_path .'upload/'. $linkName;

if(@move_uploaded_file($_FILES['uploader']['tmp_name'], $target_path)) {
  $result = 1;
}
?>
<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>,"<?php echo $oName; ?>","<?php echo $linkName; ?>");</script>   
