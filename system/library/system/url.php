<?php

/**
 * Kit.
 *
 * URL library.
 *
 * @version $Id: url.php 58 2010-06-28 05:02:39Z eprev $
 * @package System
 */

class System_Url
{
	/**
	 * Fetches the current URI.
	 *
	 * @param boolean $qs Whether to include the query string?
	 * @return string
	 */
	public static function current($qs = false)
	{
		return $qs ? Request::$uri : Request::$uri . Request::$query;
	}

	/**
	 * Base URL.
	 *
	 * @param boolean $protocol  Non-default protocol.
	 * @return string
	 */
	public static function base($protocol = null)
	{
		if (null === $protocol) {
			$protocol = 'https';
		}
		return $protocol . '://' . Request::$host . '/';
	}

	/**
	 * Fetches an absolute site URL based on a URI segment.
	 *
	 * @param string $uri       Site URI to convert.
	 * @param string $protocol  Non-default protocol.
	 * @return string
	 */
	public static function site($uri = '', $protocol = null)
	{
		if (preg_match('/^((http:\/\/)|(https:\/\/)|(ftp:\/\/)+)/i', $uri)) {
			return $uri;
		} else {
			return self::base($protocol) . ltrim($uri, '/');
		}
	}

	/**
	 * Merges an array of arguments with the current URI and query string to
	 * overload, instead of replace, the current query string.
	 *
	 * @param array $arguments  Associative array of arguments.
	 * @return string
	 */
	public static function merge(array $arguments)
	{
		if ($_GET === $arguments) {
			$query = Router::$query;
		} elseif ($query = http_build_query(array_merge($_GET, $arguments))) {
			$query = '?' . $query;
		}
		return Router::$uri . $query;
	}

	/**
	 * Replaces non-safe chars in the given URI.
	 *
	 * @param string $uri  URI.
	 * @return string
	 */
	public static function safe($uri)
	{
		return trim(
			preg_replace('#[-]{2,}#', '-',
				preg_replace('#[^0-9a-z_.]#', '-',
					str_replace(array('-', '&' , '@' , '+'), array('', 'and' , 'at' , 'plus'), strtolower($uri))
				)
			), '_');
	}
}
