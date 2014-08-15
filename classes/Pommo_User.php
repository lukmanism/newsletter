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

/*	Pommo_User
 *	Class in charge of opperations about users (adding, deleting, etc...)
 */
class Pommo_User
{
    public $pages;
    public $records;

    /*	__construct
     *
     *	@return	void
     */
    function __construct()
    {
    }

    /*	save
     *	Saves a new user in the database
     *
     *	@param	string	$username
     *	@param	string	$password
     *
     *	@return	boolean	True if the user was saved, false otherwise
     */
    function save($username, $password)
    {
        try
        {
            if (!$username || !$password) {
                throw new Exception();
            }

            $dbo = Pommo::$_dbo;
            $dbo->_dieOnQuery = false;

            $query = 'INSERT INTO '.$dbo->table['users'].'
                    SET username = "%s", password = SHA1("%s")';
            if (!$dbo->query($dbo->prepare($query, array($username, $password)))) {
                throw new Exception();
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /*	login
     *	Checks if a user-password combination exists in the database
     *
     *	@param	string	$username
     *	@param	string	$password
     *
     *	@return	boolean	True if the user was found, false otherwise
     */
    function login($username, $password)
    {
        try
        {
            $dbo = Pommo::$_dbo;
            $dbo->_dieOnQuery = false;

            $query = 'SELECT username
                    FROM '.$dbo->table['users'].'
                    WHERE username = "%s" && password = SHA1("%s")';
            $query = $dbo->prepare($query, array($username, $password));
            if ($row = $dbo->getRows($query)) {
                return true;
            }
            throw new Exception();
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /*	getList
     *	Returns array with users in DB
     *
     *	@param	array	$data = array(
     *					'limit' => 'Records per page,
     *					'order' => 'ASC or DESC',
     *					'page' => 'Page to return
     *					)
     *
     *	@return	boolean	True if the user was found, false otherwise
     */
    function getList($data)
    {
        try
        {
            $this->pages = 0;

            if (50 < $data['limit'] && 0 >= $data['limit']) {
                $data['limit'] = 10;
            }

            if (!in_array($data['order'], array('ASC', 'DESC'))) {
                $data['order'] = 'ASC';
            }

            if (!$data['page']) {
                $data['page'] = 1;
            }

            $dbo = Pommo::$_dbo;
            $dbo->_dieOnQuery = false;

            //	Calculate total number of pages
            $query = 'SELECT COUNT(username) AS total
                    FROM '.$dbo->table['users'];
            $records = (int)$dbo->query($query, 0);
            $this->records = $records;
            $this->pages = ceil($records / $data['limit']);

            if ($data['page'] > $this->pages) {
                $data['page'] = $this->pages;
            }

            $skip = (int)($data['limit'] * ($data['page'] - 1));

            //	Get the users
            $query = 'SELECT username
                    FROM '.$dbo->table['users']
                    .' ORDER BY username '.$data['order']
                    .' LIMIT '.$skip.', %i';
            $query = $dbo->prepare($query, array($data['limit']));
            $users = $dbo->getAll($query);
            return $users;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /*	delete
     *	Deletes users from DB
     *
     *	@param	mixed	$users.- String with username or array with usernames
     *
     *	@return	boolean	True on success, false otherwise
     */
    public function delete($users)
    {
        try
        {
            if (!is_array($users)) {
                $users = array($users);
            }

            $dbo = Pommo::$_dbo;
            $dbo->_dieOnQuery = false;

            $return = true;
            foreach ($users as $user) {
                $query = 'DELETE FROM '.$dbo->table['users'].'
                        WHERE username = "%s"';
                if (!$dbo->query($dbo->prepare($query, array($user)))) {
                    $return = false;
                }
            }
            return $return;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}

