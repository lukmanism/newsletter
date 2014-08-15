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

// ADD CUSTOM VALIDATOR FOR CHARSET
function check_charset($value, $empty, & $params, & $formvars)
{
	$validCharsets = array (
		'UTF-8',
		'ISO-8859-1',
		'ISO-8859-2',
		'ISO-8859-7',
		'ISO-8859-15',
		'cp1251',
		'KOI8-R',
		'GB2312',
		'EUC-JP',
		'ISO-2022-JP'
	);
	return in_array($value, $validCharsets);
}

if (empty ($_POST))
{
	$vMsg = array();
	$vMsg['maxRuntime'] = _('Enter a number.');
	$vMsg['list_fromname'] = _('Cannot be empty.');
	$vMsg['list_fromemail'] = $vMsg['list_frombounce'] = _('Invalid email address');
	$view->assign('vMsg', $vMsg);

	// populate _POST with info from database (fills in form values...)
	$dbVals = Pommo_Api::configGet(array (
		'list_fromname',
		'list_fromemail',
		'list_frombounce',
		'list_charset',
		'public_history',
		'maxRuntime'
	));
	$dbVals['demo_mode'] = (!empty (Pommo::$_config['demo_mode'])
			&& (Pommo::$_config['demo_mode'] == "on")) ? 'on' : 'off';
	$view->assign($dbVals);
}
else
{
	// ___ USER HAS SENT FORM ___
	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('list_fromname', 'Other', false);
    $validator->addData('list_fromemail', 'Email', false);
    $validator->addData('list_frombounce', 'Email', false);
    $validator->addData('public_history', 'matchRegex', false, '!^(on|off)$!');
    $validator->addData('demo_mode', 'matchRegex', false, '!^(on|off)$!');
    $validator->addData('list_fromname', 'Other', false);

	$_POST['maxRunTime'] = (int)$_POST['maxRunTime'];

	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
	$json = new Pommo_Json();
	
	if ($result = $validator->checkData())
	{
		// __ FORM IS VALID
		Pommo_Api::configUpdate($_POST);
		Pommo::reloadConfig();

		$json->success(_('Configuration Updated.'));
	}
	else
	{
		// __ FORM NOT VALID
		$fieldErrors = array();
		$errors = $validator->getErrors();
		foreach ($errors as $key => $val)
		{
			$fieldErrors[] = array (
				'field' => $key,
				'message' => $val
			);
		}
		$json->add('fieldErrors', $fieldErrors);
		$json->fail(_('Please review and correct errors with your submission.'));
	}
}

$view->assign($_POST);
$view->display('admin/setup/config/mailings');

