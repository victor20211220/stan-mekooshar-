<?php

class Download_Controller extends Controller
{
	public function before()
	{
		parent::before();
		
	}
	
	public function actionAudio($section, $alias) 
	{
		$this->file('audio', $section, $alias);
	}
	
	public function actionAttachment($section, $alias) 
	{
		$this->file('attachment', $section, $alias);
	}
	
	public function file($type, $section, $alias) 
	{
		switch($type) {
			case 'audio':
				$item = Model_Directoryaudio::getByAlias($alias, $section);
				$url = Model_Directoryaudio::dir($alias, $section) . $item->alias . '.' . $item->ext;
				break;
			case 'attachment':
				$item = Model_Directoryattachment::getByAlias($alias, $section);
				$url = Model_Directoryattachment::dir($alias, $section) . $item->alias . '.' . $item->ext;
				break;
			default:
				throw new ForbiddenException('Filetype not found');
		}
		
		switch($item->ext) {
			case 'jpeg':
			case 'jpg':
				$type = 'image/jpeg';
				break;
			case 'png':
				$type = 'image/x-png';
				break;
			case 'gif':
				$type = 'image/gif';
				break;
			case 'pdf':
				$type = 'application/pdf';
				break;
			case 'txt':
				$type = 'text/plain';
				break;
			case 'doc':
			case 'docx':
				$type = 'application/msword';
				break;
			case 'zip':
				$type = 'application/zip';
				break;
			case 'rar':
				$type = 'application/x-rar-compressed';
				break;
			case 'mp3':
				$type = 'application/x-rar-compressed';
				break;
			default :
				$type = 'application/octet-stream';
				break;
		}
		
		$rFile = @fopen($url, 'r');
		$rOutput = fopen('php://output', 'w');
		if($rFile) {
			header('Content-Disposition: attachment; filename="'.$item->filename.'"');
			header("Accept-Ranges: bytes");
			header("Content-Length: ".$item->filesize);
			header('Content-type: '.$type.'; charset: UTF-8');
			stream_copy_to_stream($rFile, $rOutput);
			exit();
		} else {
			throw new NotFoundException('File not exist');
		}
	}
}
