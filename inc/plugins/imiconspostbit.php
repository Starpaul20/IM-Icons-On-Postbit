<?php
/**
 * IM icons on Postbit
 * Copyright 2011 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Neat trick for caching our custom template(s)
if(my_strpos($_SERVER['PHP_SELF'], 'showthread.php'))
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_im';
}

// Tell MyBB when to run the hooks
$plugins->add_hook("postbit", "imiconspostbit_run");
$plugins->add_hook("postbit_pm", "imiconspostbit_run");
$plugins->add_hook("postbit_announcement", "imiconspostbit_run");
$plugins->add_hook("postbit_prev", "imiconspostbit_run");

// The information that shows up on the plugin manager
function imiconspostbit_info()
{
	return array(
		"name"				=> "IM Icons on Postbit",
		"description"		=> "Adds instant messaging icons on the postbit.",
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.0.1",
		"guid"				=> "085c4b899611342ec32f40d98ddaca8f",
		"compatibility"		=> "16*"
	);
}

// This function runs when the plugin is activated.
function imiconspostbit_activate()
{
	global $db;
	$insert_array = array(
		'title'		=> 'postbit_im',
		'template'	=> $db->escape_string('<br />{$post[\'im_icq\']}{$post[\'im_aim\']}{$post[\'im_yahoo\']}{$post[\'im_msn\']}'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'im\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'im\']}');
}

// This function runs when the plugin is deactivated.
function imiconspostbit_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('postbit_im')");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'im\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'im\']}')."#i", '', 0);
}

// Add IM Icons on Postbit
function imiconspostbit_run($post)
{
	global $db, $mybb, $lang, $templates;
	$lang->load("imiconspostbit");

	if($mybb->usergroup['canviewprofiles'] != 0)
	{
		if($post['icq'])
		{
			$send_via_icq = $lang->sprintf($lang->send_via_icq, $post['username']);
			$post['im_icq'] = "<a href=\"javascript:MyBB.popupWindow('http://www.icq.com/people/{$post['icq']}', 'icqwindow', '650', '500')\"><img src=\"images/im/im_icq.png\" alt=\"{$lang->icq}\" title=\"{$send_via_icq}\" /></a>&nbsp;"; 
		}
		else
		{	
			$post['im_icq'] == "";	
		}

		if($post['aim'])
		{
			$send_via_aim = $lang->sprintf($lang->send_via_aim, $post['username']);
			$post['im_aim'] = "<a href=\"javascript:MyBB.popupWindow('misc.php?action=imcenter&imtype=aim&uid={$post['uid']}', 'imcenter', '450', '300')\"><img src=\"images/im/im_aim.png\" alt=\"{$lang->aim}\" title=\"{$send_via_aim}\" /></a>&nbsp;"; 
		}
		else
		{	
			$post['im_aim'] == "";	
		}

		if($post['yahoo'])
		{
			$send_via_yahoo = $lang->sprintf($lang->send_via_yahoo, $post['username']);
			$post['im_yahoo'] = "<a href=\"javascript:MyBB.popupWindow('misc.php?action=imcenter&imtype=yahoo&uid={$post['uid']}', 'imcenter', '450', '300')\"><img src=\"images/im/im_yahoo.png\" alt=\"{$lang->yahoo}\" title=\"{$send_via_yahoo}\" /></a>&nbsp;";
		}
		else
		{	
			$post['im_yahoo'] == "";	
		}

		if($post['msn'])
		{
			$send_via_msn = $lang->sprintf($lang->send_via_msn, $post['username']);
			$post['im_msn'] = "<a href=\"javascript:MyBB.popupWindow('misc.php?action=imcenter&imtype=msn&uid={$post['uid']}', 'imcenter', '450', '300')\"><img src=\"images/im/im_msn.png\" alt=\"{$lang->msn}\" title=\"{$send_via_msn}\" /></a>";
		}
		else
		{	
			$post['im_msn'] == "";	
		}

		eval("\$post['im'] = \"".$templates->get("postbit_im")."\";");
	}
	else
	{
		$post['im'] = "";
	}

	return $post;
}

?>