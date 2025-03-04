<?php
require_once('../load.php');
$id = $_POST['id'];
$type = $_POST['type'];
global $wpdb;
require_db();

switch($type){
	case 'task':
		$task = $wpdb->get_row("SELECT * FROM tasks WHERE id=$id",ARRAY_A);
		if(!$task) return;
		echo "<div class='title'><span class='task_label'>任务</span>&nbsp;<span id='task_title'>".$task['title']."</span></div>";
		echo "<div style='color:#666666;font-size:12px;margin-top:5px'>".$task['description']."</div>";
		echo "<div class='hr'></div>";
		if(!$task['complete']){
			echo "<div style='margin-top:10px'><img src='../img/loading_tiny.gif' id='loading_2'/><div id='completeAlter'><input id='completed' type='checkbox' value='1' onclick='changeTask()'/><span id='completeFlag'>未完成</span> <span id='lastEdit'></span></div></div>";
		}else{
			echo "<div style='margin-top:10px'><img src='../img/loading_tiny.gif' id='loading_2'/><div id='completeAlter'><input id='completed' type='checkbox' value='1' onclick='changeTask()' checked/><span id='completeFlag' style='color:#009933'>已完成</span> <span id='lastEdit'>".$task['updater']."于".$task['lastTime']."完成</span></div></div>";
		}
		
		echo "<br><br><div class='line'></div><div><div class='item2'>创建：</div>".$task['creator']."于".$task['createTime']."创建</div>";
		echo "<div class='line'></div><div><div class='item2'>负责人：</div>".$task['responser']."</div>";
		echo "<div class='line'></div><div><div class='item2'>截止日期：</div>";
		if(!$task['expiration']){
			echo "未指定";
		}else{
			echo justDate($task['expiration']);
			if(!$task['complete']){			
				$today = strtotime(date("Y-m-d H:i:s"));
				$expiration = strtotime($task['expiration'])+24*3600;
				if($today > $expiration){
					echo "&nbsp;&nbsp;<span style='font-size:12px;color:#990000'><img src='../img/warning3.gif'/><strong>已经滞后".ceil(($today-$expiration)/24/3600)."天，请速处理</strong></span>";
				}else{
					if(ceil(($expiration-$today)/24/3600) == 1)
						echo "&nbsp;&nbsp;<span style='font-size:12px;color:#009933'><strong>在今天内处理</strong></span>";
					else
						echo "&nbsp;&nbsp;<span style='font-size:12px;color:#009933'><strong>还有".ceil(($expiration-$today)/24/3600)."天来处理该任务</strong></span>";
				}
			}else{
				echo "&nbsp;&nbsp;<span style='font-size:12px;color:#009933'><strong>已完成</strong></span>";
			}
		}
		$event = $wpdb->get_var("SELECT title FROM events WHERE id=".$task['event']);
		echo "<div id='parentEvent'><div class='line'></div><div><div class='item2'>事件：</div><a target=_blank href='event.php?&id=".$task['event']."'>".$event."</a></div></div>";
		echo "</div>";		
	break;
	case 'report':
		$report = $wpdb->get_row("SELECT * FROM reports WHERE id=$id",ARRAY_A);
		if(!$report) return;
		echo "<div class='title'><span class='report_label'>评论</span>&nbsp;<span id='task_title'>".$report['title']."</span></div>";
		echo "<div class='hr'></div>";	
		echo "<span class='createTime'>".$report['updater']."于".$report['lastTime']."发表</span>";	
		if($report['content'])
			echo "<br><br><div class='line'></div><div><div class='item2'>内容：</div><div class='content'>".$report['content']."</div></div>";
		else
			echo "<br><br><div class='line'></div><div><div class='item2'>内容：</div>无</div>";		
	break;
	case 'meeting':
		$meeting = $wpdb->get_row("SELECT * FROM meetings WHERE id=$id",ARRAY_A);
		if(!$meeting) return;
		echo "<div class='title'><span class='report_label'>评论</span>&nbsp;<span id='task_title'>".$meeting['title']."</span></div>";
		echo "<div class='hr'></div>";	
		echo "<span class='createTime'>".$meeting['updater']."于".$meeting['lastTime']."存档</span>";	
		if($meeting['content'])
			echo "<br><br><div class='line'></div><div><div class='item2'>会议记录：</div><div class='content'>".$meeting['content']."</div></div>";
		else
			echo "<br><br><div class='line'></div><div><div class='item2'>会议记录：</div>无</div>";		
	break;
	case 'file':
		$file = $wpdb->get_row("SELECT * FROM files WHERE id=$id",ARRAY_A);
		if(!$file) return;
		echo "<div class='title'><span class='report_label'>评论</span>&nbsp;<span id='task_title'>".$file['fileName']."</span></div>";
		echo "<div class='hr'></div>";	
		echo "<span class='createTime'>".$file['updater']."于".$file['lastTime']."上传</span>";	
		if($file['description'])
			echo "<br><br><div class='line'></div><div><div class='item2'>描述：</div><div class='content'>".$file['description']."</div></div>";
		else
			echo "<br><br><div class='line'></div><div><div class='item2'>描述：</div>无</div>";	
		echo "<div class='line'></div><div class='item2'>&nbsp;</div><div class='download'><a target=_blank href='".$ABS_PATH."upload/".$file['linkName']."'>下载</a></div>";	
	break;
} 

echo "<div class='line'></div><br><br>";
echo "<input type=button class=bt value='返回' style='width:100px;margin-left:100px' onclick='hide2();'/><br><br>";

?>