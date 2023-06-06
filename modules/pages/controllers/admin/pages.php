<?php

class Admin_Pages_Controller extends Controller_Admin_Template
{
	public function before()
	{
		parent::before();
		$this->view->active = 'pages';
		$this->view->script('/js/libs/ui/jquery-ui.custom.min.js');
		$this->view->script('/js/pages.js');
		$this->view->script('/js/system.js');
		$this->view->style('/css/autoform.css');
		$this->view->style('/css/pages.css');
	}

	public function __call($category, $action = false)
	{
		$this->actionIndex($category);
		$this->view->active = $category;
	}

	public function actionIndex($category)
	{
		$this->view->title = 'Pages managing';

		$pageType = '';
		switch($category){
			case 'gallery':
				$pageType = PAGE_TYPE_GALLERY;
				break;
			case 'static':
				$pageType = PAGE_TYPE_STATIC;
				break;
			case 'banners':
				$pageType = PAGE_TYPE_BANNERS;
				break;
		}

		$items = Model_Pages::getListItems($pageType);

		$pageACL = false;
		if(!empty($category)) {
			if(isset($this->config->pageACL->$category)) {
				$pageACL = $this->config->pageACL->$category;
				$this->view->active = $category;
			}
		}

		$this->view->content = new View('admin/pages-index', array(
			'title' => $this->view->title,
			'content' => new View('admin/pages-lists', array(
					'items' => $items,
					'category' => $category,
					'pageACL' => $pageACL
				)),
		));
	}


	public function actionAddPage($category) {
//		$id = Model_Pages::create(array(
//			'category' => $category,
//			'alias' => '',
//			'title' => '',
//			'text' => '',
//			'isRemoved' => 1
//		));
		$this->actionEditPage($category);
	}

