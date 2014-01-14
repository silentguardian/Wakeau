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

	$request = db_query("
		SELECT id_file, name, downloads
		FROM file
		WHERE downloads > 0
		ORDER BY downloads DESC
		LIMIT 5");
	$template['popular_files'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['popular_files'][] = array(
			'name' => $row['name'],
			'href' => build_url(array('browse', 'view', $row['id_file'])),
			'downloads' => $row['downloads'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT id_file, name, time
		FROM file
		ORDER BY time DESC
		LIMIT 5");
	$template['recent_files'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['recent_files'][] = array(
			'name' => $row['name'],
			'href' => build_url(array('browse', 'view', $row['id_file'])),
			'time' => format_time($row['time']),
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(f.id_file) AS files, t.name
		FROM file AS f
			INNER JOIN type AS t ON (t.id_type = f.id_type)
		GROUP BY f.id_type
		ORDER BY files DESC
		LIMIT 5");
	$template['popular_types'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['popular_types'][] = array(
			'name' => $row['name'],
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT COUNT(f.id_file) AS files, u.username
		FROM file AS f
			INNER JOIN user AS u ON (u.id_user = f.id_user)
		GROUP BY f.id_user
		ORDER BY files DESC
		LIMIT 5");
	$template['generous_users'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['generous_users'][] = array(
			'username' => $row['username'],
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT id_category, name, files
		FROM category
		WHERE files > 0
		ORDER BY files DESC
		LIMIT 5");
	$template['popular_categories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['popular_categories'][] = array(
			'name' => $row['name'],
			'href' => build_url(array('browse', 'category', $row['id_category'])),
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT id_subcategory, name, files
		FROM subcategory
		WHERE files > 0
		ORDER BY files DESC
		LIMIT 5");
	$template['popular_subcategories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['popular_subcategories'][] = array(
			'name' => $row['name'],
			'href' => build_url(array('browse', 'subcategory', $row['id_subcategory'])),
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Home';
	$core['current_template'] = 'home_main';
}