<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 *
 * This file is part of poMMo (http://www.pommo.org)
 *
 * poMMo is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2, or any later version.
 *
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Groups.php');

Pommo::init();
$logger = Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

// Remember the Page State
$state = Pommo_Api::stateInit('subscribers_manage');

// Fetch group + member IDs
$group = new Pommo_Groups($state['group'], $state['status'], $state['search']);

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
$json = new Pommo_Json();

/**********************************
	PAGINATION AND ORDERING
*********************************/

// Get and Remember the requested number of rows
if(!empty($_REQUEST['page']) && (
	is_numeric($_REQUEST['rows']) && (
		$_REQUEST['rows'] > 0 &&
		$_REQUEST['rows'] <= 1000
		)
	))
		$state['limit'] = $_REQUEST['rows'];

// Get and Remember the requested page
if (!empty($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
    $state['page'] = $_REQUEST['page'];
}

// Get and Remember the sort column
if(!empty($_REQUEST['sidx']) && (
	preg_match('/d\d+/',$_REQUEST['sidx']) ||
	$_REQUEST['sidx'] == 'email' ||
	$_REQUEST['sidx'] == 'ip' ||
	$_REQUEST['sidx'] == 'registered' ||
	$_REQUEST['sidx'] == 'touched'
	))
		$state['sort'] = $_REQUEST['sidx'];
// Get and Remember the sort order
if(!empty($_REQUEST['sord']) && (
	$_REQUEST['sord'] == 'asc' ||
	$_REQUEST['sord'] == 'desc'
	))
		$state['order'] = $_REQUEST['sord'];

// Calculate the offset
$start = $state['limit']*$state['page']-$state['limit'];
if($start < 0)
	$start = 0;


/**********************************
	RECORD RETREVIAL
*********************************/
// Normalize sort column to match DB column
if ($state['sort'] == 'registered' || $state['sort'] == 'touched')
	$state['sort'] = 'time_'.$state['sort'];
elseif (substr($state['sort'],0,1) == 'd')
	$state['sort'] = substr($state['sort'],1);

// fetch subscribers for this page
$subscribers = $group->members(array(
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));

/**********************************
	OUTPUT FORMATTING
*********************************/
// format subscribers for JSON output to jqGrid
$subOut = array();

foreach($subscribers as $s) {
	$sub = array(
		'id' => $s['id'],
		'email' => $s['email'],
		'touched' => $s['touched'],
		'registered' => $s['registered'],
		'ip' => $s['ip']
	);

	foreach($s['data'] as $key => $d)
		$sub['d'.$key] = $d;

	array_push($subOut,$sub);
}

$pages = ceil($group->_tally / $state['limit']);

$json->add(array(
		'page' => $state['page'],
		'total' => $pages,
		'records' => $group->_tally,
		'rows' => $subOut
	)
);
$json->serve();

