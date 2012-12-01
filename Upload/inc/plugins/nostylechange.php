<?php

/***************************************************************************
 *
 *  Author: Jordan Mussi
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
function nostylechange_info()
{
	global $plugins_cache, $db, $mybb;
	$info = array(
		"name"			=> "Stop Theme Changes",
		"description"	=> "This plugin gets rid of the style option in the usercp. You can also run a task to set all users to view the default theme.",
		"website"		=> "http://mussi.site90.net/jordan",
		"author"		=> "Jordan Mussi",
		"authorsite"	=> "http://mussi.site90.net/jordan",
		"guid"          => "7bb5b41e56b3a3658cc77c9fcd0fbdab",
		"version"		=> "1",
		"compatibility" => "16*"
	);
   if(is_array($plugins_cache) && is_array($plugins_cache['active']) && $plugins_cache['active']['nostylechange'])
    {
		$query = $db->simple_select("tasks", "tid", "file='nostylechange'");
		while($task = $db->fetch_array($query))
		{
		$tid = $task['tid'];
		}
		$query1 = $db->simple_select("tasks", "tid", "file='nostylechange'");
		$info['description'] = "[<i><a href=\"index.php?module=tools-tasks&action=edit&tid=".$tid."\">View Task</a> </i>|<i> <a href=\"index.php?module=tools-tasks&action=run&tid=".$tid."&my_post_key=".$mybb->post_code."\">Run Task</a></i>]<br />".$info['description'];
	}
    return $info;
}
function nostylechange_activate()
{
	global $db;

	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets(
		"usercp_options",
		'#'.preg_quote('<tr>\n<td colspan="2"><span class="smalltext">{$lang->style}</span></td>\n</tr>\n<tr>\n<td colspan="2">{$stylelist}</td>\n</tr>').'#',
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
		"enabled" => '1',
		"logging" => '1'
		);
	$db->insert_query("tasks", $new_task);
	$query = $db->simple_select("tasks", "tid", "file='nostylechange'");
		while($task = $db->fetch_array($query))
		{
		$tid = $task['tid'];
		}
}
function nostylechange_deactivate()
{
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
}
?>