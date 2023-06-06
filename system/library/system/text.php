<?php

/**
 * Kit.
 *
 * Text library.
 *
 * @version  $Id: text.php 97 2010-07-19 23:41:59Z eprev $
 * @package  System
 */

class System_Text
{
	/**
	 * Direction pointers.
	 */
	const RIGHT = 0;
	const LEFT  = 1;

	/**
	 * @var array  List of text resources.
	 */
	public static $resources;


	/**
	 * Static initialization.
	 *
	 * @return void
	 */
	public static function initialize()
	{
		if (null === static::$resources) {

			function parseIniFile($path)
			{
				$res = $matches = array();
				$cat = &$res;
				$s = '\s*([[:alnum:]_\- \*]+?)\s*';
				preg_match_all('#^\s*((\[' . $s . '\])|(("?)' . $s . '\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ums', file_get_contents($path), $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					if (empty($match[2])) {
						$cat[$match[6]] = $match[8];
					} else {
						$cat = &$res[$match[3]];
					}
				}
				return $res;
			}

			$files = System::findAll('resources/text.dat');
			$resources = array();
			foreach ($files as $file) {
				$data = parseIniFile($file, true, INI_SCANNER_RAW);
				$resources = array_replace_recursive($resources, $data);
			}
			static::$resources = $resources;
		}
	}

	/**
	 * Generates a random string of a given type and length.
	 *
	 * Supported types:
	 *   alphanum   Alpha-numeric characters;
	 *   alphanuml  Lowercase alpha-numeric characters;
	 *   alpha      Alphabetical characters;
	 *   alphal     Lowercase alphabetical characters;
	 *   hexdec     Hexadecimal characters, 0-9 plus a-f;
	 *   numeric    Digit characters, 0-9;
	 *   distinct   Clearly distinct alpha-numeric characters.
	 *
	 * @param string  $type    A type of pool, or a string of characters to use as the pool.
	 * @param integer $length  Length of string to return.
	 * @return string
	 */
	public static function random($type = 'alphanum', $length = 8)
	{
		switch ($type) {
			case 'alphanum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'alphanuml':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyz';
				break;
			case 'alphal':
				$pool = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case 'hexdec':
				$pool = '0123456789abcdef';
				break;
			case 'numeric':
				$pool = '0123456789';
				break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
				break;
			default:
				$pool = (string) $type;
		}
		$pool = str_split($pool, 1);
		$max  = count($pool) - 1;
		$result = '';
		for ($i = 0; $i < $length; $i++) {
			$result .= $pool[mt_rand(0, $max)];
		}
		return $result;
	}

	/**
	 * Replaces in tokens in the message.
	 *
	 * This method takes an arbitrary number of arguments to replace the tokens.
	 * Each token must be unique, and must increment in the format {0}, {1}, etc.
	 *
	 * This method supports named tokens as well. In this case it takes two arguments:
	 * the message in format {key} and the associative array.
	 *
	 * @param string $message  Message.
	 * @return string
	 */
	public static function format($message, $array = null)
	{
		if (is_array($array)) {
			$tokens = array();
			$values = array();
			foreach ($array as $k => $v) {
				$tokens[] = '{'. $k . '}';
				$values[] = $v;
			}
		} else {
			$values = func_get_args();
			array_shift($values);
			$tokens = array();
			for ($i = 0, $c = count($values); $i < $c; $i++) {
				$tokens[] = '{' . $i . '}';
			}
		}
		return str_replace($tokens, $values, $message);
	}

	/**
	 * Returns concatenated titles of crumbs with the given direction.
	 *
	 * @param array   $crumbs     Crumbs.
	 * @param integer $direction  Direction of contatination (Text::RIGHT/LEFT, optional).
	 * @param string  $separator  Separator (optional).
 	 * @return string
	 */
	public static function crumbs(array $crumbs, $direction = self::RIGHT, $separator = ' &mdash; ')
	{
		$stack = array();
		foreach ($crumbs as $crumb) {
			$title = is_array($crumb) ? $crumb[0] : $crumb;
			$stack[] = $title;
		}
		if (self::LEFT === $direction) {
			$stack = array_reverse($stack);
		}
		return implode($separator, $stack);
	}

