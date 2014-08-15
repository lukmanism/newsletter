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
require('bootstrap.php');

require_once ("classes/Pommo_Mailing.php");

Pommo_Mailing::saveHit($_GET['mailing'], $_GET['subscriber']);

header('Content-type: image/png');

echo gzinflate(base64_decode('6wzwc+flkuJiYGDg9fRwCQLSjCDMwQQkJ5QH3wNSb'
		.'CVBfsEMYJC3jH0ikOLxdHEMqZiTnJCQAOSxMDB+E7cIBcl7uvq5rHNKaAIA'));

