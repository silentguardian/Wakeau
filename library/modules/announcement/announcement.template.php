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

function template_announcement_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('announcement', 'edit')), '">Add announcement</a>
			</div>
			<h2>Announcement List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Title</th>
					<th>Type</th>
					<th>State</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['announcements']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="4">There are not any announcements added yet!</td>
				</tr>';
	}

	foreach ($template['announcements'] as $announcement)
	{
		echo '
				<tr>
					<td>', $announcement['title'], '</td>
					<td class="span3 align_center">', $announcement['type'], '</td>
					<td class="span3 align_center">', $announcement['state'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-primary" href="', build_url(array('announcement', 'edit', $announcement['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('announcement', 'delete', $announcement['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_announcement_edit()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('announcement', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['announcement']['is_new'] ? 'Edit' : 'Add'), ' Announcement</legend>
				<div class="control-group">
					<label class="control-label" for="title">Title:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="title" name="title" value="', $template['announcement']['title'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="body">Body:</label>
					<div class="controls">
						<textarea class="input-xlarge" id="body" name="body" rows="3">', $template['announcement']['body'], '</textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="type">Type:</label>
					<div class="controls">
						<select id="type" name="type">
							<option value="0"', ($template['announcement']['type'] == 0 ? ' selected="selected"' : ''), '>Information</option>
							<option value="1"', ($template['announcement']['type'] == 1 ? ' selected="selected"' : ''), '>Success</option>
							<option value="2"', ($template['announcement']['type'] == 2 ? ' selected="selected"' : ''), '>Warning</option>
							<option value="3"', ($template['announcement']['type'] == 3 ? ' selected="selected"' : ''), '>Danger</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="position">Position:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="position" name="position" value="', $template['announcement']['position'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="state">State:</label>
					<div class="controls">
						<select id="state" name="state">
							<option value="0"', ($template['announcement']['state'] == 0 ? ' selected="selected"' : ''), '>Inactive</option>
							<option value="1"', ($template['announcement']['state'] == 1 ? ' selected="selected"' : ''), '>Active</option>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="announcement" value="', $template['announcement']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}

function template_announcement_delete()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('announcement', 'delete')), '" method="post">
			<fieldset>
				<legend>Delete Announcement</legend>
				Are you sure you want to delete the announcement &quot;', $template['announcement']['title'], '&quot;?
				<div class="form-actions">
					<input type="submit" class="btn btn-danger" name="delete" value="Delete" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="announcement" value="', $template['announcement']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}