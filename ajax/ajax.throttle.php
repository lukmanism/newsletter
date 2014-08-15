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
require '../bootstrap.php';
Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// Read user requested changes	
if (!empty($_POST['throttle_restore']))
{
	$input = array (
		'throttle_MPS' => 3,
		'throttle_BPS' => 0,
		'throttle_DP' => 10,
		'throttle_DBPP' => 0,
		'throttle_DMPP' => 0
	);
	Pommo_Api::configUpdate($input, TRUE);
	$view->assign('output', _('Configuration Updated.'));
}
elseif (!empty($_POST['throttle-submit']))
{
	$input = array();
	
	$input['throttle_MPS'] = (is_numeric($_POST['mps'])
			&& $_POST['mps'] >= 0
			&& $_POST['mps'] <= 5)
			? $_POST['mps'] : 3;
	
	$input['throttle_BPS'] = (is_numeric($_POST['bps'])
			&& $_POST['bps'] >= 0
			&& $_POST['bps'] <= 400)
			? $_POST['bps']*1024 : 0;
		
	$input['throttle_DP'] = (is_numeric($_POST['dp'])
			&& $_POST['dp'] >= 5
			&& $_POST['dp'] <= 20)
			? $_POST['dp'] : 10;
		
	$input['throttle_DMPP'] = (is_numeric($_POST['dmpp'])
			&& $_POST['dmpp'] >= 0
			&& $_POST['dmpp'] <= 5)
			? $_POST['dmpp'] : 0;

	$input['throttle_DBPP'] = (is_numeric($_POST['dbpp'])
			&& $_POST['dbpp'] >= 0
			&& $_POST['dbpp'] <= 200)
			? $_POST['dbpp']*1024 : 0;

	if (!empty($input))
	{
		Pommo_Api::configUpdate($input, TRUE);
		$view->assign('output', _('Configuration Updated.'));
	}
	else
	{
		$view->assign('output',
				_('Please review and correct errors with your submission.'));
	}
}

$config= Pommo_Api::configGet(array(
	'throttle_MPS',
	'throttle_BPS',
	'throttle_DP',
	'throttle_DBPP',
	'throttle_DMPP'
));

$view->assign('mps', $config['throttle_MPS'] * 60);
$view->assign('bps', $config['throttle_BPS'] / 1024);
$view->assign('dp', $config['throttle_DP']);
$view->assign('dmpp', $config['throttle_DMPP']);
$view->assign('dbpp', $config['throttle_DBPP'] / 1024);

$view->display('admin/setup/config/ajax.throttle');

