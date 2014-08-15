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
require_once Pommo::$_baseDir.'classes/Pommo_Subscribers.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Helper_Messages.php';

Pommo::init(array('authLevel' => 0,'noSession' => true));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// make sure email/login is valid
$subscriber = current(Pommo_Subscribers::get(array (
	'email' => (empty($_REQUEST['email'])) ? '0' : $_REQUEST['email'], 'status' => 1
)));

if (empty($subscriber))
{
	Pommo::redirect('login.php');
}

// see if an activation email was sent to this subscriber in the last 2 minutes;
$query =
	'SELECT 
		*
	FROM 
		'.$dbo->table['scratch']."
	WHERE
		`type`=1
		AND `int`=%i
		AND `time` > (NOW() - INTERVAL 2 MINUTE)
	LIMIT 1";
$query = $dbo->prepare($query,array($subscriber['id']));
$test = $dbo->query($query,0);

// attempt to send activation code if once has not recently been sent
if (empty($test))
{
	$code = Pommo_Subscribers::getActCode($subscriber);
	if (Pommo_Helper_Messages::sendMessage(array (
			'to' => $subscriber['email'],
			'code' => $code,
			'type' => 'activate')))
	{
		$view->assign('sent', true);
		
		// timestamp this activation email
		$query = "
			INSERT INTO ".$dbo->table['scratch']."
			SET
				`type`=1,
				`int`=%i";
		$query = $dbo->prepare($query,array($subscriber['id']));
		$dbo->query($query);
		
		// remove ALL activation email timestamps older than 2 minutes
		$query = "
			DELETE FROM 
				".$dbo->table['scratch']."
			WHERE
				`type`=1
				AND `time` < (NOW() - INTERVAL 2 MINUTE)";
		$query = $dbo->prepare($query,array());
		$dbo->query($query);
	}
}
else {
	$view->assign('sent', false);
}


$view->assign('email', $subscriber['email']);
$view->display('user/activate');

