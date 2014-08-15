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

 // poMMo MTA - poMMo's background mailer

 // includes
require_once Pommo::$_baseDir . 'classes/Pommo_Mail_Ctl.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Mailer.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Throttler.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Mailing.php';
require_once Pommo::$_baseDir . 'classes/Pommo_Subscribers.php';

class Pommo_Mta
{
    // Attempted number of mails to process per queue batch.
    var $_queueSize;

    // Number of seconds the MTA process is allowed to run for.
    var $_maxRunTime;

    // Time the MTA process began
    var $_start;

    // (bool) Skip Security checks
    var $_skipSecurity = false;

    // The ID of the current mailing
    var $_id;

    // serial of mailing, prevents 2 scripts from working on the same mailing
    var $_serial;

    // security code - prevent oustide interferrence
    var $_code;

    // (bool) True if this is a test mailing
    var $_test;

    // the current mailing (object) body, serial, code, etc!
    var $_mailing;

    // the poMMo mailer
    var $_mailer;

    // the current queue, holds an array of subscriber objects
    var $_queue;
    var $_sent;
    var $_failed;

    // the email hash array('email' => 'subscriber_id')
    var $_hash;

    // the throttle object
    var $_throttler;

    /**
     * Register function that will be called when the script terminates abruptly.
     * Get code, test and id from URL.
     * Retrieve mailing (subject, body, attachments, etc) that will be sent.
     *
     * @param array $args.- Configuration overwrites
     */
    function Pommo_Mta($args = array())
    {
        $defaults = array (
            'queueSize' => 100,
            'maxRunTime' => 80,
            'skipSecurity' => false,
            'start' => time(),
            'serial' => false,
            'spawn' => 1
        );
        $p = Pommo_Api::getParams($defaults, $args);

        foreach($p as $k => $v) {
            $this->{'_'.$k} = $v;
        }

        // protect against safe mode timeouts
        if (ini_get('safe_mode')) {
            $this->_maxRunTime = ini_get('max_execution_time') - 10;
        } else {
            set_time_limit(0);
        }

        // protect against user (client) abort
        ignore_user_abort(true);

        // register shutdown method
        register_shutdown_function(array(&$this, "shutdown"));

        // set parameters from URL
        $this->_code = (empty($_GET['code'])) ? 'invalid' : $_GET['code'];
        $this->_test = isset($_GET['test']);
        $this->_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : false;

        // verify and initialize the current mailing
        $p = array(
            'active' => true,
            'code' => (($this->_skipSecurity) ? null : $this->_code),
            'id' => (($this->_id) ? $this->_id : null));

        $this->_mailing = current(Pommo_Mailing::get($p));

        if (!is_numeric($this->_mailing['id'])) {
            $this->shutdown('Unable to initialize mailing.');
        }
        $this->_id = $this->_mailing['id'];

        // make sure the $_GET global holds the mailing id
        // (used in personalizations, etc.) TODO: Find another way to do this
        $_GET['id'] = $this->_id;

        // security routines
        if ($this->_mailing['end'] > 0) {
            $this->shutdown(Pommo::_T('Mailing Complete.'));
        }

        if (empty($this->_mailing['serial'])) {
            if (!Pommo_Mail_Ctl::mark($this->_serial,$this->_id)) {
                $this->shutdown(
                    'Unable to serialize mailing (ID: ' . $this->_id
                    . ' SERIAL: ' . $this->_serial . ')'
                );
            }
        }

        if ($this->_maxRunTime < 15) {
            $this->shutdown('Max Runtime must be at least 15 seconds!');
        }

        $this->_queue = $this->_sent = $this->_failed = array();
    }

