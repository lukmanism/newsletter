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

require_once Pommo::$_baseDir.'classes/Pommo_Filesystem_File.php';
require_once Pommo::$_baseDir.'classes/Pommo_Filesystem_Directory.php';

/**
 * A class for working with files and directories
 */
abstract class Pommo_Filesystem_Item
{
    /**
     * The path to the file or directory associated with this class
     * @var string
     */
    protected $_itemPath = '';

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->_itemPath = $filePath;
    }

    /**
     * @static
     * @param $itemPath
     * @return bool|Pommo_Filesystem_Directory|Pommo_Filesystem_File
     */
    public static function factory($itemPath)
    {
        if (!file_exists($itemPath)) {
            return false;
        } elseif (is_file($itemPath)) {
            return new Pommo_Filesystem_File($itemPath);
        }
        return new Pommo_Filesystem_Directory($itemPath);
    }

    /**
     * @return string
     */
    public function getItemPath()
    {
        return $this->_itemPath;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->_itemPath);
    }

    /**
     * @return DateTime
     */
    public function getDateLastModified()
    {
        return new DateTime('@' . filemtime($this->_itemPath));
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return basename($this->_itemPath);
    }

    /**
     * Creates all directories in the file path if the directory doesn't already exist
     */
    public function createAllDirectoriesInPath()
    {
        $path = '';

        $directories = explode('/', dirname($this->_itemPath));

        foreach ($directories as $i => $directory) {
            if ($i > 0) {
                $path .= '/';
            }
            $path .= $directory;

            if (!file_exists($path)) {
                @mkdir($path, 0777);
            }
        }
    }

    /**
     * Deletes this file or Directory
     * @return bool Success or Failure
     */
    public abstract function delete();

    /**
     * Copies this file/directory and all its children to $destination
     * @param $destination
     * @return bool Success or Failure
     */
    public abstract function copy($destination);
}
