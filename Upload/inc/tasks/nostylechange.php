<?php
function task_nostylechange($task)
{
	global $db;
	$reset_style = "UPDATE `".TABLE_PREFIX."users` SET `style` = 0";
	$db->query($reset_style);
	add_task_log($task, "All users are now going to view the default theme");
}
?>