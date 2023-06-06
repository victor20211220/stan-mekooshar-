<?php

/**
 * HTML library.
 *
 * @version $Id: html.php 110 2010-07-30 04:03:46Z eprev $
 * @package System
 */

class System_Html
{
	/**
	 * Convert special characters to HTML entities.
	 *
	 * @param string  $value         String to convert.
	 * @param boolean $doubleEncide  Encode existing entities.
	 * @return string
	 */
	public static function chars($value, $doubleEncode = true)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', $doubleEncode);
	}

	/**
	 * Create HTML link anchors. Note that the title is not escaped, to allow
	 * HTML elements within links (images, etc).
	 *
	 * @param string $uri         URL or URI string.
	 * @param string $title       Link text.
	 * @param array  $attributes  HTML anchor attributes.
	 * @param string $protocol    Use a specific protocol.
	 * @return string
	 */
	public static function anchor($uri, $title = null, array $attributes = array(), $protocol = null)
	{
		if (null === $title) {
			$title = $uri;
		}

		if ($uri === '') {
			$uri = Url::base($protocol);
		} elseif ($uri[0] !== '#' && strpos($uri, '://') === false) {
			$uri = Url::site($uri, $protocol);
		}

		$attributes['href'] = $uri;

		return '<a' . self::attributes($attributes) . '>' . $title . '</a>';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 *
	 * @param array $attributes  Attribute list.
	 * @return string
	 */
	public static function attributes($attributes)
	{
		if (empty($attributes)) {
			return '';
		}
		$compiled = '';
		foreach ((array) $attributes as $key => $value) {
			if ($value === null) {
				// Skip attributes that have NULL values
				continue;
			}
			switch ($key) {
				case 'action':
					$compiled .= ' ' . $key . '="' . $value . '"';
					break;
				case 'style':
					if (is_array($value)) {
						if  (null !== $value = static::properties($value)) {
							$compiled .= ' ' . $key . '="' . $value . '"';
						}
						break;
					}
				default:
					$compiled .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
			}
		}
		return $compiled;
	}

	/**
	 * Compiles an array of CSS properties into an attribute STYLE string.
	 *
	 * @param array $properties Properties.
	 * @return string
	 */
	public static function properties(array $properties)
	{
		if (empty($properties)) {
			return null;
		}
		$compiled = '';
		foreach ($properties as $key => $value) {
			$compiled .= $key . ': ' . $value . ';';
		}
		return $compiled;
	}

	/**
	 * Compiles an array of breadcrumbs into an HTML string.
	 *
	 * @param array $crumbs      Associative array.
	 * @param string $separator  Separator to use.
	 * @return string
	 */
	public static function breadcrumbs(array $crumbs, $separator = ' » ')
	{
		$compiled = '';
		$i = 0;
		foreach ($crumbs as $key => $value) {
			if ($i++ > 0) {
				$compiled .= $separator;
			}
			if ($key == '') {
				$compiled .= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			} else {
				$compiled .= self::anchor($key, htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
			}
		}
		return $compiled;
	}

	/**
	 * Compiles a "breadcrumbs" into an HTML string.
	 *
	 * @param array   $crumbs     Crumbs.
	 * @param string  $separator  Separator (optional).
 	 * @return string
	 */
	public static function crumbs(array $crumbs, $separator = ' &mdash; ')
	{
		$res = '';
		$i   = 0;
		$relPath = '';
		foreach ($crumbs as $crumb) {
			if ($i++ > 0) {
				$res .= $separator;
			}
			$path = null;
			if (is_array($crumb)) {
				if (2 == count($crumb)) {
					list($title, $path) = $crumb;
					if ('/' !== $path[0]) {
						$qp = strpos($path, '?');
						if (false !== $qp) {
							$withoutQuery = substr($path, 0, $qp);
							$path = $relPath . $path;
							$relPath = $relPath . $withoutQuery;
						} else {
							$path = $relPath . $path . '/';
							$relPath = $path;
						}
					}
				} else {
					$title = $crumb[0];
				}
			} else {
				$title = $crumb;
			}
			if (null === $path) {
				$res .= htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
			} else {
				$res .= self::anchor($path, htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
			}
		}
		return $res;
	}
}
