<?php

class Controller_Player extends Controller_Template
{
	public function before()
	{
		parent::before();
		$this->model = Model::factory('directory');
	}
	
	public function actionPlay($section, $alias) {
		$this->autoRender = false;
		if ($video = $this->model->getVideoByAlias($alias, $section)) {
			$this->response->body = new View('player');
			$this->response->body->filename = '/directory/videos/' . $section . '/video/' . strtolower(substr($video['alias'], 0, 1)) . '/' . $video['alias'] . '.flv';
			$this->response->body->image = '/directory/videos/' . $section . '/preview/' . strtolower(substr($video['alias'], 0, 1)) . '/' . $video['alias'] . '.jpg';
		} else {
			$this->response->body = 'Wrong parameters';
		}
	}
}
