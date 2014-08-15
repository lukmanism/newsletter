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

Pommo::init(array('keep' => TRUE));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// validate field ID
$field = current(Pommo_Fields::get(array('id' => $_REQUEST['field_id'])));
if ($field['id'] != $_REQUEST['field_id'])
{
	die('bad field ID');
}

if (empty($_POST))
{	
	$vMsg = array ();
	$vMsg['field_name'] = $vMsg['field_prompt'] = Pommo::_T('Cannot be empty.');
	$view->assign('vMsg', $vMsg);
}
else
{
	// ___ USER HAS SENT FORM ___

	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
	$json = new Pommo_Json();

	require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';
	$validator = new Pommo_Validate();
    $validator->setPost($_POST);
    $validator->addData('field_name', 'Other', false);
    $validator->addData('field_prompt', 'Other', false);
    $validator->addData('field_required', 'matchRegex', false, '!^(on|off)$!');
    $validator->addData('field_active', 'matchRegex', false, '!^(on|off)$!');

	if ($result = $validator->checkData())
	{
		// __ FORM IS VALID

		// TODO -> Which below logic is better? the computed diff, or send all fields for update?
		
		/*
		// make a difference between updated & original field
		$update = array_diff_assoc(Pommo_Fields::makeDB($_POST),$field);
		// restore the ID
		$update['id'] = $field['id'];
		*/
		
		// let MySQL do the difference processing
		$update = Pommo_Fields::makeDB($_POST);
		if (!Pommo_Fields::update($update))
		{
			$json->fail('error updating field');
		}
	
		$json->add('callbackFunction','updateField');
		$json->add('callbackParams',$update);
		$json->success(Pommo::_T('Settings updated.'));
	}
	else
	{
		// __ FORM NOT VALID
		$fieldErrors = array();
		$errors = $validator->getErrors();
		foreach ($errors as $key => $val)
		{
			$fieldErrors[] = array (
				'field' => $key,
				'message' => $val
			);
		}
		$json->add('fieldErrors', $fieldErrors);
		$json->fail(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$f_text = sprintf(Pommo::_T('%s - Any value will be accepted for text fields. They are useful for collecting names, addresses, etc.'),'<strong>'.$field['name'].' ('.Pommo::_T('Text').')</strong>');
$f_check = sprintf(Pommo::_T('%s - Checkboxes can be toggled ON or OFF. They are useful for opt-ins and agreements.'),'<strong>'.$field['name'].' ('.Pommo::_T('Checkbox').')</strong>');
$f_num = sprintf(Pommo::_T('%s - Only Numeric values will be accepted for number fields.'),'<strong>'.$field['name'].' ('.Pommo::_T('Number').')</strong>');
$f_date = sprintf(Pommo::_T('%s - Only calendar values will be accepted for this field. A date selector (calendar popup) will appear next to the field to aid the subscriber in selecting a date.'),'<strong>'.$field['name'].' ('.Pommo::_T('Date').')</strong>');
$f_mult = sprintf(Pommo::_T('%s - Subscribers will be able to select a value from the options you provide below. Multiple choice fields have reliable values for organizing, and are useful for collecting Country, Interests, etc.'),'<strong>'.$field['name'].' ('.Pommo::_T('Multiple Choice').')</strong>');
$f_comm = sprintf(Pommo::_T('%s -. If a subscriber enters a value for a comment field, it will be mailed to the admin notification email.'),'<strong>'.$field['name'].' ('.Pommo::_T('Comment').')</strong>');

switch ($field['type']) {
		case 'text' :
			$view->assign('intro', $f_text);
			break;
		case 'checkbox' :
			$view->assign('intro', $f_check);
			break;
		case 'number' :
			$view->assign('intro', $f_num);
			break;
		case 'date' :
			$view->assign('intro', $f_date);
			break;
		case 'multiple' :
			$view->assign('intro', $f_mult);
			break;
		case 'comment' :
			$view->assign('intro', $f_comm);
			break;
	}

$view->assign('field', $field);
$view->display('admin/setup/ajax/field_edit');
Pommo::kill();
