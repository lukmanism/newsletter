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

/**
 * A class for working with files and directories
 */
class Pommo_Filesystem_File extends Pommo_Filesystem_Item
{
    /**
     * Deletes this file or Directory
     * @return bool Success or Failure
     */
    public function delete()
    {
        return unlink($this->_itemPath);
    }

    /**
     * Copies this file/directory and all its children to $destination
     * @param $destination
     * @return bool Success or Failure
     */
    public function copy($destination)
    {
        return copy($this->_itemPath, $destination);
    }

    /**
     * Returns the filename extension
     * @return string
     */
    public function getExtension()
    {
        $info = pathinfo($this->_itemPath);
        return $info['extension'];
    }

    /**
     * Gets the exact file size in bytes or the human readable file size
     * @param bool $humanReadable
     * @return int|string
     */
    public function getFileSize($humanReadable = false)
    {
        $size = filesize($this->_itemPath);

        if (!$humanReadable) {
            return $size;
        }

        $i = 0;

        $sizeSuffixes = array(
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        );

        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $i++;
        }

        return round(mb_substr($size, 0, strpos($size, '.') + 4), 2) . $sizeSuffixes[$i];
    }
}