	/**
	 * Strips whitespace from the beginning and end of a unicode string.
	 *
	 * @param string $str  The string to trim.
	 * @return string
	 */
	public static function trim($str)
	{
		return preg_replace('/^[\s|\n]*(.*?)[\s|\n]*$/u', '$1', $str);
	}

	/**
	 * Returns UNIX timestamp.
	 *
	 * The following characters are recognized in the format parameter string:
	 *
	 *   Y - Year (2 or 4 digits)
	 *   M - Month (1 or 2 digits)
	 *   D - Day (1 or 2 digits)
	 *   H - Hours (1 or 2 digits)
	 *   I - Minutes (1 or 2 digits)
	 *   S - Seconds (1 or 2 digits)
	 *   A - Ante meridiem and Post meridiem (lowercase or uppercase)
	 *
	 * @param string $format  Datetime format.
	 * @param string $value   Datetime value.
	 * @return integer
	 */
	public static function timestamp($format, $value)
	{
		$date = array(
			'hours'    => 0,
			'minutes'  => 0,
			'seconds'  => 0,
			'meridian' => null, // AM/PM
			'month'    => 0,
			'day'      => 0,
			'year'     => 0
		);

		$order   = array();
		$pattern = '/';
		for ($i = 0, $l = strlen($format); $i < $l; $i++) {
			$char = $format[$i];
			switch ($char) {
				case 'Y':
					$pattern .= '(\d{2,4})';
					$order[] = 'year';
					break;
				case 'M':
					$pattern .= '(\d{1,2})';
					$order[] = 'month';
					break;
				case 'D':
					$pattern .= '(\d{1,2})';
					$order[] = 'day';
					break;
				case 'H':
					$pattern .= '(\d{1,2})';
					$order[] = 'hours';
					break;
				case 'I':
					$pattern .= '(\d{1,2})';
					$order[] = 'minutes';
					break;
				case 'S':
					$pattern .= '(\d{1,2})';
					$order[] = 'seconds';
					break;
				case 'A':
					$pattern .= '(AM|PM)';
					$order[] = 'meridian';
					break;
				default:
					$pattern .= preg_quote($char, '/');
			}
		}
		$pattern .= '/ui';
		if (preg_match($pattern, $value, $matches)) {
			for ($i = 0, $c = count($order); $i < $c; $i++) {
				if ($order[$i] == 'meridian') {
					$date[$order[$i]] = strtoupper($matches[$i + 1]);
				} else {
					$date[$order[$i]] = intval($matches[$i + 1]);
				}
			}
			if ('AM' === $date['meridian']) {
				if (12 == $date['hours']) {
					$date['hours'] = 0;
				} elseif (0 == $date['hours'] || $date['hours']  > 12 ) {
					return false;
				}
			} elseif ('PM' === $date['meridian']) {
				if (0 == $date['hours'] || $date['hours']  > 12 ) {
					return false;
				} elseif ($date['hours'] < 12) {
					$date['hours'] += 12;
				}
			} else {
				if ($date['hours']  > 23) {
					return false;
				}
			}
			if ($date['minutes'] > 59 || $date['seconds'] > 59) {
				return false;
			}
			if ($date['month'] == 0 && $date['day'] == 0 && $date['year'] == 0) {
				// Use GMT for the time only, due to TimeZone offset is included into the date
				return gmmktime($date['hours'], $date['minutes'], $date['seconds'], 1, 1, 1970);
			}
			if (in_array('year', $order)) {
				if ($date['year'] <= 69) {
					$date['year'] += 2000;
				} elseif ($date['year'] > 69 && $date['year'] <= 99) {
					$date['year'] += 1900;
				}
			}
			if (checkdate($date['month'], $date['day'], $date['year'])) {
				return mktime($date['hours'], $date['minutes'], $date['seconds'], $date['month'], $date['day'], $date['year']);
			}
		}
		return false;
	}

