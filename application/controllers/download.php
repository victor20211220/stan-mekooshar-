<?php

class Download_Controller extends Controller_User
{

	protected $subactive = FALSE;

	public function actionApply($job_id, $alias) {

		$file = Model_Files::getItemByJobAlias($job_id, $alias);

		if($job_id != 0) {
			$job = new Model_Jobs($job_id);
			if($job->isRemoved == 1) {
				throw new NotFoundException('File not exist');
			}
			$apply = Model_Job_Apply::getItemApplicantByJobidProfileid($job->id, $file->sender_id);
			if($job->user_id != $this->user->id) {
				throw new NotFoundException('File not exist');
			}
		} else {
			if($file->sender_id != $this->user->id) {
				throw new NotFoundException('File not exist');
			}
		}

		$this->file($file);
	}

	protected function file($file) {
		$url = realpath(NULL)  . $file->url;
		switch ($file->ext) {
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
		if ($rFile) {
			header('Content-Disposition: attachment; filename="' . $file->name . '"');
			header("Accept-Ranges: bytes");
			header("Content-Length: " . $file->size);
			header('Content-type: ' . $type . '; charset: UTF-8');
			stream_copy_to_stream($rFile, $rOutput);
			exit();
		} else {
			throw new NotFoundException('File not exist');
		}
	}
}