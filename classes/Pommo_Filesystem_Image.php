<?php
/**
 * Copyright (C) 2013 OpenCart (www.opencart.com)
 */

class Pommo_Filesystem_Image extends Pommo_Filesystem_File
{
    /**
     * @var resource
     */
    private $_imageHandle;

    /**
     * @var array Info from getimagesize()
     */
    private $_info;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        if (file_exists($filePath)) {
            $info = getimagesize($filePath);

            $this->_info = array(
                'width' => $info[0],
                'height' => $info[1],
                'bits' => $info['bits'],
                'mime' => $info['mime']
            );

            $this->_imageHandle = $this->_create($filePath);
        }

        parent::__construct($filePath);
    }

    /**
     * @param string $filePath
     * @return resource
     */
    private function _create($filePath)
    {
        $mime = $this->_info['mime'];

        if ($mime == 'image/gif') {
            return imagecreatefromgif($filePath);
        } elseif ($mime == 'image/png') {
            return imagecreatefrompng($filePath);
        } elseif ($mime == 'image/jpeg') {
            return imagecreatefromjpeg($filePath);
        }
    }

    /**
     * Saves the stored image to a new file
     * @param string $savePath
     * @param int $quality
     */
    public function save($savePath, $quality = 90)
    {
        $info = pathinfo($savePath);

        $extension = strtolower($info['extension']);

        if (is_resource($this->_imageHandle)) {
            if ($extension == 'jpeg' || $extension == 'jpg') {
                imagejpeg($this->_imageHandle, $savePath, $quality);
            } elseif ($extension == 'png') {
                imagepng($this->_imageHandle, $savePath, 0);
            } elseif ($extension == 'gif') {
                imagegif($this->_imageHandle, $savePath);
            }

            imagedestroy($this->_imageHandle);
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool $smallerOnly
     * @return mixed
     */
    public function resize($width = 0, $height = 0, $smallerOnly = false)
    {
        if (!$this->_info['width'] || !$this->_info['height']) {
            return;
        }

        if ($smallerOnly && $this->_info['width'] < $width && $this->_info['height'] < $height) {
            return;
        }

        $scale = min($width / $this->_info['width'], $height / $this->_info['height']);

        if ($scale == 1) {
            return;
        }

        $new_width = (int)($this->_info['width'] * $scale);
        $new_height = (int)($this->_info['height'] * $scale);
        $xpos = (int)(($width - $new_width) / 2);
        $ypos = (int)(($height - $new_height) / 2);

        $image_old = $this->_imageHandle;
        $this->_imageHandle = imagecreatetruecolor($width, $height);

        if (isset($this->_info['mime']) && $this->_info['mime'] == 'image/png') {
            imagealphablending($this->_imageHandle, false);
            imagesavealpha($this->_imageHandle, true);
            $background = imagecolorallocatealpha($this->_imageHandle, 255, 255, 255, 127);
            imagecolortransparent($this->_imageHandle, $background);
        } else {
            $background = imagecolorallocate($this->_imageHandle, 255, 255, 255);
        }

        imagefilledrectangle($this->_imageHandle, 0, 0, $width, $height, $background);

        imagecopyresampled($this->_imageHandle, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->_info['width'], $this->_info['height']);
        imagedestroy($image_old);

        $this->_info['width'] = $width;
        $this->_info['height'] = $height;
    }
}
