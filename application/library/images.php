<?php
include_once COOT_PATH. '/vendor/gumlet/php-image-resize/lib/ImageResize.php';
use \Gumlet\ImageResize;
class Images
{

	/*
	 *
	 *	ПОТРІБНО ПЕРЕПИСАТИ
	 *
	 */

	public function getHtml($type, $size) {

	}

	public function getPalette($img, $numColors = 3, $granularity = 5)
	{
		$colors = array();
		$size = @getimagesize($img);
		if(!$size === false)	{
			user_error("Unable to get image size data");
			return false;
		}
		$img = @imagecreatefromjpeg($img);
		if(!$img) {
			user_error("Unable to open image file");
			return false;
		}
		for($x = 0; $x < $size[0]; $x += $granularity) {
			for($y = 0; $y < $size[1]; $y += $granularity) {
				$thisColor = imagecolorat($img, $x, $y);
				$rgb = imagecolorsforindex($img, $thisColor);
				$red = round(round(($rgb['red'] / 0x33)) * 0x33);
				$green = round(round(($rgb['green'] / 0x33)) * 0x33);
				$blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
				$thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
				if(array_key_exists($thisRGB, $colors)) {
					$colors[$thisRGB]++;
				} else {
					$colors[$thisRGB] = 1;
				}
			}
		}
		arsort($colors);
		return array_slice(array_keys($colors), 0, $numColors);
	}

	public static function resize($sizes, $dir, $filename, $filetype, $center = null)
	{
		$dir = trim($dir, '/') . '/';
		if (!count($sizes)) {
			return 'sizes';
		}
		$resolution = @getimagesize($dir . $filename . '.' . $filetype);
		if(empty($resolution)) {
			return 'Image has wrong format or errors';
		}

		if ($center && count(explode('x', $center)) == 2) {
			$center = explode('x', $center);
		} else {
			$center = array(round($resolution[0] / 2), round($resolution[1] / 2));
		}
		foreach ($sizes as $size => $options) {
			foreach (array('width', 'height') as $dimension) {
				$$dimension = isset($options[$dimension]) ? $options[$dimension] : null;
			}
			switch (isset($options['method']) ? $options['method'] : 'resize') {
				case 'crop':
					if (!$width || !$height) {
						return 'size_paremeters';
					}
					$ratio = self::ratio($resolution, $width, $height, 'crop');
					$cropArea = array($width / $ratio, $height / $ratio);

                    self::cropImage($dir . $filename . '.' . $filetype, $dir . $size . '.' . 'jpg', $options['width'], $options['height']);
                    break;
				case 'resize':
					if (!$width || !$height) {
						return 'size_paremeters';
					}
                    self::resizeImage($dir . $filename . '.' . $filetype, $dir . $size . '.' . 'jpg', $options['width'], $options['height']);
					break;
				case 'scale':
					if (!$width && !$height) {
						return 'size_paremeters';
					}
					if(!$height) {
						$height = $width;
					}
                    self::resizeImage($dir . $filename . '.' . $filetype, $dir . $size . '.' . 'jpg', $options['width'], $options['height']);
					break;
				case 'cropCustom':
					if(!$height) {
						$height = $width;
					}
					if (!$width || !$height) {
						return 'size_paremeters';
					}

                    $image = new ImageResize($dir . $filename . '.' . $filetype);
                    $image->freecrop( $options['sizes']['width'], $options['sizes']['height'], $options['offset']['x'], $options['offset']['y']);
                    $image->save($dir . $size . '.' . $filetype);
                    break;
				default:
					return 'resize_method';
			}
		}
		return true;
	}

	public static function geometry($cropArea, $offset)
	{
		return $cropArea[0] . 'x' . $cropArea[1] . '+' . $offset[0] . '+' . $offset[1];
	}

	private static function offset($center, $resolution, $newResolution)
	{
		for ($cnt = 0; $cnt <=1; $cnt++) {
			$offset[$cnt] = round($center[$cnt] - ($newResolution[$cnt] / 2));
			if (($offset[$cnt] + $newResolution[$cnt]) > $resolution[$cnt]) {
				$offset[$cnt] = $resolution[$cnt] - $newResolution[$cnt];
			}
			if ($offset[$cnt] < 0) {
				$offset[$cnt] = 0;
			}
		}
		return $offset;
	}

	public static function ratio($resolution, $width = null, $height = null, $type = 'scale')
	{
		$ratio = 1;
		if ($width && $height) {
			if ($type == 'scale') {
				$ratio = min(($width / $resolution[0]), ($height / $resolution[1]));
			} else {
				$ratio = max(($width / $resolution[0]), ($height / $resolution[1]));
			}
		} elseif ($width) {
			$ratio = $width / $resolution[0];
		} elseif ($height) {
			$ratio = $height / $resolution[1];
		}
		return $ratio;
	}


	public static function setCopyright($filename, $text = false) {
//		if(!$text) $text = "Ukietech corp.";
//		$image = new tagImage($filename);
//		$image->set(IPTC_COPYRIGHT_STRING, $text);
//		$image->write();
	}

	private static function resizeImage($imagePathOrigin, $imagePathToSave, $width, $height){
        $image = new ImageResize($imagePathOrigin);
        $image->resize($width,$height);
        $image->save($imagePathToSave);
    }
    private static function cropImage($imagePathOrigin, $imagePathToSave, $width, $height){
        $image = new ImageResize($imagePathOrigin);
        $image->crop($width,$height);
        $image->save($imagePathToSave);
    }
}