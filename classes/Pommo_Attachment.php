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

/**
 * Mailing attachment
 */
class Pommo_Attachment
{
    /**
     * Attachment id
     * @var int
     */
    private $_id;

    /**
     * Set id attribute
     *
     * @param int $id.- Attachment id
     */
    public function __construct($id)
    {
        $this->_id = (int)$id;
    }

    /**
     * Delete attachment from DB
     */
    public function delete()
    {
        $dbo = Pommo::$_dbo;

        // Delete from attachment_files
        $query = 'DELETE FROM ' . $dbo->table['attachment_files']
            . ' WHERE file_id = ' . $this->_id;
        $dbo->query($query);

        // Delete from mailings_attachments
        $query = 'DELETE FROM ' . $dbo->table['mailings_attachments']
            . ' WHERE file_id = ' . $this->_id;
        $dbo->query($query);

        return true;
    }
}
