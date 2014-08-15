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

class Pommo_Helper_Messages
{

    // send a message
    // accepts a parameter array;
       // to: email to send message to [str]
       // type: message type type [str] either; 'subscribe', 'unsubscribe', 'confirm', 'activate', 'update', or 'password'
       // code: confirmation code [str]
    public static function sendMessage($p = array('to' => false, 'type' => false, 'code' => false)) {
        $logger = Pommo::$_logger;

        // retrieve messages
        $dbvalues = Pommo_Api::configGet('messages');
        $messages = unserialize($dbvalues['messages']);

        $type = $p['type'];

        $output = false;
        switch($type) {
        case 'subscribe' :
        case 'unsubscribe' :

            $output = $messages[$type]['web'];

            // break out of switch statement if subscribe/unsubscribe emails are disabled
            if (!$messages[$type]['email'])
                break;

        case 'activate' :
            $output = ($output) ? $output :
                sprintf(Pommo::_T('An actvation mail has been sent to %s. Please follow its instructions to access your records.'),$p['to']);
        case 'confirm' :
        case 'password' :
        case 'update' :

            $output = ($output) ? $output :
                sprintf(Pommo::_T('A confirmation mail has been sent to %s. Please follow its instructions to complete your request.'),$p['to']);

            // fetch subject, body
            $subject = $messages[$type]['sub'];
            $body = $messages[$type]['msg'];

            // personalize body
            $url = ($type == 'activate') ?
                Pommo::$_http.Pommo::$_baseUrl.'update.php?email='.$p['to'].'&code='.$p['code'] :
                Pommo::$_http.Pommo::$_baseUrl.'confirm.php?code='.$p['code'];
            $body = preg_replace('@\[\[URL\]\]@i',$url,$body);


            if (empty($subject) || empty($body)) {
                $logger->addErr('Pommo_Helper_Messages::sendMessage() - subject or body empty');
                return false;
            }



            require_once(Pommo::$_baseDir.'classes/Pommo_Mailer.php');
            $mail = new Pommo_Mailer();

            // allow mail to be sent, even if demo mode is on
            $mail->toggleDemoMode("off");

            // send the confirmation mail
            $mail->prepareMail($subject, $body);
            if (!$mail->bmSendmail($p['to'])) {
                $logger->addErr(Pommo::_T('Error sending mail'));
                return false;
            }

            // reset demo mode to default
            $mail->toggleDemoMode();

            break;

        default:
            $logger->addErr('unknown type passed');
            return false;
        }

        $logger->addMsg($output);
        return true;
    }

