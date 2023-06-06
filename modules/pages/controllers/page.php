<?php

class Page_Controller extends Controller_Common
{
	public function before()
	{
		parent::before();
	}

	public function __call($alias, $action = false)
	{
		$item = Model_Pages::getItemByAlias($alias);
		$this->view->title = $item->title;

		$images = Model_Files::getByParentId($item->id, FILE_PHOTOS, true);
		$this->view->content = Model_Pages::replaceGallery($item->text, 'parts/galleries/gallery', $images);
	}


}
?>