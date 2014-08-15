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
 * A class for working with directories
 */
class Pommo_Filesystem_Directory extends Pommo_Filesystem_Item
{
    /**
     * Deletes this directory and all its children
     * @return bool Success or Failure
     */
    public function delete()
    {
        $handle = opendir($this->_itemPath);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $filesystemItem = Pommo_Filesystem_Item::factory($this->_itemPath . '/' . $file);
                $filesystemItem->delete();
            }
        }

        closedir($handle);
        rmdir($this->_itemPath);
        return true;
    }

    /**
     * Copies this directory and all its children to $destination
     * @param $destination
     * @return bool Success or Failure
     */
    public function copy($destination)
    {
        @mkdir($destination);

        $directory = opendir($this->_itemPath);

        @mkdir($destination);

        while (false !== ($file = readdir($directory))) {
            if (($file != '.') && ($file != '..')) {
                $filesystemItem = Pommo_Filesystem_Item::factory($this->_itemPath . '/' . $file);
                $filesystemItem->copy($destination . '/' . $file);
            }
        }

        closedir($directory);
        return true;
    }

    /**
     * @return array|Pommo_Filesystem_Directory[]
     */
    public function getSubDirectories()
    {
        $subDirectories = glob(rtrim($this->_itemPath) . '/*', GLOB_ONLYDIR);

        $subDirectoryItems = array();
        foreach ($subDirectories as $subDirectory) {
            $subDirectoryItems[] = new self($subDirectory);
        }
        return $subDirectoryItems;
    }

    /**
     * Gets all file objects that are immediate sub-children of this directory
     * @return array|Pommo_Filesystem_File[]
     */
    public function getDirectoryFiles()
    {
        $files = glob(rtrim($this->_itemPath, '/') . '/*');

        $fileObjects = array();
        if ($files) {
            foreach ($files as $file) {
                $fileObject = Pommo_Filesystem_Item::factory($file);
                if ($fileObject instanceof Pommo_Filesystem_File)  {
                    $fileObjects[] = $fileObject;
                }
            }
        }
        return $fileObjects;
    }

    /**
     * Gets an array list of all the sub-directories found in this directory... recursively
     * @return array
     */
    public function getAllSubfoldersList()
    {
        return $this->_getAllFoldersRecursive($this->_itemPath);
    }

    /**
     * Recursive Helper function for getAllSubfoldersList()
     * @param string $directory
     * @return array
     */
    private function _getAllFoldersRecursive($directory)
    {
        $allFolders = array($directory);

        $directories = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

        foreach ($directories as $subDirectory) {
            $allFolders = array_merge($allFolders, $this->_getAllFoldersRecursive($subDirectory));
        }

        return $allFolders;
    }
}
