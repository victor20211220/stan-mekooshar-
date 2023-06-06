<?php

/**
 * Kit.
 *
 * File library.
 *
 * @version $Id: file.php 67 2010-07-05 04:59:48Z perfilev $
 * @package System
 */

class System_File
{
	/**
	 * @var object  FileInfo class.
	 */
	public static $fi;

//	/**
//	 * Retruns mime type of the given file.
//	 *
//	 * @param string $path  Path to file.
//	 * @return string
//	 */
//	public static function mime($path)
//	{
//		if ($info = self::$fi->file($path)) {
//			return current(explode(';', $info));
//		}
//		return false;
//	}

//	/**
//	 * Retruns charset of the given file.
//	 *
//	 * @param string $path  Path to file.
//	 * @return string
//	 */
//	public static function charset($path)
//	{
//		if ($info = self::$fi->file($path)) {
//			$result = explode(';', $info);
//			return trim(substr($result[1], strpos($result[1], '=') + 1));
//		}
//		return false;
//	}

	/**
	 * Returns the extension of the given file.
	 *
	 * @param string $path  Path to the file.
	 * @return mixed
	 */
	public static function extension($path)
	{
		$basename = basename($path);
		$dot = strrpos($basename, '.');
		return (false === $dot) ? false : substr($basename, $dot + 1);
	}
}

//System_File::$fi = new finfo(FILEINFO_MIME);
