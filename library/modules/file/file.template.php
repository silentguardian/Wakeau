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

function template_file_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('file', 'edit')), '">Add File</a>
			</div>
			<h2>File List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Type</th>
					<th>User</th>
					<th>Time</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['files']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="7">There are not any files added yet!</td>
				</tr>';
	}

	foreach ($template['files'] as $file)
	{
		echo '
				<tr>
					<td>', $file['name'], '</td>
					<td>', $file['category'], '</td>
					<td>', $file['subcategory'], '</td>
					<td>', $file['type'], '</td>
					<td>', $file['user'], '</td>
					<td class="span2 align_center">', $file['time'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'view', $file['id'])), '">View</a>
						<a class="btn btn-primary" href="', build_url(array('file', 'edit', $file['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('file', 'delete', $file['id'])), '" onclick="return confirm(\'Are you sure that you want to delete this item?\');">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_file_edit()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('file', 'edit')), '" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>', (!$template['file']['is_new'] ? 'Edit' : 'Add'), ' File</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" value="', $template['file']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_category">Category:</label>
					<div class="controls">
						<select id="id_category" name="id_category" onchange="update_subcategories();">
							<option value="0"', ($template['file']['category'] == 0 ? ' selected="selected"' : ''), '>Select category</option>';

	foreach ($template['categories'] as $id => $name)
	{
		echo '
							<option value="', $id, '"', ($template['file']['category'] == $id ? ' selected="selected"' : ''), '>', $name, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_subcategory">Subcategory:</label>
					<div class="controls">
						<select id="id_subcategory" name="id_subcategory">
							<option value="0"', ($template['file']['subcategory'] == 0 ? ' selected="selected"' : ''), '>Select subcategory</option>';

	if (!empty($template['file']['category']))
	{
		foreach ($template['subcategories'][$template['file']['category']] as $id => $name)
		{
			echo '
							<option value="', $id, '"', ($template['file']['subcategory'] == $id ? ' selected="selected"' : ''), '>', $name, '</option>';
		}
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_type">Type:</label>
					<div class="controls">
						<select id="id_type" name="id_type">
							<option value="0"', ($template['file']['type'] == 0 ? ' selected="selected"' : ''), '>Select type</option>';

	foreach ($template['types'] as $id => $name)
	{
		echo '
							<option value="', $id, '"', ($template['file']['type'] == $id ? ' selected="selected"' : ''), '>', $name, '</option>';
	}

	echo '
						</select>
					</div>
				</div>';

	if (!$template['file']['is_new'])
	{
		echo '
				<div class="control-group">
					<label class="control-label" for="current">Current file:</label>
					<div class="controls">
						<span class="input-xlarge uneditable-input" id="current">', $template['file']['store']['name'], '</span>
					</div>
				</div>';
	}

	echo '
				<div class="control-group">
					<label class="control-label" for="store">', (!$template['file']['is_new'] ? 'Replace' : 'Select'), ' file:</label>
					<div class="controls">
						<input type="file" class="input-xlarge" id="store" name="store" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="file" value="', $template['file']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>
		<script type="text/javascript"><!-- // --><![CDATA[
			var subcategories = {';

	$count_categories = 0;
	$total_categories = count($template['categories']);

	foreach ($template['subcategories'] as $id_category => $subcategories)
	{
		$count_subcategories = 0;
		$total_subcategories = count($subcategories);

		echo '
				', $id_category, ': {';

		foreach ($subcategories as $id_subcategory => $name)
		{
			echo '
					', $count_subcategories++, ': [', $id_subcategory, ', \'', $name, '\']', $count_subcategories == $total_subcategories ? '' : ',';
		}

		echo '
				}', ++$count_categories == $total_categories ? '' : ',';
	}

	echo '
			};

			function update_subcategories()
			{
				var category_select = document.getElementById(\'id_category\');
				var subcategory_select = document.getElementById(\'id_subcategory\');
				var value = category_select.options[category_select.selectedIndex].value;

				while (subcategory_select.options.length - 1)
					subcategory_select.options[1] = null;
				for (var key in subcategories[value])
					subcategory_select.options[subcategory_select.length] = new Option(subcategories[value][key][1], subcategories[value][key][0]);
			}
		// ]]></script>';
}