<?php
/**
 *  Original Code Copyright (C) 2012 Adrian Ancona <soonick5@yahoo.com.mx>
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
 */

require('../bootstrap.php');

require_once Pommo::$_baseDir . 'classes/Pommo_Attachment.php';
$attachment = new Pommo_Attachment($_POST['id']);
$attachment->delete();

echo json_encode(array('success' => true));
