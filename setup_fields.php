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
require 'bootstrap.php';
require_once Pommo::$_baseDir.'classes/Pommo_Fields.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Template.php');
$view = new Pommo_Template();

// add field if requested, redirect to its edit page on success
if (!empty ($_POST['field_name']))
{
	$field = Pommo_Fields::make(array(
		'name' => $_POST['field_name'],
		'type' => $_POST['field_type'],
		'prompt' => 'Field Prompt',
		'required' => 'off',
		'active' => 'off'
	));

	$id = Pommo_Fields::add($field);
	if ($id)
	{
		$view->assign('added',$id);
	}
	else
	{
		$logger->addMsg(Pommo::_T('Error with addition.'));
	}
}

// check for a deletion request
if (!empty ($_GET['delete']))
{
	$field = Pommo_Fields::get(array('id' => $_GET['field_id']));
	$field =& current($field);

	if (count($field) === 0)
	{
		$logger->addMsg(Pommo::_T('Error with deletion.'));
	}
	else
	{
		$affected = Pommo_Fields::subscribersAffected($field['id']);
		if (count($affected) > 0 && empty($_GET['dVal-force']))
		{
			$view->assign('confirm', array (
				'title' => Pommo::_T('Confirm Action'),
				'nourl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'],
				'yesurl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'] . '&delete=TRUE&dVal-force=TRUE',
				'msg' => sprintf(Pommo::_T('Currently, %1$s subscribers have a non empty value for %2$s. All Subscriber data relating to this field will be lost.'), '<b>' . count($affected) . '</b>','<b>' . $field['name'] . '</b>')));
			$view->display('admin/confirm');
			Pommo::kill();
		}
		else
		{
			(Pommo_Fields::delete($field['id'])) ?
				Pommo::redirect($_SERVER['PHP_SELF']) :
				$logger->addMsg(Pommo::_T('Error with deletion.'));
		}
	}
}

// Get array of fields. Key is ID, value is an array of the demo's info
$fields = Pommo_Fields::get(array('byName' => FALSE));
if (!empty($fields))
{
	$view->assign('fields', $fields);
}

$view->display('admin/setup/setup_fields');
