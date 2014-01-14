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

function user_main()
{
	global $core;

	$actions = array('list', 'edit', 'delete');

	$core['current_action'] = 'list';
	if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $actions))
		$core['current_action'] = $_REQUEST['action'];

	call_user_func($core['current_module'] . '_' . $core['current_action']);
}

function user_list()
{
	global $core, $template;

	$request = db_query("
		SELECT
			id_user, username, email_address,
			registered, admin
		FROM user
		ORDER BY id_user");
	$template['users'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['users'][] = array(
			'id' => $row['id_user'],
			'username' => $row['username'],
			'email_address' => $row['email_address'],
			'registered' => format_time($row['registered']),
			'admin' => $row['admin'] ? 'Yes' : 'No',
		);
	}
	db_free_result($request);

	$template['page_title'] = 'User List';
	$core['current_template'] = 'user_list';
}

function user_edit()
{
	global $core, $template;

	$id_user = !empty($_REQUEST['user']) ? (int) $_REQUEST['user'] : 0;
	$is_new = empty($id_user);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'username' => 'username',
			'email_address' => 'email',
			'password' => 'password',
			'verify_password' => 'password',
			'admin' => 'int',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'password')
				$values[$field] = !empty($_POST[$field]) ? sha1($_POST[$field]) : '';
			elseif ($type === 'username')
				$values[$field] = !empty($_POST[$field]) && !preg_match('~[^A-Za-z0-9\._]~', $_POST[$field]) ? $_POST[$field] : '';
			elseif ($type === 'email')
				$values[$field] = !empty($_POST[$field]) && preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', $_POST[$field]) ? $_POST[$field] : '';
			elseif ($type === 'int')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		if ($values['username'] === '')
			fatal_error('You did not enter a valid username!');

		$request = db_query("
			SELECT id_user
			FROM user
			WHERE username = '$values[username]'
				AND id_user != $id_user
			LIMIT 1");
		list ($duplicate_id) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate_id))
			fatal_error('The username entered is already in use!');

		if ($values['email_address'] === '')
			fatal_error('You did not enter a valid email address!');

		$request = db_query("
			SELECT id_user
			FROM user
			WHERE email_address = '$values[email_address]'
				AND id_user != $id_user
			LIMIT 1");
		list ($duplicate_id) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate_id))
			fatal_error('The email address entered is already in use!');

		if ($values['password'] === '' && $is_new)
			fatal_error('You did not enter a valid password!');
		elseif ($values['password'] === '')
			unset($values['password'], $values['verify_password']);
		elseif ($values['password'] !== $values['verify_password'])
			fatal_error('The passwords entered do not match!');
		else
			unset($values['verify_password']);

		if ($is_new)
		{
			$insert = array('registered' => time());
			foreach ($values as $field => $value)
				$insert[$field] = "'" . $value . "'";

			db_query("
				INSERT INTO user
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
				UPDATE user
				SET " . implode(', ', $update) . "
				WHERE id_user = $id_user
				LIMIT 1");
		}
	}

	if (!empty($_POST['save']) || !empty($_POST['cancel']))
		redirect(build_url('user'));

	if ($is_new)
	{
		$template['user'] = array(
			'is_new' => true,
			'id' => 0,
			'username' => '',
			'email_address' => '',
			'admin' => 0,
		);
	}
	else
	{
		$request = db_query("
			SELECT
				id_user, username, email_address, admin,
				login_count, last_login, last_password_change
			FROM user
			WHERE id_user = $id_user
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$template['user'] = array(
				'is_new' => false,
				'id' => $row['id_user'],
				'username' => $row['username'],
				'email_address' => $row['email_address'],
				'admin' => $row['admin'],
				'login_count' => $row['login_count'],
				'last_login' => empty($row['last_login']) ? 'Never' : format_time($row['last_login'], 'long'),
				'last_password_change' => empty($row['last_password_change']) ? 'Never' : format_time($row['last_password_change'], 'long'),
			);
		}
		db_free_result($request);

		if (!isset($template['user']))
			fatal_error('The user requested does not exist!');
	}

	$template['page_title'] = (!$is_new ? 'Edit' : 'Add') . ' User';
	$core['current_template'] = 'user_edit';
}

function user_delete()
{
	$id_user = !empty($_REQUEST['user']) ? (int) $_REQUEST['user'] : 0;

	$request = db_query("
		SELECT id_user
		FROM user
		WHERE id_user = $id_user
		LIMIT 1");
	list ($id_user) = db_fetch_row($request);
	db_free_result($request);

	if (!empty($id_user))
	{
		db_query("
			DELETE FROM user
			WHERE id_user = $id_user
			LIMIT 1");

		redirect(build_url('user'));
	}
	else
		fatal_error('The user requested does not exist!');
}