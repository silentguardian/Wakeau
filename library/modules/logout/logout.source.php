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

function logout_main()
{
	global $user;

	db_query("
		DELETE FROM online
		WHERE id_user = $user[id]
		LIMIT 1");

	create_cookie(-3600, 0);

	redirect(build_url());
}