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
 
//	TODO: Check that magic quotes is turned off

// 	While poMMo is in development state, we'll attempt to display PHP notices,
//	warnings, errors
ini_set('display_errors', '1');

//error_reporting(E_ALL); // [DEVELOPMENT]
error_reporting(E_ALL ^ E_NOTICE); // [RELEASE]

// 	Include core components
require('classes/Pommo_Helper.php');
require('classes/Pommo_Api.php');
require('classes/Pommo.php');

//	Instantiate pommo
Pommo::preinit(dirname(__FILE__).'/');

