<?php
$id = $_POST['id'];
require_once('../load.php');
require_once('event.class.php');
global $wpdb;
require_db();
$event = new Event($id);
$event->setTasks();
$event->setExpiredTasks();
$expiredTasks = $event->getExpiredTasks();
if(count($expiredTasks)>0){
	echo "<div class='content_area'><div class='content_label'>滞后的任务</div><div class='content_list'>";
	foreach($expiredTasks as $task){
		echo "<span class='expiration'>滞后".$task['detail']['expiration']."：</span>";
		echo "<a href='javascript:void(0)' onclick=\"show(".$task['id'].",'task');\">".$task['title']."</a>";
		echo " &nbsp;".$task['detail']['responser']."负责 | &nbsp;".$task['detail']['expireTime']."截止&nbsp;<br>";
	}	
	echo "</div></div>";
}
$event->setReports();
$event->setMeetings();
$event->setFiles();
$mix = $event->getMix();
if(count($mix)>0){
	$newDate = '';
	echo "<div class='content_label'>最近活动项</div><div class='content_list'>";
	foreach($mix as $item){
		if(justDate($item['lastTime']) != $newDate){
			$newDate = justDate($item['lastTime']);
			echo "<div class='date_label'>$newDate</div>";
		}
		switch($item['type']){
			case 'task':
				echo "<div class='item_container'>";
				echo "<div class='type_label'><span class='task_label'>";
				if($item['detail']['complete'] == 1){
					echo "<img src='../img/ok3.gif'/>";
				}
				echo "任务</span></div>";
				echo "<div class='title_container'><div class='title_label'><a href='javascript:void(0)' onclick=\"show(".$item['id'].",'task');\">".$item['title']."</a>&nbsp;&nbsp;".$item['detail']['responser']."</div><div class='updater_label'>".$item['updater']."</div></div>";
				echo "<div class='time_label'><div style='float:left'>";
				if($item['detail']['complete'] == 1){
					echo "完成";
				}else{
					if($item['lastTime'] == $item['detail']['createTime'])
						echo "创建";
					else
						echo "更新";
				}
				echo "</div><div class='updater_label'>".justTime($item['lastTime'])."</div></div>";
				echo "</div>";
			break;
			case 'report':
				echo "<div class='item_container'>";
				echo "<div class='type_label'><span class='report_label'>评论</span></div>";
				echo "<div class='title_container'><div class='title_label'><a href='javascript:void(0)' onclick=\"show(".$item['id'].",'report');\">".$item['title']."</a></div><div class='updater_label'>".$item['updater']."</div></div>";
				echo "<div class='time_label'><div style='float:left'>发表</div><div class='updater_label'>".justTime($item['lastTime'])."</div></div>";
				echo "</div>";
			break;
			case 'meeting':
				echo "<div class='item_container'>";
				echo "<div class='type_label'><span class='meeting_label'>会议记录</span></div>";
				echo "<div class='title_container'><div class='title_label'><a href='javascript:void(0)' onclick=\"show(".$item['id'].",'meeting');\">".$item['title']."</a></div><div class='updater_label'>".$item['updater']."</div></div>";
				echo "<div class='time_label'><div style='float:left'>存档</div><div class='updater_label'>".justTime($item['lastTime'])."</div></div>";
				echo "</div>";
			break;
			case 'file':
				echo "<div class='item_container'>";
				echo "<div class='type_label'><span class='file_label'>文件</span></div>";
				echo "<div class='title_container'><div class='title_label'><a href='javascript:void(0)' onclick=\"show(".$item['id'].",'file');\">".$item['title']."</a></div><div class='updater_label'>".$item['updater']."</div></div>";
				echo "<div class='time_label'><div style='float:left'>上传</div><div class='updater_label'>".justTime($item['lastTime'])."</div></div>";
				echo "</div>";
			break;
		}
	}	
	echo "</div>";
}
?>