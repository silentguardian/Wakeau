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

function template_browse_category()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn btn-warning" href="', build_url('upload'), '">Upload File</a>
			</div>
			<h2>Browse Categories</h2>
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
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_browse_subcategory()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn" href="', build_url(array('browse', 'category')), '">Back</a>
				<a class="btn btn-warning" href="', build_url(array('module' => 'upload', 'category' => $template['category']['id']), false), '">Upload File</a>
			</div>
			<h2>Browse Subcategories - ', $template['category']['name'], '</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Files</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>';

	if (empty($template['subcategories']))
	{
		echo '
				<tr>
					<td class="align_center" colspan="3">There are not any subcategories added yet!</td>
				</tr>';
	}

	foreach ($template['subcategories'] as $subcategory)
	{
		echo '
				<tr>
					<td>', $subcategory['name'], '</td>
					<td class="span2 align_center">', $subcategory['files'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'file', $subcategory['id'])), '">Browse</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_browse_file()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn" href="', build_url(array('browse', 'subcategory', $template['category']['id'])), '">Back</a>
				<a class="btn btn-warning" href="', build_url(array('module' => 'upload', 'category' => $template['category']['id'], 'subcategory' => $template['subcategory']['id']), false), '">Upload File</a>
			</div>
			<h2>Browse Files - ', $template['category']['name'], ' - ', $template['subcategory']['name'], '</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
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
					<td class="align_center" colspan="7">There are not any files added yet!</td>
				</tr>';
	}

	foreach ($template['files'] as $file)
	{
		echo '
				<tr>
					<td>', $file['name'], '</td>
					<td>', $file['type'], '</td>
					<td>', $file['user'], '</td>
					<td class="span1 align_center">', $file['downloads'], '</td>
					<td class="span1 align_center">', $file['comments'], '</td>
					<td class="span2 align_center">', $file['time'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'view', $file['id'])), '">View</a>
						<a class="btn btn-success" href="', build_url(array('browse', 'download', $file['id'])), '">Download</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_browse_type()
{
	global $template;

	echo '
		<div class="page-header">
			<h2>Browse Files - ', $template['type']['name'], '</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
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
					<td class="align_center" colspan="8">There are not any files added yet!</td>
				</tr>';
	}

	foreach ($template['files'] as $file)
	{
		echo '
				<tr>
					<td>', $file['name'], '</td>
					<td>', $file['category'], '</td>
					<td>', $file['subcategory'], '</td>
					<td>', $file['user'], '</td>
					<td class="span1 align_center">', $file['downloads'], '</td>
					<td class="span1 align_center">', $file['comments'], '</td>
					<td class="span2 align_center">', $file['time'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'view', $file['id'])), '">View</a>
						<a class="btn btn-success" href="', build_url(array('browse', 'download', $file['id'])), '">Download</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_browse_user()
{
	global $template;

	echo '
		<div class="page-header">
			<h2>Browse Files - ', $template['user']['username'], '</h2>
		</div>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Type</th>
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
					<td class="align_center" colspan="8">There are not any files added yet!</td>
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
					<td class="span1 align_center">', $file['downloads'], '</td>
					<td class="span1 align_center">', $file['comments'], '</td>
					<td class="span2 align_center">', $file['time'], '</td>
					<td class="span3 align_center">
						<a class="btn btn-info" href="', build_url(array('browse', 'view', $file['id'])), '">View</a>
						<a class="btn btn-success" href="', build_url(array('browse', 'download', $file['id'])), '">Download</a>
					</td>
				</tr>';
	}

	echo '
			</tbody>
		</table>';
}

function template_browse_view()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				<a class="btn" href="', build_url(array('browse', 'file', $template['file']['subcategory']['id'])), '">Back</a>
				<a class="btn btn-success" href="', build_url(array('browse', 'download', $template['file']['id'])), '">Download</a>';

	if ($template['can_manage'])
	{
		echo '
				<a class="btn btn-primary" href="', build_url(array('browse', 'edit', $template['file']['id'])), '">Edit</a>
				<a class="btn btn-danger" href="', build_url(array('browse', 'delete', $template['file']['id'])), '">Delete</a>';
	}

	echo '
			</div>
			<h2>View File - ', $template['file']['name'], '</h2>
		</div>
		<dl class="dl-horizontal well">
			<dt>Name:</dt>
			<dd>', $template['file']['name'], '</dd>
			<dt>Category:</dt>
			<dd>', $template['file']['category'], '</dd>
			<dt>Subcategory:</dt>
			<dd>', $template['file']['subcategory']['name'], '</dd>
			<dt>Type:</dt>
			<dd>', $template['file']['type'], '</dd>
			<dt>User:</dt>
			<dd>', $template['file']['user']['username'], '</dd>
			<dt>Downloads:</dt>
			<dd>', $template['file']['downloads'], '</dd>
			<dt>Comments:</dt>
			<dd>', $template['file']['comments'], '</dd>
			<dt>Time:</dt>
			<dd>', $template['file']['time'], '</dd>
			<dt>File:</dt>
			<dd>', $template['file']['store']['name'], '</dd>
			<dt>Size:</dt>
			<dd>', $template['file']['store']['size'], '</dd>
		</dl>
		<div class="page-header">
			<h3>Comments</h3>
		</div>';

	if (empty($template['comments']))
	{
		echo '
		<div class="well">
			There are not any comments for this file yet. Be the first one to comment!
		</div>';
	}

	foreach ($template['comments'] as $comment)
	{
		echo '
		<div class="well">
			', $comment['body'], '
			<hr />';

		if ($comment['can_delete'])
		{
			echo '
			<div class="pull-right">
				<a class="btn btn-danger" href="', build_url(array('browse', 'comment', $template['file']['id'], $comment['id'])), '" onclick="return confirm(\'Are you sure that you want to delete this item?\');">Delete</a>
			</div>';
		}

		echo '
			<div class="muted">
				Comment by ', $comment['username'], ' on ', $comment['time'], '
			</div>
		</div>';
	}

	if ($template['can_comment'])
	{
		echo '
		<form class="form-horizontal" action="', build_url(array('browse', 'comment', $template['file']['id'])), '" method="post">
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="body">Comment:</label>
					<div class="controls">
						<textarea class="input-xlarge span5" id="body" name="body" rows="3"></textarea>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
				</div>
			</fieldset>
		</form>';
	}
}

function template_browse_edit()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('browse', 'edit')), '" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>Edit File</legend>
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
				</div>
				<div class="control-group">
					<label class="control-label" for="current">Current file:</label>
					<div class="controls">
						<span class="input-xlarge uneditable-input" id="current">', $template['file']['store']['name'], '</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="store">Replace file:</label>
					<div class="controls">
						<input type="file" class="input-xlarge" id="store" name="store" />
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save" value="Save changes" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="browse" value="', $template['file']['id'], '" />
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

function template_browse_delete()
{
	global $user, $template;

	echo '
		<form class="form-horizontal" action="', build_url(array('browse', 'delete')), '" method="post">
			<fieldset>
				<legend>Delete File</legend>
				Are you sure you want to delete the file &quot;', $template['file']['name'], '&quot;?
				<div class="form-actions">
					<input type="submit" class="btn btn-danger" name="delete" value="Delete" />
					<input type="submit" class="btn" name="cancel" value="Cancel" />
				</div>
			</fieldset>
			<input type="hidden" name="browse" value="', $template['file']['id'], '" />
			<input type="hidden" name="session_id" value="', $user['session_id'], '" />
		</form>';
}