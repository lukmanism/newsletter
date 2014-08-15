<?php
/**
 *  Original Code Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 *  released originally under GPLV2
 * 
 *  This file is part of poMMo.
 *
 *  poMMo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  poMMo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Pommo.  If not, see <http://www.gnu.org/licenses/>.
 * 
 *  This fork is from https://github.com/soonick/poMMo
 *  Please see docs/contribs for Contributors
 *
 */

 /**********************************
	INITIALIZATION METHODS
 *********************************/
require 'bootstrap.php';
require_once Pommo::$_baseDir.'classes/Pommo_Csv_Stream.php';
require_once Pommo::$_baseDir.'classes/Pommo_Subscribers.php';
require_once Pommo::$_baseDir.'classes/Pommo_Fields.php';

Pommo::init(array ('keep' => TRUE));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

$emails = Pommo::get('emails');
$dupes = Pommo::get('dupes');
$fields = Pommo_Fields::get();
$flag = FALSE;

foreach ($fields as $field)
{
	if ($field['required'] == 'on')
	{
		$flag = TRUE;
	}
}
		
if(isset($_GET['continue'])) {
	foreach($emails as $email) {
		$subscriber = array(
			'email' => $email,
			'registered' => time(),
			'ip' => $_SERVER['REMOTE_ADDR'],
			'status' => 1,
			'data' => array());
		if($flag)
			$subscriber['flag'] = 9;
		
		if(!Pommo_Subscribers::add($subscriber))
			die('Error importing subscriber');
	}

	sleep(1);
	die(Pommo::_T('Complete!').' <a href="subscribers_import.php">'.Pommo::_T('Return to').' '.Pommo::_T('Import').'</a>');
}

$view->assign('flag',$flag);
$view->assign('tally',count($emails));
$view->assign('dupes',$dupes);

$view->display('admin/subscribers/import_txt');

