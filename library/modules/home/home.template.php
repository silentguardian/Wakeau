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

function template_home_main()
{
	echo '
		<div class="page-header">
			<div class="pull-right">
				0 users &bull; 0 files
			</div>
			<h2>Wakeau</h2>
		</div>
		<div class="well">
			Nothing to see here, for now...
		</div>';
}