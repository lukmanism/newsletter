<?php
/**
 * Copyright (C) 2010  Adrian Ancona Novelo <soonick5@yahoo.com.mx>
 * 
 * This file is part of poMMo (https://github.com/soonick/pommo)
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
require '../bootstrap.php';
require_once Pommo::$_baseDir.'classes/Pommo_Mailing.php';
require_once Pommo::$_baseDir.'classes/Pommo_User.php';

Pommo::init();
$logger	= &Pommo::$_logger;
$dbo	= &Pommo::$_dbo;

//	Get the users
$user = new Pommo_User();
$data = array('limit' => $_GET['rows'], 'page' => $_GET['page']);
$list = $user->getList($data);

//	Format the list for jqgrid
$rows = array();
$i = 0;
foreach ($list as $item)
{
	$rows[$i]['id'] = $item['username'];
	$rows[$i]['username'] = $item['username'];
	$i++;
}

$json = array('total' => $user->pages, 'page' => $_GET['page'], 'records' =>
		$user->records, 'rows' => $rows);
echo json_encode($json);

