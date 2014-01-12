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

function template_type_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('type', 'edit')), '">Add Type</a>
			</div>
			<h2>Type List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['types']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="2">There are not any types added yet!</td>
				</tr>';
	}

	foreach ($template['types'] as $type)
	{
		echo '
				<tr>
					<td>', $type['name'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'type', $type['id'])), '">Browse</a>
						<a class="btn btn-primary" href="', build_url(array('type', 'edit', $type['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('type', 'delete', $type['id'])), '" onclick="return confirm(\'Are you sure that you want to delete this item?\');">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_type_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('type', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['type']['is_new'] ? 'Edit' : 'Add'), ' Type</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" value="', $template['type']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="position">Position:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="position" name="position" value="', $template['type']['position'], '" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="type" value="', $template['type']['id'], '" />
		</form>';
}