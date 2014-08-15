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
require_once Pommo::$_baseDir.'classes/Pommo_Csv_Stream.php';
require_once Pommo::$_baseDir.'classes/Pommo_Subscribers.php';
require_once Pommo::$_baseDir.'classes/Pommo_Validate.php';

Pommo::init(array('keep' => TRUE));
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

$dupes = $tally = $flagged = 0;
$dupe_emails = array();
$fp = fopen(Pommo::$_workDir.'/import.csv','r') 
	or die('Unable to open CSV file');

$includeUnsubscribed = isset($_REQUEST['excludeUnsubscribed']) ? false : true;

while (($row = fgetcsv($fp,2048,',','"')) !== FALSE) {
	$subscriber = array(
		'email' => false,
		'registered' => time(),
		'ip' => $_SERVER['REMOTE_ADDR'],
		'status' => 1,
		'data' => array());
	foreach ($row as $key => $col) {
		$fid =& $_POST['f'][$key];
		if (is_numeric($fid))
			$subscriber['data'][$fid] = $col;
		elseif ($fid == 'email' && Pommo_Helper::isEmail($col))
			$subscriber['email'] = $col;
		elseif ($fid == 'registered')
			$subscriber['registered'] = Pommo_Helper::timeFromStr($col);
		elseif ($fid == 'ip')
			$subscriber['ip'] = $col;
	}
	if ($subscriber['email']) {
		// check for dupe
		// TODO -- DO THIS IN BATCH ??
		if (Pommo_Helper::isDupe($subscriber['email'],$includeUnsubscribed)) {
			$dupes++;
			$dupe_emails []= $subscriber['email'];
			continue;
		}

		// validate/fix data
		if(!Pommo_Validate::subscriberData($subscriber['data'], array(
			'log' => false,
			'ignore' => true,
			'active' => false)))
			$subscriber['flag'] = 9;

		// add subscriber
		if (Pommo_Subscribers::add($subscriber)) {
			$tally++;
			if (isset($subscriber['flag']))
				$flagged++;
		}
	}

}

unlink(Pommo::$_workDir.'/import.csv');
echo '<div class="warn"><p>'.sprintf(_('%s subscribers imported! Of these, %s'
		.' were flagged to update their records.'), $tally, $flagged).'<p>'
		.sprintf(_('%s duplicates encountered.'), $dupes).'</p></div>';

echo "<table>";
foreach($dupe_emails as $de)
{
  echo '<tr><td>'.$de.'</td></tr>';
}
echo "</table>";

die(_('Complete!').' <a href="subscribers_import.php">'._('Return to').' '
		._('Import').'</a>');

