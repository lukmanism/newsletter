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
require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
require_once Pommo::$_baseDir.'classes/Pommo_Subscribers.php';

Pommo::init(array('authLevel' => 0,'noSession' => true));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// attempt to detect if referer was set 
//  TODO; enable HTTP_REFERER after stripping out ?input= tags. These will continually repeat
//$referer = (!empty($_POST['bmReferer'])) ? $_POST['bmReferer'] : $_SERVER['HTTP_REFERER'];
$referer = (!empty($_POST['bmReferer'])) ? $_POST['bmReferer'] : Pommo::$_http.Pommo::$_baseUrl.'subscribe.php';

// append stored input
$view->assign('referer',$referer.'?input='.urlencode(serialize($_POST)));

/**********************************
	VALIDATE INPUT
 *********************************/

if (empty ($_POST['pommo_signup']))
	// Pommo::redirect('login.php');

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 1,
	'data' => @$_POST['d'],
);

// ** check for correct email syntax
if (!Pommo_Helper::isEmail($subscriber['email']))
	$logger->addErr(Pommo::_T('Invalid Email Address'));
		
// ** check if email already exists in DB ("duplicates are bad..")
if (Pommo_Helper::isDupe($subscriber['email'])) {
	Pommo::redirect('subscribe.php?action=duplicate');
	// $logger->addErr(Pommo::_T('Email address already exists. Duplicates are not allowed.'));
	// $view->assign('dupe', TRUE);
}

// check if errors exist with data, if so print results and die.
if ($logger->isErr() || !Pommo_Validate::subscriberData($subscriber['data'], array(
	'active' => FALSE))) {
	Pommo::redirect('subscribe.php?action=incomplete');
	// $view->assign('back', TRUE);
	// $view->display('user/process');
	Pommo::kill();
}

$comments = (isset($_POST['comments'])) ? substr($_POST['comments'],0,255) : false;

/**********************************
	ADD SUBSCRIBER
 *********************************/
 
$config = Pommo_Api::configGet(array (
	'site_success', // URL to redirect to on success, null is us (default)
	'site_confirm', // URL users will see upon subscription attempt, null is us (default)
	'list_confirm', // Requires email confirmation
	'notices'
));
$notices = unserialize($config['notices']);
require_once(Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php');

if ($config['list_confirm'] == 'on') { // email confirmation required. 
	// add user as "pending"
	
	$subscriber['pending_code'] = Pommo_Helper::makeCode();
	$subscriber['pending_type'] = 'add';
	$subscriber['status'] = 2;
	
	$id = Pommo_Subscribers::add($subscriber);
	if (!$id) {
		$logger->addErr('Error adding subscriber! Please contact the administrator.');
		// $view->assign('back', TRUE);
	}
	else {
		
		$logger->addMsg(Pommo::_T('Subscription request received.'));
		
		// send confirmation message.
		if (Pommo_Helper_Messages::sendMessage(array('to' => $subscriber['email'], 'code' => $subscriber['pending_code'], 'type' => 'confirm'))) {
			$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
			if ($comments || isset($notices['pending']) && $notices['pending'] == 'on')
				Pommo_Helper_Messages::notify($notices, $subscriber, 'pending', $comments);
			
				Pommo::redirect('confirmed-subscribe.html');
			// if ($config['site_confirm'])
			// 	Pommo::redirect($config['site_confirm']);
		}
		else {
			// $view->assign('back', TRUE);
			// delete the subscriber
			Pommo_Subscribers::delete($id);
		}
	}
}
else { // no email confirmation required
	if (!Pommo_Subscribers::add($subscriber)) {
		$logger->addErr('Error adding subscriber! Please contact the administrator.');
		// $view->assign('back', TRUE);
	}
	else {
		
		// send/print welcome message
		Pommo_Helper_Messages::sendMessage(array('to' => $subscriber['email'], 'type' => 'subscribe'));
	
		$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
		if ($comments || isset($notices['subscribe']) && $notices['subscribe'] == 'on')
			Pommo_Helper_Messages::notify($notices, $subscriber, 'subscribe',$comments);
		
		// redirect
		if ($config['site_success'])
			Pommo::redirect('subscribe.php?action=success');
			// Pommo::redirect($config['site_success']);
	}
	
}
$view->display('process');

