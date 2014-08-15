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
require_once Pommo::$_baseDir.'classes/Pommo_Groups.php';
require_once Pommo::$_baseDir.'classes/Pommo_Mail_Ctl.php';
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
			'<a href="mailing_status.php">','</a>'));
}

// TODO -- fix stateInit so we don't NEED to supply the defaults that have already been defined

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

$state['charset'] = $state['list_charset'];

// validate composition
$tempbody = trim($state['body']);
$tempalt = trim($state['altbody']);
if (empty($tempbody) && empty($tempalt) || empty($state['subject']))
{
	$logger->addErr(Pommo::_T('Subject or Message cannot be empty!'));
	$view->assign($state);
	$view->display('admin/mailings/mailing/preview');
	Pommo::kill();
}

// get the groups
$groups = explode( ',', $state['mailgroup'] );
$memberIDs = array();
$names = array();
$tally = 0;
foreach( $groups as $group ) {
	$pgroup = new Pommo_Groups($group, 1);
	$names []= $pgroup->_name;
	if ( is_array( $pgroup->_memberIDs ) ) {
		$memberIDs = array_merge( $memberIDs, $pgroup->_memberIDs );
	}
	$tally += $pgroup->_tally;
}

//If a user is in more than one group we don't want to send them the same email twice
$memberIDs = array_unique( $memberIDs );
$num_members = sizeof( $memberIDs );

//If the size of $num_members is greater than 0 then we're not sending to 'All Subscribers'
$state['tally'] = $num_members ? $num_members : $tally;
$state['group'] = implode( ', ', $names );


// determine html status
$state['ishtml'] = (empty($tempbody))? 'off' : 'on';


// processs send request
if (!empty ($_REQUEST['sendaway']))
{
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
	$json = new Pommo_Json();

	if ($state['tally'] > 0)
	{
		if ($state['ishtml'] == 'off')
		{
			$state['body'] = $state['altbody'];
			$state['altbody'] = '';
		}
		$mailing = Pommo_Mailing::make(array(), TRUE);

		$state['status'] = 1;
		$state['current_status'] = 'stopped';
		$state['command'] = 'restart';
		$mailing = Pommo_Helper::arrayIntersect($state, $mailing);

		$code = Pommo_Mailing::add($mailing);

		if (!Pommo_Mail_Ctl::queueMake($memberIDs))
		{
			$json->fail('Unable to populate queue');
		}

		if (!Pommo_Mail_Ctl::spawn(
				Pommo::$_baseUrl.'ajax/mailings_send4.php?code='.$code))
		{
			$json->fail('Unable to spawn background mailer');
		}

		// clear mailing composistion data from session
		Pommo_Api::stateReset(array('mailing'));
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',Pommo::$_baseUrl.'mailing_status.php');

	}
	else
	{
		$json->fail(Pommo::_T('Cannot send a mailing to 0 subscribers!'));
	}
	$json->serve();
}

$view->assign($state);
$view->display('admin/mailings/mailing/preview');
