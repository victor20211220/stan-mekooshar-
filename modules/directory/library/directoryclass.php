<?php

class Directoryclass
{
	public static function validateItem($item)
	{
		if (($original = Model_Directoryitem::getItem($item['id'], $item['section']))) {
			$item = array(
				'id'		=> $item['id'],
				'section'	=> $item['section'],
				'price' 	=> $original->price,
				'name'		=> $original->name,
			);
			return $item;
		}
		return false;
	}

	public static function setPaid($item)
	{
		return true;
	}
}
?>