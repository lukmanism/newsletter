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

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../bootstrap.php');
Pommo::init();
$dbo = Pommo::$_dbo;

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once(Pommo::$_baseDir.'classes/Pommo_Json.php');
$json = new Pommo_Json();


// EXAMINE CALL
switch ($_REQUEST['call']) {
	case 'wysiwyg': // update wysiwyg ++ state
		$wysiwyg = (isset($_REQUEST['enable'])) ? 'on' : 'off';
		Pommo::$_session['state']['mailing']['wysiwyg'] = $wysiwyg;
		Pommo_Api::configUpdate(array('list_wysiwyg' => $wysiwyg), true);
	break;
	
	case 'savebody' : 
		Pommo::$_session['state']['mailing']['body'] = $_REQUEST['body'];
		Pommo::$_session['state']['mailing']['altbody'] = $_REQUEST['altbody'];
		Pommo::$_session['state']['mailing']['attachments'] = @implode(',', $_REQUEST['attachment']);

	break;
	
	case 'altbody' :
		require_once(Pommo::$_baseDir.'lib/lib.html2txt.php');
		$h2t = new html2text($_REQUEST['body']);
		$json->add('altbody',$h2t->get_text());
	break;
	
	case 'getTemplateDescription' :
		require_once(Pommo::$_baseDir.'classes/Pommo_Mailing_Template.php');
		$template = Pommo_Mailing_Template::getDescriptions(array('id' => $_REQUEST['id']));
		$msg = (empty($template[$_REQUEST['id']])) ? 'Unknown' : $template[$_REQUEST['id']];
		die($msg);
	
	default:
		$json->fail();
	break;
}

$json->success();
?>
