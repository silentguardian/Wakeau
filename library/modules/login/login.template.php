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

function template_login_main()
{
	global $user;

	echo '
		<form class="form-horizontal" action="', build_url('login'), '" method="post">
			<fieldset>
				<legend>User Login</legend>
				<div class="control-group">
					<label class="control-label" for="username">Username:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="username" name="username" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">Password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="password" name="password" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
				</div>
			</fieldset>
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}