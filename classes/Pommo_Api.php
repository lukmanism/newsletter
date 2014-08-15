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
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA 02110-1301 USA.
 */

// common API

class Pommo_Api
{
    /*	getParams
     *	Merges default and given params
     *
     *	@param	array	$defaults.- Default parameters
     *	@param	array	$args.- Args passed
     *
     *	@return	array	$p.- Merged array
     */
    public static function getParams($defaults, $args)
    {
        $p = array_merge($defaults, $args);

        // 	make sure all submitted parameters are "known" by verifying size of
        //	final array
        if (count($p) > count($defaults)) {
            if (Pommo::$_verbosity < 3) {
                var_dump($defaults, $args);
            }
            Pommo::kill(
                'Unknown argument passed to Pommo_Api::getParams()', TRUE
            );
        }

        return $p;
    }

    /*	configGetBase
     *	Returns Base Configuration Data
     *
     *	@return	array	$config.- Config Base
     */
    public static function configGetBase()
    {
        $dbo = Pommo::$_dbo;
        $dbo->dieOnQuery(FALSE);

        $config = array();

        $query = 'SELECT config_name, config_value
            FROM '.$dbo->table['config'].'
            WHERE autoload="on"';
        $query = $dbo->prepare($query);

        while ($row = $dbo->getRows($query)) {
            $config[$row['config_name']] = $row['config_value'];
        }

        $dbo->dieOnQUery(TRUE);
        return $config;
    }

    /*	configGet
     *	Gets specified config value(s) from the DB.
     * 	Pass a single or array of config_names, returns array name->value.
     *
     *	@param	mixed	$arg.- Value or values to get
     *
     *	@return	array	$config.- Config Base
     */
    static function configGet($arg)
    {
        $dbo = Pommo::$_dbo;
        $dbo->dieOnQuery(FALSE);

        if ($arg == 'all') {
            $arg = null;
        }

        $query = 'SELECT config_name,config_value
            FROM '.$dbo->table['config'].'
            [WHERE config_name IN(%Q)]';
        $query = $dbo->prepare($query, array($arg));

        while ($row = $dbo->getRows($query)) {
            $config[$row['config_name']] = $row['config_value'];
        }

        $dbo->dieOnQUery(TRUE);
        return $config;
    }

    // update the config table.
    //  $input must be an array as key:value ([config_name] => config_value)
    static function configUpdate($input, $force = FALSE)
    {
        $dbo = Pommo::$_dbo;

        if (!is_array($input)) {
            Pommo::kill('Bad input passed to updateConfig', TRUE);
        }

        // if this is password, skip if empty
        if (isset($input['admin_password'])
            && empty($input['admin_password'])) {
            unset($input['admin_password']);
        }

        // get eligible config rows/options to change
        $force = ($force) ? null : 'on';
        $query = "
            SELECT config_name
            FROM " . $dbo->table['config'] . "
            WHERE config_name IN(%q)
            [AND user_change='%S']";
        $query = $dbo->prepare($query, array (array_keys($input), $force));

        // update rows/options
        $when = '';
        // multi-row update in a single query syntax
        while ($row = $dbo->getRows($query)) {
            $when .= $dbo->prepare(
                "WHEN '%s' THEN '%s'",
                array($row['config_name'], $input[$row['config_name']])
            ).' ';
            // limits multi-row update query to specific rows
            // (vs updating entire table)
            $where[] = $row['config_name'];
        }
        $query = "
            UPDATE " . $dbo->table['config'] . "
            SET config_value =
                CASE config_name ".$when." ELSE config_value END
            [WHERE config_name IN(%Q)]";
        $query = $dbo->prepare($query, array($where));

        if (!$dbo->query($query)) {
            Pommo::kill('Error updating config');
        }
        return true;
    }

    /*	stateInit
     *	initializes a page state
     *
     *	@param	string	$name.- Name of page state (usually unique per page)
     *	@param 	array	$defaults.- Default state variables
     *	@param	array	$source.- Overriding variables
     *
     *	@return	array	$state.- Current state
     */
    static function &stateInit($name = 'default', $defaults = array (),
        $source = array())
    {
        if (empty(Pommo::$_session['state'][$name])) {
            Pommo::$_session['state'][$name] = &$defaults;
        }

        $state = &Pommo::$_session['state'][$name];

        if (empty($defaults)) {
            return $state;
        }

        //Add support for passing multi select options
        if (is_array($source)) {
            foreach ($source as $k => $v) {
                if (is_array($source[$k])) {
                    $source[$k] = implode(',', $source[$k]);
                }
            }
        }

        foreach (array_keys($state) as $key) {
            if (array_key_exists($key, $source)) {
                $state[$key] = $source[$key];
            }
        }

        // normalize the page state
        if (count($state) > count($defaults)) {
            $state = Pommo_Helper::arrayIntersect($state, $defaults);
        }

        return $state;
    }

    // clears page state(s)
    // accepts a state name or array of state names to clear
    //   if not supplied, ALL page states are cleared
    // returns (bool)
    function stateReset($state = array())
    {
        if (!is_array($state))
            $state = array($state);

        if (empty($state))
            Pommo::$_session['state'] = array();
        else
            foreach($state as $s)
                unset(Pommo::$_session['state'][$s]);

        return true;
    }

    /**
     * Get attachments names based on attachment indexes
     *
     * @param string $attachments.- Attachments indexes separated by comma:
     *               $attachments = '1,2,4'
     *
     * @return array $result = Array with attachments details:
     *              array(
     *                  array(
     *                      'id' => 1,
     *                      'name' => 'file.txt'
     *                  ),
     *                  array(
     *                      'id' => 2,
     *                      'name' => 'image.jpg'
     *                  )
     *              )
     */
    public static function getAttachmentsWithNames($attachments)
    {
        if (empty($attachments)) {
            return false;
        }

        $attachments = explode(',', $attachments);
        $dbo = Pommo::$_dbo;
        $query =
            'SELECT
                file_id, file_name
            FROM
                ' . $dbo->table['attachment_files'] . '
            WHERE
                file_id IN(%q)';
        $query = $dbo->prepare($query, array($attachments));

        $result = array();
        while ($row = $dbo->getRows($query)) {
            $result[] = $row;
        }
        return $result;
    }
}
