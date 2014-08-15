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
require_once Pommo::$_baseDir.'classes/Pommo_Groups.php';
require_once Pommo::$_baseDir.'classes/Pommo_Fields.php';
require_once Pommo::$_baseDir.'classes/Pommo_Rules.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo 	= Pommo::$_dbo;

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Json.php';
$json = new Pommo_Json();

// Remember the Page State
$state = Pommo_Api::stateInit('groups_edit');

// EXAMINE CALL
switch ($_REQUEST['call'])
{
	case 'displayRule' :
		/**********************************
			SETUP TEMPLATE, PAGE
		 *********************************/
		require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
		$view = new Pommo_Template();

		$group = current(Pommo_Groups::get(array('id' => $state['group'])));
		if (empty($group))
		{
			die('invalid input');
		}
	
		if ($_REQUEST['ruleType'] == 'field')
		{
			$field = current(Pommo_Fields::get(array(
					'id' => $_REQUEST['fieldID'])));
			$logic = (isset($_REQUEST['logic']) && $_REQUEST['logic'] != "0") ?
					$_REQUEST['logic'] : false;
			$type = ($_REQUEST['type'] == 'or') ? 'or' : 'and';
			
			$values = array();
			
			// check to see if we're editing [logic is passed *only* when edit button is clicked]
			if ($logic)
			{
				foreach ($group['rules'] as $rule)
				{
					if ($rule['logic'] == $logic && $rule['field_id'] ==
							$_REQUEST['fieldID'])
					{
						$values[] = ($field['type'] == 'date') ? 
								Pommo_Helper::timeFromStr($rule['value']) :
								$rule['value'];
					}
				}
			}

			$firstVal = (empty($values)) ? false : array_shift($values);

			$logic = ($logic) ? 
					Pommo_Rules::getEnglish(array($logic)) : 
					Pommo_Rules::getEnglish(end(
							Pommo_Rules::getLegal($group,array($field))));
							
			$view->assign('type', $type);
			$view->assign('field',$field);
			$view->assign('logic',$logic);
			$view->assign('values',$values);
			$view->assign('firstVal',$firstVal);
		
			$view->display('admin/subscribers/ajax/rule.field');
		}
		elseif($_REQUEST['ruleType'] == 'group') {
			$match = Pommo_Groups::getNames($_REQUEST['fieldID']);
			$key = key($match);
			
			$view->assign('match_name',$match[$key]);
			$view->assign('match_id',$key);
			
			$view->display('admin/subscribers/ajax/rule.group');
			Pommo::kill();
		}
	break;
	
	case 'addRule': 
		switch($_REQUEST['logic']) {
			case 'is_in':
			case 'not_in':
				Pommo_Rules::addGroupRule($state['group'], $_REQUEST['field'], $_REQUEST['logic']);
				break;
			case 'true':
			case 'false':
				Pommo_Rules::addBoolRule($state['group'], $_REQUEST['field'], $_REQUEST['logic']);
				break;
			case 'is':
			case 'not':
			case 'less':
			case 'greater':
				
				$values = array_unique($_REQUEST['match']);
				$type = ($_REQUEST['type'] == 'or') ? 'or' : 'and';
				
				Pommo_Rules::addFieldRule($state['group'], $_REQUEST['field'], $_REQUEST['logic'], $values, $type);
				break;
		}
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',Pommo::$_baseUrl.'groups_edit.php');
		$json->serve();
	break;
	
	case 'updateRule' :
		require_once(Pommo::$_baseDir.'classes/Pommo_Sql.php');
		$group =& current(Pommo_Groups::get(array('id' => $state['group'])));
		$rules = Pommo_Sql::sortRules($group['rules']);
		
		switch ($_REQUEST['request']) {
			case 'update' :
				if($_REQUEST['type'] == 'or' && count($rules['and']) < 2) {
					$json->add('callbackFunction','resume');
					$json->success(Pommo::_T('At least 1 "and" rule must exist before an "or" rule takes effect.'));
				}
				Pommo_Rules::changeType($group['id'], $_REQUEST['fieldID'], $_REQUEST['logic'], $_REQUEST['type']);
				break;
				
			case 'delete' :
				Pommo_Rules::deleteRule($group['id'], $_REQUEST['fieldID'], $_REQUEST['logic']);
				break;
		}
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',Pommo::$_baseUrl.'groups_edit.php');
		$json->serve();
	break;

	case 'renameGroup': 
		if (!empty($_REQUEST['group_name']))
			if (Pommo_Groups::nameChange($state['group'], $_REQUEST['group_name']))
				$json->success(Pommo::_T('Group Renamed'));
			$json->fail('invalid group name');
		break;

	default:
		die('invalid request passed to '.__FILE__);
	break;
}

