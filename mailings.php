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

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Mailing.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Subscribers.php');

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();
$view->assign('title', Pommo::$_config['site_name'] . ' - ' . Pommo::_T('Mailing History'));

$config = Pommo_Api::configGet('public_history');
if ($config['public_history'] == 'on') {
	Pommo::init(array('authLevel' => 0));
} else {
    // Show message to the guest telling them that public mailings is not on
    $view->display('public_mailings_disabled');
    Pommo::kill();
}
$logger = & Pommo::$_logger;
$dbo = & Pommo::$_dbo;

/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, started]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& Pommo_Api::stateInit('mailings_history',array(
	'limit' => 100,
	'sort' => 'finished',
	'order' => 'asc',
	'page' => 1),
	$_REQUEST);

// if mail_id is passed, display the mailing.
if(isset($_GET['mail_id']) && is_numeric($_GET['mail_id'])) {
	$input = current(Pommo_Mailing::get(array('id' => $_GET['mail_id'])));

	// attempt personalizations
	if(isset($_GET['email']) && isset($_GET['code'])) {
		$subscriber = current(Pommo_Subscribers::get(array('email' => $_GET['email'], 'status' => 1)));
		if($_GET['code'] == Pommo_Subscribers::getActCode($subscriber)) {
			require_once(Pommo::$_baseDir.'classes/Pommo_Helper_Personalize.php'); // require once here so that mailer can use

			$matches = array();
			preg_match('/\[\[[^\]]+]]/', $input['body'], $matches);
			if (!empty($matches)) {
				$pBody = Pommo_Helper_Personalize::search($input['body']);
				$input['body'] = Pommo_Helper_Personalize::replace($input['body'], $subscriber, $pBody);

			}
			preg_match('/\[\[[^\]]+]]/',  $input['altbody'], $matches);
			if (!empty($matches)) {
				$pAltBody = Pommo_Helper_Personalize::search($input['altbody']);
				$input['altbody'] = Pommo_Helper_Personalize::replace($input['altbody'], $subscriber, $pAltBody);
			}
		}

	}

	$view->assign($input);
	$view->display('inc/mailing');
	Pommo::kill();
}


/**********************************
	VALIDATION ROUTINES
*********************************/

if(!is_numeric($state['limit']) || $state['limit'] < 10 || $state['limit'] > 200)
	$state['limit'] = 100;

if($state['order'] != 'asc' && $state['order'] != 'desc')
	$state['order'] = 'asc';

if($state['sort'] != 'start' &&
	$state['sort'] != 'subject')
		$state['sort'] = 'start';


/**********************************
	DISPLAY METHODS
*********************************/

// Calculate and Remember number of pages
$tally = Pommo_Mailing::tally();
$state['pages'] = (is_numeric($tally) && $tally > 0) ?
	ceil($tally/$state['limit']) :
	0;

$view->assign('state',$state);
$view->assign('tally',$tally);
$view->assign('mailings', $mailings);

$view->display('user/mailings');
