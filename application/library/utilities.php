<?php

class Utilities
{
	public static function compressCss()
	{
		$css['files'] = array('css/style.css');
		$css['output'] = 'css/c/style.css';
		$cssAutoform['files'] = array('css/style.css', 'css/autoform.css');
		$cssAutoform['output'] = 'css/c/astyle.css';
		$items = array($css, $cssAutoform);
		foreach ($items as $item) {
			$content = '';
			foreach ($item['files'] as $file) {
				$content .= file_get_contents($file);
			}
			$content = preg_replace('/\/\*(.+)\*\//i', '', $content);
			$content = str_ireplace("\r", '', $content);
			$content = str_ireplace("\n", '', $content);
			$content = str_ireplace('	', '', $content);
			file_put_contents($item['output'], $content);
		}
	}

	public static function compressJs()
	{
		$js['files'] = array('js/jquery/jquery.js');
		$js['output'] = 'js/c/js.js';
		$items = array($js);
		foreach ($items as $item) {
			$content = '';
			foreach ($item['files'] as $file) {
				$content .= file_get_contents($file);
			}
			$content = preg_replace('/\/\*(.+)\*\//i', '', $content);
			$content = str_ireplace("\r", '', $content);
			$content = str_ireplace("\n", '', $content);
			$content = str_ireplace('	', '', $content);
			file_put_contents($item['output'], $content);
		}
	}
	
	public static function getNumerFormat($numer, $ends)
	{
		$numer = $numer % 10;
		switch ($numer)
		{	
			case 1:
				return $ends[0];
				break;
			case 2:
				return $ends[1];
				break;
			case 3:
				return $ends[2];
				break;
			default :
				return $ends[3];
			
		}
	}
	
	public static function sizeFormat($bytes)
	{
		$i = -1;
		do{
			$bytes = $bytes /1024;
			$i++;
		}
		while($bytes>99);
		
		$sizes = array('kB', 'MB', 'GB', 'TB', 'PB', 'EB');
		return number_format(round(max(array($bytes, 0.1)), 1), 1) . $sizes[$i];
	}
	
	public static function dateToHuman($date, $withTime = false, $falseIfNull = false)
	{
		$mask2 = 'Y-M-D';
		$test = explode(' ', $date);
		
		if($falseIfNull)
		{
			$newDateTmp = explode('-', $test[0]);
			$isEmpty = true;
			foreach ($newDateTmp as $newElement)
			{
				if((int)$newElement)
				{
					$isEmpty = false;
				}
			}
			if($isEmpty)
			{
				return '';
			}
		}
		
		if(count($test) > 1)
		{
			$mask2 .= ' H:I:S';
		}
		$mask = 'M d, Y';
		if($withTime)
		{
			$mask .= ' h:i';
		}
		return date($mask, Text::timestamp($mask2, $date));
	}
	
	public static function pendosToHuman($date, $withTime = false)
	{
		$mask2 = 'M.D.Y';
		$test = explode(' ', $date);
		if(count($test) > 1)
		{
			$mask2 .= ' H:I:S';
		}
		$mask = 'F d, Y';
		if($withTime)
		{
			$mask .= ' h:i';
		}
		return date($mask, Text::timestamp($mask2, $date));
	}
	
	public static function pendosToSQL($date)
	{
		if(empty ($date))
		{
			return '';
		}
		$newDate = preg_replace('/^(\d\d)\.(\d\d)\.(\d\d\d\d)$/', '$3-$1-$2', $date);
		return !empty ($newDate) ? $newDate : $date;
	}
	
	public static function SQLToPendos($date, $falseIfNull = false)
	{
		if(empty ($date))
		{
			return '';
		}
		$newDate =  preg_replace('/^(\d\d\d\d)-(\d\d)-(\d\d).*$/', '$2.$3.$1', $date);
		
		if($falseIfNull)
		{
			$newDateTmp = explode('.', $newDate);
			$isEmpty = true;
			foreach ($newDateTmp as $newElement)
			{
				if((int)$newElement)
				{
					$isEmpty = false;
				}
			}
			if($isEmpty)
			{
				return '';
			}
		}
		
		return !empty ($newDate) ? $newDate : $date;
	}

	public static function moveElement(&$array, $a, $b) {
		$out = array_splice($array, $a, 1);
		array_splice($array, $b, 0, $out);
	}
}