<?php
/**
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
 * @author Adrian Ancona<soonick5@yahoo.com.mx>
 */

/**********************************
    INITIALIZATION METHODS
*********************************/
require 'bootstrap.php';
Pommo::init();

require_once Pommo::$_baseDir.'classes/Pommo_Subscribers.php';
$subscribers = new Pommo_Subscribers();
$hits = $subscribers->getSubscribersHits($_GET['mailing']);

/**********************************
    SETUP TEMPLATE, PAGE
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Template.php';
$view = new Pommo_Template();

/**********************************
    DISPLAY METHODS
*********************************/

$view->assign('hits', $hits);

$view->display('admin/mailings/export_hits');
