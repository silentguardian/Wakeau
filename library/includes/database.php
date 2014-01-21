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

function db_initiate()
{
	global $db, $database;

	$database = new mysqli($db['server'], $db['user'], $db['password'], $db['name']);

	if ($database->connect_errno)
		fatal_error('Could not connect to database.');

	db_query("SET NAMES utf8");
}

function db_query($sql)
{
	global $db, $database;

	$db['debug'][] = $sql;

	$result = $database->query($sql);

	if ($result === false)
		fatal_error('Database error: [' . $database->errno . '] ' . $database->error);

	return $result;
}

function db_affected_rows()
{
	global $database;

	return $database->affected_rows;
}

function db_insert_id()
{
	global $database;

	return $database->insert_id;
}

function db_fetch_row($resource)
{
	return $resource ? $resource->fetch_row() : false;
}

function db_fetch_assoc($resource)
{
	return $resource ? $resource->fetch_assoc() : false;
}

function db_free_result($resource)
{
	if ($resource)
		$resource->free;
}

function db_exit()
{
	global $database;

	$database->close();
}