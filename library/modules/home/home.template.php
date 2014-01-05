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

function template_home_main()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				', $template['total_users'], ' users &bull; ', $template['total_files'], ' files &bull; ', $template['total_downloads'], ' downloads &bull; ', $template['total_comments'], ' comments
			</div>
			<h2>Wakeau</h2>
		</div>
		<p class="content">
			Nothing to see here, for now...
		</p>';
}