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

function type_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function type_list()
{
	global $core, $template;

	$request = db_query("
		SELECT id_type, name
		FROM type
		ORDER BY position");
	$template['types'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['types'][] = array(
			'id' => $row['id_type'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Type List';
	$core['current_template'] = 'type_list';
}

function type_edit()
{
	global $core, $template;

	$id_type = !empty($_REQUEST['type']) ? (int) $_REQUEST['type'] : 0;
	$is_new = empty($id_type);

	if (!empty($_POST['save']))
	{
		check_session('type');

		$values = array();
		$fields = array(
			'name' => 'string',
			'position' => 'int',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['name'] === '')
			fatal_error('Type name field cannot be empty!');
		elseif ($values['position'] === 0)
			fatal_error('Type position field cannot be empty!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO type
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
				UPDATE type
				SET " . implode(', ', $update) . "
				WHERE id_type = $id_type
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('type'));

	if ($is_new)
	{
		$template['type'] = array(
			'is_new' => true,
			'id' => 0,
			'name' => '',
			'position' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT id_type, name, position
			FROM type
			WHERE id_type = $id_type
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['type'] = array(
				'is_new' => false,
				'id' => $row['id_type'],
				'name' => $row['name'],
				'position' => $row['position'],
			);
		}
		db_free_result($request);

		if (!isset($template['type']))
			fatal_error('The type requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Type';
	$core['current_template'] = 'type_edit';
}

function type_delete()
{
	global $core, $template;

	$id_type = !empty($_REQUEST['type']) ? (int) $_REQUEST['type'] : 0;

	$request = db_query("
		SELECT id_type, name
		FROM type
		WHERE id_type = $id_type
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['type'] = array(
			'id' => $row['id_type'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	if (!isset($template['type']))
		fatal_error('The type requested does not exist!');

	if (!empty($_POST['delete']))
	{
		check_session('type');

		db_query("
			DELETE FROM type
			WHERE id_type = $id_type
			LIMIT 1");

		db_query("
			UPDATE file
			SET id_type = 0
			WHERE id_type = $id_type");
	}

	if (!empty($_POST['delete']) || !empty($_POST['cancel']))
		redirect(build_url('type'));

	$template['page_title'] = 'Delete Type';
	$core['current_template'] = 'type_delete';
}