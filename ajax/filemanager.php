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

/**********************************
	INITIALIZATION METHODS
*********************************/
require '../bootstrap.php';

Pommo::init();
$logger = Pommo::$_logger;
$dbo = Pommo::$_dbo;

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once Pommo::$_baseDir.'classes/Pommo_Filesystem_Item.php';
$json = array();

define('ROOT_IMAGE_DIRECTORY', '../uploadedimages/');
define('IMAGE_DIRECTORY', ROOT_IMAGE_DIRECTORY . 'data/');

// EXAMINE CALL
switch ($_GET['action']) {
	// List all sub-directories in the specified directory
	case 'directory': 
		$directoryObject = new Pommo_Filesystem_Directory(IMAGE_DIRECTORY . $_POST['directory']);

		foreach ($directoryObject->getSubDirectories() as $directory) {
			$directoryArray = array(
				'data' => $directory->getBasename(),
				'attributes' => array('directory' => mb_substr($directory->getItemPath(), strlen(IMAGE_DIRECTORY))),
				'children' => $directory->getSubDirectories() ? ' ' : ''
			);
							
			$json[] = $directoryArray;
		}
				
		break;
	// List all files in the specified directory
	case 'files' :
		$directory = IMAGE_DIRECTORY;
		if (!empty($_POST['directory'])) {
			$directory .= $_POST['directory'];
		}
		$directoryObject = Pommo_Filesystem_Item::factory($directory);
				
		foreach ($directoryObject->getDirectoryFiles() as $file) {						
			$json[] = array(
				'filename' => basename($file->getItemPath()),
				'file'     => mb_substr($file->getItemPath(), strlen(IMAGE_DIRECTORY)),
				'size'     => $file->getFileSize(true)
			);
		}

		break;
	// Create a cached thumbnail for a given image and return the thumbnail path
	case 'image' :
		require_once('../classes/Pommo_Filesystem_Image.php');
		$imagePath = ltrim($_GET['image'], '/');
		$thumbWidth = 100;
		$thumbHeight = 100;
				
		if (!file_exists(IMAGE_DIRECTORY . $imagePath) || !is_file(IMAGE_DIRECTORY . $imagePath)) {
			// We don't want to output the $json array for this action, so just exit if an error occurs
			exit;
		}
				
		$oldImageObject = new Pommo_Filesystem_Image(IMAGE_DIRECTORY . $imagePath);
		$newImagePath = 'cache/' . mb_substr($imagePath, 0, strrpos($imagePath, '.')) . '-' 
			. $thumbWidth . 'x' . $thumbHeight . '.' . $oldImageObject->getExtension();
		$newImageObject = new Pommo_Filesystem_File(ROOT_IMAGE_DIRECTORY . $newImagePath);
				
		if (!$newImageObject->exists() 
			|| ($oldImageObject->getDateLastModified() > $newImageObject->getDateLastModified())) 
		{
			$newImageObject->createAllDirectoriesInPath();
			$oldImageObject->resize($thumbWidth, $thumbHeight, true);
			$oldImageObject->save($newImageObject->getItemPath());
		}
		
		echo mb_substr($newImageObject->getItemPath(), 3); // Remove "../" for javascript
		exit;
	// Create a new directory in the given location
	case 'create':
		$directory = '';
				
		if (isset($_POST['directory'])) {
			if (isset($_POST['name']) || $_POST['name']) {
				$directory = rtrim(IMAGE_DIRECTORY . $_POST['directory'], '/');							   
								
				if (!is_dir($directory)) {
					$json['error'] = _('Warning: Please select a directory!');
				}
								
				if (file_exists($directory . '/' . $_POST['name'])) {
					$json['error'] = _('Warning: A file or directory with the same name already exists!');
				}
			} else {
				$json['error'] = _('Warning: Please enter a new name!');
			}
		} else {
			$json['error'] = _('Warning: Please select a directory!');
		}
				
		if (!isset($json['error'])) {	
			mkdir($directory . '/' . $_POST['name'], 0777);
						
			$json['success'] = _('Success: Directory created!');
		}	
		break;
	// Delete the specified file or directory
	case 'delete':
				
		$path = '';
		if (isset($_POST['path'])) {
			$path = rtrim(IMAGE_DIRECTORY . html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8'), '/');
			 			 
			if (!file_exists($path)) {
				$json['error'] = _('Warning: Please select a directory or file!');
			}
						
			if ($path == rtrim(IMAGE_DIRECTORY, '/')) {
				$json['error'] = _('Warning: You can not delete this directory!');
			}
		} else {
			$json['error'] = _('Warning: Please select a directory or file!');
		}
				
		if (!isset($json['error'])) {
			$fileObject = Pommo_Filesystem_Item::factory($path);
			$fileObject->delete();
			$json['success'] = _('Success: Your file or directory has been deleted!');
		}				
				
		break;
	// Handle an uploaded file
	case 'upload':
				
		if (isset($_POST['directory'])) {
			if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
				$filename = basename(html_entity_decode($_FILES['image']['name'], ENT_QUOTES, 'UTF-8'));
								
				if ((strlen($filename) < 3) || (strlen($filename) > 255)) {
					$json['error'] = _('Warning: Filename must be a between 3 and 255!');
				}
										
				$directory = rtrim(IMAGE_DIRECTORY . $_POST['directory'], '/');
								
				if (!is_dir($directory)) {
					$json['error'] = _('Warning: Please select a directory!');
				}

				if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = 'error_upload_' . $_FILES['image']['error'];
				}			
			} else {
				$json['error'] = _('Warning: Please select a file!');
			}
		} else {
			$json['error'] = _('Warning: Please select a directory!');
		}
				
		if (!isset($json['error'])) {	
			if (@move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . $filename)) {		
				$json['success'] = _('Success: Your file has been uploaded!');
			} else {
				$json['error'] = _('Warning: File could not be uploaded for an unknown reason!');
			}
		}
				
		break;
	// Move a file or directory to another directory
	case 'move':
				
		if (isset($_POST['from']) && isset($_POST['to'])) {
			$from = rtrim(IMAGE_DIRECTORY . html_entity_decode($_POST['from'], ENT_QUOTES, 'UTF-8'), '/');
						
			if (!file_exists($from)) {
				$json['error'] = _('Warning: File or directory does not exist!');
			}
						
			if ($from . '/' == IMAGE_DIRECTORY) {
				$json['error'] = _('Warning: Can not alter your root directory!');
			}
						
			$to = rtrim(IMAGE_DIRECTORY . html_entity_decode($_POST['to'], ENT_QUOTES, 'UTF-8'), '/');

			if (!file_exists($to)) {
				$json['error'] = _('Warning: Move to directory does not exists!');
			}	
						
			if (file_exists($to . '/' . basename($from))) {
				$json['error'] = _('Warning: A file or directory with the same name already exists!');
			}
		} else {
			$json['error'] = _('Warning: Please select a directory!');
		}
				
		if (!isset($json['error'])) {
			rename($from, $to . '/' . basename($from));
						
			$json['success'] = _('Success: Your file or directory has been moved!');
		}
				
		break;
	// Get a list of all folders in the image directory and return as a select list of options
	case 'folders':
		
		$rootDirectory = new Pommo_Filesystem_Directory(IMAGE_DIRECTORY);
		$folders = $rootDirectory->getAllSubfoldersList();
				
		$options = '';
		foreach ($folders as $folder) {
			$pathRelative = mb_substr($folder, strlen(IMAGE_DIRECTORY));
			$depth = substr_count($pathRelative, '/');
			$options .= '<option value="' . $pathRelative . '">' 
				. str_repeat('.', $depth * 4) . $pathRelative . '</option>';
		}
				
		echo $options;
		exit;
	// Copy a file or directory to another directory
	case 'copy':
		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((mb_strlen($_POST['name']) < 3) || (mb_strlen($_POST['name']) > 255)) {
				$json['error'] = _('Warning: Filename must be a between 3 and 255!');
			}
								
			$old_name = rtrim(IMAGE_DIRECTORY . html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8'), '/');
						
			if (!file_exists($old_name) || $old_name . '/' == IMAGE_DIRECTORY) {
				$json['error'] = _('Warning: Can not copy this file or directory!');
			}
						
			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}		
						
			$new_name = dirname($old_name) . '/' . html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext;
																			   																			   
			if (file_exists($new_name)) {
				$json['error'] = _('Warning: A file or directory with the same name already exists!');
			}			
		} else {
			$json['error'] = _('Warning: Please select a directory or file!');
		}
				
		if (!isset($json['error'])) {
			$fileObject = Pommo_Filesystem_Item::factory($old_name);
			$fileObject->copy($new_name);
						
			$json['success'] = _('Success: Your file or directory has been copied!');
		}
				
		break;
	// Rename a file or directory 
	case 'rename':
		if (isset($_POST['path']) && isset($_POST['name'])) {
			if ((mb_strlen($_POST['name']) < 3) || (mb_strlen($_POST['name']) > 255)) {
				$json['error'] = _('Warning: Filename must be a between 3 and 255!');
			}
								
			$old_name = rtrim(IMAGE_DIRECTORY . html_entity_decode($_POST['path'], ENT_QUOTES, 'UTF-8'), '/');
						
			if (!file_exists($old_name) || $old_name . '/' == IMAGE_DIRECTORY) {
				$json['error'] = _('Warning: Can not rename this directory!');
			}
						
			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}
						
			$new_name = dirname($old_name) . '/' . html_entity_decode($_POST['name'], ENT_QUOTES, 'UTF-8') . $ext;
																			   																			   
			if (file_exists($new_name)) {
				$json['error'] = _('Warning: A file or directory with the same name already exists!');
			}			
		}
				
		if (!isset($json['error'])) {
			rename($old_name, $new_name);
						
			$json['success'] = _('Success: Your file or directory has been renamed!');
		}
				
		break;
	default:
		die('invalid request passed to '.__FILE__);
		break;
}

echo json_encode($json);
die();
