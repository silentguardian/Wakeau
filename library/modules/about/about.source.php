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

function about_main()
{
	global $core, $template;

	$template['page_title'] = 'About';
	$core['current_template'] = 'about_main';
}