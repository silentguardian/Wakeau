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

function search_main()
{
	global $core, $template;

	if (!empty($_POST['search']))
	{
		check_session('search');

		$query = array();

		if (!empty($_POST['name']))
		{
			$sub_query = array();
			$parts = explode('+', $_POST['name']);

			foreach ($parts as $part)
			{
				$part = strtolower(strtr(htmlspecialchars($part, ENT_QUOTES), array('%' => '\%', '_' => '\_', '*' => '%', '?' => '_')));
				$sub_query[] = "f.name LIKE '%$part%'";
			}

			$query[] = "(" . implode(' OR ', $sub_query) . ")";
		}

		foreach (array('id_subcategory', 'id_type', 'id_user') as $field)
		{
			if (!empty($_POST[$field]) && is_array($_POST[$field]))
			{
				$selected = array();
				foreach ($_POST[$field] as $value)
					$selected[] = (int) $value;
				$query[] = "f.$field IN(" . implode(', ', $selected) . ")";
			}
		}

		$sort_fields = array(
			'name' => 'f.name',
			'category' => 'c.name',
			'subcategory' => 's.name',
			'type' => 't.name',
			'user' => 'u.username',
			'downloads' => 'f.downloads',
			'comments' => 'f.comments',
			'time' => 'f.time',
		);

		if (empty($_POST['sort_field']) || empty($sort_fields[$_POST['sort_field']]))
			$_POST['sort_field'] = 'name';
		$sort_field = $sort_fields[$_POST['sort_field']];

		if (!empty($_POST['sort_direction']))
			$sort_direction = ' DESC';
		else
			$sort_direction = '';

		$request = db_query("
			SELECT
				f.id_file, f.name, f.time, f.downloads, f.comments, t.name AS type,
				c.name AS category, s.name AS subcategory, u.username
			FROM file AS f
				LEFT JOIN category AS c ON (c.id_category = f.id_category)
				LEFT JOIN subcategory AS s ON (s.id_subcategory = f.id_subcategory)
				LEFT JOIN type AS t ON (t.id_type = f.id_type)
				LEFT JOIN user AS u ON (u.id_user = f.id_user)" . (empty($query) ? "" : "
			WHERE " . implode (' AND ', $query)) . "
			ORDER BY $sort_field$sort_direction");
		$template['files'] = array();
		while ($row = db_fetch_assoc($request))
		{
			$template['files'][] = array(
				'id' => $row['id_file'],
				'name' => $row['name'],
				'category' => $row['category'],
				'subcategory' => $row['subcategory'],
				'type' => $row['type'],
				'user' => $row['username'],
				'downloads' => $row['downloads'],
				'comments' => $row['comments'],
				'time' => format_time($row['time']),
			);
		}
		db_free_result($request);

		$template['page_title'] = 'Search Results';
		$core['current_template'] = 'search_result';
	}
	else
	{
		$request = db_query("
			SELECT id_category, name
			FROM category
			ORDER BY position");
		$template['categories'] = array();
		while ($row = db_fetch_assoc($request))
			$template['categories'][$row['id_category']] = $row['name'];
		db_free_result($request);

		if (empty($template['categories']))
			fatal_error('There are no categories added yet! You cannot have files without categories!');

		$request = db_query("
			SELECT id_subcategory, id_category, name
			FROM subcategory
			ORDER BY position");
		$template['subcategories'] = array();
		while ($row = db_fetch_assoc($request))
			$template['subcategories'][$row['id_category']][$row['id_subcategory']] = $row['name'];
		db_free_result($request);

		if (empty($template['subcategories']))
			fatal_error('There are no subcategories added yet! You cannot have files without subcategories!');

		$request = db_query("
			SELECT id_type, name
			FROM type
			ORDER BY position");
		$template['types'] = array();
		while ($row = db_fetch_assoc($request))
			$template['types'][$row['id_type']] = $row['name'];
		db_free_result($request);

		if (empty($template['types']))
			fatal_error('There are no types added yet! You cannot have files without types!');

		$request = db_query("
			SELECT id_user, username
			FROM user
			ORDER BY username");
		$template['users'] = array();
		while ($row = db_fetch_assoc($request))
			$template['users'][$row['id_user']] = $row['username'];
		db_free_result($request);

		if (empty($template['users']))
			fatal_error('There are no users added yet! You cannot have files without users!');

		$template['page_title'] = 'Search Files';
		$core['current_template'] = 'search_form';
	}
}