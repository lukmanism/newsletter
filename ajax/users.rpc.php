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

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// fetch the mailing IDs
$mailingIDS = $_REQUEST['mailings'];
if(!is_array($mailingIDS))
{
	$mailingIDS = array($mailingIDS);
}

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
$json = new Pommo_Json(false); // do not toggle escaping
	
// EXAMINE CALL
switch ($_REQUEST['call']) {
	case 'notice':
		foreach($mailingIDS as $id) {
			$logger->AddMsg('<br /><br />###'. sprintf(Pommo::_T('Displaying notices for mailing %s'),Pommo_Mailing::getSubject($id)).' ###<br /><br />');
			$notices = Pommo_Mailing::getNotices($id);	
			$logger->AddMsg($notices);
		}
	break;
	
	case 'reload' :
	
		require_once(Pommo::$_baseDir.'classes/Pommo_Groups.php');

		$mailing = current(Pommo_Mailing::get(array('id' => $_REQUEST['mailings'])));
		
		// change group name to ID
		$groups = Pommo_Groups::getNames();
		$gid = 'all';
		foreach($groups as $group) 
			if ($group['name'] == $mailing['group'])
				$gid = $group['id'];
		
		Pommo_Api::stateReset(array('mailing'));
		
		// if this is a plain text mailing, switch body + altbody.
		if($mailing['ishtml'] == 'off') {
			$mailing['altbody'] = $mailing['body'];
			$mailing['body'] = null;
		}
		
		// Initialize page state with default values overriden by those held in $_REQUEST
		$state =& Pommo_Api::stateInit('mailing',array(
			'fromname' => $mailing['fromname'],
			'fromemail' => $mailing['fromemail'],
			'frombounce' => $mailing['frombounce'],
			'list_charset' => $mailing['charset'],
			'mailgroup' => $gid,
			'subject' => $mailing['subject'],
			'body' => $mailing['body'],
			'altbody' => $mailing['altbody']
		));

		Pommo::redirect(Pommo::$_baseUrl.'mailings_start.php');
	break;
	
	case 'delete' :
		$currentUser = Pommo::$_auth->_username;

		//	We dont want to delete the current user
		$key = array_search($currentUser, $_GET['users']);
		if ($key !== false)
		{
			unset($_GET['users'][$key]);
		}

		require_once Pommo::$_baseDir.'classes/Pommo_User.php';
		$pu = new Pommo_User();
		$deleted = $pu->delete($_GET['users']);
		$logger->addMsg(Pommo::_T('Please Wait').'...');
		
		$params = $json->encode(array('users' => $_GET['users']));
		$view->assign('callbackFunction','deleteUser');
		$view->assign('callbackParams',$params);
	break;
	
	case 'add':
		require_once Pommo::$_baseDir.'classes/Pommo_User.php';
		$pu = new Pommo_User();
		if ($pu->save($_POST['user'], $_POST['password']))
		{
			echo $_POST['user'];
			return;
		}
		break;
	
	default:
		$logger->AddErr('invalid call');
	break;
}

$view->display('admin/rpc');
