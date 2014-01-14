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

function profile_main()
{
	global $core, $template, $user;

	$request = db_query("
		SELECT
			email_address, login_count,
			last_login, last_password_change
		FROM user
		WHERE id_user = $user[id]
		LIMIT 1");
	while ($row = db_fetch_assoc($request))
	{
		$template['profile'] = array(
			'email_address' => $row['email_address'],
			'login_count' => $row['login_count'],
			'last_login' => empty($row['last_login']) ? 'Never' : format_time($row['last_login'], 'long'),
			'last_password_change' => empty($row['last_password_change']) ? 'Never' : format_time($row['last_password_change'], 'long'),
		);
	}
	db_free_result($request);

	if (!empty($_POST['save']))
	{
		$values = array();
		$fields = array(
			'email_address' => 'email',
			'choose_password' => 'password',
			'verify_password' => 'password',
			'current_password' => 'password',
		);

		foreach ($fields as $field => $type)
		{
			if ($type === 'password')
				$values[$field] = !empty($_POST[$field]) ? sha1($_POST[$field]) : '';
			elseif ($type === 'email')
				$values[$field] = !empty($_POST[$field]) && preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', $_POST[$field]) ? $_POST[$field] : '';
			elseif ($type === 'integer')
				$values[$field] = !empty($_POST[$field]) ? (int) $_POST[$field] : 0;
		}

		$request = db_query("
			SELECT password
			FROM user
			WHERE id_user = $user[id]
			LIMIT 1");
		list ($current_password) = db_fetch_row($request);
		db_free_result($request);

		if ($current_password !== $values['current_password'])
			fatal_error('The password entered is not correct!');

		if ($values['choose_password'] !== $values['verify_password'])
			fatal_error('The new passwords entered do not match.');

		if ($values['email_address'] === '')
			fatal_error('You did not enter a valid email address!');

		$request = db_query("
			SELECT id_user
			FROM user
			WHERE email_address = '$values[email_address]'
				AND id_user != '$user[id]'
			LIMIT 1");
		list ($duplicate_id) = db_fetch_row($request);
		db_free_result($request);

		if (!empty($duplicate_id))
			fatal_error('The email address entered is already in use!');

		$changes = array();
		if ($values['email_address'] !== $template['email_address'])
			$changes[] = "email_address = '$values[email_address]'";
		if ($values['choose_password'] !== '')
		{
			$changes[] = "password = '$values[verify_password]'";
			$changes[] = "last_password_change = " . time();
		}

		if (!empty($changes))
		{
			db_query("
				UPDATE user
				SET " . implode(', ', $changes) . "
				WHERE id_user = $user[id]
				LIMIT 1");
		}

		if ($values['choose_password'] !== '')
			redirect(build_url('login'));
		else
			redirect(build_url('profile'));
	}

	$template['page_title'] = 'Edit Profile';
	$core['current_template'] = 'profile_main';
}