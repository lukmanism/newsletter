<?php
/**
 *  Original Code Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess
 *  <bhb@iceburg.net>
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
require_once Pommo::$_baseDir.'classes/Pommo_Mailing_Template.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
  SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

if (empty ($_POST)) {
    // ___ USER HAS NOT SENT FORM ___

    $vMsg = array ();
    $vMsg['name'] = Pommo::_T('Cannot be empty.');
    $view->assign('vMsg', $vMsg);
} else {
    // ___ USER HAS SENT FORM ___
    include_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
    $validator = new Pommo_Validate();
      $validator->setPost($_POST);
      $validator->addData('name', 'Other', false);

    if ($result = $validator->checkData()) {
        // __ FORM IS VALID

        $t = Pommo_Mailing_Template::make(
            array(
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'body' => Pommo::$_session['state']['mailing']['body'],
                'altbody' => Pommo::$_session['state']['mailing']['altbody']
            )
        );
        $id = Pommo_Mailing_Template::add($t);

        if ($id) {
            $logger->addMsg(
                sprintf(
                    Pommo::_T('Template %s saved.'),
                    '<strong>' . $_POST['name'] . '</strong>'
                )
            );
            $view->assign('success', true);
        } else {
            $logger->addMsg(Pommo::_T('Error with addition.'));
        }
    } else {
        // __ FORM NOT VALID
        $logger->addMsg(Pommo::_T('Please choose a name for your template.'));
    }
}

$view->display('admin/mailings/mailing/ajax.addtemplate');

