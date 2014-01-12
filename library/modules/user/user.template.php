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

function template_user_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('user', 'edit')), '">Add User</a>
			</div>
			<h2>User List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Username</th>
					<th>Email Address</th>
					<th>Registered</th>
					<th>Admin</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['users']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="5">There are not any users added yet!</td>
				</tr>';
	}

	foreach ($template['users'] as $user)
	{
		echo '
				<tr>
					<td>', $user['username'], '</td>
					<td>', $user['email_address'], '</td>
					<td class="span2 align_center">', $user['registered'], '</td>
					<td class="align_center">', $user['admin'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'user', $user['id'])), '">Browse</a>
						<a class="btn btn-primary" href="', build_url(array('user', 'edit', $user['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('user', 'delete', $user['id'])), '" onclick="return confirm(\'Are you sure that you want to delete this item?\');">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_user_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('user', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['user']['is_new'] ? 'Edit' : 'Add'), ' User</legend>
				<div class="control-group">
					<label class="control-label" for="username">Username:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="username" name="username" value="', $template['user']['username'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email_address">Email address:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="email_address" name="email_address" value="', $template['user']['email_address'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">Password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="password" name="password" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="verify_password">Verify password:</label>
					<div class="controls">
						<input type="password" class="input-xlarge" id="verify_password" name="verify_password" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="admin">Admin:</label>
					<div class="controls">
						<select id="admin" name="admin">
							<option value="0"', ($template['user']['admin'] == 0 ? ' selected="selected"' : ''), '>No</option>
							<option value="1"', ($template['user']['admin'] == 1 ? ' selected="selected"' : ''), '>Yes</option>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="user" value="', $template['user']['id'], '" />
		</form>';
}