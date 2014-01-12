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

function browse_main()
{
	global $core;

	$actions = array('category', 'subcategory', 'file', 'view', 'comment', 'download', 'edit', 'delete');

	$core['current_action'] = 'category';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function browse_category()
{
	global $core, $template;

	$request = db_query("
		SELECT
			id_category, name,
			subcategories, files
		FROM category
		ORDER BY position");
	$template['categories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['categories'][] = array(
			'id' => $row['id_category'],
			'name' => $row['name'],
			'subcategories' => $row['subcategories'],
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Browse Categories';
	$core['current_template'] = 'browse_category';
}

function browse_subcategory()
{
	global $core, $template;

	$id_category = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

	$request = db_query("
		SELECT id_category, name
		FROM category
		WHERE id_category = $id_category
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['category'] = array(
			'id' => $row['id_category'],
			'name' => $row['name'],
		);
	}
	db_free_result($request);

	if (!isset($template['category']))
		fatal_error('The category requested does not exist!');

	$request = db_query("
		SELECT id_subcategory, name, files
		FROM subcategory
		WHERE id_category = $id_category
		ORDER BY position");
	$template['subcategories'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['subcategories'][] = array(
			'id' => $row['id_subcategory'],
			'name' => $row['name'],
			'files' => $row['files'],
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Browse Subcategories - ' . $template['category']['name'];
	$core['current_template'] = 'browse_subcategory';
}

function browse_file()
{
	global $core, $template;

	$id_subcategory = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

	$request = db_query("
		SELECT
			s.id_subcategory, s.id_category,
			s.name AS subcategory_name, c.name AS category_name
		FROM subcategory AS s
			INNER JOIN category AS c ON (c.id_category = s.id_category)
		WHERE s.id_subcategory = $id_subcategory
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['subcategory'] = array(
			'id' => $row['id_subcategory'],
			'name' => $row['subcategory_name'],
		);
		$template['category'] = array(
			'id' => $row['id_category'],
			'name' => $row['category_name'],
		);
	}
	db_free_result($request);

	if (!isset($template['subcategory']))
		fatal_error('The subcategory requested does not exist!');

	$request = db_query("
		SELECT
			f.id_file, f.name, f.downloads, f.comments,
			f.time, t.name AS type, u.username
		FROM file AS f
			INNER JOIN type AS t ON (t.id_type = f.id_type)
			INNER JOIN user AS u ON (u.id_user = f.id_user)
		WHERE f.id_subcategory = $id_subcategory
		ORDER BY t.position, f.name");
	$template['files'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['files'][] = array(
			'id' => $row['id_file'],
			'name' => $row['name'],
			'type' => $row['type'],
			'user' => $row['username'],
			'downloads' => $row['downloads'],
			'comments' => $row['comments'],
			'time' => strftime('%d/%m/%Y, %H:%M', $row['time']),
		);
	}
	db_free_result($request);

	$template['page_title'] = 'Browse Files - ' . $template['category']['name'] . ' - ' . $template['subcategory']['name'];
	$core['current_template'] = 'browse_file';
}

function browse_view()
{
	global $core, $template, $user;

	$id_file = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

	$request = db_query("
		SELECT
			f.id_file, f.name, f.time, f.downloads, f.comments, f.id_user, u.username,
			c.name AS category, f.id_subcategory, s.name AS subcategory, t.name AS type,
			m.name AS store_name, m.size
		FROM file AS f
			LEFT JOIN category AS c ON (c.id_category = f.id_category)
			LEFT JOIN subcategory AS s ON (s.id_subcategory = f.id_subcategory)
			LEFT JOIN type AS t ON (t.id_type = f.id_type)
			LEFT JOIN user AS u ON (u.id_user = f.id_user)
			LEFT JOIN store AS m ON (m.id_store = f.id_store)
		WHERE f.id_file = $id_file
		LIMIT 1");
	$template['file'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['file'] = array(
			'id' => $row['id_file'],
			'name' => $row['name'],
			'category' => $row['category'],
			'subcategory' => array(
				'id' => $row['id_subcategory'],
				'name' => $row['subcategory'],
			),
			'type' => $row['type'],
			'user' => array(
				'id' => $row['id_user'],
				'username' => $row['username'],
			),
			'downloads' => $row['downloads'],
			'comments' => $row['comments'],
			'time' => strftime('%d %B %Y, %H:%M', $row['time']),
			'store' => array(
				'name' => $row['store_name'],
				'size' => round($row['size'] / 1024, 2) . ' KB',
			)
		);
	}
	db_free_result($request);

	if (empty($template['file']))
		fatal_error('The file requested does not exist!');

	$request = db_query("
		SELECT c.id_comment, c.id_user, c.body, c.time, u.username
		FROM comment AS c
			INNER JOIN user AS u ON (u.id_user = c.id_user)
		WHERE c.id_file = $id_file
		ORDER BY c.id_comment DESC");
	$template['comments'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['comments'][] = array(
			'id' => $row['id_comment'],
			'username' => $row['username'],
			'body' => $row['body'],
			'time' => strftime('%d %B %Y, %H:%M', $row['time']),
			'can_delete' => $user['admin'] || ($user['id'] == $row['id_user']),
		);
	}
	db_free_result($request);

	$template['can_manage'] = $user['admin'] || ($user['id'] == $template['file']['user']['id']);
	$template['can_comment'] = true;
	$template['page_title'] = 'View File - ' . $template['file']['name'];
	$core['current_template'] = 'browse_view';
}

function browse_comment()
{
	global $user;

	$id_file = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;
	$id_comment = !empty($_GET['comment']) ? (int) $_GET['comment'] : 0;
	$body = !empty($_POST['body']) ? htmlspecialchars($_POST['body'], ENT_QUOTES) : '';

	$request = db_query("
		SELECT id_file
		FROM file
		WHERE id_file = $id_file
		LIMIT 1");
	list ($id_file) = db_fetch_row($request);
	db_free_result($request);

	if (empty($id_file))
		fatal_error('The game requested does not exist!');

	if (!empty($body))
	{
		db_query("
			INSERT INTO comment
				(id_file, id_user, body, time)
			VALUES
				($id_file, $user[id], '$body', " . time() . ")");

		db_query("
			UPDATE file
			SET comments = comments + 1
			WHERE id_file = $id_file
			LIMIT 1");
	}
	elseif (!empty($id_comment))
	{
		$request = db_query("
			SELECT id_comment, id_user
			FROM comment
			WHERE id_comment = $id_comment
				AND id_file = $id_file
			LIMIT 1");
		list ($id_comment, $id_user) = db_fetch_row($request);
		db_free_result($request);

		if (empty($id_comment))
			fatal_error('The comment requested does not exist!');
		elseif (!$user['admin'] && $user['id'] != $id_user)
			fatal_error('You are not allowed to carry out this action!');

		db_query("
			DELETE FROM comment
			WHERE id_comment = $id_comment
			LIMIT 1");

		db_query("
			UPDATE file
			SET comments = comments - 1
			WHERE id_file = $id_file
			LIMIT 1");
	}

	redirect(build_url(array('browse', 'view', $id_file)));
}

function browse_download()
{
	global $core;

	$id_file = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

	$request = db_query("
		SELECT f.id_file, m.name, m.alias, m.size
		FROM file AS f
			LEFT JOIN store AS m ON (m.id_store = f.id_store)
		WHERE f.id_file = $id_file
		LIMIT 1");
	list ($id_file, $name, $alias, $size) = db_fetch_row($request);
	db_free_result($request);

	if (empty($id_file))
		fatal_error('The file requested does not exist!');

	if (empty($alias) || !file_exists($core['storage_dir'] . '/' . $alias . '.w'))
		fatal_error('File does not exist!');

	db_query("
		UPDATE LOW_PRIORITY file
		SET downloads = downloads + 1
		WHERE id_file = $id_file
		LIMIT 1");

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . htmlspecialchars_decode($name, ENT_QUOTES));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . $size);

	ob_clean();
	flush();
	readfile($core['storage_dir'] . '/' . $alias . '.w');

	exit();
}

function browse_edit()
{
	global $core, $template, $user;

	$id_file = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

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
			'user' => $row['id_user'],
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
	elseif (!$user['admin'] && $user['id'] != $template['file']['user'])
		fatal_error('You are not allowed to carry out this action!');

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

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url(array('browse', 'view', $id_file)));

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

	$template['page_title'] = 'Edit File';
	$core['current_template'] = 'browse_edit';
}

function browse_delete()
{
	global $core, $user;

	$id_file = !empty($_REQUEST['browse']) ? (int) $_REQUEST['browse'] : 0;

	$request = db_query("
		SELECT id_file, id_user, id_subcategory, id_store
		FROM file
		WHERE id_file = $id_file
		LIMIT 1");
	list ($id_file, $id_user, $id_subcategory, $id_store) = db_fetch_row($request);
	db_free_result($request);

	if (empty($id_file))
		fatal_error('The file requested does not exist!');
	elseif (!$user['admin'] && $user['id'] != $id_user)
		fatal_error('You are not allowed to carry out this action!');

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

	redirect(build_url(array('browse', 'file', $id_subcategory)));
}