    /**
     * Retrieves the current command from the DB and acts based on that.
     * restart: Set command to "none" in the DB.
     */
    function poll()
    {
        $dbo = Pommo::$_dbo;
        $logger = Pommo::$_logger;

        $query = "
            SELECT command, current_status, serial
            FROM ". $dbo->table['mailing_current']."
            WHERE current_id=%i";
        $query = $dbo->prepare($query,array($this->_id));

        $row = mysql_fetch_assoc($dbo->query($query));
        if (empty($row)) {
            $this->shutdown('Unable to poll mailing.');
        }

        switch ($row['command']) {
            case 'restart': // terminate if this is not a "fresh"/"new" process
                if (is_object($this->_mailer)) {
                    $this->_mailer->SmtpClose();
                    $this->shutdown(sprintf(Pommo::_T('Restarting Mailing #%s'),$this->_id));
                }

                $query = "
                    UPDATE ". $dbo->table['mailing_current']."
                    SET
                        serial=%i,
                        command='none',
                        current_status='started'
                        WHERE current_id=%i";
                $query = $dbo->prepare($query,array($this->_serial,$this->_id));
                if (!$dbo->query($query)) {
                    $this->shutdown('Database Query failed: ' . $query);
                }

                $logger->addMsg(sprintf(Pommo::_T('Started Mailing #%s'),$this->_id), 3);

                break;

            case 'stop':
                if (is_object($this->_mailer))
                    $this->_mailer->SmtpClose();

                $query = "
                    UPDATE ". $dbo->table['mailing_current']."
                    SET
                        command='none',
                        current_status='stopped' WHERE current_id=%i";
                $query = $dbo->prepare($query,array($this->_id));
                if (!$dbo->query($query))
                    $this->shutdown('Database Query failed: '.$query);

                $logger->addMsg(sprintf(Pommo::_T('Stopped Mailing #%s'),$this->_id), 3, TRUE);
                break;

            case 'cancel':
                Pommo_Mail_Ctl::finish($this->_id, true);
                $this->shutdown(Pommo::_T('Mailing Cancelled.'), true);
                break;

            default:
                if (!$this->_skipSecurity && $row['serial'] != $this->_serial) {
                    $this->shutdown('Terminating due to Serial Mismatch!');
                }
                if ($row['current_status'] == 'stopped') {
                    $this->shutdown(Pommo::_T('You must restart the mailing.'));
                }

                // upate the timestamp
                $query = "UPDATE ". $dbo->table['mailing_current']." SET touched=NULL WHERE current_id=%i";
                $query = $dbo->prepare($query,array($this->_id));
                if (!$dbo->query($query)) {
                    $this->shutdown('Database Query failed: '.$query);
                }
                break;
        }

        // update the notices, queue
        $this->update();
    }

    /**
     * Verify if there are still subscribers to whom the mailing hasn't been
     * sent. If there aren't, stop. If there are: Get some subscribers from the
     * DB.
     */
    function pullQueue() {
        $dbo = Pommo::$_dbo;

        $relay = 1; // switched to static relay in PR15, will utilize swiftmailer's multi-SMTP support.

        // check mailing status + update queue, notices
        $this->poll();

        // ensure queue is active
        $query = "
            SELECT COUNT(subscriber_id)
            FROM ".$dbo->table['queue']."
            WHERE status=0";

        // no unsent mails left in queue, mailing complete!
        if ($dbo->query($query,0) < 1) {
            $this->stop(true);
        }

        // release lock on queue
        $query = "
            UPDATE ".$dbo->table['queue']."
            SET smtp=0
            WHERE smtp=%i";
        $query = $dbo->prepare($query, array($relay));
        if(!$dbo->query($query)) {
            $this->shutdown('Database Query failed: '.$query);
        }

        // mark our working queue
        $query = "
            UPDATE ".$dbo->table['queue']."
            SET smtp=%i
            WHERE smtp=0 AND status=0
            LIMIT %i";
        $query = $dbo->prepare($query,array($relay,$this->_queueSize));
        if(!$dbo->query($query)) {
            $this->shutdown('Database Query failed: '.$query);
        }

        // pull our queue
        $query = "
            SELECT subscriber_id
            FROM ".$dbo->table['queue']."
            WHERE smtp=%i";
        $query = $dbo->prepare($query,array($relay));

        if(!$dbo->query($query)) {
            $this->shutdown('Database Query failed: '.$query);
        }

        $this->_queue = Pommo_Subscribers::get(array(
            'id' => $dbo->getAll(false, 'assoc', 'subscriber_id')));

        if (empty($this->_queue)) {
            $this->shutdown('Unable to pull queue.');
        }
    }

    // pushes queue into throttler
    function pushThrottler() {
        $this->_throttler->clearQueue();

        // seperate emails into an array ([email],[domain]) to feed to throttler
        $emails = array();
        $emailHash = array(); // used to quickly lookup subscriberID based off email
        foreach($this->_queue as $s) {
            array_push($emails, array(
                $s['email'],
                substr($s['email'],strpos($s['email'],'@')+1)
                )
            );
            $emailHash[$s['email']] = $s['id'];
        }

        $this->_hash = & $emailHash;
        $this->_throttler->pushQueue($emails);
    }

