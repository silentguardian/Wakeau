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

function template_search_form()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url('search'), '" method="post">
			<fieldset>
				<legend>Search Files</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_subcategory">Category:</label>
					<div class="controls">
						<select id="id_subcategory" name="id_subcategory[]" size="10" multiple>';

	foreach ($template['categories'] as $category_id => $category_name)
	{
		echo '
							<optgroup label="', $category_name, '">';

		if (!empty($template['subcategories'][$category_id]))
		{
			foreach ($template['subcategories'][$category_id] as $subcategory_id => $subcategory_name)
			{
				echo '
								<option value="', $subcategory_id, '">', $subcategory_name, '</option>';
			}
		}

		echo '
							</optgroup>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_type">Type:</label>
					<div class="controls">
						<select id="id_type" name="id_type[]" size="5" multiple>';

	foreach ($template['types'] as $id => $name)
	{
		echo '
							<option value="', $id, '">', $name, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_user">User:</label>
					<div class="controls">
						<select id="id_user" name="id_user[]" size="5" multiple>';

	foreach ($template['users'] as $id => $username)
	{
		echo '
							<option value="', $id, '">', $username, '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="sort_field">Sort Field:</label>
					<div class="controls">
						<select id="sort_field" name="sort_field">
							<option value="name">Name</option>
							<option value="category">Category</option>
							<option value="subcategory">Subcategory</option>
							<option value="type">Type</option>
							<option value="user">User</option>
							<option value="downloads">Downloads</option>
							<option value="comments">Comments</option>
							<option value="time">Time</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="sort_direction">Sort Direction:</label>
					<div class="controls">
						<select id="sort_direction" name="sort_direction">
							<option value="0">Ascending</option>
							<option value="1">Descending</option>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="search" value="Search" />
				</div>
			</fieldset>
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}

function template_search_result()
{
	global $template;

	echo '
		<div class="page-header">
			<h2>Search Results (', count($template['files']), ')</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Type</th>
					<th>User</th>
					<th>Downloads</th>
					<th>Comments</th>
					<th>Time</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['files']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="9">There are not any files matching the criteria!</td>
				</tr>';
	}

	foreach ($template['files'] as $file)
	{
		echo '
				<tr>
					<td class="span3">', $file['name'], '</td>
					<td class="align_center">', $file['category'], '</td>
					<td class="align_center">', $file['subcategory'], '</td>
					<td class="align_center">', $file['type'], '</td>
					<td class="align_center">', $file['user'], '</td>
					<td class="span1 align_center">', $file['downloads'], '</td>
					<td class="span1 align_center">', $file['comments'], '</td>
					<td class="span2 align_center">', $file['time'], '</td>
					<td class="span2 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'view', $file['id'])), '">View</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}