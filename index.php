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
Pommo::init(array('authLevel' => 0));
$logger = Pommo::$_logger;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();

$fatal_error = null;
// Check we have the mysql module installed
if (!extension_loaded('mysql')) {
    $fatal_error[] = "Php mysql module is not installed.";
}

// Check if module is installed
if (!extension_loaded('gettext')) {
    $fatal_error[] = "Php gettext module is not installed.";
}

//Check write permission to the cache directory
if (!is_writable(dirname(__FILE__).'/cache')) {
    $fatal_error[] = "The cache directory needs to be writable";
}

if ($fatal_error) {
    $view->assign('errors', $fatal_error);
    $view->display('message');
    exit();
}

//	log the user out if requested
if (isset($_GET['logout']))
{
	Pommo::$_auth->logout();
	header('Location: ' . Pommo::$_http . Pommo::$_baseUrl . 'index.php');
}

// 	check if user is already logged in
if (Pommo::$_hasConfigFile && Pommo::$_auth->isAuthenticated())
{
	// If user is authenticated (has logged in), redirect to admin.php
	Pommo::redirect(Pommo::$_http . Pommo::$_baseUrl . 'admin.php');
}
// 	Log in attempt. Authenticate.
elseif (isset($_POST['submit'])
		&& !empty($_POST['username'])
		&& !empty($_POST['password']))
{
	require_once Pommo::$_baseDir.'classes/Pommo_User.php';
	$user = new Pommo_user();
	if ($user->login($_POST['username'], $_POST['password']))
	{
		// don't perform maintenance if accessing support area
		if(!isset($_GET['referer'])
				|| !basename($_GET['referer']) == 'support.php')
		{
			// login success. Perform maintenance, set auth, redirect to referer
			require_once(Pommo::$_baseDir.'classes/Pommo_Helper_Maintenance.php');
			Pommo_Helper_Maintenance::perform();
		}

		Pommo::$_auth->login($_POST['username']);

		Pommo::redirect(Pommo::$_http.$_POST['referer']);
	}
	else
	{
		$logger->addMsg(Pommo::_T('Failed login attempt. Try again.'));
	}
}
elseif (!empty ($_POST['resetPassword']))
{
	// TODO -- visit this function later
	// Check if a reset password request has been received
	// check that captcha matched
	if (!isset($_POST['captcha']))
	{
		// generate captcha
		$captcha = substr(md5(rand()), 0, 4);

		$view->assign('captcha', $captcha);
	}
	elseif ($_POST['captcha'] == $_POST['realdeal'])
	{
		// user inputted captcha matched. Reset password
		require_once(Pommo::$_baseDir.'classes/Pommo_Pending.php');
		require_once(Pommo::$_baseDir.'classes/Pommo_Helper_Messages.php');

		// see if there is already a pending request for the administrator
		// [subscriber id == 0]
		if (Pommo_Pending::isPending(0))
		{
			$input = urlencode(serialize(array('adminID' => TRUE,
					'Email' => Pommo::$_config['admin_email'])));
			Pommo::redirect(Pommo::$_http . Pommo::$_baseUrl
					.'pending.php?input='.$input);
		}

		// create a password change request, send confirmation mail
		$subscriber = array('id' => 0);
		$code = Pommo_Pending::add($subscriber,'password');
		Pommo_Helper_Messages::sendMessage(
				array('to' => Pommo::$_config['admin_email'],
				'code' => $code, 'type' => 'password'));

		$view->assign('captcha',FALSE);
	}
	else
	{
		// captcha did not match
		$logger->addMsg(Pommo::_T('Captcha did not match. Try again.'));
	}
}
elseif (!Pommo::$_hasConfigFile && $_POST['configure'])
{
	//	Try to connect to database with data entered from the user.
	//	I am not using /inc/classes/db.php because it kills the proccess when
	//	connection is not possible
	//	TODO: db.php shouldnt kill the process
	$link = @mysql_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass']);
	if (!$link)
	{
		//	Could not connect
		$configErrors[]	= 'Could not connect to host. Check your settings
				and try again.';
	}
	else
	{
		if (!@mysql_select_db($_POST['dbname'], $link))
		{
			//	Database does not exist. Lets try to create it.
			if (!mysql_query('CREATE DATABASE '.$_POST['dbname'], $link))
			{
				$configErrors[]	= 'Database does not exist. And the provided
						user does not have the necessary permissions to create
						it. You will have to create it manually first.';
			}
		}
	}

	//	If there were no errors then try to create the file
	if (!$configErrors)
	{
		//	I am sure there must be better ways to do this, but this works
		// 	for now.
		//	TODO: If there is a better method change this, if not. Delete
		//			this line.
		$handle = @fopen('config.php', 'w');
		if (!$handle)
		{
			$configErrors[]	= 'Script was not able to create config.php
					file. You should assign write permission for this script
					to pommo root folder or create config.php yourself.';
		}
		else
		{
			$string = '<?php die(); /* DO NOT REMOVE THIS LINE! */ ?>'.
					PHP_EOL.PHP_EOL
					.'[db_hostname] = '.$_POST['dbhost'].PHP_EOL
					.'[db_username] = '.$_POST['dbuser'].PHP_EOL
					.'[db_password] = '.$_POST['dbpass'].PHP_EOL
					.'[db_database] = '.$_POST['dbname'].PHP_EOL
					.'[db_prefix] = pommo_'.PHP_EOL
					.PHP_EOL
					.'[lang] = en'.PHP_EOL
					.'[debug] = off'.PHP_EOL
					.'[verbosity] = 3'.PHP_EOL
					.'[date_format] = 1'.PHP_EOL;
			fwrite($handle, $string);
			fclose($handle);
			$redir = Pommo::$_baseUrl.'install.php';
			header('Location: '.$redir);
			exit();
		}
	}
}

if (Pommo::$_hasConfigFile)
{
	//	referer (used to return user to requested page upon login success)
	$view->assign('referer',
			(isset($_REQUEST['referer']) ?
			$_REQUEST['referer'] : Pommo::$_baseUrl.'admin.php'));

	$view->display('index');
}
else
{
	$view->assign('messages', $configErrors);
	$view->assign('dbhost', $_POST['dbhost']);
	$view->assign('dbname', $_POST['dbname']);
	$view->assign('dbuser', $_POST['dbuser']);
	$view->display('configure');
}
