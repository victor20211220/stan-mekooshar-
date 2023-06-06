<?php

class Captcha_Controller extends Controller
{
	public function  before() {
		parent::before();
		
	}

	public function actionLoad($hero)
	{
		$this->autoRender = false;
		
		$captcha = Captcha::getCreatedInstance();
		$elements = $captcha->get();
		
		if(!isset($elements[$hero])) {
			throw new NotFoundException('File not exist');
		}
		
		$url = $captcha->src($elements[$hero]);

		$rFile = @fopen($url, 'r');
		$rOutput = fopen('php://output', 'w');
		if($rFile) {
			header('Content-Disposition: attachment; filename="'.$hero.'.png"');
			header("Accept-Ranges: bytes");
			header('Content-type: image/x-png; charset: UTF-8');
			stream_copy_to_stream($rFile, $rOutput);
			exit();
		} else {
			throw new NotFoundException('File not existtttt');
		}
	}
}