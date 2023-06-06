<?php

class Filesystem
{
	public static function buildPath($path)
	{
		if (false == is_dir($path)) {
			mkdir($path, 0777, true);
		}
		return rtrim($path, '/') . '/';
	}
	
	public static function compilePath($root, $fraction, $depth = 3)
	{
		$result = rtrim($root, '/') . '/';
		$fraction = trim($fraction, '/');
		$result .= self::divide($fraction, $depth) . $fraction . '/';
		return $result;
	}

	public static function remove($root, $fraction, $depth = 3)
	{
		$root = rtrim($root, '/') . '/';
		for ($cnt = $depth; $cnt >= 0; $cnt--) {
			if ($cnt == $depth) {
				$path = $root . self::divide($fraction, $cnt) . $fraction . '/';
				try {
					$hdir = opendir($path);
				} catch (Exception $e) {
					continue;
				}
				while (false !== ($filename = readdir($hdir))) {
					if ($filename == '.' || $filename == '..') {
						continue;
					}
					if (!is_dir($path.'/'.$filename)) {
						@unlink($path.'/'.$filename);
					}
				}
				closedir($hdir);
				try {
					rmdir($path);
				} catch (Exception $e) {}
			}
			$path = $root . self::divide($fraction, $cnt);
			try {
				rmdir($path);
			} catch (Exception $e) {}
		}
	}
	
	private static function divide($fraction, $depth)
	{
		$result = '';
		for ($cnt = 0; $cnt < $depth; $cnt++) {
			$result .= $fraction[$cnt] . '/';
		}
		return $result;
	}
	
	public static function isDirEmpty($path)
	{
		if(is_dir($path)) {
			$dir = dir($path);
			if($dir) {
				while (false !== ($field = $dir->read())) {
					$dirs[] = $field;
				}
				$dir->close();
				if (count($dirs) == 2) {
					return true;
				}
			}
		}
		
		return false;
	}

	public static function removeDirectory($dir) {
		if ($objs = glob($dir . "/*")) {
			foreach ($objs as $obj) {
				is_dir($obj) ? removeDirectory($obj) : unlink($obj);
			}
		}
		if (is_dir($dir)) {
			rmdir($dir);
		}
	}
}