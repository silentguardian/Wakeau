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

function file_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function file_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			f.id_file, f.name, f.time, c.name AS category,
			s.name AS subcategory, t.name AS type, u.username
		FROM file AS f
			LEFT JOIN category AS c ON (c.id_category = f.id_category)
			LEFT JOIN subcategory AS s ON (s.id_subcategory = f.id_subcategory)
			LEFT JOIN type AS t ON (t.id_type = f.id_type)
			LEFT JOIN user AS u ON (u.id_user = f.id_user)
		ORDER BY f.id_file DESC");
	$template['files'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['files'][] = array(
			'id' => $row['id_file'],
			'name' => $row['name'],
			'category' => $row['category'],
			'subcategory' => $row['subcategory'],
			'type' => $row['type'],
			'user' => $row['username'],
			'time' => strftime('%d/%m/%Y, %H:%M', $row['time']),
		);
	}
	db_free_result($request);

	$template['page_title'] = 'File List';
	$core['current_template'] = 'file_list';
}

function file_edit()
{
	global $core, $template, $user;

	$id_file = !empty($_REQUEST['file']) ? (int) $_REQUEST['file'] : 0;
	$is_new = empty($id_file);

	if ($is_new)
	{
		$template['file'] = array(
			'is_new' => true,
			'id' => 0,
			'category' => 0,
			'subcategory' => 0,
			'type' => 0,
			'name' => '',
		);
	}
	else
	{
		$request = db_query("
			SELECT
				f.id_file, f.id_category, f.id_subcategory,
				f.id_type, f.id_user, f.name, f.id_store,
				m.name AS store_name, m.alias
			FROM file AS f
				LEFT JOIN store AS m ON (m.id_store = f.id_store)
			WHERE f.id_file = $id_file
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['file'] = array(
				'is_new' => false,
				'id' => $row['id_file'],
				'category' => $row['id_category'],
				'subcategory' => $row['id_subcategory'],
				'type' => $row['id_type'],
				'name' => $row['name'],
				'store' => array(
					'id' => $row['id_store'],
					'name' => $row['store_name'],
					'alias' => $row['alias'],
				),
			);
		}
		db_free_result($request);

		if (!isset($template['file']))
			fatal_error('The file requested does not exist!');
	}

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'id_category' => 'int',
			'id_subcategory' => 'int',
			'id_type' => 'int',
			'name' => 'string',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'string')
				$values[$field] = !empty($_POST[$field]) ? htmlspecialchars($_POST[$field], ENT_QUOTES) : '';
			elseif ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['name'] === '')
			fatal_error('File name field cannot be empty!');
		elseif ($values['id_category'] === 0)
			fatal_error('File category field cannot be empty!');
		elseif ($values['id_subcategory'] === 0)
			fatal_error('File subcategory field cannot be empty!');
		elseif ($values['id_type'] === 0)
			fatal_error('File type field cannot be empty!');

		if (!empty($_FILES['store']) && !empty($_FILES['store']['name']))
		{
			$store_name = htmlspecialchars($_FILES['store']['name'], ENT_QUOTES);
			$store_alias = substr(md5(session_id() . mt_rand() . (string) microtime()), 0, 14);
			$store_size = (int) $_FILES['store']['size'];
			$store_extension = htmlspecialchars(strtolower(substr(strrchr($_FILES['store']['name'], '.'), 1)), ENT_QUOTES);

			if (!is_uploaded_file($_FILES['store']['tmp_name']) || (@ini_get('open_basedir') == '' && !file_exists($_FILES['store']['tmp_name'])))
				fatal_error('File could not be uploaded!');

			if ($store_size > $core['storage_size'] * 1024 * 1024)
				fatal_error('Files cannot be larger than ' . $core['storage_size'] . ' MB!');

			if (!in_array($store_extension, $core['storage_extension']))
				fatal_error('Only files with the following extensions can be uploaded: ' . implode(', ', $core['storage_extension']));

			if (!move_uploaded_file($_FILES['store']['tmp_name'], $core['storage_dir'] . '/' . $store_alias . '.w'))
				fatal_error('File could not be uploaded!');

			db_query("
				INSERT INTO store
					(name, alias, size, extension)
				VALUES
					('$store_name', '$store_alias', $store_size, '$store_extension')");

			if (!($id_store = db_insert_id()))
				fatal_error('File could not be uploaded!');
			else
				$values['id_store'] = $id_store;

			if (!empty($template['file']['store']['id']))
			{
				db_query("
					DELETE FROM store
					WHERE id_store = {$template['file']['store']['id']}
					LIMIT 1");

				@unlink($core['storage_dir'] . '/' . $template['file']['store']['alias'] . '.w');
			}
		}
		elseif ($is_new)
			fatal_error('File has to be uploaded!');

		if ($is_new)
		{
			$insert = array(
				'id_user' => $user['id'],
				'time' => time(),
			);
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO file
					(" . implode(', ', array_keys($insert)) . ")
				VALUES
					(" . implode(', ', $insert) . ")");

			recount_stats('file', $values['id_subcategory']);
		}
		else
		{
			$update = array();
			foreach ($values as $field => $value)
				$update[] = $field . " = '" . $value . "'";

			db_query("
				UPDATE file
				SET " . implode(', ', $update) . "
				WHERE id_file = $id_file
				LIMIT 1");

			if ($template['file']['subcategory'] != $values['id_subcategory'])
				recount_stats('file', array($template['file']['subcategory'], $values['id_subcategory']));
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('file'));

	$request = db_query("
		SELECT id_category, name
		FROM category
		ORDER BY position");
	$template['categories'] = array();
	while ($row = db_fetch_assoc($request))
		$template['categories'][$row['id_category']] = $row['name'];
	db_free_result($request);

	if (empty($template['categories']))
		fatal_error('There are no categories added yet! You cannot manage files without categories!');

	$request = db_query("
		SELECT id_subcategory, id_category, name
		FROM subcategory
		ORDER BY position");
	$template['subcategories'] = array();
	while ($row = db_fetch_assoc($request))
		$template['subcategories'][$row['id_category']][$row['id_subcategory']] = $row['name'];
	db_free_result($request);

	if (empty($template['subcategories']))
		fatal_error('There are no subcategories added yet! You cannot manage files without subcategories!');

	$request = db_query("
		SELECT id_type, name
		FROM type
		ORDER BY position");
	$template['types'] = array();
	while ($row = db_fetch_assoc($request))
		$template['types'][$row['id_type']] = $row['name'];
	db_free_result($request);

	if (empty($template['types']))
		fatal_error('There are no types added yet! You cannot manage files without types!');

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' File';
	$core['current_template'] = 'file_edit';
}

function file_delete()
{
	global $core;

	$id_file = !empty($_REQUEST['file']) ? (int) $_REQUEST['file'] : 0;

	$request = db_query("
		SELECT id_file, id_subcategory, id_store
		FROM file
		WHERE id_file = $id_file
		LIMIT 1");
	list ($id_file, $id_subcategory, $id_store) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_file))
	{
		$request = db_query("
			SELECT alias
			FROM store
			WHERE id_store = $id_store
			LIMIT 1");
		list ($alias) = db_fetch_row($request);
		db_free_result($request);

		db_query("
			DELETE FROM store
			WHERE id_store = $id_store
			LIMIT 1");

		@unlink($core['storage_dir'] . '/' . $alias . '.w');

		db_query("
			DELETE FROM comment
			WHERE id_file = $id_file");

		db_query("
			DELETE FROM file
			WHERE id_file = $id_file
			LIMIT 1");

		recount_stats('file', $id_subcategory);

		redirect(build_url('file'));
	}
	else
		fatal_error('The file requested does not exist!');
}