	public function actionEditPage($category, $id = false)
	{
		$this->view->active = $category;
		if($id) {
			$item = new Model_Pages($id);
			$this->view->title = 'Edit page';
		} else {
			$this->view->title = 'Add page';
		}
//		Model_Pages::removeItems();

		$obj = new Form_Page();

		if($category == 'banners') {
			$src = false;
			$data = false;
			if($id) {
				$file = Model_Files::getByParentId($id, FILE_BANNER);
				$file = current($file);
				$src = $file->url;
				$data = $item->text;
			}

			$obj->setBanner($id, $src, $data);
		}
		if($category == 'static'){
			$obj->addTitle1();
		}


		if(Request::isPost()) {

			$banner_validate = TRUE;
			if($category == 'banners' && isset($_SESSION['uploaded_banner'])) {
				$banner_validate = FALSE;
				$file = Model_Files::getByIds($_SESSION['uploaded_banner']);

				if(count($file) > 0) {
					$banner_validate = TRUE;
				}
			}
			if($obj->form->validate() && $banner_validate) {
				$values = $obj->form->getValues();

				if($category == 'banners') {
					$data = array();
					foreach($values as $key => $value) {
						if(substr($key, 0, 8) == 'country_'){
							if(!empty($value)) {
								$data['countries'][] = substr($key, 8);
							}
							unset($values[$key]);
						}
					}
					$data['banner_type'] = $values['banner_type'];
					if(substr(trim($values['weburl']), 0, 4) != 'http') {
						$data['weburl'] = 'http://' . $values['weburl'];
					} else {
						$data['weburl'] = $values['weburl'];
					}
					unset($values['banner_type'], $values['weburl']);

					$values['text'] = serialize($data);
					$values['typePage'] = PAGE_TYPE_BANNERS;
				}
//				dump($values, 1);

				$alias = Url::safe(trim($values['title']));
				$i = 1;
				$newAlias = $alias;
				while(Model_Pages::exists('alias', $newAlias)) {
					$newAlias = $alias . '-' . $i++;
				}
				$values['alias'] = $newAlias;


				if($id) {
					$values['updateDate'] = CURRENT_DATETIME;
					Model_Pages::update($values, $item->id);
					$page_id = $id;
				} else {
					$values['category'] = $category;
					$page = Model_Pages::create($values);
					$page_id = $page->id;
				}

				// FOR new gallery
				if(isset($_SESSION['gallery_new'])) {
					foreach ($_SESSION['gallery_new'] as $id => $value) {
						$ids[] = $value;
					}
					if(!empty($ids)) {
						Model_Gallery::update(array('page_id' => $page_id), array('id in (?) AND page_id IS NULL', $ids));
					}
					unset($_SESSION['gallery_new']);
				}

				// For remove galleries
				if(isset($_SESSION['gallery_remove'])) {
					foreach ($_SESSION['gallery_remove'] as $gallery_id => $status) {
						Model_Galleryitems::removeGalleryItems($gallery_id);
					}
					unset($_SESSION['gallery_remove']);
				}

				// For remove gallery items
				if(isset($_SESSION['gallery_remove_item'])) {
					foreach ($_SESSION['gallery_remove_item'] as $id => $value) {
						$file = new Model_UploadFiles($id);
						$file->removeFile();
						Model_Galleryitems::remove(array('file_id = ?', $id));
					}
					unset($_SESSION['gallery_remove_item']);
				}

				// For change position gallery items
				if(isset($_SESSION['gallery_items_position'])) {
					foreach($_SESSION['gallery_items_position'] as $gallery_id => $position){
						$i = 0;
						foreach ($position as $key => $v) {
							Model_Galleryitems::update(array('position' => ++$i), array('file_id = ? AND gallery_id = ?', $v, $gallery_id));
//							Model_Files::update(array('position' => ++$i), array('id=? AND sender_id = ? AND parent_id = ? AND `group` = ?', $v, $this->user->id, $id, $galleryId));
						}
					}
					unset($_SESSION['gallery_items_position']);
				}

				// For BANNER
				if($category == 'banners' && isset($_SESSION['uploaded_banner'])) {

//					if(!$id) {
//						Model_Files::remove(array('parent_id = ? AND type = ?', $page_id, FILE_BANNER));
//					}
					Model_Files::update(array(
						'parent_id' => $page_id
					), $_SESSION['uploaded_banner']);

					unset($_SESSION['uploaded_banner']);
				}

				$this->response->redirect(Request::generateUri('admin', 'pages', $category));
			}
		} else {
			unset($_SESSION['gallery_remove'], $_SESSION['position'], $_SESSION['gallery_remove_item'], $_SESSION['gallery_last_group']);
			if($id) {
				$obj->setData($item->getValues());
			}
			$obj->setId(($id) ? $id : 0	);
		}

		$this->view->content = new View('admin/pages-index', array(
			'title' => $this->view->title,
			'itemId' => ($id) ? $id : '0',
			'backLink' => Request::generateUri('/admin/pages/', $category),
			'content' => $obj->form . '<div class="testing"></div>',
			'isItem' => TRUE
		));

		$this->view->script('/js/libs/fancybox/jquery.fancybox.pack.js');
		$this->view->style('/css/libs/fancybox/jquery.fancybox.dashboard.css');


		$this->view->script('/js/uploader.js');
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/libs/ckeditor/ckeditor.js');
		$this->view->script('/js/libs/ckeditor/adapters/jquery.js');
		$this->view->style('/js/libs/ckeditor/plugins/gallery/css/style.css');
	}

	public function actionRemovePage($category, $id) {
		$this->view->title = 'Rempove page';

		$item = new Model_Pages($id);
		$files = Model_Files::getByParentId($id, FILE_PHOTOS);
		foreach($files as $file) {
			$file->removeFile();
		}
		$item->remove($item->id);
		$this->response->redirect(Request::generateUri('admin', 'pages', $category));
	}


	public function actionAddCategory() {
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$categories = Model_Page_Category::getListCategories();


			$this->response->body = json_encode(array(
				'status' => true,
				'content' => '',
			));
			return;
		}
		$this->response->redirect(Request::generateUri('admin', 'index'));
	}


	protected function setGallery ($content) {
		$text = '';
		$parcehtml = explode('<ul', $content);
		foreach($parcehtml as $key => $ul) {
			if(strpos($ul, 'gallery')) {
				$group = substr($ul, strpos($ul, 'data-id') + 9, 4);
				$group = substr($group, 0, strpos($group, '"'));

				$endul = strpos($ul, '</ul>') + 5;
				$text .= '<gallery>' . $group . '</gallery>' . substr($ul, $endul, strlen($ul) - $endul);
			} else {
				$text .= '<ul' . $ul;
			}
		}
		$text = substr($text, 3);
		return $text;
	}



}
?>