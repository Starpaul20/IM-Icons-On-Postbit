<?php
/**
 * IM Icons on Postbit
 * Copyright 2011 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Neat trick for caching our custom template(s)
if(defined('THIS_SCRIPT'))
{
	if(THIS_SCRIPT == 'showthread.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}

	if(THIS_SCRIPT == 'private.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}

	if(THIS_SCRIPT == 'announcements.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}

	if(THIS_SCRIPT == 'newthread.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}

	if(THIS_SCRIPT == 'newreply.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}

	if(THIS_SCRIPT == 'editpost.php')
	{
		global $templatelist;
		if(isset($templatelist))
		{
			$templatelist .= ',';
		}
		$templatelist .= 'postbit_im,postbit_im_icq,postbit_im_skype,postbit_im_google';
	}
}

// Tell MyBB when to run the hooks
$plugins->add_hook("postbit", "imiconspostbit_run");
$plugins->add_hook("postbit_pm", "imiconspostbit_run");
$plugins->add_hook("postbit_announcement", "imiconspostbit_run");
$plugins->add_hook("postbit_prev", "imiconspostbit_run");

// The information that shows up on the plugin manager
function imiconspostbit_info()
{
	global $lang;
	$lang->load("imiconspostbit", true);

	return array(
		"name"				=> $lang->imiconspostbit_info_name,
		"description"		=> $lang->imiconspostbit_info_desc,
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.3",
		"codename"			=> "imiconspostbit",
		"compatibility"		=> "18*"
	);
}

// This function runs when the plugin is activated.
function imiconspostbit_activate()
{
	global $db;
	$insert_array = array(
		'title'		=> 'postbit_im',
		'template'	=> $db->escape_string('<br />{$post[\'im_icq\']}{$post[\'im_skype\']}{$post[\'im_google\']}'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'postbit_im_icq',
		'template'	=> $db->escape_string('<a href="https://www.icq.com/people/{$post[\'icq\']}" target="_blank"><img src="images/im/im_icq.png" alt="{$lang->icq}" title="{$send_via_icq}" /></a>&nbsp;'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'postbit_im_skype',
		'template'	=> $db->escape_string('<a href="{$post[\'profilelink_plain\']}" target="_blank"><img src="images/im/im_skype.png" alt="{$lang->skype}" title="{$send_via_skype}" /></a>&nbsp;'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'postbit_im_google',
		'template'	=> $db->escape_string('<a href="{$post[\'profilelink_plain\']}" target="_blank"><img src="images/im/im_google.png" alt="{$lang->google}" title="{$send_via_google}" /></a>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'im\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'im\']}');
}

// This function runs when the plugin is deactivated.
function imiconspostbit_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('postbit_im','postbit_im_icq','postbit_im_skype','postbit_im_google')");

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'im\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'im\']}')."#i", '', 0);
}

// Add IM Icons on Postbit
function imiconspostbit_run($post)
{
	global $mybb, $lang, $templates;
	$lang->load("imiconspostbit");

	$post['im'] = '';
	if($mybb->usergroup['canviewprofiles'] != 0)
	{
		$post['im_icq'] = $send_via_icq = '';
		if(!empty($post['icq']) && is_member($mybb->settings['allowicqfield'], array('usergroup' => $post['usergroup'], 'additionalgroups' => $post['additionalgroups'])))
		{
			$post['icq'] = (int)$post['icq'];
			$send_via_icq = $lang->sprintf($lang->send_via_icq, $post['username']);
			eval("\$post['im_icq'] = \"".$templates->get("postbit_im_icq")."\";");
		}

		$post['im_skype'] = $send_via_skype = '';
		if(!empty($post['skype']) && is_member($mybb->settings['allowskypefield'], array('usergroup' => $post['usergroup'], 'additionalgroups' => $post['additionalgroups'])))
		{
			$send_via_skype = $lang->sprintf($lang->send_via_skype, $post['username']);
			eval("\$post['im_skype'] = \"".$templates->get("postbit_im_skype")."\";");
		}

		$post['im_google'] = $send_via_google = '';
		if(!empty($post['google']) && is_member($mybb->settings['allowgooglefield'], array('usergroup' => $post['usergroup'], 'additionalgroups' => $post['additionalgroups'])))
		{
			$send_via_google = $lang->sprintf($lang->send_via_google, $post['username']);
			eval("\$post['im_google'] = \"".$templates->get("postbit_im_google")."\";");
		}

		if(!empty($post['im_icq']) || !empty($post['im_skype']) || !empty($post['im_google']))
		{
			eval("\$post['im'] = \"".$templates->get("postbit_im")."\";");
		}
	}

	return $post;
}
