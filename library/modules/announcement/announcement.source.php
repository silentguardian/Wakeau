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

function announcement_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function announcement_list()
{
	global $core, $template;

	$types = array(
		0 => 'Information',
		1 => 'Success',
		2 => 'Warning',
		3 => 'Danger',
	);

	$request = db_query("
		SELECT id_announcement, title, type, state
		FROM announcement
		ORDER BY state DESC, position");
	$template['announcements'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['announcements'][] = array(
			'id' => $row['id_announcement'],
			'title' => $row['title'],
			'type' => $types[$row['type']],
			'state' => $row['state'] ? 'Active' : 'Inactive',
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Announcement List';
	$core['current_template'] = 'announcement_list';
}

function announcement_edit()
{
	global $core, $template;

	$id_announcement = !empty($_REQUEST['announcement']) ? (int) $_REQUEST['announcement'] : 0;
	$is_new = empty($id_announcement);

	if (!empty($_POST['save']))
	{
		check_session('announcement');

		$values = array();
		$fields = array(
			'title' => 'string',
			'body' => 'string',
			'type' => 'int',
			'position' => 'int',
			'state' => 'int',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['title'] === '')
			fatal_error('Announcement title field cannot be empty!');
		elseif ($values['body'] === '')
			fatal_error('Announcement body field cannot be empty!');
		elseif ($values['position'] === 0)
			fatal_error('Announcement position field cannot be empty!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO announcement
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE announcement
				SET " . implode(', ', $update) . "
				WHERE id_announcement = $id_announcement
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('announcement'));

	if ($is_new)
	{
		$template['announcement'] = array(
			'is_new' => true,
			'id' => 0,
			'title' => '',
			'body' => '',
			'type' => 0,
			'position' => 0,
			'state' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT
				id_announcement, title, body,
				type, position, state
			FROM announcement
			WHERE id_announcement = $id_announcement
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['announcement'] = array(
				'is_new' => false,
				'id' => $row['id_announcement'],
				'title' => $row['title'],
				'body' => $row['body'],
				'type' => $row['type'],
				'position' => $row['position'],
				'state' => $row['state'],
			);
		}
		db_free_result($request);

		if (!isset($template['announcement']))
			fatal_error('The announcement requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Announcement';
	$core['current_template'] = 'announcement_edit';
}

function announcement_delete()
{
	$id_announcement = !empty($_REQUEST['announcement']) ? (int) $_REQUEST['announcement'] : 0;

	$request = db_query("
		SELECT id_announcement
		FROM announcement
		WHERE id_announcement = $id_announcement
		LIMIT 1");
	list ($id_announcement) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_announcement))
	{
		db_query("
			DELETE FROM announcement
			WHERE id_announcement = $id_announcement
			LIMIT 1");

		redirect(build_url('announcement'));
	}
	else
		fatal_error('The announcement requested does not exist!');
}