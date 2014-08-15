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
require_once Pommo::$_baseDir.'classes/Pommo_Fields.php';
require_once Pommo::$_baseDir.'classes/Pommo_Mailing.php';

Pommo::init(array('keep' => true));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
  SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

$current = Pommo_Mailing::isCurrent();

if (empty ($_POST)) {
    // ___ USER HAS NOT SENT FORM ___
    $vMsg = array ();
    $vMsg['email'] = Pommo::_T('Invalid email address');
    $view->assign('vMsg', $vMsg);
} else {
    // ___ USER HAS SENT FORM ___
    include_once Pommo::$_baseDir . 'classes/Pommo_Validate.php';
    $validator = new Pommo_Validate();
      $validator->setPost($_POST);
      $validator->addData('email', 'Email', false);

    if ($result = $validator->checkData() && !$current) {
        // __ FORM IS VALID
        include_once Pommo::$_baseDir . 'classes/Pommo_Mail_Ctl.php';
        include_once Pommo::$_baseDir . 'classes/Pommo_Subscribers.php';
        include_once Pommo::$_baseDir . 'classes/Pommo_Validate.php';

        // get a copy of the message state
        // composition is valid (via preview.php)
        $state = Pommo::$_session['state']['mailing'];

        // create temp subscriber
        $subscriber = array(
          'email' => $_POST['email'],
          'registered' => time(),
          'ip' => $_SERVER['REMOTE_ADDR'],
          'status' => 0,
          'data' => $_POST['d']
        );
        Pommo_Validate::subscriberData(
            $subscriber['data'],
            array('active' => false, 'ignore' => true, 'log' => false)
        );
        $key = Pommo_Subscribers::add($subscriber);
        if (!$key) {
            $logger->addErr('Unable to Add Subscriber');
        } else {
            // temp subscriber created
            $state['tally'] = 1;
            $state['group'] = _('Test Mailing');

            if ('off' == $state['ishtml']) {
                $state['body'] = $state['altbody'];
                $state['altbody'] = '';
            } else {
                $state['ishtml'] = 'on';
            }

            // create mailing
            $mailing = Pommo_Mailing::make(array(), true);
            $state['status'] = 1;
            $state['current_status'] = 'stopped';
            $state['command'] = 'restart';
            $state['charset'] = $state['list_charset'];
            $mailing = Pommo_Helper::arrayIntersect($state, $mailing);
            $code = Pommo_Mailing::add($mailing);

            // populate queue
            $queue = array($key);
            if (!Pommo_Mail_Ctl::queueMake($queue)) {
                $logger->addErr('Unable to Populate Queue');
            } else if (!Pommo_Mail_Ctl::spawn(
                Pommo::$_baseUrl .
                'ajax/mailings_send4.php?test=TRUE&code='.$code
            )) {
                $logger->addErr('Unable to spawn background mailer');
            } else {
                $view->assign('sent', $_POST['email']);
            }
        }
    } elseif ($current) {
        $logger->addMsg(
            _('A mailing is currently taking place. Please try again later.')
        );
        $view->assign($_POST);
    } else {
        // __ FORM NOT VALID
        $logger->addMsg(_('Please review and correct errors with your submission.'));
        $view->assign($_POST);
    }
}

if (Pommo::$_config['demo_mode'] == 'on') {
    $logger->addMsg(
        sprintf(
            _(
                '%sDemonstration Mode%s is on -- no Emails will actually'
                . ' be sent. This is good for testing settings.'
            ),
            '<a href="'.Pommo::$_baseUrl.'setup_configure.php#mailings">',
            '</a>'
        )
    );
}

$view->assign('fields', Pommo_Fields::get());
$view->display('admin/mailings/mailing/ajax.mailingtest');

