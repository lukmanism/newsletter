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
require_once Pommo::$_baseDir.'classes/Pommo_Pending.php';

Pommo::init(array('authLevel' => 0, 'noSession' => true));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

session_start();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();
$view->assign('title', Pommo::$_config['site_name'] . ' - ' . Pommo::_T('subscriber logon'));

if (empty($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	// Assign email to form if pre-provided
	if (isset($_REQUEST['Email']))
		$view->assign('Email',$_REQUEST['Email']);
	elseif (isset($_REQUEST['email']))
		$view->assign('Email',$_REQUEST['email']);
		
} else {
	// ___ USER HAS SENT FORM ___
	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('Email', 'Email', false);

	if ($result = $validator->checkData())
	{
		if (Pommo_Helper::isDupe($_POST['Email']))
		{
			if (Pommo_Pending::isEmailPending($_POST['Email'])) {
				$input = urlencode(serialize(array('Email' => $_POST['Email'])));
				Pommo::redirect('pending.php?input='.$input);
			}
			else {
				// __ EMAIL IN SUBSCRIBERS TABLE, REDIRECT
				Pommo::redirect('activate.php?email='.$_POST['Email']);
			}
		}
		else
		{
			// __ REPORT STATUS
			$logger->addMsg(Pommo::_T('Email address not found! Please try again.'));
			$logger->addMsg(sprintf(Pommo::_T('To subscribe, %sclick here%s'),'<a href="'.Pommo::$_baseUrl.'subscribe.php?Email='.$_POST['Email'].'">','</a>'));
		}
	}

	$view->assign($_POST);
}

$view->display('user/login');

