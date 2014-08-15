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
require_once Pommo::$_baseDir.'classes/Pommo_Mailing.php';
require_once Pommo::$_baseDir.'classes/Pommo_Groups.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

if (Pommo_Mailing::isCurrent())
{
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the'
			.' %sStatus%s page to check its progress.'),
			'<a href="mailing_status.php">', '</a>'));
}

$dbvalues = Pommo_Api::configGet(array(
	'list_fromname',
	'list_fromemail',
	'list_frombounce',
	'list_charset',
	'list_wysiwyg'
));

// Initialize page state with default values overriden by those held in $_REQUEST
$state = Pommo_Api::stateInit('mailing',
		array(
			'fromname' => $dbvalues['list_fromname'],
			'fromemail' => $dbvalues['list_fromemail'],
			'frombounce' => $dbvalues['list_frombounce'],
			'list_charset' => $dbvalues['list_charset'],
			'wysiwyg' => $dbvalues['list_wysiwyg'],
			'mailgroup' => 'all',
			'subject' => '',
			'body' => '',
			'altbody' => '',
			'track' => '',
            'attachments' => ''
		),
		$_POST);

function check_charset($value, $empty, & $params, & $formvars) {
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
	// ___ USER HAS NOT SENT FORM ___
	$vMsg = array ();
	$vMsg['fromname'] = $vMsg['subject'] = Pommo::_T('Cannot be empty.');
	$vMsg['charset'] = Pommo::_T('Invalid Character Set');
	$vMsg['fromemail'] = $vMsg['frombounce'] = Pommo::_T('Invalid email address');
	$vMsg['ishtml'] = $vMsg['mailgroup'] = Pommo::_T('Invalid Input');
	$view->assign('vMsg', $vMsg);

}
else
{
	// ___ USER HAS SENT FORM ___

	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
	$json = new Pommo_Json();

	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('fromname', 'Other', false);
    $validator->addData('subject', 'Other', false);
    $validator->addData('fromemail', 'Email', false);
    $validator->addData('frombounce', 'Email', false);

	if ($result = $validator->checkData())
	{
		$json->success();
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

$mailgroups = explode( ',', $state['mailgroup'] );
$view->assign( 'mailgroups', $mailgroups );
$view->assign('groups', Pommo_Groups::get());
$view->assign($state);
$view->display('admin/mailings/mailing/setup');
