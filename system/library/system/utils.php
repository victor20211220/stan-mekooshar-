<?php

/**
 * Utils library.
 *
 * @version $Id: utils.php 101 2010-07-21 06:33:22Z eprev $
 * @package System
 */

class System_Utils
{
	/**
	 * NB: DO NOT USE THIS FUNCTIONALITY. NEED TO BE REFACTORED.
	 *
	 * Properties casting types.
	 */
	const PROPERTIES_OBJECT = 1;
	const PROPERTIES_ARRAY  = 2;

	/**
	 * NB: DO NOT USE THIS FUNCTIONALITY. NEED TO BE REFACTORED.
	 *
	 * Performs safe type casting of properties.
	 *
	 * @param mixed   $var  Array or object.
	 * @param integer $type Result type of properties.
	 * @return mixed
	 */
	public static function properties($props, $type)
	{
		if ($type == self::PROPERTIES_ARRAY) {
			if (is_array($props)) {
				return $props;
			}
			// e.g. textAlign -> text-align
			$result = array();
			$replacer = function ($matches) {
				return '-' . strtolower($matches[0]);
			};
			foreach ((array) $props as $name => $value) {
				$name = preg_replace_callback('/[A-Z]/', $replacer, $name);
				$result[$name] = $value;
			}
			return $result;
		}
		if ($type == self::PROPERTIES_OBJECT) {
			if (is_object($props)) {
				return $props;
			}
			// e.g. text-align -> textAlign
			$result = array();
			foreach ($props as $name => $value) {
				$parts = explode('-', $name);
				$name = $parts[0];
				foreach (array_slice($parts, 1) as $part) {
					$name .= ucfirst($part);
				}
				$result[$name] = $value;
			}
			return (object) $result;
		}
	}
}
