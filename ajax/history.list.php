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
require_once(Pommo::$_baseDir.'classes/Pommo_Mailing.php');

Pommo::init();
$logger = & Pommo::$_logger;
$dbo = & Pommo::$_dbo;

// Remember the Page State
$state =& Pommo_Api::stateInit('mailings_history');

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
if(!empty($_REQUEST['page']) && (
	is_numeric($_REQUEST['page']) && 
	$_REQUEST['page'] <= $state['pages']
	))
		$state['page'] = $_REQUEST['page'];

// Get and Remember the sort column
if(!empty($_REQUEST['sidx']) && (
	$_REQUEST['sidx'] == 'start' || 
	$_REQUEST['sidx'] == 'end' || 
	$_REQUEST['sidx'] == 'subject' || 
	$_REQUEST['sidx'] == 'sent' ||
	$_REQUEST['sidx'] == 'status' || 
	$_REQUEST['sidx'] == 'group'
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
	
// normalize sort to database column
if($state['sort'] == 'group') $state['sort'] = 'mailgroup';
elseif($state['sort'] == 'start') $state['sort'] = 'started';
elseif($state['sort'] == 'end') $state['sort'] = 'finished';
	
// Fetch Mailings for this Page
$mailings = Pommo_Mailing::get(array(
	'noBody' => TRUE,
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start,
	'forHistory' => true));

/**********************************
	OUTPUT FORMATTING
*********************************/

$records = array();
foreach($mailings as $o)
{
	//	If the mailing is not beeing tracked show "not tracked" instead of 0
	if (0 == $o['track'])
	{
		$o['hits'] = Pommo::_T('Not tracked');
	}

	$row = array(
		'id' => $o['id'],
		'subject' => $o['subject'],
		'group' => $o['group'].' ('.$o['tally'].')',
		'sent' => $o['sent'],
		'start' => $o['start'],
		'end' => $o['end'],
		'hits' => $o['hits']
	);
	
	if($o['status'] == 0)
		$o['status'] = Pommo::_T('Complete');
	elseif($o['status'] == 1)
		$o['status'] = Pommo::_T('Processing');
	else
		$o['status'] = Pommo::_T('Cancelled');
	$row['status'] = $o['status'];
	
	// calculate mails per hour
	if(!empty($o['end']) && !empty($o['sent'])) {
		$runtime = strtotime($o['end'])-strtotime($o['start']);
		$mph = ($runtime == 0)? $o['sent']*3600 : round(($o['sent'] / ($runtime)) * 3600);
	}
	else
		$mph = 0;
		
	$row['end'] .= '<br />'.$mph.' '.Pommo::_T('Mails/Hour');
	
	array_push($records,$row);
}

// format for JSON output to jqGrid
$json->add(array(
		'page' => $state['page'],
		'total' => $state['pages'],
		'records' => Pommo_Mailing::tally(),
		'rows' => $records
	)
);
$json->serve();
?>
