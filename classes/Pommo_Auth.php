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
 *  MA 02110-1301 USA.
 */

// authentication object. Handles logged in user, permission level.
class Pommo_Auth
{
    private $_username;	// current logged in user (default: null|session value)
    private $_permissionLevel; // permission level of logged in user
    private $_requiredLevel; // required level of permission (default: 1)

    /*	__construct
     *	default constructor. Get current logged in user from session.
     *	permissions.
     *
     *	@param	array	$args
     *
     *	@return	void
     */
    function __construct($args = array ())
    {
        $defaults = array (
            'username' => null,
            'requiredLevel' => 0
        );

        $p = Pommo_Api::getParams($defaults, $args);

        if (empty(Pommo::$_session['username'])) {
            Pommo::$_session['username'] = $p['username'];
        }

        $this->_username = &Pommo::$_session['username'];
        $this->_permissionLevel = $this->getPermissionLevel($this->_username);

        if ($p['requiredLevel'] > $this->_permissionLevel) {
            Pommo::kill(
                sprintf(
                    Pommo::_T(
                        'Denied access. You must %slogin%s to' .
                        ' access this page...'
                    ),
                    '<a href="' . Pommo::$_baseUrl .
                    'index.php?referer=' . $_SERVER['PHP_SELF'] .
                    '">',
                    '</a>'
                )
            );
        }

    }


    /*	getPermissionLevel
     *	Returns current user permission level
     *
     *	@param	string	$username
     *
     *	@return	int		0 if not logged in
     */
    function getPermissionLevel($username = null)
    {
        if ($username) {
            return 5;
        }
        return 0;
    }

    function logout()
    {
        $this->_username = null;
        $this->_permissionLevel = 0;
        session_destroy();
        return;
    }

    /*	login
     *	Saves a user login
     *
     *	@param	string	$username.- User name to save for session
     *
     *	@return	boolean	True;
     */
    function login($username)
    {
        $this->_username = $username;
        return true;
    }

    // Check if a user is authenticated (logged on)
    function isAuthenticated()
    {
        return (empty($this->_username)) ? false : true;
    }
}

