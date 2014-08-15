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
require_once Pommo::$_baseDir.'classes/Pommo_Helper_Messages.php';
Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
$json = new Pommo_Json();

// Check if user requested to restore defaults
if (isset($_POST['restore']))
{
	switch (key($_POST['restore']))
	{
		case 'subscribe':
			$messages = Pommo_Helper_Messages::ResetDefault('subscribe');
			break;
		case 'activate':
			$messages = Pommo_Helper_Messages::resetDefault('activate');
			break;
		case 'unsubscribe':
			$messages = Pommo_Helper_Messages::resetDefault('unsubscribe');
			break;
		case 'confirm':
			$messages = Pommo_Helper_Messages::resetDefault('confirm');
			break;
		case 'update':
			$messages = Pommo_Helper_Messages::resetDefault('update');
			break;
	}
	// reset _POST.
	$_POST = array();

	$json->add('callbackFunction','redirect');
	$json->add('callbackParams',Pommo::$_baseUrl.'setup_configure.php?tab=Messages');
	$json->serve();
}

// ADD CUSTOM VALIDATOR FOR CHARSET
function check_notifyMails($value, $empty, & $params, & $formvars)
{
	$mails = Pommo_Helper::trimArray(explode(',',$value));
	$ret = true;
	foreach ($mails as $mail)
	{
		if (!empty($mail) && !Pommo_Helper::isEmail($mail))
		{
			$ret = false;
		}
	}
	return $ret;
}

