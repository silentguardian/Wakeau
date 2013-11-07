<?php

/**
 * @package Wakeau
 *
 * @author Selman Eser
 * @copyright 2013 Selman Eser
 * @license BSD 2-clause
 *
 * @version 1.0
 */

if (!defined('CORE'))
	exit();

function logout_main()
{
	create_cookie(-3600, 0);

	redirect(build_url());
}