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
require_once Pommo::$_baseDir.'classes/Pommo_Mailing.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();
$view->assign('returnStr', Pommo::_T('Mailings Page'));

/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, mailgroup, subscriberCount, started, etc.]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& Pommo_Api::stateInit('mailings_history',array(
	'limit' => 10,
	'sort' => 'end',
	'order' => 'desc',
	'page' => 1),
	$_REQUEST);
	
/**********************************
	VALIDATION ROUTINES
*********************************/
	
if (!is_numeric($state['limit']) || $state['limit'] < 1 || $state['limit'] > 1000)
{
	$state['limit'] = 10;
}
	
if ($state['order'] != 'asc' && $state['order'] != 'desc')
{
	$state['order'] = 'asc';
}
	
if ($state['sort'] != 'start' &&
	$state['sort'] != 'end' &&
	$state['sort'] != 'subject' &&
	$state['sort'] != 'sent' &&
	$state['sort'] != 'status' &&
	$state['sort'] != 'group')
{
	$state['sort'] = 'end';
}
		
		
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

$view->display('admin/mailings/mailings_history');

