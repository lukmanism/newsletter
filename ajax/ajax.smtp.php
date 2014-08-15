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
Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

require_once Pommo::$_baseDir . 'lib/phpmailer/class.phpmailer.php';
require_once Pommo::$_baseDir . 'lib/phpmailer/class.smtp.php';

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();
$view->assign('returnStr', _('Configure'));

// Read user requested changes
if (!empty ($_POST['addSmtpServer']))
{
	$server = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'security' => 'none',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);
	$input['smtp_' . key($_POST['addSmtpServer'])] = serialize($server);
	Pommo_Api::configUpdate($input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['updateSmtpServer']))
{
	$key = key($_POST['updateSmtpServer']);
    $server = array (
        'host' => $_POST['host'][$key],
        'security' => $_POST['security'][$key],
        'port' => $_POST['port'][$key],
        'auth' => $_POST['auth'][$key],
        'user' => $_POST['user'][$key],
        'pass' => $_POST['pass'][$key]
    );
	$input['smtp_' . $key] = serialize($server);
	Pommo_Api::configUpdate( $input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['deleteSmtpServer']))
{
	$input['smtp_' . key($_POST['deleteSmtpServer'])] = '';
	Pommo_Api::configUpdate( $input, TRUE);
	$update = true;
}
elseif (!empty ($_POST['throttle_SMTP']))
{
	$input['throttle_SMTP'] = $_POST['throttle_SMTP'];
	Pommo_Api::configUpdate( $input);
	$update = true;
}

if(isset($update))
	$view->assign('output',($update)?Pommo::_T('Configuration Updated.'):Pommo::_T('Please review and correct errors with your submission.'));

// Get the SMTP settings from DB
$smtpConfig = Pommo_Api::configGet(array (
	'smtp_1',
	'smtp_2',
	'smtp_3',
	'smtp_4',
	'throttle_SMTP'
));

$smtp[1] = unserialize($smtpConfig['smtp_1']);
$smtp[2] = unserialize($smtpConfig['smtp_2']);
$smtp[3] = unserialize($smtpConfig['smtp_3']);
$smtp[4] = unserialize($smtpConfig['smtp_4']);

if (empty ($smtp[1]))
	$smtp[1] = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);

// Test the servers
$addServer = FALSE;
$smtpStatus = array ();
for ($i = 1; $i < 5; $i++) {

	if (empty ($smtp[$i])) {
		if (!$addServer)
			$addServer = $i;
		continue;
	}

	$test[$i] = new PHPMailer();

	$test[$i]->Host = (empty ($smtp[$i]['host'])) ? null : $smtp[$i]['host'];
	$test[$i]->Port = (empty ($smtp[$i]['port'])) ? null : $smtp[$i]['port'];
	if (!empty ($smtp[$i]['auth']) && $smtp[$i]['auth'] == 'on') {
		$test[$i]->SMTPAuth = TRUE;
        $test[$i]->SMTPSecure = $smtp[$i]['security'];
		$test[$i]->Username = (empty ($smtp[$i]['user'])) ? null : $smtp[$i]['user'];
		$test[$i]->Password = (empty ($smtp[$i]['pass'])) ? null : $smtp[$i]['pass'];
	}

    try {
        if (@ $test[$i]->SmtpConnect()) {
            $smtpStatus[$i] = TRUE;
            $test[$i]->SmtpClose();
        } else {
            $smtpStatus[$i] = FALSE;
        }
    } catch (phpmailerException $e) {
        $smtpStatus[$i] = FALSE;
    }
}

$view->assign('addServer',$addServer);
$view->assign('smtpStatus',$smtpStatus);
$view->assign('smtp', $smtp);
$view->assign('throttle_SMTP', $smtpConfig['throttle_SMTP']);

$view->display('admin/setup/config/ajax.smtp');
