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

function template_home_main()
{
	global $template;

	echo '
		<div class="page-header">
			<div class="pull-right">
				', $template['total_users'], ' users &bull; ', $template['total_files'], ' files &bull; ', $template['total_downloads'], ' downloads &bull; ', $template['total_comments'], ' comments
			</div>
			<h2>Wakeau</h2>
		</div>
		<div class="pull-left half">
			<div class="page-header">
				<h3>Most popular files</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>File</th>
						<th class="span2">Downloads</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['popular_files']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any popular files!</td>
					</tr>';
	}

	foreach ($template['popular_files'] as $file)
	{
		echo '
					<tr>
						<td><a href="', $file['href'], '">', $file['name'], '</a></td>
						<td class="align_center">', $file['downloads'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<div class="pull-right half">
			<div class="page-header">
				<h3>Recently uploaded files</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>File</th>
						<th class="span2">Time</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['recent_files']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any recent files!</td>
					</tr>';
	}

	foreach ($template['recent_files'] as $file)
	{
		echo '
					<tr>
						<td><a href="', $file['href'], '">', $file['name'], '</a></td>
						<td class="align_center">', $file['time'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />
		<div class="pull-left half">
			<div class="page-header">
				<h3>Most popular types</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Type</th>
						<th class="span2">Files</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['popular_types']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any popular types!</td>
					</tr>';
	}

	foreach ($template['popular_types'] as $type)
	{
		echo '
					<tr>
						<td>', $type['name'], '</td>
						<td class="align_center">', $type['files'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<div class="pull-right half">
			<div class="page-header">
				<h3>Most generous users</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Username</th>
						<th class="span2">Files</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['generous_users']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any generous users!</td>
					</tr>';
	}

	foreach ($template['generous_users'] as $user)
	{
		echo '
					<tr>
						<td>', $user['username'], '</td>
						<td class="align_center">', $user['files'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />
		<div class="pull-left half">
			<div class="page-header">
				<h3>Most popular categories</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Category</th>
						<th class="span2">Files</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['popular_categories']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any popular categories!</td>
					</tr>';
	}

	foreach ($template['popular_categories'] as $category)
	{
		echo '
					<tr>
						<td>', $category['name'], '</td>
						<td class="align_center">', $category['files'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<div class="pull-right half">
			<div class="page-header">
				<h3>Most popular subcategories</h3>
			</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Subcategory</th>
						<th class="span2">Files</th>
					</tr>
				</thead>
				<tbody>';

	if (empty($template['popular_subcategories']))
	{
		echo '
					<tr>
						<td class="align_center" colspan="2">There are not any popular subcategories!</td>
					</tr>';
	}

	foreach ($template['popular_subcategories'] as $subcategory)
	{
		echo '
					<tr>
						<td>', $subcategory['name'], '</td>
						<td class="align_center">', $subcategory['files'], '</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
		</div>
		<br class="clear" />';
}