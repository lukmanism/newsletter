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
require ('bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Validate.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Subscribers.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Pending.php');

Pommo::init(array('authLevel' => 0,'noSession' => true));
$logger = & Pommo::$_logger;
$dbo = & Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();

// Prepare for subscriber form -- load in fields + POST/Saved Subscribe Form
$view->prepareForSubscribeForm();

// fetch the subscriber, validate code
$subscriber = current(Pommo_Subscribers::get(array('email' => (empty($_REQUEST['email'])) ? '0' : $_REQUEST['email'], 'status' => 1)));
if (empty($subscriber))
	Pommo::redirect('login.php');
if ($_REQUEST['code'] != Pommo_Subscribers::getActCode($subscriber))
	Pommo::kill(Pommo::_T('Invalid activation code.'));
	
// check if we have pending request
if (Pommo_Pending::isPending($subscriber['id'])) {
	$input = urlencode(serialize(array('Email' => $_POST['Email'])));
	Pommo::redirect('pending.php?input='.$input);
}


$config = Pommo_Api::configGet(array('notices'));
$notices = unserialize($config['notices']);

if (!isset($_POST['d']))
	$view->assign('d', $subscriber['data']);

// check for an update + validate new subscriber info (also converts dates to ints)
if (!empty ($_POST['update']) && Pommo_Validate::subscriberData($_POST['d'])) {
	
	$newsub = array(
		'id' => $subscriber['id'],
		'email' => $subscriber['email'],
		'data' => $_POST['d']
	);
	
	if (!empty($_POST['newemail'])) { // if change in email, validate and send confirmation of update
		if ($_POST['newemail'] != $_POST['newemail2']) 
			$logger->addErr(Pommo::_T('Emails must match.'));
		elseif (!Pommo_Helper::isEmail($_POST['newemail']))
			$logger->addErr(Pommo::_T('Invalid Email Address'));
		elseif (Pommo_Helper::isDupe($_POST['newemail']))
			$logger->addMsg(Pommo::_T('Email address already exists. Duplicates are not allowed.'));	
		else {
			$newsub['email'] = $_POST['newemail'];
			$code = Pommo_Pending::add($newsub, 'change');
			if(!$code)
				die('Failed to Generate Pending Subscriber Code');
			require_once(Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php');
			Pommo_Helper_Messages::sendMessage(array('to' => $newsub['email'], 'code' => $code, 'type' => 'update'));
			
			if (isset($notices['update']) && $notices['update'] == 'on')
				Pommo_Helper_Messages::notify($notices, $newsub, 'update');
		}		
	}
	// else if NO change in email, update subscriber
	elseif (!Pommo_Subscribers::update($newsub, 'REPLACE_ACTIVE')) 
		$logger->addErr('Error updating subscriber.');
	else { // update successful
		$logger->addMsg(Pommo::_T('Your records have been updated.'));
		require_once(Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php');
		if (isset($notices['update']) && $notices['update'] == 'on')
			Pommo_Helper_Messages::notify($notices, $newsub, 'update');	
	}
}
// check if an unsubscribe was requested
elseif (!empty ($_POST['unsubscribe'])) {
	
	$comments = (isset($_POST['comments'])) ? substr($_POST['comments'],0,255) : false;
	
	$newsub = array(
		'id' => $subscriber['id'],
		'status' => 0,
		'data' => array()
	);
	if (!Pommo_Subscribers::update($newsub))
		$logger->addErr('Error updating subscriber.');
	else {
		$dbvalues = Pommo_Api::configGet(array('messages'));
		$messages = unserialize($dbvalues['messages']);
		
		require_once(Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php');
		
		// send unsubscription email / print unsubscription message
		Pommo_Helper_Messages::sendMessage(array('to' => $subscriber['email'], 'type' => 'unsubscribe'));
		
		if ($comments || isset($notices['unsubscribe']) && $notices['unsubscribe'] == 'on') 
			Pommo_Helper_Messages::notify($notices, $subscriber, 'unsubscribe',$comments);
		
		$view->assign('unsubscribe', TRUE);
	}
		?>
        <script type="text/javascript">
		<!--
		window.location = "http://ayam.com/newsletter/update/confirmed-unsubscribe.html"
		//-->
		</script>

        <?php
}

$view->assign('email',$subscriber['email']);
$view->assign('code',$_REQUEST['code']);
$view->display('user/update');

