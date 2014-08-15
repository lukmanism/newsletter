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
class Pommo_Install
{

    public static $errors;

    /**
     * 	validateInstallationData
     * 	Validates data necessary for installation
     *
     * 	@param	array	$data.- Data to be validated
     *
     * 	@return	boolean	True on success, false otherwise
     */
    public static function validateInstallationData($data)
    {
        require_once(Pommo::$_baseDir.'classes/Pommo_Validate.php');
        self::$errors = array();
        $validator = new Pommo_Validate();
        $validator->setPost($data);
        $validator->addData('list_name', 'Other', false);
        $validator->addData('site_name', 'Other', false);
        $validator->addData('site_url', 'Url', false);
        $validator->addData('admin_password', 'Other', false);
        $validator->addData('admin_email', 'Email', false);
        $validator->addPasswordMatch('admin_password', 'admin_password2');
        $result = $validator->checkData();
        self::$errors = $validator->getErrors();
        return $result;
    }

    /* 	parseSQL
     * 	parses a SQL file (usually generated via mysqldump)
     * 	text like ':::table:::' will be replaced with $dbo->table['table'];
     * 	(to add prefix)
     *
     * 	@param	boolean	$ignoreerrors
     * 	@param	boolean	$file
     *
     * 	@return	boolean	True on success, false otherwise
     */

    static function parseSQL($ignoreerrors = false, $file = false)
    {
        $dbo = Pommo::$_dbo;
        $logger = Pommo::$_logger;

        if (!$file)
        {
            $file = Pommo::$_baseDir.'sql/sql.schema.php';
        }

        $file_content = @file($file);
        if (empty($file_content))
        {
            Pommo::kill('Error installing. Could not read '.$file);
        }
        $query = '';
        foreach ($file_content as $sql_line)
        {
            $tsl = trim($sql_line);
            if (($sql_line != "")
                    && (substr($tsl, 0, 2) != "--")
                    && (substr($tsl, 0, 1) != "#"))
            {
                $query .= $sql_line;
                if (preg_match("/;\s*$/", $sql_line))
                {
                    $matches = array();
                    preg_match('/:::(.+):::/', $query, $matches);
                    if ($matches[1])
                    {
                        $query = preg_replace('/:::(.+):::/', $dbo->table[$matches[1]], $query);
                    }
                    $query = trim($query);
                    if (!$dbo->query($query) && !$ignoreerrors)
                    {
                        $logger->addErr(Pommo::_T('Database Error: ').
                                $dbo->getError());
                        return false;
                    }
                    $query = '';
                }
            }
        }
        return true;
    }

    /**
     * 	verify
     * 	verify if poMMo has been installed
     *
     * 	@param	void
     *
     * 	@return	boolean	True if installed, false otherwise
     */
    static function verify()
    {
        global $pommo;
        $dbo = & Pommo::$_dbo;
        if (is_object($dbo))
        {
            $query = "SHOW TABLES LIKE '%s'";
            $query = $dbo->prepare($query, array($dbo->_prefix.'%'));
            if ($dbo->records($query) > 10)
                return true;
        }
        return false;
    }

    /**
     * 	incUpdate
     * 	performs an update increment
     *
     * 	@param  integer $serial - Version being checked
     *                  $sql    -
     *
     * 	@return	boolean	True if installed, false otherwise
     */
    function incUpdate($serial, $sql, $msg = "Performing Update", $eval = false)
    {
        global $pommo;
        $dbo = & Pommo::$_dbo;
        $logger = & Pommo::$_logger;

        if (!is_numeric($serial))
            Pommo::kill('Invalid serial passed; '.$serial);

        $msg = $serial.". $msg ...";

        $query = "
			SELECT serial FROM ".$dbo->table['updates']."
			WHERE serial=%i";

        $query = $dbo->prepare($query, array($serial));
        if ($dbo->records($query))
        {
            $msg .= "skipped.";
            $logger->addMsg($msg);
            return true;
        }

        if (!isset($GLOBALS['pommoFakeUpgrade']))
        {
            // run the update
            if ($eval)
            {
                eval($sql);
            } else
            {
                $query = $dbo->prepare($sql);
                if (!$dbo->query($query))
                {
                    // query failed...
                    $msg .= ($GLOBALS['pommoLooseUpgrade']) ?
                            'IGNORED.' : 'FAILED';
                    $logger->addErr($msg);

                    return $GLOBALS['pommoLooseUpgrade'];
                }
            }

            $msg .= "done.";
            $logger->addMsg($msg);
        } else
        {
            $msg .= "skipped.";
            $logger->addMsg($msg, 2);
        }

        $query = "
			INSERT INTO ".$dbo->table['updates']."
			(serial) VALUES(%i)";
        $query = $dbo->prepare($query, array($serial));
        if (!$dbo->query($query))
            Pommo::kill('Unable to serialize');

        return true;
    }

    /**
     * Saves user supplied installation values
     * @param array data - Array with user supplied information. new Array(
     *   'list_name' => 'Name of mailing list',
     *   'site_name' => 'Name of the site',
     *   'site_url' => 'Url of the user website'
     * );
     * @return boolean True if good, false otherwise
     */
    public static function saveUserValues($data)
    {
        $dbo = Pommo::$_dbo;

        $query = 'UPDATE ' . $dbo->table['config'] . '
            SET config_value = "%s"
            WHERE config_name = "list_name"';
        $query = $dbo->prepare($query, array($data['list_name']));
        $dbo->query($query);

        $query = 'UPDATE ' . $dbo->table['config'] . '
            SET config_value = "%s"
            WHERE config_name = "site_name"';
        $query = $dbo->prepare($query, array($data['site_name']));
        $dbo->query($query);

        $query = 'UPDATE ' . $dbo->table['config'] . '
            SET config_value = "%s"
            WHERE config_name = "site_url"';
        $query = $dbo->prepare($query, array($data['site_url']));
        $dbo->query($query);

        return true;
    }
}
