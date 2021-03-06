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

$core = array();

$core['title_short'] = 'Wakeau';
$core['title_long'] = 'Wakeau';
$core['version'] = '1.0';
$core['cookie'] = 'wakeau2011';
$core['time_offset'] = 0;
$core['clean_url'] = false;

$core['site_url'] = '';
$core['site_dir'] = dirname(__FILE__);

$core['root_dir'] = $core['site_dir'] . '/library';
$core['includes_dir'] = $core['root_dir'] . '/includes';
$core['modules_dir'] = $core['root_dir'] . '/modules';
$core['storage_dir'] = $core['site_dir'] . '/storage';

$core['storage_size'] = 20;
$core['storage_extension'] = array(
	'jpg', 'jpeg', 'gif', 'png',
	'avi', 'mp4', 'mov', 'flv',
	'mp3', 'wav', 'wma', 'ogg',
	'doc', 'xls', 'ppt',
	'docx', 'xlsx', 'pptx',
	'txt', 'rtf', 'pdf',
	'zip', 'rar', 'tar', 'gz',
	'tst', 'bnk',
);

$db = array();

$db['server'] = '';
$db['name'] = '';
$db['user'] = '';
$db['password'] = '';