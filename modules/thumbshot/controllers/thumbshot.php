<?php

class Thumbshot_Controller extends Controller_Common
{

	public function  before() {

	}

	public function actionIndex()
	{
		$text = '';
		$text .= '<a href="' . Request::generateUri('thumbshot', '') . '">WWW to jpg</a><br>';

		$this->view->content = $text;
	}

	public function actionWww()
	{

	}

	function screenshot($url,$ss){
		$name = 'images/screen.jpg';
		$url=@urlencode($url);
		$url='http://netrenderer.com/index.php?url=' .$url .'&browser=ie7';
		$file = 'netrenderer.php';
		$ch = curl_init($url);
		$fp = fopen($file, "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		$adr=@file_get_contents($file);
		$adr = eregi_replace( '(.*)<img src="', '',$adr);
		$adr = eregi_replace( '"(.*)', '',$adr);
		$scr=@file_get_contents($adr);
		file_put_contents ($name,$scr);
		create_small($name,$ss);
		$fs=filesize ($name);
		$rez='<img src="' .$name .'" border="1"><br>Размер файла: ' .$fs .' byte';
		return $rez;
	}

	function create_small($name,$ss)
	{
		if ($ss>600) $ss=600;if ($ss<16) $ss=16;
		list($x, $y, $t, $attr) = getimagesize($name);
		$big=imagecreatefrompng($name);
		if ($x > $y) {$xs=$ss; $ys=$ss/($x/$y);}
		else {$ys=$ss * 3/4; $xs=$ys/($y/$x); }
		$small=imagecreatetruecolor ($xs,$ys);
		$res = imagecopyresampled($small,$big,0,0,0,0,$xs,$ys,$x,$y);
		imagedestroy($big);
		imagejpeg($small,$name);
		imagedestroy($small);
	}
}