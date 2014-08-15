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
$serial = (empty($_GET['serial'])) ? time() : addslashes($_GET['serial']);

require '../bootstrap.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Mta.php';

Pommo::init(array('sessionID' => $serial, 'keep' => TRUE, 'authLevel' => 0));
$logger = Pommo::$_logger;
$dbo    = Pommo::$_dbo;

// don't die on query so we can capture logs'
// NOTE: Be extra careful to check the success of queries/methods!
$dbo->dieOnQuery(FALSE);

// turn logging off unless verbosity is 1
if (Pommo::$_verbosity > 1) {
    $dbo->debug(FALSE);
}

// start error logging
Pommo::logErrors();

/**********************************
    STARTUP ROUTINES
 *********************************/
$config = Pommo_Api::configGet(array(
	'list_exchanger',
	'maxRuntime',
	'smtp_1',
	'smtp_2',
	'smtp_3',
	'smtp_4',
	'throttle_SMTP',
	'throttle_MPS',
	'throttle_BPS',
	'throttle_DP',
	'throttle_DMPP',
	'throttle_DBPP'));

// NOTE: PR15 removed multimode (simultaneous SMTP relays) variables + functionality!
//	we will be migrating to swiftmailer, and its multi-SMTP support/balancing in PR16-ish.

/**********************************
 * MAILING INITIALIZATION
 *********************************/

// calculate spawn # (number of times this MTA has spawned under this serial)
Pommo::$_session['spawn'] = (isset(Pommo::$_session['spawn'])) ?
		Pommo::$_session['spawn'] + 1 : 1;

// initialize MTA
$pommoMtaConfig = array(
    'queueSize' => 100,
    'maxRunTime' => $config['maxRuntime'],
    'serial' => $serial,
    'spawn' => Pommo::$_session['spawn']
);
$mailing = new Pommo_Mta($pommoMtaConfig);

$logger->addMsg(sprintf(Pommo::_T('Started Mailing MTA. Spawn #%s.'),
		$pommoMtaConfig['spawn']), 3, TRUE);

// poll mailing status
$mailing->poll();

// check if message body contains personalizations
// personalizations are cached in session
// require once here so that mailer can use
require_once Pommo::$_baseDir.'classes/Pommo_Helper_Personalize.php';

if (!isset(Pommo::$_session['personalization'])) {
    Pommo::$_session['personalization'] = FALSE;
    $matches = array();
    preg_match('/\[\[[^\]]+]]/', $mailing->_mailing['body'], $matches);
    if (!empty($matches))
    {
        Pommo::$_session['personalization'] = TRUE;
    }
    preg_match('/\[\[[^\]]+]]/',  $mailing->_mailing['altbody'], $matches);
    if (!empty($matches))
    {
        Pommo::$_session['personalization'] = TRUE;
    }
    // cache personalizations in session
    if (Pommo::$_session['personalization'])
    {
        Pommo::$_session['personalization_body'] =
                Pommo_Helper_Personalize::search($mailing->_mailing['body']);
        Pommo::$_session['personalization_altbody'] =
                Pommo_Helper_Personalize::search($mailing->_mailing['altbody']);
    }
}

/**********************************
 * PREPARE THE MAILER
 *********************************/
$html = ($mailing->_mailing['ishtml'] == 'on') ? TRUE : FALSE;

$mailer = new Pommo_Mailer($mailing->_mailing['fromname'],
		$mailing->_mailing['fromemail'],
		$mailing->_mailing['frombounce'],
		$config['list_exchanger'],
		NULL,
		$mailing->_mailing['charset'],
		Pommo::$_session['personalization']);

if (!$mailer->prepareMail($mailing->_mailing['subject'],
		$mailing->_mailing['body'],
		$html,
		$mailing->_mailing['altbody'],
		$mailing->_mailing['attachments'])) {
	$mailer->shutdown('*** ERROR *** prepareMail() returned errors.');
}

// Set appropriate SMTP relay
if ($config['list_exchanger'] == 'smtp') {
	$mailer->setRelay(unserialize($config['smtp_1']));
	$mailer->SMTPKeepAlive = TRUE;
}

// necessary? (better method!)
$mailing->attach('_mailer', $mailer);

/**********************************
 * INITIALIZE Throttler
 *********************************/

$tid = 1; // forced shared throttler, until swiftmailer implementation
//$tid = ($config['throttle_SMTP'] == 'shared') ? 1 : $relayID; /* old shared throttle support */

if (empty(Pommo::$_session['throttler'][$tid])) {
	Pommo::$_session['throttler'] = array (
		$tid => array(
			'base' => array(
				'MPS' => $config['throttle_MPS'],
				'BPS' => $config['throttle_BPS'],
				'DP' => $config['throttle_DP'],
				'DMPP' => $config['throttle_DMPP'],
				'DBPP' => $config['throttle_DBPP'],
				'genesis' => time()
			),
			'domainHistory' => array(),
			'sent' => floatval(0),
			'sentBytes' => floatval(0)
			)
		);
}

$throttler = new Pommo_Throttler(
    Pommo::$_session['throttler'][$tid]['base'],
    Pommo::$_session['throttler'][$tid]['domainHistory'],
    Pommo::$_session['throttler'][$tid]['sent'],
    Pommo::$_session['throttler'][$tid]['sentBytes']
);

// byte tracking/throttling enabled
$byteMask = $throttler->byteTracking();
if ($byteMask > 1) {
    $mailer->trackMessageSize();
}

$mailing->attach('_byteMask', $byteMask);

// necessary? (better method!)
$mailing->attach('_throttler', $throttler);

/**********************************
 * INITIALIZE Queue
 *********************************/

$mailing->pullQueue();
$mailing->pushThrottler();

/**********************************
   PROCESS QUEUE
 *********************************/

$mailing->processQueue();
