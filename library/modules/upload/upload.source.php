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

function upload_main()
{
	global $core, $template, $user;

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
		}
		else
			fatal_error('File has to be uploaded!');

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

		$id_file = db_insert_id();

		recount_stats('file', $values['id_subcategory']);

		redirect(build_url(array('browse', 'view', $id_file)));
	}

	if (!empty($_POST['cancel']))
		redirect(build_url());

	$template['file'] = array(
		'category' => !empty($_REQUEST['category']) ? (int) $_REQUEST['category'] : 0,
		'subcategory' => !empty($_REQUEST['subcategory']) ? (int) $_REQUEST['subcategory'] : 0,
		'type' => !empty($_REQUEST['type']) ? (int) $_REQUEST['type'] : 0,
		'name' => '',
	);

	$request = db_query("
		SELECT id_category, name
		FROM category
		ORDER BY position");
	$template['categories'] = array();
	while ($row = db_fetch_assoc($request))
		$template['categories'][$row['id_category']] = $row['name'];
	db_free_result($request);

	if (empty($template['categories']))
		fatal_error('There are no categories added yet! You cannot add files without categories!');

	$request = db_query("
		SELECT id_subcategory, id_category, name
		FROM subcategory
		ORDER BY position");
	$template['subcategories'] = array();
	while ($row = db_fetch_assoc($request))
		$template['subcategories'][$row['id_category']][$row['id_subcategory']] = $row['name'];
	db_free_result($request);

	if (empty($template['subcategories']))
		fatal_error('There are no subcategories added yet! You cannot add files without subcategories!');

	$request = db_query("
		SELECT id_type, name
		FROM type
		ORDER BY position");
	$template['types'] = array();
	while ($row = db_fetch_assoc($request))
		$template['types'][$row['id_type']] = $row['name'];
	db_free_result($request);

	if (empty($template['types']))
		fatal_error('There are no types added yet! You cannot add files without types!');

	$template['page_title'] = 'Upload File';
	$core['current_template'] = 'upload_main';
}