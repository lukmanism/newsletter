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
require_once Pommo::$_baseDir.'classes/Pommo_Sql.php';
require_once Pommo::$_baseDir.'classes/Pommo_Groups.php';
require_once Pommo::$_baseDir.'classes/Pommo_Fields.php';
require_once Pommo::$_baseDir.'classes/Pommo_Rules.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();
$view->assign('returnStr', _('Groups Page'));

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& Pommo_Api::stateInit('groups_edit',array(
	'group' => 0),
	$_REQUEST);


$groups = Pommo_Groups::get();
$fields = Pommo_Fields::get();

$group =& $groups[$state['group']];

if(empty($group))
	Pommo::redirect('subscribers_groups.php');

$rules = Pommo_Sql::sortRules($group['rules']);
$rules['and'] = Pommo_Sql::sortLogic($rules['and']);
$rules['or'] = Pommo_Sql::sortLogic($rules['or']);

foreach($rules as $key => $a) {
	if ($key == 'include' || $key == 'exclude')
		foreach($a as $k => $gid)
			$rules[$key][$k] = $groups[$gid]['name'];
}


$view->assign('fields',$fields);

$view->assign('legalFieldIDs', Pommo_Rules::getLegal($group, $fields));
$view->assign('legalGroups', Pommo_Rules::getLegalGroups($group, $groups));



$view->assign('group',$group);

$view->assign('logicNames',Pommo_Rules::getEnglish());



$view->assign('rules', $rules);
$view->assign('tally', Pommo_Groups::tally($group));
$view->assign('ruleCount', count($rules['and'])+count($rules['or'])+count($rules['include'])+count($rules['exclude']));

$view->assign('getURL',$_SERVER['PHP_SELF'].'?group_id='.$group['id']);
$view->assign('t_include',Pommo::_T('INCLUDE'));

$view->display('admin/subscribers/groups_edit');

