<?php
/**
 * 加载config.php文件，如果该文件不存在，则要求用户创建。
 */

/** Define ABSPATH as this files directory */
define( 'ABSPATH', dirname(__FILE__) . '/' );
$ABS_PATH = '/freespc/';

error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
//error_reporting(E_USER_NOTICE);

if ( file_exists( ABSPATH . 'config.php') ) {

	/** The config file resides in ABSPATH */
	require_once( ABSPATH . 'config.php' );

} elseif ( file_exists( dirname(ABSPATH) . '/config.php' ) ) {

	/** The config file resides one level below ABSPATH */
	require_once( dirname(ABSPATH) . '/config.php' );

} else {

	// A config file doesn't exist

	// Die with an error message
	require_once( ABSPATH . '/includes/functions.php' );
	tx_die("您可能是第一次运行FreeSPC，或者已经将<code>config.php</code>文件删除，您需要重新配置<code>config.php</code>文件，请点击下方按钮进入配置页面。配置过程只需2步：<br><ul><li>设置数据库</li><li>设置公司或组织名称、管理邮箱</li></ul>FreeSPC安装帮助请参考：<a target=_blank  href=http://freespc.tonxon.com/help/setup.html>http://freespc.tonxon.com/help/setup.html</a><br><br><form method=post action='".$ABS_PATH."admin/setup-config.php'><input type=submit class=bt value='配置' /></a>",'请配置系统文件');
	
}

if ( file_exists( ABSPATH . 'includes/functions.php') ) {
	/** The config file resides in ABSPATH */
	require_once( ABSPATH . 'includes/functions.php' );
	require_once( ABSPATH . 'includes/wp-functions.php' );

} elseif ( file_exists( dirname(ABSPATH) . 'includes//functions.php' ) ) {
	/** The config file resides one level below ABSPATH */
	require_once( dirname(ABSPATH) . 'includes//functions.php' );
	require_once( dirname(ABSPATH) . 'includes//wp-functions.php' );

}

date_default_timezone_set('Asia/Shanghai');

define( 'TYPE_XR', 1 );
define( 'TYPE_XS', 2 );
define( 'TYPE_IMR', 3 );
define( 'TYPE_P', 4 );
define( 'TYPE_NP', 5 );
define( 'TYPE_U', 6 );
define( 'TYPE_C', 7 );

$RULES = array('1个点距离中心线大于3个标准差',
			   '连续9点在中心线同一侧',
			   '连续6个点，全部递增或全部递减',
			   '连续 14个点，上下交错',
			   '3个点中有2个点，距离中心线（同侧）大于2个标准差',
			   '5个点中有4个点，距离中心线（同侧）大于1个标准差',
			   '连续15个点，距离中心线（任一侧）1个标准差以内',
			   '连续8个点，距离中心线（任一侧）大于1个标准差');
$RULES_ENG = array('1 point more than 3 standard deviations from center line',
			   '9 points in a row on same side of center line',
			   '6 points in a row, all increasing or all decreasing',
			   '14 points in a row, alternating up and down',
			   '2 out of 3 points > 2 standard deviations from center line (same side)',
			   '4 out of 5 points > 1 standard deviation from center line (same side)',
			   '15 points in a row within 1 standard deviation of center line (either side)',
			   '8 points in a row > 1 standard deviation from center line (either side)');
			   
$FILE_IMG = array('jpg','gif','png','bmp');	
			   

if( !empty($needAuthenticate) && $needAuthenticate && empty($_COOKIE['login_isAdmin'])){
	redirect($ABS_PATH."login.php");
}
?>
