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
require_once Pommo::$_baseDir.'classes/Pommo_Groups.php';

Pommo::init();
$logger	= Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

// add group if requested
if (!empty ($_POST['group_name']))
{
	if (Pommo_Groups::nameExists($_POST['group_name']))
	{
		$logger->addMsg(sprintf(Pommo::_T('Group name (%s) already exists'),$_POST['group_name']));
	}
	else
	{
		$group = Pommo_Groups::make(array('name' => $_POST['group_name']));
		$id = Pommo_Groups::add($group);
		($id) ?
			Pommo::redirect("groups_edit.php?group=$id") :
			$logger->addMsg(Pommo::_T('Error with addition.'));
	}
}

if (!empty ($_GET['delete'])) {
	// make sure it is a valid group
	$group = current(Pommo_Groups::get(array('id' => $_GET['group_id'])));
	if (empty($group))
		Pommo::redirect($_SERVER['PHP_SELF']);

	$affected = Pommo_Groups::rulesAffected($group['id']);

	// See if this change will affect any subscribers, if so, confirm the change.
	if ($affected > 1 && empty ($_GET['dVal-force'])) {
		$view->assign('confirm', array (
			'title' => Pommo::_T('Confirm Action'),
			'nourl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'],
			'yesurl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'] . '&delete=TRUE&dVal-force=TRUE',
			'msg' => sprintf(Pommo::_T('%1$s filters belong this group . Are you sure you want to remove %2$s?'), '<b>' . $affected . '</b>','<b>' . $group['name'] . '</b>')));
		$view->display('admin/confirm');
		Pommo::kill();
	} else {
		// delete group
		if (!Pommo_Groups::delete($group['id']))
			$logger->addMsg(Pommo::_T('Group cannot be deleted.'));
		else
			$logger->addMsg(sprintf(Pommo::_T('%s deleted.'),$group['name']));
	}
}

// Get array of mailing groups. Key is ID, value is name
$groups = Pommo_Groups::getNames();

$view->assign('groups', $groups);
$view->display('admin/subscribers/subscribers_groups');

