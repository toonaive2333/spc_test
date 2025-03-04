<?php
require_once('../load.php');
$id = $_COOKIE["chart_id"];
redirect($ABS_PATH."setup/chartDetail.php?id=$id");
?>