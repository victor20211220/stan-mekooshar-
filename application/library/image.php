<?php

/**
 * Image helper class.
 *
 * @version  $Id: image.php 2 2009-10-02 23:06:43Z perfilev $
 * @package  Application
 */
class Image {
	
	public static function SetRatio($img) {
                list ($imgWidth, $imgHeight, $type, $attr) = getimagesize($img);
                return sprintf('%.3f', $imgWidth / $imgHeight);
        }

        public static function GetRatio($img) {
                list ($imgWidth, $imgHeight, $type, $attr) = getimagesize($img);
                return sprintf('%.3f', $imgWidth / $imgHeight);
        }

        public static function getSize($img) {
                if ($img[0] == '/') {
                        $img = substr($img, 1);
                }
                try {
                        list($imgWidth, $imgHeight, $type, $attr) = getimagesize($img);
                } catch (Exception $e) {
                        $imgWidth = 0;
                        $imgHeight = 0;
                }
                return array($imgWidth, $imgHeight);
        }

        public static function Scale($original, $newSize) {
                $result = false;
                if (!isset($newSize['height']) && isset($newSize['width'])) {
                        $result['width'] = $newSize['width'];
                        $result['height'] = sprintf('%u', (($original['height'] * $newSize['width']) / $original['width']));
                }
                if (!isset($newSize['width']) && isset($newSize['height'])) {
                        $result['height'] = $newSize['height'];
                        $result['width'] = sprintf('%u', (($original['width'] / $original['height']) * $newSize['height']));
                }
                return $result;
        }

        public static function Watermark($img, $wm, $output) {
                $result = false;
                list($watermarkWidth, $watermarkHeight, $type, $attr) = getimagesize($wm);
                list($imgWidth, $imgHeight, $type, $attr) = getimagesize($img);
                $info = pathinfo($img);
                $fileExtension = $info['extension'];
                switch ($fileExtension) {
                        case 'jpg':
                        case 'jpeg':
                                $imgDest = imagecreatefromjpeg($img);
                                break;
                        case 'gif':
                                $imgDest = imagecreatefromgif($img);
                                break;
                        case 'png':
                                $imgDest = imagecreatefrompng($img);
                                break;
                }
                $info = pathinfo($wm);
                $fileExtension = $info['extension'];
                switch ($fileExtension) {
                        case 'jpg':
                        case 'jpeg':
                                $watermark = imagecreatefromjpeg($wm);
                                break;
                        case 'gif':
                                $watermark = imagecreatefromgif($wm);
                                break;
                        case 'png':
                                $watermark = imagecreatefrompng($wm);
                                break;
                }
                $destX = ($imgWidth - $watermarkWidth) / 2;
                $destY = ($imgHeight - $watermarkHeight) / 2;
                imagecopymerge($imgDest, $watermark, $destX, $destY, 0, 0, $watermarkWidth, $watermarkHeight, 50);
                if (imagejpeg($imgDest, $output, 85)) {
			self::setCopyright($options['filename']);
                        $result = true;
			
			
                }
                imagedestroy($imgDest);
                return $result;
        }