if (empty ($_POST))
{
	// ___ USER HAS NOT SENT FORM ___
	$vMsg = array();
	$vMsg['subscribe_sub'] =
	$vMsg['subscribe_msg'] =
	$vMsg['subscribe_web'] =
	$vMsg['unsubscribe_sub'] = 
	$vMsg['unsubscribe_msg'] =
	$vMsg['unsubscribe_web'] = 
	$vMsg['confirm_sub'] = 
	$vMsg['update_sub'] = 
	$vMsg['activate_sub'] = _('Cannot be empty.');

	$vMsg['confirm_msg'] = 
	$vMsg['update_msg'] = 
	$vMsg['activate_msg'] = _('You must include "[[URL]]" for the confirm link');

	$view->assign('vMsg', $vMsg);

	// populate _POST with info from database (fills in form values...)
	$dbvalues = Pommo_Api::configGet(array(
		'messages',
		'notices'));

	$notices = unserialize($dbvalues['notices']);
	$messages = unserialize($dbvalues['messages']);

	if (empty($messages))
	{
		$messages = Pommo_Helper_Messages::resetDefault('all');
	}
	
	if (empty($notices))
	{
		$notices = array(
			'email' => Pommo::$_config['admin_email'],
			'subject' => _('[poMMo Notice]'),
			'subscribe' => 'off',
			'unsubscribe' => 'off',
			'update' => 'off',
			'pending' => 'off');
	}

	$p = array();	
	$p['notify_email'] = $notices['email'];
	$p['notify_subject'] = $notices['subject'];
	$p['notify_subscribe'] = $notices['subscribe'];
	$p['notify_unsubscribe'] = $notices['unsubscribe'];
	$p['notify_update'] = $notices['update'];
	$p['notify_pending'] = $notices['pending'];

	$p['subscribe_sub'] = $messages['subscribe']['sub'];
	$p['subscribe_msg'] = $messages['subscribe']['msg'];
	$p['subscribe_web'] = $messages['subscribe']['web'];
	$p['subscribe_email'] = $messages['subscribe']['email'];

	$p['unsubscribe_sub'] = $messages['unsubscribe']['sub'];
	$p['unsubscribe_msg'] = $messages['unsubscribe']['msg'];
	$p['unsubscribe_web'] = $messages['unsubscribe']['web'];
	$p['unsubscribe_email'] = $messages['unsubscribe']['email'];

	$p['confirm_sub'] = $messages['confirm']['sub'];
	$p['confirm_msg'] = $messages['confirm']['msg'];

	$p['activate_sub'] = $messages['activate']['sub'];
	$p['activate_msg'] = $messages['activate']['msg'];

	$p['update_sub'] = $messages['update']['sub'];
	$p['update_msg'] = $messages['update']['msg'];

	$view->assign($p);
}
else
{
	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('subscribe_sub', 'Other', false);
    $validator->addData('subscribe_msg', 'Other', false);
    $validator->addData('subscribe_web', 'Other', false);
    $validator->addData('unsubscribe_sub', 'Other', false);
    $validator->addData('unsubscribe_msg', 'Other', false);
    $validator->addData('unsubscribe_web', 'Other', false);
    $validator->addData('confirm_sub', 'Other', false);
	$validator->addData('confirm_msg', 'matchRegex', false, '!\[\[URL\]\]!i');
	$validator->addData('activate_sub', 'Other', false);
	$validator->addData('activate_msg', 'matchRegex', false, '!\[\[URL\]\]!i');
	$validator->addData('update_sub', 'Other', false);
	$validator->addData('update_msg', 'matchRegex', false, '!\[\[URL\]\]!i');
	$validator->addData('notify_email', 'Email', false);
	$validator->addData('notify_subscribe', 'matchRegex', false, '!^(on|off)$!');
	$validator->addData('notify_unsubscribe', 'matchRegex', false, '!^(on|off)$!');
	$validator->addData('notify_update', 'matchRegex', false, '!^(on|off)$!');
	$validator->addData('notify_pending', 'matchRegex', false, '!^(on|off)$!');

	// ___ USER HAS SENT FORM ___
	if ($result = $validator->checkData())
	{
		// __ FORM IS VALID
		$messages = array();

		$messages['subscribe'] = array();
		$messages['subscribe']['sub'] = $_POST['subscribe_sub'];
		$messages['subscribe']['msg'] = $_POST['subscribe_msg'];
		$messages['subscribe']['web'] = $_POST['subscribe_web'];
		$messages['subscribe']['email'] = (isset($_POST['subscribe_email']))
				? true : false;

		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['sub'] = $_POST['unsubscribe_sub'];
		$messages['unsubscribe']['msg'] = $_POST['unsubscribe_msg'];
		$messages['unsubscribe']['web'] = $_POST['unsubscribe_web'];
		$messages['unsubscribe']['email'] = (isset($_POST['unsubscribe_email']))
				? true : false;

		$messages['confirm'] = array();
		$messages['confirm']['sub'] = $_POST['confirm_sub'];
		$messages['confirm']['msg'] = $_POST['confirm_msg'];

		$messages['activate'] = array();
		$messages['activate']['sub'] = $_POST['activate_sub'];
		$messages['activate']['msg'] = $_POST['activate_msg'];

		$messages['update'] = array();
		$messages['update']['sub'] = $_POST['update_sub'];
		$messages['update']['msg'] = $_POST['update_msg'];

		$notices = array();
		$notices['email'] = $_POST['notify_email'];
		$notices['subject'] = $_POST['notify_subject'];
		$notices['subscribe'] = $_POST['notify_subscribe'];
		$notices['unsubscribe'] = $_POST['notify_unsubscribe'];
		$notices['update'] = $_POST['notify_update'];
		$notices['pending'] = $_POST['notify_pending'];

		$input = array('messages' => serialize($messages),
				'notices' => serialize($notices));
		Pommo_Api::configUpdate( $input, TRUE);

		$json->success(_('Configuration Updated.'));
	}
	else
	{
		// __ FORM NOT VALID
		$fieldErrors = array();
		$errors = $validator->getErrors();
		foreach ($errors as $key => $val)
		{
			$fieldErrors[] = array (
				'field' => $key,
				'message' => $val
			);
		}
		$json->add('fieldErrors', $fieldErrors);
		$json->fail(_('Please review and correct errors with your submission.'));
	}
}

$view->assign('t_subscribe', _('Subscription'));
$view->assign('t_unsubscribe', _('Unsubscription'));
$view->assign('t_pending', _('Pending'));
$view->assign('t_update', _('Update'));

$view->assign($_POST);
$view->display('admin/setup/config/messages');

