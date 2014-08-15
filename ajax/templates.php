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
require_once Pommo::$_baseDir.'classes/Pommo_Mailing_Template.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

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

$success = false;

if(isset($_POST['skip']) || (isset($_POST['template']) && !is_numeric($_POST['template'])))
{
	$success = true;
}
elseif(isset($_POST['load']))
{
	$template = current(Pommo_Mailing_Template::get(array('id' => $_POST['template'])));
	Pommo::$_session['state']['mailing']['body'] = $template['body'];
	Pommo::$_session['state']['mailing']['altbody'] = $template['altbody'];
	
	$success = true;
}
elseif(isset($_POST['delete']))
{
	$msg = (Pommo_Mailing_Template::delete($_POST['template'])) ?
		Pommo::_T('Template Deleted') :
		Pommo::_T('Error with deletion.');
	
	$json->add('callbackFunction','deleteTemplate');
	$json->add('callbackParams', array(
		'id' => $_POST['template'],
		'msg' => $msg)
	);
}
else
{
	$view->assign('templates', Pommo_Mailing_Template::getNames());
	$view->display('admin/mailings/mailing/templates');
	Pommo::kill();
}

$json->serve($success);