    static function resetDefault($section = 'all') {
        global $pommo;
        $dbo =& Pommo::$_dbo;

        $messages = array();
        if ($section != 'all') {
            $config = Pommo_Api::configGet(array('messages'));
            $messages = unserialize($config['messages']);
        }

        if ($section == 'all' || $section == 'subscribe') {
        $messages['subscribe'] = array();
        $messages['subscribe']['msg'] = sprintf(Pommo::_T('Welcome to our mailing list. You can always login to update your records or unsubscribe by visiting: %s'),"\n  ".Pommo::$_http.Pommo::$_baseUrl.'login.php');
        $messages['subscribe']['sub'] = sprintf(Pommo::_T('Welcome to %s'), Pommo::$_config['list_name']);
        $messages['subscribe']['web'] = Pommo::_T('Welcome to our mailing list. Enjoy your stay.');
        $messages['subscribe']['email'] = false;
        }

        if ($section == 'all' || $section == 'unsubscribe') {
        $messages['unsubscribe'] = array();
        $messages['unsubscribe']['sub'] = sprintf(Pommo::_T('Farewell from %s'), Pommo::$_config['list_name']);
        $messages['unsubscribe']['msg'] = Pommo::_T('You have been unsubscribed and will not receive any more mailings from us. Feel free to come back anytime!');
        $messages['unsubscribe']['web'] = Pommo::_T('You have successfully unsubscribed. Enjoy your travels.');
        $messages['unsubscribe']['email'] = false;
        }

        if ($section == 'all' || $section == 'confirm') {
        $messages['confirm'] = array();
        $messages['confirm']['msg'] = sprintf(Pommo::_T('You have requested to subscribe to %s. We would like to validate your email address before adding you as a subscriber. Please click the link below to be added ->'), Pommo::$_config['list_name'])."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
        $messages['confirm']['sub'] = Pommo::_T('Subscription request');
        }

        if ($section == 'all' || $section == 'activate') {
        $messages['activate'] = array();
        $messages['activate']['msg'] =  sprintf(Pommo::_T('Someone has requested to access to your records for %s.'),Pommo::$_config['list_name']).' '.Pommo::_T('You may edit your information or unsubscribe by visiting the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
        $messages['activate']['sub'] = sprintf(Pommo::_T('%s: Account Access.'),Pommo::$_config['list_name']);
        }


        if ($section == 'all' || $section == 'password') {
        $messages['password'] = array();
        $messages['password']['msg'] =  sprintf(Pommo::_T('You have requested to change your password for %s.'),Pommo::$_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
        $messages['password']['sub'] = Pommo::_T('Change Password request');
        }

        if ($section == 'all' || $section == 'update') {
            $messages['update'] = array();
            $messages['update']['msg'] =  sprintf(Pommo::_T('You have requested to update your records for %s.'),Pommo::$_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\n\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
            $messages['update']['sub'] = Pommo::_T('Update Records request');
        }

        $input = array('messages' => serialize($messages));
        Pommo_Api::configUpdate($input, TRUE);

        return $messages;
    }

    function testExchanger($to, $exchanger)
    {
        $logger = Pommo::$_logger;

        require_once Pommo::$_baseDir.'classes/Pommo_Mailer.php';

        $subject = _('poMMo test message');
        $body = sprintf(_('This message indicates that poMMo is able to use the'
                .' %s exchanger.'), $exchanger);

        $mail = new Pommo_Mailer();

        // allow mail to be sent, even if demo mode is on
        $mail->toggleDemoMode("off");

        // send the confirmation mail
        $mail->prepareMail($subject, $body);

        $ret = true;

        // Catch output so error messages from PHPMailer are not echoed
        ob_start();

        if (!$mail->bmSendmail($to))
        {
            $logger->addErr(_('Error Sending Mail'));
            $ret = false;
        }

        // Stop catching output
        ob_get_clean();

        // reset demo mode to default
        $mail->toggleDemoMode();
        return $ret;
    }

    function notify(&$notices,&$sub,$type,$comments=false) {
        global $pommo;
        require_once(Pommo::$_baseDir.'classes/Pommo_Mailer.php');

        $mails = Pommo_Helper::trimArray(explode(',',$notices['email']));
        if(empty($mails[0]))
            $mails = array(Pommo::$_config['admin_email']);

        $subject = $notices['subject'].' ';
        $body = sprintf(Pommo::_T('poMMo %s Notice'),$type);
        $body .= "  [".date("F j, Y, g:i a")."]\n\n";

        $body .= "EMAIL: ".$sub['email']."\n";
        $body .= "IP: ".$sub['ip']."\n";
        $body .= "REGISTERED: ".$sub['registered']."\n\n";
        if($comments) $body .= "COMMENTS: $comments \n\n";
        $body .= "DATA:\n";

        require_once(Pommo::$_baseDir.'classes/Pommo_Fields.php');
        $fields = Pommo_Fields::getNames();

        foreach($sub['data'] as $fid => $v)
            $body .= "\t".$fields[$fid].": $v\n";

        switch($type) {
            case 'subscribe':
                $subject .= Pommo::_T('new subscriber!');
                break;
            case 'unsubscribe':
                $subject .= Pommo::_T('user unsubscribed.');
                break;
            case 'pending':
                $subject .= Pommo::_T('new pending!');
                break;
            case 'update':
                $subject .= Pommo::_T('subscriber updated.');
                break;
        }

        $mail = new Pommo_Mailer();

        // allow mail to be sent, even if demo mode is on
        $mail->toggleDemoMode("off");

        // send the confirmation mail
        $mail->prepareMail($subject, $body);

        foreach($mails as $to)
            $mail->bmSendmail($to);

        // reset demo mode to default
        $mail->toggleDemoMode();
        return;
    }
}
