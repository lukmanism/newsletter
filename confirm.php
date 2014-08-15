<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// TODO -> Add auto firewalling [DOS protection] scripts here.. ie. if Bad/no code received by same IP 3 times, temp/perm ban. 
//  If page is being bombed/DOSed... temp shutdown. should all be handled inside @ _IS_VALID or fireup(); ..

/**********************************
	INITIALIZATION METHODS
*********************************/
require('bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Pending.php');

Pommo::init(array('authLevel' => 0, 'noSession' => true));
$logger = & Pommo::$_logger;
$dbo = & Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();

if (empty($_GET['code'])) {
	$logger->addMsg(Pommo::_T('No code given.'));
	$view->display('user/confirm');
	Pommo::kill();
}

// lookup code
$pending = Pommo_Pending::get($_GET['code']);

if (!$pending) {
	$logger->addMsg(Pommo::_T('Invalid code! Make sure you copied it correctly from the email.'));
	$view->display('user/confirm');
	Pommo::kill();
}

// Load success messages and redirection URL from config
$config = Pommo_Api::configGet(array (
	'site_success',
	'messages',
	'notices'
));
$messages = unserialize($config['messages']);
$notices = unserialize($config['notices']);

if(Pommo_Pending::perform($pending)) {
	
	require_once(Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php');
	
	// get subscriber info
	require_once(Pommo::$_baseDir.'classes/Pommo_Subscribers.php');
	$subscriber = current(Pommo_Subscribers::get(array('id' => $pending['subscriber_id'])));
			
	switch ($pending['type']) {
		case "add" :
			// send/print welcome message
			Pommo_Helper_Messages::sendMessage(array('to' => $subscriber['email'], 'type' => 'subscribe'));
		
			if (isset($notices['subscribe']) && $notices['subscribe'] == 'on') 
				Pommo_Helper_Messages::notify($notices, $subscriber, 'subscribe');
				
			if (!empty($config['site_success']))
				// Pommo::redirect($config['site_success']);
				Pommo::redirect('subscribe.php?action=success');
				
			break;
			
		case "change" :
		
			if (isset($notices['update']) && $notices['update'] == 'on')
				Pommo_Helper_Messages::notify($notices, $subscriber, 'update');
				
			$logger->addMsg(Pommo::_T('Your records have been updated.'));
			break;
		
		case "password" :
			break;
			
		default :
			$logger->addMsg('Unknown Pending Type.');
			break;
	}
}
$view->display('user/confirm');