        public static function Resize($img, $newSize, $options = array()) {
                $result = false;
                //$method = ((isset($options['method']) ? $options['method'] : 'scale') == 'crop' ? ((isset($newSize['width']) && isset($newSize['height'])) ? 'crop' : 'scale') : 'scale');
                $method = (isset($options['method']) ? $options['method'] : 'scale');
                $quality = (isset($options['quality']) ? $options['quality'] : 90);
                list ($imgWidth, $imgHeight, $type, $attr) = getimagesize($img);
                if (!isset($newSize['height']) && isset($newSize['width'])) {
                        $newSize['height'] = floor(($imgHeight * $newSize['width']) / $imgWidth);
                }
                if (!isset($newSize['width']) && isset($newSize['height'])) {
                        $newSize['width'] = floor(($imgWidth / $imgHeight) * $newSize['height']);
                }
                if (isset($newSize['width']) && isset($newSize['height']) && $method == 'inscribe') {
                        $xRatio = $newSize['width'] / $imgWidth;
                        $yRatio = $newSize['height'] / $imgHeight;
                        $ratio = min($xRatio, $yRatio);
                        if (isset($options['enlarge']) ? !$options['enlarge'] : false) {
                                if ($ratio > 1) {
                                        $ratio = 1;
                                }
                        }
                        if ($xRatio == $ratio) {
                                $width = ceil($imgWidth * $ratio);
                                $height = floor($imgHeight * $ratio);
                        } else {
                                $width = floor($imgWidth * $ratio);
                                $height = ceil($imgHeight * $ratio);
                        }
                        $newSize['width'] = $width;
                        $newSize['height'] = $height;
                }
                switch ($type) {
                        case IMAGETYPE_JPEG:
                                $img = imagecreatefromjpeg($img);
                                break;
                        case IMAGETYPE_GIF:
                                $img = imagecreatefromgif($img);
                                break;
                        case IMAGETYPE_PNG:
                                $img = imagecreatefrompng($img);
                                break;
                }
                list($red, $green, $blue) = explode(',', (isset($options['color']) ? $options['color'] : '0,0,0'));
                $imgDest = ImageCreateTrueColor($newSize['width'], $newSize['height']);
                $outputExtension = (isset($options['outputExtension']) ? $options['outputExtension'] : 'jpg');
                if ($outputExtension == 'png') {
                        if (($type == IMAGETYPE_GIF) || ($type == IMAGETYPE_PNG)) {
                                $trnprtIndx = imagecolortransparent($img);
                                if ($trnprtIndx >= 0) {
                                        $trnprtColor = imagecolorsforindex($img, $trnprtIndx);
                                        $trnprtIndx = imagecolorallocate($imgDest, $trnprtColor['red'], $trnprtColor['green'], $trnprtColor['blue']);
                                        imagefill($imgDest, 0, 0, $trnprt_indx);
                                        imagecolortransparent($imgDest, $trnprtIndx);
                                } elseif ($type == IMAGETYPE_PNG) {
                                        imagealphablending($imgDest, false);
                                        $color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
                                        imagefill($imgDest, 0, 0, $color);
                                        imagesavealpha($imgDest, true);
                                }
                        } else {
                                imagealphablending($imgDest, false);
                                $color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
                                imagefill($imgDest, 0, 0, $color);
                                imagesavealpha($imgDest, true);
                        }
                } else {
                        imagefill($imgDest, 0, 0, imagecolorallocate($imgDest, $red, $green, $blue));
                }
                if (isset($newSize['width']) && isset($newSize['height'])) {
                        switch ($method) {
                                case 'crop':
                                        $ratio = $imgWidth / $imgHeight;
                                        if ($newSize['width'] / $newSize['height'] > $ratio) {
                                                $newHeight = $newSize['width'] / $ratio;
                                                $newWidth = $newSize['width'];
                                        } else {
                                                $newWidth = $newSize['height'] * $ratio;
                                                $newHeight = $newSize['height'];
                                        }
                                        $xMid = $newWidth / 2;
                                        $yMid = $newHeight / 2;
                                        $process = imagecreatetruecolor(round($newWidth), round($newHeight));
                                        imagecopyresampled($process, $img, 0, 0, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);
                                        imagecopyresampled($imgDest, $process, 0, 0, ($xMid - ($newSize['width'] / 2)), ($yMid - ($newSize['height'] / 2)), $newSize['width'], $newSize['height'], $newSize['width'], $newSize['height']);
                                        imagedestroy($process);
                                        break;
                                case 'inscribe':
                                case 'scale':
                                        $xRatio = $newSize['width'] / $imgWidth;
                                        $yRatio = $newSize['height'] / $imgHeight;
                                        $ratio = min($xRatio, $yRatio);
                                        $useXRatio = ($xRatio == $ratio);
                                        $newWidth = $useXRatio ? $newSize['width'] : ceil($imgWidth * $ratio);
                                        $newHeight = !$useXRatio ? $newSize['height'] : ceil($imgHeight * $ratio);
                                        $newLeft = $useXRatio ? 0 : floor(($newSize['width'] - $newWidth) / 2);
                                        $newTop = !$useXRatio ? 0 : floor(($newSize['height'] - $newHeight) / 2);
                                        imagecopyresampled($imgDest, $img, $newLeft, $newTop, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);
                                        break;
                                case 'resample':
                                        imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $newSize['width'], $newSize['height'], $imgWidth, $imgHeight);
                                        break;
                        }
                }
                if (isset($options['filename'])) {
                        if ($outputExtension == 'png') {
                                $pngQuality = ($quality - 100) / 11.111111;
                                $pngQuality = round(abs($pngQuality));
                                if (imagepng($imgDest, $options['filename'],$pngQuality)) {
                                        $result = true;
                                }
                        } else {
                                if (imagejpeg($imgDest, $options['filename'], $quality)) {
					self::setCopyright($options['filename']);
                                        $result = true;
                                }
                        }
                }
                imagedestroy($imgDest);
                return $result;
        }
	
	
	public static function setCopyright($filename, $text = false) {
		if(!$text) $text = "Ukietech corp.";
		$image = new tagImage($filename);
		$image->set(IPTC_COPYRIGHT_STRING, $text);
		$image->write();
	}


}