	/**
	 * Pluralization for English language.
	 *
	 * Word forms is an array with two or three elements.
	 * The third element (if exists) will be used if the number is zero.
	 *
	 * Every occurrence of % in the word form will be replaced by the number.
	 * Note that \% becomes a literal % character.
	 *
	 * @param integer $n     The number.
	 * @param array   $forms Word forms.
	 * @return string
	 */
	public static function plural($n, array $forms)
	{
		$n = intval($n);
		if (3 == count($forms)) {
			$form = 0 == $n ? $forms[2] : $forms[1 == $n ? 0 : 1];
		} else {
			$form = $forms[1 == $n ? 0 : 1];
		}
		if (false !== mb_strpos($form, '%')) {
			return preg_replace(array('/(^|[^\\\\])%/u', '/\\\\%/u'), array('${1}' . $n, '%'), $form);
		} else {
			return $form;
		}
	}

	/**
	 * Returns formated message with pluralization. Message could be passed
	 * directly or by the identifier of text resource.
	 *
	 * This method takes an arbitrary number of arguments to replace the tokens.
	 * Each token must be unique, and must increment in the format {0}, {1}, etc.
	 *
	 * If token contains word forms in the format {0|...|...} it will be pluralized.
	 *
	 * @param string $id Message or text resource identifier.
	 * @param ...
	 * @returns string
	 */
	public static function get($id)
	{
		$dot = strpos($id, '.');
		if (false !== $dot) {
			$cat = substr($id, 0, $dot);
			$key = substr($id, $dot + 1);
			if (
				   array_key_exists($cat, static::$resources)
				&& array_key_exists($key, static::$resources[$cat])
			) {
				$message = static::$resources[$cat][$key];
			} else {
				$message = $id;
			}
		} else {
			if (array_key_exists($id, static::$resources)) {
				$message = static::$resources[$id];
			} else {
				$message = $id;
			}
		}
		if (func_num_args() > 1) {
			$values = func_get_args();
			array_shift($values);
			return preg_replace_callback('/{(\d+)(\|.*?)?}/us', function ($matches) use ($values) {
					$index = $matches[1];
					if (array_key_exists($index, $values)) {
						if (count($matches) == 3) {
							$forms = array_slice(explode('|', $matches[2]), 1);
							return System_Text::plural($values[$index], $forms);
						} else {
							return $values[$index];
						}
					} else {
						return $matches[0];
					}
				}, $message);
		} else {
			return $message;
		}
	}

	/**
	 * Highlights with <strong> words in the given text.
	 *
	 * If $words is boolean and is set to TRUE then the whole $text will be highlighted.
	 * If $words is an array of words then each word will be highlighted.
	 *
	 * @param string  $text    Input text.
	 * @param mixed   $words   What to highlight
	 * @param boolean $escape  Whether to escape HTML chars.
	 * @return string
	 */
	public static function highlight($text, $words = false, $escape = false)
	{
		if (is_bool($words) && $words) {
			return '<strong>' . ($escape ? Html::chars($text) : $text) . '</strong>';
		} else {
			if (is_null($words)) {
				return ($escape ? Html::chars($text) : $text);
			}
			if (is_array($words)) {
				if ($escape) {
					$text  = Html::chars($text);
					$words = array_map(array('html', 'chars'), $words);
				}
				$patterns = array();
				foreach ($words as $word) {
					$patterns[] = '/' .  preg_quote($word) . '/iu';
				}
				return preg_replace($patterns, '<strong>$0</strong>', $text);
			} else {
				if ($escape) {
					$text  = Html::chars($text);
					$words = Html::chars($words);
				}
				return preg_replace('/' .  preg_quote($words) . '/iu', '<strong>$0</strong>', $text);
			}
		}
	}
	
	public static function short($title, $length = 24)  
	{
		if(mb_strlen($title, 'utf-8') > ($length + ceil($length*0.2))) {
			$title = mb_substr($title, 0, $length, 'utf-8') . '...';
		}
		return $title;
	}
}
