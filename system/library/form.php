<?php

/**
 * Kit.
 *
 * Dummy Form library.
 *
 * @version $Id: form.php 69 2010-07-08 05:34:38Z eprev $
 * @package System
 */

class Form extends System_Form {
	
	public static function getDisabled($array)
	{
		$disabled = array();
		foreach ($array as $key=>$val)
		{
			if(strlen($key) > 1 && substr($key, 0, 1) == '_')
			{
				$disabled[] = $key;
			}
		}

		return $disabled;
	}
};

//class Form_Batch extends System_Form_Batch {};
