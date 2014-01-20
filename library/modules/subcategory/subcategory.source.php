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

function subcategory_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function subcategory_list()
{
	global $core, $template;

	$request = db_query("
		SELECT s.id_subcategory, s.name, s.files, c.name AS category
		FROM subcategory AS s
			LEFT JOIN category AS c ON (c.id_category = s.id_category)
		ORDER BY c.position, s.position");
	$template['subcategories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['subcategories'][] = array(
			'id' => $row['id_subcategory'],
			'name' => $row['name'],
			'category' => $row['category'],
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Subcategory List';
	$core['current_template'] = 'subcategory_list';
}

function subcategory_edit()
{
	global $core, $template;

	$id_subcategory = !empty($_REQUEST['subcategory']) ? (int) $_REQUEST['subcategory'] : 0;
	$is_new = empty($id_subcategory);

	if ($is_new)
	{
		$template['subcategory'] = array(
			'is_new' => true,
			'id' => 0,
			'category' => 0,
			'name' => '',
			'position' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT id_subcategory, id_category, name, position
			FROM subcategory
			WHERE id_subcategory = $id_subcategory
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['subcategory'] = array(
				'is_new' => false,
				'id' => $row['id_subcategory'],
				'category' => $row['id_category'],
				'name' => $row['name'],
				'position' => $row['position'],
			);
		}
		db_free_result($request);

		if (!isset($template['subcategory']))
			fatal_error('The subcategory requested does not exist!');
	}

	if (!empty($_POST['save']))
	{
		check_session('subcategory');

		$values = array();
		$fields = array(
			'id_category' => 'int',
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
			fatal_error('Subcategory name field cannot be empty!');
		elseif ($values['position'] === 0)
			fatal_error('Subcategory position field cannot be empty!');
		elseif ($values['id_category'] === 0)
			fatal_error('Subcategory category field cannot be empty!');

		if ($is_new)
		{
			$insert = array();
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO subcategory
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");

			recount_stats('subcategory', $values['id_category']);
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE subcategory
				SET " . implode(', ', $update) . "
				WHERE id_subcategory = $id_subcategory
				LIMIT 1");

			if ($template['subcategory']['category'] != $values['id_category'])
			{
				db_query("
					UPDATE file
					SET id_category = $values[id_category]
					WHERE id_subcategory = $id_subcategory");

				recount_stats('subcategory', array($template['subcategory']['category'], $values['id_category']));
				recount_stats('category', array($template['subcategory']['category'], $values['id_category']));
			}
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('subcategory'));

	$request = db_query("
		SELECT id_category, name
		FROM category
		ORDER BY position");
	$template['categories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['categories'][] = array(
			'id' => $row['id_category'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	if (empty($template['categories']))
		fatal_error('There are no categories added yet! You cannot add subcategories without categories!');

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' Subcategory';
	$core['current_template'] = 'subcategory_edit';
}

function subcategory_delete()
{
	global $core, $template;

	$id_subcategory = !empty($_REQUEST['subcategory']) ? (int) $_REQUEST['subcategory'] : 0;

	$request = db_query("
		SELECT id_subcategory, id_category, name
		FROM subcategory
		WHERE id_subcategory = $id_subcategory
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$id_category = $row['id_category'];

		$template['subcategory'] = array(
			'id' => $row['id_subcategory'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	if (!isset($template['subcategory']))
		fatal_error('The subcategory requested does not exist!');

	if (!empty($_POST['delete']))
	{
		check_session('subcategory');

		db_query("
			DELETE FROM subcategory
			WHERE id_subcategory = $id_subcategory
			LIMIT 1");

		db_query("
			UPDATE file
			SET id_subcategory = 0
			WHERE id_subcategory = $id_subcategory");

		recount_stats('subcategory', $id_category);

		redirect(build_url('subcategory'));
	}

	if (!empty($_POST['delete']) || !empty($_POST['cancel']))
		redirect(build_url('subcategory'));

	$template['page_title'] = 'Delete Subcategory';
	$core['current_template'] = 'subcategory_delete';
}