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
require('../bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Subscribers.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Fields.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Groups.php');

Pommo::init();
$logger = & Pommo::$_logger;
$dbo = & Pommo::$_dbo;

Pommo::toggleEscaping(TRUE);

$state 	= Pommo_API::stateInit('subscribers_manage');
$fields = Pommo_Fields::get();
	

$ids = FALSE;
if (!empty($_POST['ids']))
{
	$ids = explode(',',$_POST['ids']);
}

// ====== CSV EXPORT ======
if ($_GET['type'] == 'csv')
{
	if (!$ids)
	{
		$group = new Pommo_Groups($state['group'], $state['status']);
		$subscribers = $group->members();
	}
	else 
		$subscribers = Pommo_Subscribers::get(array('id' => $ids));
	
	// supply headers
	$o = '"'.Pommo::_T('Email').'"';
	if(!empty($_POST['registered']))
		$o .= ',"'.Pommo::_T('Date Registered').'"';
	if(!empty($_POST['ip']))
		$o .= ',"'.Pommo::_T('IP Address').'"';
	foreach($fields as $f)
		$o.=",\"{$f['name']}\"";
	$o .= "\r\n";
	
	function csvWrap(&$in) {
		$in = '"'.addslashes($in).'"';
		return;
	}
	foreach($subscribers as $sub) {
		$d = array();
		
		// normalize field order in export
		foreach(array_keys($fields) as $id)
			if(array_key_exists($id,$sub['data']))
				$d[$id] = $sub['data'][$id];
			else
				$d[$id] = null;
		
		$s = array($sub['email']);
		if(!empty($_POST['registered']))
			$s[] = $sub['registered'];
		if(!empty($_POST['ip']))
			$s[] = $sub['ip'];
		
		array_walk($d, 'csvWrap');
		array_walk($s, 'csvWrap');
		
		$a = array_merge($s,$d);
		$o .= implode(',',$a)."\r\n";
	}
	
	$size_in_bytes = strlen($o);
	header("Content-disposition:  attachment; filename=poMMo_".Pommo::_T('Subscribers').".csv; size=$size_in_bytes");
	print $o;
	
	die();
}

// ====== TXT EXPORT ======

if (!$ids) {
	$group = new Pommo_Groups($state['group'], $state['status']);
	$ids =& $group->_memberIDs; 	
}

$emails = Pommo_Subscribers::getEmail(array('id' => $ids));

$o = '';
foreach($emails as $e)
	$o .= "$e\r\n";
	
$size_in_bytes = strlen($o);
header("Content-disposition:  attachment; filename=poMMo_".Pommo::_T('Subscribers').".txt; size=$size_in_bytes");
print $o;
die();

