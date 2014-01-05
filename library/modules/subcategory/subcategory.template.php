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

function template_subcategory_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('subcategory', 'edit')), '">Add Subcategory</a>
			</div>
			<h2>Subcategory List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Files</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['subcategories']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="4">There are not any subcategories added yet!</td>
				</tr>';
	}

	foreach ($template['subcategories'] as $subcategory)
	{
		echo '
				<tr>
					<td>', $subcategory['name'], '</td>
					<td>', $subcategory['category'], '</td>
					<td class="span2 align_center">', $subcategory['files'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'file', $subcategory['id'])), '">Browse</a>
						<a class="btn btn-primary" href="', build_url(array('subcategory', 'edit', $subcategory['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('subcategory', 'delete', $subcategory['id'])), '">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_subcategory_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('subcategory', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['subcategory']['is_new'] ? 'Edit' : 'Add'), ' Subcategory</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" value="', $template['subcategory']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="position">Position:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="position" name="position" value="', $template['subcategory']['position'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="id_category">Category:</label>
					<div class="controls">
						<select id="id_category" name="id_category">
							<option value="0"', ($template['subcategory']['category'] == 0 ? ' selected="selected"' : ''), '>Select category</option>';

	foreach ($template['categories'] as $category)
	{
		echo '
							<option value="', $category['id'], '"', ($template['subcategory']['category'] == $category['id'] ? ' selected="selected"' : ''), '>', $category['name'], '</option>';
	}

	echo '
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="subcategory" value="', $template['subcategory']['id'], '" />
		</form>';
}