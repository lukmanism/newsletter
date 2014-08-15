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

/************************************
  INITIALIZATION METHODS
 ************************************/
require('bootstrap.php');
require_once(Pommo::$_baseDir.'classes/Pommo_Install.php');
Pommo::init(array('authLevel' => 0, 'install' => TRUE));

session_start();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;
$dbo->dieOnQuery(FALSE);

/************************************
  SETUP TEMPLATE, PAGE
 ************************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();

// Check to make sure poMMo is not already installed.
if (Pommo_Install::verify())
{
    $logger->addErr(Pommo::_T('poMMo is already installed.'));
    $view->assign('installed', TRUE);
    $view->display('install');
    Pommo::kill();
}

if (isset($_REQUEST['disableDebug']))
    unset($_REQUEST['debugInstall']);
elseif (isset($_REQUEST['debugInstall']))
    $view->assign('debug', TRUE);

if (!empty($_POST))
{
    if (isset($_POST['installerooni']) &&
            Pommo_Install::validateInstallationData($_POST)) {
        // FORM IS VALID
        // drop existing poMMo tables
        foreach (array_keys($dbo->table) as $key) {
            $table = $dbo->table[$key];
            $sql = 'DROP TABLE IF EXISTS '.$table;
            $dbo->query($sql);
        }

        if (isset($_REQUEST['debugInstall'])) {
            $dbo->debug(TRUE);
        }

        $install = Pommo_Install::parseSQL();

        if ($install) {
            // installation of DB went OK, set configuration values to user
            // supplied ones
            require_once Pommo::$_baseDir . 'classes/Pommo_User.php';
            $pass = $_POST['admin_password'];
            $user = new Pommo_User();
            $user->save('admin', $pass);

            // Save Mailing List, Name of Website, Website URL
            Pommo_Install::saveUserValues($_POST);

            // generate key to uniquely identify this installation
            $key = Pommo_Helper::makeCode(6);
            Pommo_Api::configUpdate(array('key' => $key), TRUE);

            Pommo::reloadConfig();

            // load configuration [depricated?], set message defaults, load templates
            require_once(Pommo::$_baseDir.
                    'classes/Pommo_Helper_Messages.php');
            Pommo_Helper_Messages::resetDefault('all');

            // install templates
            $file = Pommo::$_baseDir.'sql/sql.templates.php';
            if (!Pommo_Install::parseSQL(false, $file))
                $logger->addErr('Error Loading Default Mailing Templates.');

            $logger->addMsg(Pommo::_T('Installation Complete! You may now login and setup poMMo.'));
            $logger->addMsg(Pommo::_T('Login Username: ').'admin');
            $logger->addMsg(Pommo::_T('Login Password: ').$pass);

            $view->assign('installed', TRUE);
        } else {
            // INSTALL FAILED
            $dbo->debug(FALSE);

            // drop existing poMMo tables
            foreach (array_keys($dbo->table) as $key)
            {
                $table = $dbo->table[$key];
                $sql = 'DROP TABLE IF EXISTS '.$table;
                $dbo->query($sql);
            }

            $logger->addErr('Installation failed! Enable debbuging to expose the problem.');
        }
    } else {
        // __ FORM NOT VALID
        $view->assign('formError', Pommo_Install::$errors);
        $logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
    }
}
$view->assign($_POST);
$view->display('install');
Pommo::kill();