    // continually sends mails from the queue until mailing completes or max runtime reached
    function processQueue() {
        $logger = Pommo::$_logger;

        $timer = time();
        while(true) {

            // repopulate throttler's queue if empty
            if (!$this->_throttler->mailsInQueue()) {
                $this->pullQueue(); // get unsent
                $this->pushThrottler(); // push unsent
            }

            // attempt to pull email from throttler's queue
            $mail = $this->_throttler->pullQueue();

            // if an email was returned, send it.
            if (!empty($mail)) {

                // set $personal as subscriber if personalization is enabled
                $personal = FALSE;
                if (Pommo::$_session['personalization']) {
                    $personal =& $this->_queue[$this->_hash[$mail[0]]];

                    if (empty($personal)) {
                        $this->stop();
                    }
                }

                if (!$this->_mailer->bmSendmail($mail[0], $personal)) // sending failed, write to log
                    $this->_failed[] = $mail[0];
                else
                    $this->_sent[] = $mail[0];


                // If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
                if ($this->_byteMask > 1) {
                    $bytes = $this->_mailer->GetMessageSize();
                    if ($this->_byteMask > 2)
                        $this->_throttler->updateBytes($bytes, $mail[1]);
                    else
                        $this->_throttler->updateBytes($bytes);
                    $logger->addMsg('Added ' . $bytes . ' bytes to throttler.', 1);
                }
            }

            // update & poll every 10 seconds || if logger is large
            if (((time() - $timer) > 9) || count($logger->_messages) > 40) {
                $this->poll();
                $timer = time();
            }

            // check to see if we have exceeded max runtime
            if ((time() - $this->_start) > $this->_maxRunTime)
                $this->stop();
        }
    }

    // updates the queue and notices
    // accepts a array of failed emails
    // accepts a array of sent emails
    function update()
    {
        $dbo = Pommo::$_dbo;

        if (!empty($this->_sent)) {
            $a = array();
            foreach($this->_sent as $e)
                $a[] = $this->_hash[$e];

            $query = "
                UPDATE ".$dbo->table['queue']."
                SET status=1
                WHERE subscriber_id IN(%q)";
            $query = $dbo->prepare($query,array($a));

            if (!$dbo->query($query))
                $this->shutdown('Database Query failed: '.$query);

        }

        if (!empty($this->_failed)) {
            $a = array();
            foreach($this->_failed as $e)
                $a[] = $this->_hash[$e];

            $query = "
                UPDATE ".$dbo->table['queue']."
                SET status=2
                WHERE subscriber_id IN(%q)";
            $query = $dbo->prepare($query,array($a));

            if (!$dbo->query($query))
                $this->shutdown('Database Query failed: '.$query);
        }

        // add notices
        Pommo_Mail_Ctl::addNotices($this->_id);

        // reset sent/failed
        $this->_sent = $this->_failed = array();
        return;
    }


    function attach($name, &$obj) {
        $this->{$name} =& $obj;
        return;
    }

    function stop($finish = false) {
        $this->_mailer->SmtpClose();

        if ($this->_test) { // don't respawn if this is a test mailing
            Pommo_Mail_Ctl::finish($this->_id,TRUE,TRUE);
            Pommo_Subscribers::delete(current($this->_hash));
            session_destroy();
            exit();
        }

        if($finish) {
            Pommo_Mail_Ctl::finish($this->_id);
            $this->shutdown(Pommo::_T('Mailing Complete.'));
        }

        // respwn
        if (!Pommo_Mail_Ctl::spawn(Pommo::$_baseUrl.'ajax/mailings_send4.php?'.
            'code='.$this->_code.
            '&serial='.$this->_serial.
            '&id='.$this->_id))
                $this->shutdown('*** RESPAWN FAILED! ***');

        $this->shutdown(sprintf(Pommo::_T('Runtime (%s seconds) reached, respawning.'),$this->_maxRunTime), false);
    }

    function shutdown($msg = false, $destroy = true) {
        // prevent recursion
        static $static = false;
        if($static) exit();
        $static = true;

        if($this->_test)
            exit();

        global $pommo;
        $logger =& Pommo::$_logger;

        $msg = ($msg) ? $msg : '*** ERROR THROWN *** PHP Invoked Shutdown Function. Processor Abruptly Terminated. See ERROR_LOG IN WORK DIRECTORY. Runtime: '.(time() - $this->_start).' seconds.';

        $logger->addMsg($msg,3,TRUE);
        echo $msg;

        // update queue sent/failed and notices
        $this->update();

        if($destroy)
            session_destroy();

        exit($msg);
    }
 }
 ?>
