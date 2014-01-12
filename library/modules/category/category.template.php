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

function template_category_list()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url(array('category', 'edit')), '">Add Category</a>
			</div>
			<h2>Category List</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Subcategories</th>
					<th>Files</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['categories']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="4">There are not any categories added yet!</td>
				</tr>';
	}

	foreach ($template['categories'] as $category)
	{
		echo '
				<tr>
					<td>', $category['name'], '</td>
					<td class="span2 align_center">', $category['subcategories'], '</td>
					<td class="span2 align_center">', $category['files'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'subcategory', $category['id'])), '">Browse</a>
						<a class="btn btn-primary" href="', build_url(array('category', 'edit', $category['id'])), '">Edit</a>
						<a class="btn btn-danger" href="', build_url(array('category', 'delete', $category['id'])), '" onclick="return confirm(\'Are you sure that you want to delete this item?\');">Delete</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_category_edit()
{
	global $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('category', 'edit')), '" method="post">
			<fieldset>
				<legend>', (!$template['category']['is_new'] ? 'Edit' : 'Add'), ' Category</legend>
				<div class="control-group">
					<label class="control-label" for="name">Name:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="name" name="name" value="', $template['category']['name'], '" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="position">Position:</label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="position" name="position" value="', $template['category']['position'], '" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="category" value="', $template['category']['id'], '" />
		</form>';
}