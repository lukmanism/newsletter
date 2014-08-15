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
			'<a href="mailing_status.php">',
			'</a>'));
}

// TODO -- fix stateInit so we don't NEED to supply the defaults that have already been defined

if (isset($_REQUEST['compose']))
{
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
	$json = new Pommo_Json();
	$json->success();
}

$dbvalues = Pommo_Api::configGet(array(
	'list_fromname',
	'list_fromemail',
	'list_frombounce',
	'list_charset',
	'list_wysiwyg'
));

// Initialize page state with default values overriden by those held in $_REQUEST
$state = Pommo_Api::stateInit('mailing',array(
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

$view->assign($state);

// assign language (for wysiwyg)
$view->assign('lang',(Pommo::$_slanguage) ? Pommo::$_slanguage : Pommo::$_language);

$view->display('admin/mailings/mailing/compose');
