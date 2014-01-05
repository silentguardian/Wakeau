<?php

/**
 * @package Wakeau
 *
 * @author Selman Eser
 * @copyright 2014 Selman Eser
 * @license BSD 2-clause
 *
 * @version 1.0
 */

if (!defined('CORE'))
	exit();

function home_main()
{
	global $core, $template;

	$request = db_query("
		SELECT COUNT(id_user)
		FROM user
		LIMIT 1");
	list ($template['total_users']) = db_fetch_row($request);
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(id_file), SUM(downloads), SUM(comments)
		FROM file
		LIMIT 1");
	list ($template['total_files'], $template['total_downloads'], $template['total_comments']) = db_fetch_row($request);
	db_free_result($request);

	$template['page_title'] = 'Home';
	$core['current_template'] = 'home_main';
}