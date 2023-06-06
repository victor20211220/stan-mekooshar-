<?php

class Controller_Hotmap extends Controller_Template
{
	private $db;

	public function before()
	{
		parent::before();
		$this->model = Model::factory('directory');
	}

	public function actionXml($section, $alias)
	{
		if (!$image = $this->model->getImageByAlias($alias, $section)) {
			throw new NotFoundException('Hotmap not found');
		}
		$this->autoRender = false;
		$content = new View('hotmap');
		$content->image = $image;
		$content->hotspots = $this->model->getImageHotspots($image['id'], $image['section'], 'position', 'ASC');
		$this->response->body = $content;
	}
}
?>