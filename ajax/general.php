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

if (empty ($_POST))
{	
	// no validation for exchanger
	$vMsg = array();
	$vMsg['site_url'] = $vMsg['site_success'] = $vMsg['site_confirm'] = Pommo::_T('Must be a valid URL');
	$vMsg['list_name'] = $vMsg['site_name'] = Pommo::_T('Cannot be empty.');
	$view->assign('vMsg', $vMsg);
	
	// populate _POST with info from database (fills in form values...)
	$dbVals = Pommo_Api::configGet(array (
		'site_success',
		'site_confirm',
		'list_exchanger',
		'list_confirm'
	));
	$dbVals['site_url'] = Pommo::$_config['site_url'];
	$dbVals['site_name'] = Pommo::$_config['site_name'];
	$dbVals['list_name'] = Pommo::$_config['list_name'];
	
	$view->assign($dbVals);
}
else
{
	// ___ USER HAS SENT FORM ___
	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('list_name', 'Other', false);
    $validator->addData('site_name', 'Other', false);
    $validator->addData('site_url', 'Url', false);
    $validator->addData('site_success', 'Url', true);
	$validator->addData('site_confirm', 'Url', true);
	$validator->addData('list_confirm', 'matchRegex', false, '!^(on|off)$!');
	$validator->addData('list_exchanger', 'matchRegex', false,
			'!^(sendmail|mail|smtp)$!');
	
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
	$json = new Pommo_Json();

	if ($result = $validator->checkData())
	{
		// __ FORM IS VALID
		Pommo_Api::configUpdate($_POST);
		Pommo::reloadConfig();

		$json->success(Pommo::_T('Configuration Updated.'));
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
		$json->fail(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$view->assign($_POST);
$view->display('admin/setup/config/general');
