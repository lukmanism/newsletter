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


/**********************************
  SETUP TEMPLATE, PAGE
*********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

if (empty($_POST))
{
    //Initiate Page
    //populate _POST with info from database (fills in form values...)
    $dbVals = Pommo_Api::configGet(array(
                'admin_username',
            ));
    $dbVals['admin_email'] = Pommo::$_config['admin_email'];
    $view->assign($dbVals);
} 
else
{
    // ___ USER HAS SENT FORM ___
    require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
    $json = new Pommo_Json();

    if (isset($_POST['admin_email']))
    {
        //Do inline validation
        require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
        $errors = array();
        $validator = new Pommo_Validate();
        $validator->setPost($_POST);
        $validator->addData('admin_email', 'Email', false);
        $result = $validator->checkData();
        $errors = $validator->getErrors();

        //Is result ok?
        if ($result)
        {
            Pommo_Api::configUpdate($_POST);
            Pommo::reloadConfig();
            $json->success(Pommo::_T('Configuration Updated.'));
        } 
        else
        {
            $json->fail(Pommo::_T('Invalid email address'));
        }
    }
}

$view->assign($_POST);
$view->display('admin/setup/config/users');

