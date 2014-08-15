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

$exchanger = current(Pommo_Api::configGet(array ('list_exchanger')));

if (empty ($_POST))
{
	// ___ USER HAS NOT SENT FORM ___
	$vMsg = array();
	$vMsg['email'] = Pommo::_T('Invalid email address');
	$view->assign('vMsg', $vMsg);
	
	$dbvals = array('exchanger' => $exchanger, 'email' => Pommo::$_config['admin_email']);
	$view->assign($dbvals);
	
}
else
{
	// ___ USER HAS SENT FORM ___
	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('email', 'Email', false);

	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
	$json = new Pommo_Json();

	if ($result = $validator->checkData())
	{
		// __ FORM IS VALID
		require_once(Pommo::$_baseDir.'classes/Pommo_Helper_Messages.php');

		$msg = Pommo_Helper_Messages::testExchanger($_POST['email'], $exchanger)
				? Pommo::_T('Mail Sent.')
				: Pommo::_T('Error Sending Mail');

		$json->success($msg);
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
$view->display('admin/setup/config/ajax.testexchanger');
