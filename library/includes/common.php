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

function recount_stats($type, $ids)
{
	if (!is_array($ids))
		$ids = array($ids);

	foreach ($ids as $id)
	{
		if ($type == 'subcategory')
		{
			$request = db_query("
				SELECT COUNT(id_subcategory)
				FROM subcategory
				WHERE id_category = $id
				LIMIT 1");
			list ($subcategories) = db_fetch_row($request);
			db_free_result($request);

			db_query("
				UPDATE category
				SET subcategories = $subcategories
				WHERE id_category = $id
				LIMIT 1");
		}
		elseif ($type == 'file')
		{
			$request = db_query("
				SELECT COUNT(id_file)
				FROM file
				WHERE id_subcategory = $id
				LIMIT 1");
			list ($files) = db_fetch_row($request);
			db_free_result($request);

			db_query("
				UPDATE subcategory
				SET files = $files
				WHERE id_subcategory = $id
				LIMIT 1");

			$request = db_query("
				SELECT id_category
				FROM subcategory
				WHERE id_subcategory = $id
				LIMIT 1");
			list ($id_category) = db_fetch_row($request);
			db_free_result($request);

			recount_stats('category', $id_category);
		}
		elseif ($type == 'category')
		{
			$request = db_query("
				SELECT COUNT(id_file)
				FROM file
				WHERE id_category = $id
				LIMIT 1");
			list ($files) = db_fetch_row($request);
			db_free_result($request);

			db_query("
				UPDATE category
				SET files = $files
				WHERE id_category = $id
				LIMIT 1");
		}
	}
}

function load_module($module)
{
	global $core;

	require_once $core['modules_dir'] . '/' . $module . '/' . $module . '.source.php';
	require_once $core['modules_dir'] . '/' . $module . '/' . $module . '.template.php';
}

function load_template()
{
	global $user, $template;

	if (!$user['logged'])
		return;

	$types = array(
		0 => 'alert-info',
		1 => 'alert-success',
		2 => '',
		3 => 'alert-error',
	);

	$request = db_query("
		SELECT title, body, type
		FROM announcement
		WHERE state = 1
		ORDER BY position");
	$template['active_announcements'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['active_announcements'][] = array(
			'title' => $row['title'],
			'body' => $row['body'],
			'type' => $types[$row['type']],
		);
	}
	db_free_result($request);

	$request = db_query("
		SELECT id_user, username
		FROM online
		ORDER BY time DESC");
	$template['online_users'] = array();
	while ($row = db_fetch_assoc($request))
	{
		$template['online_users'][] = array(
			'id' => $row['id_user'],
			'username' => $row['username'],
		);
	}
	db_free_result($request);
}

function load_user()
{
	global $core, $user;

	if (isset($_COOKIE[$core['cookie']]))
	{
		$_COOKIE[$core['cookie']] = stripslashes($_COOKIE[$core['cookie']]);

		if (preg_match('~^a:[34]:\{i:0;(i:\d{1,6}|s:[1-8]:"\d{1,8}");i:1;s:(0|40):"([a-fA-F0-9]{40})?";i:2;[id]:\d{1,14};(i:3;i:\d;)?\}$~', $_COOKIE[$core['cookie']]) == 1)
			list ($user['id'], $pass) = @unserialize($_COOKIE[$core['cookie']]);

		$user['id'] = !empty($user['id']) && !empty($pass) ? (int) $user['id'] : 0;
	}
	elseif (isset($_SESSION['login_' . $core['cookie']]))
	{
		list ($user['id'], $pass, $login) = @unserialize(stripslashes($_SESSION['login_' . $core['cookie']]));
		$user['id'] = !empty($user['id']) && strlen($pass) == 40 && $login > time() ? (int) $user['id'] : 0;
	}

	if (!empty($user['id']))
	{
		$request = db_query("
			SELECT
				id_user, username, password,
				admin, inactive
			FROM user
			WHERE id_user = $user[id]
			LIMIT 1");
		while ($row = db_fetch_assoc($request))
		{
			$real_password = $row['password'];
			$inactive = !empty($row['inactive']);

			$temp = array(
				'id' => (int) $row['id_user'],
				'username' => $row['username'],
				'admin' => !empty($row['admin']),
				'logged' => true,
				'ip' => guess_ip(),
				'session_id' => $core['session_id'],
			);
		}
		db_free_result($request);

		if (empty($temp) || strlen($pass) != 40 || $pass !== $real_password || $inactive)
			$user['id'] = 0;
	}

	if (empty($user['id']))
	{
		$user = array(
			'id' => 0,
			'username' => '',
			'admin' => false,
			'logged' => false,
			'ip' => guess_ip(),
			'session_id' => $core['session_id'],
		);
	}
	else
	{
		$user = $temp;

		if (isset($_COOKIE[$core['cookie']]))
			$_COOKIE[$core['cookie']] = '';
	}

	if ($user['logged'] && (empty($_SESSION['log_online']) || time() - $_SESSION['log_online'] > 10))
	{
		db_query("
			DELETE FROM online
			WHERE time < " . (time() - 300));

		db_query("
			REPLACE INTO online
				(id_user, username, time)
			VALUES
				($user[id], '$user[username]', " . time() . ")");

		$_SESSION['log_online'] = time();
	}
}

function check_session($action = '')
{
	global $core;

	if (empty($_POST['session_id']) || $_POST['session_id'] != $core['session_id'])
		fatal_error('Session timed out!');

	if ((!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT']))
		fatal_error('Session could not be verified!');

	if (!isset($_SESSION['old_action']) || $_SESSION['old_action'] != $action)
		fatal_error('Session URL could not be verified!');
}

function start_session()
{
	global $core;

	if (session_id() == '')
		session_start();

	if (!isset($_SESSION['session_id']))
		$_SESSION['session_id'] = md5(session_id() . mt_rand() . (string) microtime());

	$core['session_id'] = $_SESSION['session_id'];
}

function create_cookie($length, $user, $pass = '')
{
	global $core;

	$data = serialize(empty($user) ? array(0, '', 0) : array($user, $pass, time() + $length));
	$url = parse_url($core['site_url']);

	setcookie($core['cookie'], $data, 0, $url['path'], '', 0);

	$_COOKIE[$core['cookie']] = $data;

	if (!isset($_SESSION['login_' . $core['cookie']]) || $_SESSION['login_' . $core['cookie']] !== $data)
	{
		$old_session = $_SESSION;
		$_SESSION = array();
		session_destroy();

		start_session();
		session_regenerate_id();
		$_SESSION = $old_session;

		setcookie(session_name(), session_id(), 0, $url['path'], '', 0);

		$_SESSION['login_' . $core['cookie']] = $data;
	}
}

function guess_ip()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']) && preg_match('~\b(?:\d{1,3}\.){3}\d{1,3}\b~', $_SERVER['HTTP_CLIENT_IP'], $match))
		return $match[0];
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('~\b(?:\d{1,3}\.){3}\d{1,3}\b~', $_SERVER['HTTP_X_FORWARDED_FOR'], $match))
		return $match[0];
	elseif (!empty($_SERVER['REMOTE_ADDR']) && preg_match('~\b(?:\d{1,3}\.){3}\d{1,3}\b~', $_SERVER['REMOTE_ADDR'], $match))
		return $match[0];
	else
		return 'unknown';
}

function clean_request()
{
	unset($GLOBALS['HTTP_POST_VARS'], $GLOBALS['HTTP_POST_VARS']);
	unset($GLOBALS['HTTP_POST_FILES'], $GLOBALS['HTTP_POST_FILES']);

	if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']))
		fatal_error('Invalid request!');

	foreach (array_merge(array_keys($_POST), array_keys($_GET), array_keys($_FILES)) as $key)
	{
		if (is_numeric($key))
			fatal_error('Invalid request!');
	}

	foreach ($_COOKIE as $key => $value)
	{
		if (is_numeric($key))
			unset($_COOKIE[$key]);
	}

	foreach (array('module', 'action') as $index)
	{
		if (isset($_GET[$index]))
			$_GET[$index] = (string) $_GET[$index];
	}

	$_REQUEST = $_POST + $_GET;
}

function format_time($stamp, $format = 'short')
{
	global $core;
	static $formats;

	if (!isset($formats))
	{
		$formats = array(
			'short' => '%d/%m/%Y, %H:%M',
			'long' => '%d %B %Y, %H:%M',
		);
	}

	$stamp += $core['time_offset'] * 3600;

	return strftime($formats[$format], $stamp);
}

function build_url($parts = array(), $quick = true)
{
	global $core;

	$url = $core['site_url'];

	if (!is_array($parts))
		$parts = array($parts);
	if (empty($parts) || $parts == array('home'))
		return $url;

	if ($core['clean_url'] === true)
		$url .= implode('/', $parts);
	else
	{
		if ($quick)
		{
			foreach ($parts as $level => $part)
			{
				if ($level == 0)
					$url .= '?module=' . $part;
				elseif ($level == 1)
					$url .= '&amp;action=' . $part;
				elseif ($level == 2)
					$url .= '&amp;' . $parts[0] . '=' . $part;
				elseif ($level == 3)
					$url .= '&amp;' . $parts[1] . '=' . $part;
			}
		}
		else
		{
			$temp_parts = array();

			foreach ($parts as $key => $value)
				$temp_parts[] = $key . '=' . $value;

			$url .= '?' . implode('&amp;', $temp_parts);
		}
	}

	return $url;
}

function template_menu()
{
	global $core, $user, $modules;

	$items = array(
		'',
		'home' => 'Home',
		'login' => 'Login',
		'register' => 'Register',
		'about' => 'About',
		'browse' => 'Browse',
		'upload' => 'Upload',
		'profile' => 'Profile',
		'logout' => 'Logout',
		'',
		'category' => 'Category',
		'subcategory' => 'Subcategory',
		'type' => 'Type',
		'file' => 'File',
		'user' => 'User',
		'announcement' => 'Announcement',
	);

	echo '
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="', build_url(), '">', $core['title_short'], '</a>
				<div class="nav-collapse">
					<ul class="nav">';

	foreach ($items as $key => $value)
	{
		if ($value === '')
		{
			echo '
						<li class="divider-vertical"></li>';
		}
		elseif (!empty($modules) && in_array($key, $modules))
		{
			echo '
						<li', ($key === $core['current_module'] ? ' class="active"' : ''), '><a href="', build_url($key), '">', $value, '</a></li>';
		}
	}

	echo '
					</ul>';

	if ($user['logged'])
	{
		echo '
					<p class="navbar-text pull-right">
						Logged in as <strong>', $user['username'], '</strong>
					</p>';
	}

	echo '
				</div>
			</div>
		</div>
	</div>';
}

function template_header()
{
	global $core, $template;

	echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>', $template['page_title'], '</title>
	<link href="', $core['site_url'], 'interface/css/bootstrap.min.css" rel="stylesheet">
	<link href="', $core['site_url'], 'interface/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="', $core['site_url'], 'interface/css/style.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body>';

	template_menu();

	echo '
	<div class="container">';

	if (!empty($template['active_announcements']))
	{
		foreach ($template['active_announcements'] as $announcement)
		{
			echo '
		<div class="alert ', $announcement['type'], '">
			<h4 class="alert-heading">', $announcement['title'], '</h4>
			', $announcement['body'], '
		</div>';
		}
	}
}

function template_footer()
{
	global $core, $db, $template, $start_time;

	$time = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $start_time)), 3);
	$queries = !empty($db['debug']) ? count($db['debug']) : 0;

	echo '
		<hr />
		<p>
			<small class="pull-left">Wakeau ', $core['version'], ' &copy; 2014, Selman Eser</small>
			<small class="pull-right">';

	if (!empty($template['online_users']))
	{
		echo '
				<span id="online_users" style="display: none;">';

		foreach ($template['online_users'] as $user)
			echo '
					<span class="label label-info">', $user['username'], '</span> ';

		echo '
				</span>
				<span class="label" onclick="document.getElementById(\'online_users\').style.display = \'\';">', ($users = count($template['online_users'])), '</span> user', ($users > 1 ? 's' :''), ' online';
	}

	echo '
				<span class="label">', $time, '</span> seconds
				<span class="label">', $queries, '</span> queries
			</small>
		</p>
	</div>
	<script src="', $core['site_url'], 'interface/js/jquery.js"></script>
	<script src="', $core['site_url'], 'interface/js/bootstrap.min.js"></script>
</body>
</html>';
}

function redirect($location)
{
	header('Location: ' . str_replace(array(' ', '&amp;'), array('%20', '&'), $location));

	ob_end_flush();

	exit();
}

function fatal_error($error)
{
	global $core, $template;

	$template['error'] = $error;
	$core['current_module'] = 'error';

	load_module('error');

	call_user_func('error_main');

	ob_exit();
}

function ob_exit()
{
	global $core, $template;

	if (empty($template['page_title']))
		$template['page_title'] = $core['title_long'];
	else
		$template['page_title'] .= ' - ' . $core['title_long'];

	template_header();

	call_user_func('template_' . $core['current_template']);

	template_footer();

	ob_end_flush();

	$_SESSION['old_action'] = $core['current_module'];
	$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

	db_exit();

	exit();
}