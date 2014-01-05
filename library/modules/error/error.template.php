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

function template_error_main()
{
	global $template;

	echo '
		<div class="alert alert-error">
			<h4 class="alert-heading">Error!</h4>
			', $template['error'], '
		</div>';
}