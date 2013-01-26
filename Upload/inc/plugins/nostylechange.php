<?php
/***************************************************************************
 *
 *  Author: Jordan Mussi
 *	File:	./inc/plugins/friendly_name.php
 *  
 *  License:
 *  
 *  This program is free software: you can redistribute it and/or modify it under 
 *  the terms of the GNU General Public License as published by the Free Software 
 *  Foundation, either version 3 of the License, or (at your option) any later 
 *  version.
 *  
 *  This program is distributed in the hope that it will be useful, but WITHOUT ANY 
 *  WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 *  FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 *  for more details.
 *  
 ***************************************************************************/
 
if(!defined("IN_MYBB"))
{
	die("Sorry but this awsome file cannot be accessed directly.");
}

$plugins->add_hook("usercp_do_options_start", "nostylechange_run");

function nostylechange_info(){
	global $plugins_cache, $db, $mybb;
	$info = array(
		"name"			=> "Stop Theme Changes",
		"description"	=> "This plugin gets rid of the style option in the usercp. You can also run a task to set all users to view the default theme.",
		"website"		=> "http://mussi.site90.net/jordan",
		"author"		=> "Jordan Mussi",
		"authorsite"	=> "http://mussi.site90.net/jordan",
		"guid"          => "7bb5b41e56b3a3658cc77c9fcd0fbdab",
		"version"		=> "1.1",
		"compatibility" => "16*,18*"
	);
   if(is_array($plugins_cache) && is_array($plugins_cache['active']) && $plugins_cache['active']['nostylechange'])
    {
		require_once(MYBB_ROOT.'inc/plugins/nostylechange/nostylechange.php');
		$tid = $nostylechange['tid'];
		$info['description'] = "[<a href=\"index.php?module=tools-tasks&action=edit&tid=".$tid."\">View Task</a> | <a href=\"index.php?module=tools-tasks&action=run&tid=".$tid."&my_post_key=".$mybb->post_code."\">Run Task</a>]<br />".$info['description'];
	}
    return $info;
}
function nostylechange_activate(){
	global $db;
	
	$reset_style = "UPDATE `".TABLE_PREFIX."users` SET `style` = 0";
	$db->query($reset_style);
	
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets(
		"usercp_options",
		'#'.preg_quote('<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>
</tr>
<tr>
<td colspan="2">{$stylelist}</td>
</tr>').'#',
		'{$nostylechange}'
		);
	// Create task
	$new_task = array(
		"title" => "Use Default Theme",
		"description" => "Sets all users to use the default theme.",
		"file" => "nostylechange",
		"minute" => '*',
		"hour" => '23',
		"day" => '1',
		"month" => '*',
		"weekday" => '*',
		"enabled" => '0',
		"logging" => '1'
		);
	$db->insert_query("tasks", $new_task);
	$query = $db->simple_select("tasks", "tid", "file='nostylechange'");
		while($task = $db->fetch_array($query))
		{
		$tid = $task['tid'];
		}
	$query = $db->simple_select("tasks", "tid", "file='nostylechange'");
	while($task = $db->fetch_array($query))
	{
		$tid = $task['tid'];
	}
	
	if(!is_dir(MYBB_ROOT.'inc/plugins/nostylechange'))
	{
		mkdir(MYBB_ROOT.'inc/plugins/nostylechange');
	}
	
	$data = "<?php
// Auto generated file by the nostylechnage plugin written by JordanMussi
\$nostylechange['tid'] = '".$tid."'
?>";
	$file = fopen(MYBB_ROOT.'inc/plugins/nostylechange/nostylechange.php', 'w');
	fwrite($file, $data);
	fclose($file);
	
	$index_data = '<html>
<head>
<title></title>
</head>
<body>
&nbsp;
</body>
</html>';
	$index_file = fopen(MYBB_ROOT.'inc/plugins/nostylechange/index.html', 'w');
	fwrite($index_file, $index_data);
	fclose($index_file);
}
function nostylechange_deactivate(){
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets(
		"usercp_options",
		'#'.preg_quote('{$nostylechange}').'#',
		'<tr>
<td colspan="2"><span class="smalltext">{$lang->style}</span></td>
</tr>
<tr>
<td colspan="2">{$stylelist}</td>
</tr>'
		);
	$db->delete_query("tasks", "file='nostylechange'");
	
	$file = MYBB_ROOT.'inc/plugins/nostylechange/nostylechange.php';
	unlink($file);
	
	$index_file = MYBB_ROOT.'inc/plugins/nostylechange/index.html';
	unlink($index_file);
	
	rmdir(MYBB_ROOT.'inc/plugins/nostylechange');
}

function nostylechange_run(){
	global $mybb;
	if(intval($mybb->input['style'])){
		error_no_permission();
	}
}
?>
