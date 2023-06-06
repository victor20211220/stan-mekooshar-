<?php

class Model_Settings extends Model
{
	protected static $table = 'settings';
	protected static $key = 'key';
	
	private static $settings;
	
	public static function inst()
	{
		if(empty(self::$settings)) {
			self::$settings = array();

			foreach(self::query(array(
				'order' => '`position` ASC'
			)) as $v) {
				self::$settings[$v->key] = $v;
			}
		}
		
		return self::$settings;
	}

	public static function get($full = false)
	{
		$settings = self::inst();
		
		if(!$full) {
			$result = array();
			
			foreach($settings as $v) {
				$result[$v->key] = $v->value;
			}
			
			return $result;
		}
		
		return $settings;
	}
}
