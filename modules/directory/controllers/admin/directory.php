<?php

class Admin_Directory_Controller extends Controller_Admin_Template {

	private $db;
	private $categories = array();
	private $itemData = array();
	private $moduleSettings;
	private $itemModuleSettings;
	private $maxFileSize;
	private $sizes = array(
	    'tiny' => array(
		'size' => array(
		    'width' => '100',
		    'height' => '100',
		),
		'options' => array(
		    'method' => 'crop'
		),
	    ),
	    'hotspot' => array(
		'size' => array(
		    'width' => '300',
		    'height' => '300',
		),
		'options' => array(
		    'method' => 'inscribe'
		),
	    ),
	    'hotmap' => array(
		'size' => array(
		    'width' => '960',
		    'height' => '600',
		),
		'options' => array(
		    'method' => 'inscribe'
		),
	    ),
	    'fullsize' => array(
		'size' => array(
		    'width' => '900',
		),
		'options' => array(
		    'method' => 'scale',
		    'enlarge' => false,
		),
	    ),
	    'content' => array(
		'size' => array(
		    'width' => '800',
		),
		'options' => array(
		    'method' => 'scale',
		    'enlarge' => false,
		),
	    ),
	);

	public function before() {
		parent::before();
		$this->view->title = 'Directory managing';
		$this->view->script('/js/directory.js');

		$this->setup();
//		dump($_SESSION['uploadFiles'],1);
		if (!in_array(Request::$action, array('upload', 'saveuploadedfiles', 'removeuploadfile')) && !empty($_SESSION['uploadFiles'])) {
//			$this->clearTmp('Model_Directoryimage');
//			$this->clearTmp('Model_Directoryattachment');
//			$this->clearTmp('Model_Directoryaudio');
			unset($_SESSION['uploadFiles']);

			$tmpDir = Model_Directoryimage::$path . '_tmp/';
			$tmpflag = $tmpDir . 'flaguploader';
			if (file_exists($tmpflag)) {
				unlink($tmpflag);
			}
			$tmpDir = Model_Directoryaudio::$path . '_tmp/';
			$tmpflag = $tmpDir . 'flaguploader';
			if (file_exists($tmpflag)) {
				unlink($tmpflag);
			}
			$tmpDir = Model_Directoryattachment::$path . '_tmp/';
			$tmpflag = $tmpDir . 'flaguploader';
			if (file_exists($tmpflag)) {
				unlink($tmpflag);
			}
		}
	}

//	private function clearTmp($class) {
//		$files = array();
//		$path = $class::$path . '_tmp/';
//
//		if (!file_exists($path)) {
//			mkdir($path, 0777, true);
//		}
//
//		if ($handle = opendir($path)) {
//			while (false !== ($entry = readdir($handle))) {
//				if ($entry != "." && $entry != "..") {
//					$files[] = $entry;
//				}
//			}
//			closedir($handle);
//		}
//
//		if (!empty($files)) {
//			foreach ($files as $item) {
//				if ($item != "tmp") {
//					unlink($path . $item);
//				}
//			}
//		}
//	}

	private function setup() {
		require MODULES_PATH . 'directory/config.php';

		if (!empty($sizes)) {
			$this->sizes = array_replace($this->sizes, $sizes);
		}

		$this->moduleSettings = (!empty($settings)) ? $settings : array();
		$this->maxFileSize = Config::getInstance()->maxFileSize;
		$this->extension = Config::getInstance()->extension;
	}

	private function checkSection($section) {
		if (!isset($this->moduleSettings[$section])) {
			throw new InvalidArgumentException('Invalid section');
		}

		$categories = Model_Directoryitem::getCategories($section, 'id', 'ASC', false);
		$categories = $categories['data'];
		foreach ($categories as $cat) {
			$level = 1;
			$categories[$cat->id]->level = $level;
			$currentParentId = $cat->parentId;
			while ($currentParentId != 0 && $level < 15) {
				if (isset($categories[$cat->parentId])) {
					$currentParentId = $categories[$currentParentId]->parentId;
					$level++;
				}
			}
			if ($level < 15) {
				$categories[$cat->id]->level = $level;
			} else {
				$categories[$cat->id]->level = 0;
			}
		}
		$this->categories = $categories;
	}

	private function getPermissions($section, $item, $isList = true) {
		if ($item) {
			if (!is_object($item)) {
				if (isset($this->categories[$item])) {
					$item = $this->categories[$item];
				} else {
					$item = Model_Directoryitem::getItem((int) $item, $section);
				}
			}
			if (empty($item) || ($isList && !isset($this->categories[$item->id])) || (!$isList && $item->parentId && !isset($this->categories[$item->parentId]))) {
				throw new ForbiddenException('Category not found');
			}

			$itemId = $item->id;
		} else {
			$itemId = 0;
		}

		$itemData['permissions'] = array();
		$moduleSettings = $this->moduleSettings[$section];
		if (!isset($moduleSettings['levels'])) {
			$moduleSettings['levels'] = 0;
		}

		$itemData['permissions']['edit'] = false;
		$itemData['permissions']['delete'] = false;

		$level = 0;
		if ($itemId) {
			if ($isList) {
				$level = $this->categories[$item->id]->level;
			} else {
				$level = ($item->parentId) ? $this->categories[$item->parentId]->level : 0;
			}

			if ($isList && $level > 0) {
				if (isset($moduleSettings['levelRules'][$level - 1])) {
					$parentSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level - 1]);
				}
			}
		}


		if (isset($moduleSettings['levelRules'][$level])) {
			$moduleSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level]);
		}

		$moduleSettings['level'] = $level;
		$showed = Request::get('show', false);

		if ($showed != 'categories' && $showed != 'items') {
			if ($isList) {
				$showed = ($moduleSettings['levels'] && $moduleSettings['levels'] > $level) ? 'categories' : 'items';
			} else {
				$showed = ($item->isCategory) ? 'categories' : 'items';
			}
		}

		if (isset($parentSettings)) {
			$itemData['permissions']['edit'] = (isset($parentSettings['categories']['actions']['edit']) ? $parentSettings['categories']['actions']['edit'] : true);
			$itemData['permissions']['delete'] = (isset($parentSettings['categories']['actions']['delete']) ? $parentSettings['categories']['actions']['delete'] : true);
		} else {
			$itemData['permissions']['edit'] = (isset($moduleSettings[$showed]['actions']['edit']) ? $moduleSettings[$showed]['actions']['edit'] : true);
			$itemData['permissions']['delete'] = (isset($moduleSettings[$showed]['actions']['delete']) ? $moduleSettings[$showed]['actions']['delete'] : true);
		}

		$types = array('items');
		if ($moduleSettings['levels']) {
			array_push($types, 'categories');
		}

		$availableTypes = array('categories', 'items', 'images', 'attachments', 'videos', 'audios');
		foreach ($availableTypes as $type) {
			foreach ($moduleSettings as $key => $values) {
				if (is_array($values) && isset($values[$type])) {
					switch ($type) {
						case 'images':
							if (!isset($moduleSettings[$key][$type]['extension'])) {
								$ext = current((array) $this->extension->images);
								$moduleSettings[$key][$type]['extension'] = $ext;
							}
						case 'attachments':
							if (!isset($moduleSettings[$key][$type]['extension'])) {
								$ext = current((array) $this->extension->attachments);
								$moduleSettings[$key][$type]['extension'] = $ext;
							}
						case 'audios':
							if (!isset($moduleSettings[$key][$type]['extension'])) {
								$ext = current((array) $this->extension->audios);
								$moduleSettings[$key][$type]['extension'] = $ext;
							}
							if (!isset($moduleSettings[$key][$type]['maxSize']))
								$moduleSettings[$key][$type]['maxSize'] = $this->maxFileSize;
						case 'videos':
							if ($key == $showed) {
								$moduleSettings[$type] = $moduleSettings[$key][$type];
							}

							$types[] = $type;

							break;
					}
				}
			}
		}

		foreach ($types as $type) {
			$itemData['permissions'][$type]['sorting'] = isset($moduleSettings[$type]['sorting']) ? $moduleSettings[$type]['sorting'] : false;
			$defaultPermission = true;
			if ($type != 'items' && $type != 'categories') {
				$defaultPermission = false;
			} else {
				if (empty($moduleSettings[$type]['fields'])) {
					$moduleSettings[$type]['fields'] = array('name');
				}
			}

			$itemData['permissions'][$type]['edit'] = (isset($moduleSettings[$type]['actions']['edit']) ? $moduleSettings[$type]['actions']['edit'] : $defaultPermission);
			$itemData['permissions'][$type]['delete'] = (isset($moduleSettings[$type]['actions']['delete']) ? $moduleSettings[$type]['actions']['delete'] : $defaultPermission);
			$itemData['permissions'][$type]['add'] = (isset($moduleSettings[$type]['actions']['add']) ? $moduleSettings[$type]['actions']['add'] : $defaultPermission);

			if ($moduleSettings['levels']) {
				switch ($type) {
					case 'categories':
						if ($level >= $moduleSettings['levels']) {
							$itemData['permissions'][$type]['add'] = false;
						}
						break;
					case 'items':
						if ($level > $moduleSettings['levels']) {
							$itemData['permissions'][$type]['add'] = false;
						}
						break;
				}
			}

			switch ($type) {
				case 'categories':
					$itemCount = Model_Directoryitem::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section` = ? AND `parentId` =? AND `isCategory` = 1', $section, $itemId)
						))->fetch();
					break;
				case 'items':
					$itemCount = Model_Directoryitem::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section` = ? AND `parentId` =? AND `isCategory` = 0', $section, $itemId)
						))->fetch();
					break;
				case 'attachments':
					$itemCount = Model_Directoryattachment::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section`=? AND `itemId`=?', $section, $itemId)
						))->fetch();
					break;
				case 'audios':
					$itemCount = Model_Directoryaudio::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section`=? AND `itemId`=?', $section, $itemId)
						))->fetch();
					break;
				case 'videos':
					$itemCount = Model_Directoryvideo::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section`=? AND `itemId`=?', $section, $itemId)
						))->fetch();

					break;
				case 'images':
					$itemCount = Model_Directoryimage::query(array(
						    'select' => 'COUNT(id) as `count`',
						    'where' => array('`section`=? AND `itemId`=?', $section, $itemId)
						))->fetch();
					break;
			}

			$itemData['stat']['count'][$type] = $itemCount->count;

			if (isset($moduleSettings[$type]['max'])) {
				if ($itemCount->count >= $moduleSettings[$type]['max']) {
					$itemData['permissions'][$type]['add'] = false;
				}
			}
		}

		$this->itemModuleSettings = $moduleSettings;
		$this->itemData = $itemData;
		return $itemData;
	}

	private function setCategoriesCrumbs($section, $parentId = 0, $lastLink = false) {
		$view = $this->view;

		$moduleSettings = $this->moduleSettings[$section];

		$mainTitle = (isset($moduleSettings['title']) ? $moduleSettings['title'] : ucfirst($section));

		if (!$parentId) {
			if ($lastLink) {
				$view->crumbs($mainTitle, Request::$controller . 'browse/' . $section . '/');
			} else {
				$view->crumbs($mainTitle);
			}
		} else {
			$view->crumbs($mainTitle, Request::$controller . 'browse/' . $section . '/');

			$last = $this->categories[$parentId];
			$parent = $this->categories[$parentId];
			$parents = array($parent);
			if ($parent->parentId != 0) {
				do {
					if (empty($this->categories[$parent->parentId])) {
						throw new ForbiddenException('Parent category not found');
					}
					$parent = $this->categories[$parent->parentId];
					array_unshift($parents, $parent);
				} while ($parent->parentId != 0);
			}
			foreach ($parents as $parent) {
				$levelTitle = 'name';
				$level = $parent->level;
				$levelSettings = $moduleSettings;

				if (isset($moduleSettings['levelRules'][$level])) {
					$levelSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level]);
				}

				if (isset($levelSettings['categories']['title'])) {
					$levelTitle = $levelSettings['categories']['title'];
				}

				$title = $parent->$levelTitle;
				if (!$title) {
					$title = 'Category';
				}

				if ($parent->id == $last->id && !$lastLink) {
					$view->crumbs($title);
				} else {
					$view->crumbs($title, Request::$controller . 'browse/' . $section . '/' . $parent->id . '/');
				}
			}
		}
	}

	public function actionBrowse($section, $parentId = 0) {
		$section = strtolower($section);
		$this->checkSection($section);
		$this->getPermissions($section, $parentId);

		if ($parentId) {
			$category = $this->categories[$parentId];
		}

		$this->setCategoriesCrumbs($section, $parentId);

		$moduleSettings = $this->itemModuleSettings;

		$showed = Request::get('show', false);
		if ($moduleSettings['levels']) {
			if ($moduleSettings['levels'] > $moduleSettings['level']) {
				if (!$showed) {
					$this->response->redirect(Request::$uri . '?show=categories');
				}
				if ($showed == 'items' && !$moduleSettings['items']['actions']['add']) {
					$this->response->redirect(Request::$uri . '?show=categories');
				}
			}
			if ($moduleSettings['levels'] == $moduleSettings['level']) {
				if ($showed == 'categories') {
					$this->response->redirect(Request::$uri);
				}
				$showed = 'items';
			}
		} else {
			$showed = 'items';
		}

		// sorting
		if (Request::$isAjax && isset($_POST['sorting'])) {
			switch ($showed) {
				case 'items':
					$items = Model_Directoryitem::getCategoryItems($parentId, $section, 'id', 'ASC', false);
					break;
				case 'categories':
					$items = Model_Directoryitem::getChildrenCategories($parentId, $section, 'id', 'ASC', false);
					break;
				default:
					throw new ForbiddenException('Error');
					break;
			}
			foreach ($_POST[$showed] as $k => $v) {
				$position = ($k + 1);
				if (isset($items['data'][$v]) && $items['data'][$v]->position != $position) {
					Model_Directoryitem::update(array('position' => $position), (int) $v);
				}
			}
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');
			$this->response->body = json_encode(array('answer' => 'Changes has been saved'));
			return;
		}

		$fullContent = false;
		if ($moduleSettings['levels'] && $moduleSettings['levels'] > $moduleSettings['level'] && $moduleSettings['items']['actions']['add']) {
			$fullContent = true;
		}

		if ($showed == 'categories') {
			$permisions = $this->itemData['permissions']['categories'];
			$modSettings = $moduleSettings['categories'];
		} else {
			$permisions = $this->itemData['permissions']['items'];
			$modSettings = $moduleSettings['items'];
		}

		$filtersOrder = array(
		    'name' => array('title' => 'name', 'active' => true),
		    'cdate' => array('title' => 'date created', 'active' => true)
		);
		if (isset($modSettings['fields']['date'])) {
			$filtersOrder['date'] = array('title' => 'date (field)', 'active' => true);
		}
		if ($permisions['sorting']) {
			$filtersOrder['position'] = array('title' => 'position', 'active' => true);
		}

		$filtersDir = array(
		    'ASC' => array('title' => 'ASC', 'active' => true),
		    'DESC' => array('title' => 'DESC', 'active' => true)
		);
		$filters = array(
		    'order' => Request::get('order', false, array_keys($filtersOrder)),
		    'dir' => Request::get('dir', 'ASC', array_keys($filtersDir))
		);

		$contentTitle = isset($modSettings['title']) ? $modSettings['title'] : 'name';

		if ($showed == 'categories') {
			if ($filters['order'] == false) {
				if ($permisions['sorting']) {
					$filters['order'] = 'position';
					$filters['dir'] = 'ASC';
				} elseif (isset($modSettings['fields']['date'])) {
					$filters['order'] = 'date';
					$filters['dir'] = 'DESC';
				} else {
					$filters['order'] = 'name';
				}
			}

			if ($filters['order'] == 'name') {
				$order = $contentTitle;
			} else {
				$order = $filters['order'];
			}

			$items = Model_Directoryitem::getChildrenCategories($parentId, $section, $order, $filters['dir'], 20);
		} else {
			if ($filters['order'] == false) {
				if ($permisions['sorting']) {
					$filters['order'] = 'position';
					$filters['dir'] = 'ASC';
				} elseif (isset($modSettings['fields']['date'])) {
					$filters['order'] = 'date';
					$filters['dir'] = 'DESC';
				} else {
					$filters['order'] = 'name';
				}
			}

			if ($filters['order'] == 'name') {
				$order = $contentTitle;
			} else {
				$order = $filters['order'];
			}

			$items = Model_Directoryitem::getCategoryItems($parentId, $section, $order, $filters['dir'], 20);
		}

		if ($permisions['sorting'] && ($filters['order'] != 'position' || $filters['dir'] != 'ASC')) {
			$permisions['sorting'] = false;
		}

		if (Request::$isAjax && isset($_POST['pager'])) {
			$content = new View('admin/parts/categoryIndexItems');
		} else {
			$this->view->content = $content = new View('admin/categoryIndex');
			$this->view->active = $section;
		}

		$content->section = $section;
		$content->permisions = $permisions;
		$content->itemData = $this->itemData;
		$content->contentTitle = $contentTitle;
		$content->showed = $showed;
		$content->items = $items['data'];
		$content->modSettings = $modSettings;

		if (Request::$isAjax && isset($_POST['pager'])) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');

			$this->response->body = json_encode(array(
			    'content' => (string) $content,
			    'next' => $items['paginator']['next']
			));
			return false;
		}

//		System::dump($modSettings, 1);

		$content->title = ($parentId == 0 && isset($moduleSettings['title'])) ? $moduleSettings['title'] : false;
		$content->parentId = $parentId;
		$content->category = ($parentId) ? $category : 0;
		$content->count = $this->itemData['stat']['count'];
		$content->fullContent = $fullContent;
		$content->paginator = $items['paginator'];
		$content->filtersOrder = $filtersOrder;
		$content->filtersDir = $filtersDir;
		$content->filters = $filters;

		$this->getMessages();
	}

	public function actionItem($section, $itemId) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryitem::getItem($itemId, $section);

		if ($item->parentId) {
			if (!isset($this->categories[$item->parentId])) {
				throw new ForbiddenException('Category not found');
			}
		}

		$this->getPermissions($section, $item, false);
		$this->setCategoriesCrumbs($section, $item->parentId, true);

		$showed = Request::get('edit', false);
		$moduleSettings = $this->itemModuleSettings;

		$block = $item->isCategory ? 'categories' : 'items';
		$crumbLabel = isset($moduleSettings[$block]['title']) ? $moduleSettings[$block]['title'] : 'name';
		$crumbTitle = $item->$crumbLabel ? $item->$crumbLabel : ($block == 'items' ? 'Item' : 'Category');

		$this->view->crumbs($crumbTitle);
		$this->view->active = $section;

		if ($block == 'categories') {
			$permisions = $this->itemData['permissions']['categories'];
			$modSettings = $moduleSettings['categories'];
		} else {
			$permisions = $this->itemData['permissions']['items'];
			$modSettings = $moduleSettings['items'];
		}

		$order = 'id';
		$dir = 'DESC';
		if (isset($modSettings[$showed]['sorting']) && $modSettings[$showed]['sorting']) {
			$order = 'position';
			$dir = 'ASC';
		}

		switch ($showed) {
			case 'images':
				$this->view->crumbs('Images list');
				$items = Model_Directoryimage::getByParentId($item->id, $section, $order, $dir);

				break;
			case 'attachments':
				$this->view->crumbs('Attachments list');
				$items = Model_Directoryattachment::getByParentId($item->id, $section, $order, $dir);

				break;
			case 'videos':
				$this->view->crumbs('Videos list');
				$size = isset($modSettings['videos']['preview']) ? $modSettings['videos']['preview'] : false;
				$items = Model_Directoryvideo::getByParentId($item->id, $section, $order, $dir);

				break;
			case 'audios':
				$this->view->crumbs('Audios list');
				$items = Model_Directoryaudio::getByParentId($item->id, $section, $order, $dir);

				break;
			default:
				throw new NotFoundException('Section not found');
				break;
		}

		if (Request::$isAjax && !empty($_POST[$showed])) {
			foreach ($_POST[$showed] as $k => $v) {
				$position = ($k + 1);
				if ($items[$v]->position != $position) {
					switch ($showed) {
						case 'images':
							Model_Directoryimage::update(array('position' => $position), (int) $v);

							break;
						case 'attachments':
							Model_Directoryattachment::update(array('position' => $position), (int) $v);

							break;
						case 'videos':
							Model_Directoryvideo::update(array('position' => $position), (int) $v);

							break;
						case 'audios':
							Model_Directoryaudio::update(array('position' => $position), (int) $v);

							break;
					}
				}
			}

			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/json');
			$this->response->body = json_encode(array('answer' => 'Changes has been saved'));
			return;
		}

		$this->view->content = $content = new View('admin/itemIndex');

		$content->section = $section;
		$content->showed = $showed;
		$content->item = $item;
		$content->itemPermissions = $permisions;
		$content->items = $items;
		$content->sorting = $this->itemData['permissions'][$showed]['sorting'];
		$content->modSettings = $modSettings;
		$content->moduleSettings = $moduleSettings;
		$content->itemData = $this->itemData;

		$this->getMessages();

		$this->view->script('/js/libs/fancybox/jquery.fancybox.pack.js');
		$this->view->style('/css/libs/fancybox/jquery.fancybox.dashboard.css');
	}

//	public function actionHotspots($section, $imageId)
//	{
//		exit();
//		
//		$section = strtolower($section);
//		$this->checkSection($section);
//		$image = $this->model->getImage($imageId, $section);
//		$item = $this->model->getItem($image['itemId'], $section);
//		if (!$item) {
//			throw new ForbiddenException('Item not found');
//		}
//		$this->getPermissions($section, $item);
//		$moduleSettings = $this->moduleSettings[$section];
//		$level = ($item['parentId'] > 0 ? (string)$this->categories[$item['parentId']]['level'] : '0');
//		if (isset($moduleSettings['levelRules'][$level])) {
//			$moduleSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level]);
//		}
//		$hotspots = $this->model->getImageHotspots($image['id'], $section, 'position', 'ASC');
//		if (Request::$isAjax) {
//			if ('POST' == Request::$method) {
//				if (isset($_POST['hotspots'])) {
//					$action = 'hotspots';
//				}
//				if (!isset($action)) {
//					$this->response->body = json_encode(array('answer' => 'error'));
//					$this->response->send();
//				}
//				switch ($action) {
//					case 'hotspots':
//						$items = $hotspots;
//						break;
//				}
//				foreach ($_POST[$action] as $k=>$v) {
//					$position = ($k + 1);
//					if ($items[$v]['position'] != $position) {
//						Model_Table::instance('directoryHotspots')->update(array('position' => $position), (int)$v);
//					}
//				}
//				$this->autoRender = false;
//				$this->response->setHeader('Content-Type', 'application/json');
//				$this->response->body = json_encode(array('answer' => 'Changes has been saved'));
//				return;
//			}
//		}
//		$this->addScript('/scripts/jquery/jquery.js');
//		$this->addScript('/scripts/jquery/jquery-ui.js');
//		$this->addScript('/scripts/plugin/sortable.js');
//		$this->addScript('/scripts/swfobject.js');
//		$this->template->content = new View('admin/hotspotIndex');
//		$this->template->content->section          = $section;
//		$this->template->content->image            = $image;
//		$this->template->content->item             = $item;
//		$this->template->content->hotspots         = $hotspots;
//		$this->template->content->pathHotspots       = '/' . $this->pathHotspots;
//		$this->template->content->backUrl          = Request::$controller . 'item/' . $section  . '/' . $item['id'] . '/';
//		$this->template->content->moduleSettings = $moduleSettings;
//		$this->template->content->itemData = $this->itemData;
//	}


	public function actionAddCategory($section, $categoryId = 0) {
		$section = strtolower($section);
		$this->checkSection($section);

		if ($categoryId && !isset($this->categories[$categoryId])) {
			throw new NotFoundException('Category not found');
		}
		$this->actionEditItem($section, null, $categoryId, 1);
	}

	public function actionEditCategory($section, $categoryId = 0) {
		$section = strtolower($section);
		$this->checkSection($section);

		if ($categoryId && !isset($this->categories[$categoryId])) {
			throw new NotFoundException('Category not found');
		}

		$this->actionEditItem($section, $categoryId, null, 1);
	}

	public function actionAddItem($section, $categoryId = 0) {
		$section = strtolower($section);
		$this->checkSection($section);

		if ($categoryId && !isset($this->categories[$categoryId])) {
			throw new NotFoundException('Category not found');
		}

		$this->actionEditItem($section, null, $categoryId);
	}

	/**
	 *
	 * Init form
	 * 
	 * @param array $section Module settings
	 * @param items|images|attachments|videos|audios $type Type
	 * 
	 * return object new Form
	 */
	private function initForm($settings, $type) {
		$settingsType = $settings;

		$fields = isset($settingsType['fields']) ? $settingsType['fields'] : array('name');

		$form = new Form($type, '', Request::$uri . Request::$query);
		$form->labelWidth = '120px';
		$form->fieldset->name = 'default';
		$form->fieldset->attribute('class', 'no-stripes');

		switch ($type) {
			case 'images':
				$fielsDefault = array('name', 'select1', 'select2', 'checkbox1', 'checkbox2', 'text1', 'text2');
				$exts = isset($settingsType['extensions']) ? $settingsType['extensions'] : array('jpg', 'jpeg', 'png', 'gif');
				$maxSize = isset($settingsType['extensions']) ? $settingsType['extensions'] : 5242880;
				$form->file('file', 'Image file')
					->attribute('size', 35)
					->rule('extension', $exts);

				break;
			case 'attachments':
				$fielsDefault = array('name', 'select1', 'select2', 'checkbox1', 'checkbox2', 'text1', 'text2');
				$exts = isset($settingsType['extensions']) ? $settingsType['extensions'] : array('jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'rar', 'zip', 'pdf', 'txt', 'mp3');
				$form->file('file', 'Attachment')
					->rule('extension', $exts)
					->grouped();
				break;
			case 'videos':
				$fielsDefault = array('name', 'select1', 'select2', 'checkbox1', 'checkbox2', 'text1', 'text2');
				$form->select('type', array('' => '- select type -', VIDEO_TYPE_YOUTUBE => 'Youtube', VIDEO_TYPE_VIMEO => 'Vimeo'), 'Type')
					->attribute('required', 'required')
					->required();
				$form->text('url', 'Video link')
					->attribute('disabled', '')
					->attribute('size', 40)
					->rule(function($field) {
							$field->attribute('disabled', false);
							if ($field->fieldset->elements['type']->value == VIDEO_TYPE_YOUTUBE) {
								if (!preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $field->value, $match)) {
									return 'Validation error';
								}
							} elseif ($field->fieldset->elements['type']->value == VIDEO_TYPE_VIMEO) {
								if (!preg_match('/\/(\d+).*/', $field->value, $match)) {
									return 'Validation error';
								}
							}
						})
					->required();
				if (isset($settings['preview']) && $settings['preview']) {
					$form->checkbox('fileDefault', '', 'Use default preview')
						->contentTop('<div class="thumb"></div>')
						->grouped();
					$form->file('file')
						->contentTop('or upload custom:')
						->before(function($field) {
								if (!$field->fieldset->elements['fileDefault']->value) {
									$field->rule('required');
								}
							})
						->rule('extension', array('jpg', 'jpeg', 'png', 'gif'))
						->grouped();
				}

				break;
			case 'audios':
				$fielsDefault = array('name', 'select1', 'select2', 'checkbox1', 'checkbox2', 'text1', 'text2');
				$exts = isset($settingsType['extensions']) ? $settingsType['extensions'] : array('mp3');
				$form->file('file', 'Audio file')
					->attribute('size', 35)
					->rule('extension', $exts);
				break;
			default:
				$fielsDefault = array('name', 'note1', 'note2', 'note3', 'select1', 'select2', 'checkbox1', 'checkbox2', 'text1', 'text2', 'address', 'phone', 'email', 'url', 'price', 'date', 'datetime');
				break;
		}

		foreach ($fields as $field => $label) {
			if (is_numeric($field)) {
				$field = $label;
				$label = false;
			}

			if (in_array($field, $fielsDefault)) {
				switch ($field) {
					case 'name':
						$form->text($field, $label ? $label : 'Name')
							->attribute('size', 40)
							->rule('maxLength', 128);
						break;
					case 'date':
						$form->text($field, $label ? $label : 'Date')
							->rule('date')
							->rule('maxLength', 10)
							->hint('Format: YYYY-MM-DD')
							->attribute('class', 'DatePicker')
							->attribute('size', 10);
						break;
					case 'datetime':
						$form->text($field, $label ? $label : 'Date and Time')
							->attribute('size', 16)
							->attribute('class', 'TimePicker')
							->hint('Format: Y-m-d H:i')
							->rule('maxLength', 64)
							->rule('datetime')
							->after(function($field) {
									$field->value .= ':00';
								});
						break;
					case 'note1':
					case 'note2':
						$form->text($field, $label ? $label : 'Note')
							->attribute('size', 40)
							->rule('maxLength', 64);
						break;
					case 'select1':
					case 'select2':
						$select = array();
						$source = $settingsType[$field]['source'];

						if (isset($source['before'])) {
							$select = $source['before'];
						}
						if (isset($source['query'])) {
							$key = (isset($source['value']) ? $source['value'] : 'id');
							$value = (isset($source['name']) ? $source['name'] : 'name');

							foreach (Database::getInstance()->query($source['query']) as $row) {
								$select[$row->$key] = $row->$value;
							}

							if (empty($select)) {
								return isset($source['message']) ? $source['message'] : 'Error in ' . $label . ' field.';
							}
						}
						$form->select($field, $select, $label ? $label : '&nbsp;');

						if (isset($source['multiple']) and $source['multiple']) {
							$form->elements[$field]->multiple();
						}
						break;
					case 'checkbox1':
					case 'checkbox2':
						$form->checkbox($field, '', $label ? $label : '&nbsp;');
						break;
					case 'text1':
					case 'text2':
						$editor = isset($settingsType['editor']) ? $settingsType['editor'] : array();

						if (in_array($field, $editor)) {
							$form->html('insert-' . $field, '', '<img src="/resources/images/dashboard/paste.png" width="273" heigth="63" alt="Paste icon" style="vertical-align:middle;"/>')
								->contentBottom('Paste text by clicking this button!');
						}
						$form->textarea($field, $label ? $label : 'Description')
							->attribute('cols', 80)
							->attribute('rows', 12);
						if (in_array($field, $editor)) {
							$form->elements[$field]->grouped();
							$form->elements[$field]->attribute('class', 'elrte');
						}

						if (!empty($settingsType['editorModules'])) {
							foreach ($settingsType['editorModules'] as $module) {
								switch ($module) {
									case 'images':
										$form->elements[$field]->attribute('data-images', Request::generateUri(false, 'files', Request::$params));
										break;
									case 'attachments':
										$form->elements[$field]->attribute('data-attachments', Request::generateUri(false, 'files', Request::$params));
										break;
									default:
										break;
								}
							}
						};

						break;
					case 'address':
						$form->text($field, $label ? $label : 'Address')
							->attribute('size', 40)
							->rule('maxLength', 64);
						break;
					case 'phone':
						$form->text($field, $label ? $label : 'Phone')
							->attribute('size', 40)
							->rule('maxLength', 64);
						break;
					case 'email':
						$form->text($field, $label ? $label : 'E-mail')
							->attribute('size', 40)
							->rule('maxLength', 64)
							->rule('email');
						break;
					case 'url':
						$form->text($field, $label ? $label : 'URL')
							->attribute('size', 40)
							->rule('maxLength', 64)
							->rule('url');
						break;
					case 'price':
						$form->text($field, $label ? $label : 'Price (USD)')
							->attribute('size', 10)
							->rule('maxLength', 10)
							->rule('float');
						break;
				}
			} else {
				return "Field {$field} not found";
			}
		}

		if (!empty($settingsType['required'])) {
			foreach ($settingsType['required'] as $field) {
				if (isset($form->elements[$field]) && !(isset($settingsType['editor']) && in_array($field, $settingsType['editor']))) {
					$form->elements[$field]->rule('required');
					$form->elements[$field]->attribute('required', 'required');
				}
			}
		}


		$form->fieldset('submit');
		$form->submit('submit', 'Save')
			->attribute('eva-content', 'Save changes')
			->attribute('class', 'btn btn-ok');

		$this->view->style('/css/autoform.css');

		return $form;
	}

	private function addPlugins($settings) {
		$fields = isset($settings['fields']) ? $settings['fields'] : array('name');

		if (in_array('date', $fields) || isset($fields['date'])) {
			$this->view->script('/js/jquery/jquery-ui.js');
			$this->view->script('/js/jquery/timepicker/timepicker.js');
			$this->view->style('/css/jquery-ui/ui-custom/jquery-ui.css');
		}
		if (in_array('datetime', $fields) || isset($fields['datetime'])) {
			$this->view->script('/js/jquery/jquery-ui.js');
			$this->view->script('/js/jquery/timepicker/timepicker.js');
			$this->view->style('/css/jquery-ui/ui-custom/jquery-ui.css');
		}
		if (isset($settings['editor'])) {
			$this->view->script('/js/jquery/jquery-ui.js');
			$this->view->script('/js/libs/elrte/elrte.min.js');
			$this->view->style('/css/jquery-ui/ui-custom/jquery-ui.css');
			$this->view->style('/css/libs/elrte/elrte.full.css');

			if (!empty($settings['editorModules'])) {
				$this->view->script('/js/libs/fancybox/jquery.fancybox.pack.js');
				$this->view->style('/css/libs/fancybox/jquery.fancybox.dashboard.css');
			}
		}
	}

	public function actionEditItem($section, $id, $categoryId = null, $isCategory = 0) {
		$section = strtolower($section);
		$this->checkSection($section);

		if ($id) {
			$item = Model_Directoryitem::getItem($id, $section);
			$parentId = $item->parentId;
			$isCategory = $item->isCategory;

			$this->getPermissions($section, $item, false);
		} else {
			$parentId = $categoryId;
			$this->getPermissions($section, $categoryId);
		}

		$type = ($isCategory ? 'categories' : 'items');

		if ((!$id && !$this->itemData['permissions'][$type]['add']) || ($id && !$this->itemData['permissions'][$type]['edit'])) {
			throw new NotFoundException('Permissions denied');
		}

		$moduleSettings = $this->itemModuleSettings;

		$this->setCategoriesCrumbs($section, $parentId, true);

		$backUrl = Request::$controller . 'browse/' . $section . '/' . $parentId . '/?show=' . $type;

		if ($id) {
			$crumbLabel = isset($moduleSettings[$type]['title']) ? $moduleSettings[$type]['title'] : 'name';
			$crumbTitle = $item->$crumbLabel ? $item->$crumbLabel : ($type == 'items' ? 'Item' : 'Category');

			$this->view->crumbs($crumbTitle);
			$this->view->crumbs('Edit ' . ($isCategory ? 'category' : 'item'));
		} else {
			$this->view->crumbs('Add new ' . ($isCategory ? 'category' : 'item'));
		}

		$this->view->content = $content = new View('admin/form');
		$this->view->active = $section;

		$form = $this->initForm($moduleSettings[$type], 'item', $id);
		$this->addPlugins($moduleSettings[$type]);

		if (is_object($form)) {
			if ($id) {
				if (isset($item->datetime) && strlen($item->datetime) == 19) {
					$item->datetime = substr($item->datetime, 0, -3);
				}

				$form->loadValues($item->getValues());
			}
			if (Request::$method == 'POST') {
				if ($form->validate()) {
					if (isset($form->elements['name'])) {
						$form->elements['name']->value = trim($form->elements['name']->value);
					}

					$alias = Url::safe((isset($form->elements['name']) ? $form->elements['name']->value : ''));
					$alias = $alias != '' ? $alias : $section;
					$getNewAlias = false;

					if ($id) {
						$itemAlias = substr($item->alias, 0, strrpos($item->alias, '-'));
						$itemAlias = $itemAlias ? $itemAlias : $item->alias;
					} else {
						$getNewAlias = true;
					}
					if (($id !== null ? ($itemAlias != $alias) : false)) {
						if (is_numeric(substr($item->alias, strrpos($item->alias, '-'), 1)) || substr($item->alias, 0, strrpos($item->alias, '-')) == '') {
							$getNewAlias = true;
						} else {
							$alias = $item->alias;
						}
					}
					if ($getNewAlias) {
						$newAlias = $alias;
						$i = 1;
						while (Model_Directoryitem::exists('alias', $newAlias)) {
							$newAlias = $alias . '-' . $i++;
						}
						$alias = $newAlias;
					}
					if ($id) {
						$values = $form->getModified();

						if (isset($values['date'])) {
							if ($values['date'] == '') {
								$values['date'] = null;
							}
						}
						if (isset($values['datetime'])) {
							if ($values['datetime'] == '') {
								$values['datetime'] = null;
							}
						}
						$values['alias'] = $alias;
						$values['parentId'] = $parentId;
						$values['section'] = $section;

						Model_Directoryitem::update($values, $id);

						$this->message('Item has been changed');
					} else {
						$values = $form->getValues();
						$cnt = 0;

//						Model_Directoryitem::setKey('token');
						while (true === Model_Directoryitem::exists('token', ($token = Text::random('alphanuml', 3))) && $cnt < 7) {
							$cnt++;
						}

						$values['token'] = $token;
						if (isset($values['date']) ? ($values['date'] == '') : true) {
							$values['date'] = null;
						}
						if (isset($values['datetime'])) {
							if ($values['datetime'] == '') {
								$values['datetime'] = null;
							}
						}
						$values['alias'] = $alias;
						$values['parentId'] = $categoryId;
						$values['section'] = $section;
						if ($isCategory) {
							$values['isCategory'] = 1;
						}

						Model_Directoryitem::create($values);

						$this->message('New item has been created');
					}
					$this->response->redirect($backUrl);
				}
			}
		}

		$content->form = $form;
		$content->backUrl = $backUrl;
		$content->showed = 'text';
		$content->section = $section;
		$content->modSettings = $moduleSettings;
		$content->itemData = $this->itemData;

		$content->item = ($id) ? $item : false;

		$this->getMessages();
	}

	public function actionFiles($section, $itemId) {
		if (Request::$isAjax) {
			$section = strtolower($section);
			$this->checkSection($section);

			$item = Model_Directoryitem::getItem($itemId, $section);

			if ($item->parentId) {
				if (!isset($this->categories[$item->parentId])) {
					throw new ForbiddenException('Category not found');
				}
			}

			$this->getPermissions($section, $item, false);

			$images = Model_Directoryimage::getByParentId($item->id, $section);
			$attachments = Model_Directoryattachment::getByParentId($item->id, $section);
			$files = array(
			    'images' => $images,
			    'attachments' => $attachments
			);

			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'application/html');
			$this->response->body = new View('admin/parts/list-files', array('files' => $files));

			return;
		}
	}

	public function actionAddImage($section, $itemId) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryitem::getItem($itemId, $section);
		$this->getPermissions($section, $item, false);
		if (!$item) {
			throw new InvalidArgumentException('Invalid item ID.');
		} else {
			$this->actionEditImage($section, null, $itemId);
		}
	}

	public function actionUpload($type, $section, $itemId) {
		if (Request::$isAjax or (Request::$method == 'POST' and isset($_FILES['qqfile']))) {
			$this->autoRender = false;

			// GET config
			$section = strtolower($section);
			$this->checkSection($section);
			$item = Model_Directoryitem::getItem($itemId, $section);
			$this->getPermissions($section, $item, false);
			$moduleSettings = $this->itemModuleSettings;


			// Allow extension
			$imgExtensions = $moduleSettings[$type]['extension'];
			$allowedExtensions = $moduleSettings[$type]['extension'];

			// max file size in bytes
			$sizeLimit = $moduleSettings[$type]['maxSize'];
			$maxFiles = 999999;
			if (!empty($moduleSettings[$type]['max']))
				$maxFiles = $moduleSettings[$type]['max'];

			$class = 'Model_Directory' . strtolower(substr($type, 0, strlen($type) - 1));

			$count = $class::getCount($itemId, $section)->countId;
			$sesionCount = 0;
			if (!empty($_SESSION['uploadFiles'][$class])) {
				$sesionCount = count($_SESSION['uploadFiles'][$class]);
			}
			$count += $sesionCount;

			$class::cleanItem($section, $this->sizes);

			$tmpDir = $class::$path . '_tmp/';
			if (!file_exists($tmpDir)) {
				mkdir($tmpDir, 0777, true);
			}


			$cnt = 0;
			while (true === $class::exists('alias', ($alias = Text::random('alphanuml', 6))) && $cnt < 10) {
				$cnt++;
			}
			if ($cnt >= 10) {
				return array(
				    'success' => false,
				    'error' => 'Can not create token for file.'
				);
			}

			$uploader = new fileuploader($allowedExtensions, $sizeLimit);

			if ($uploader->getSize() >= $sizeLimit) {
				$message = array(
				    'success' => false,
				    'error' => 'You have exceeded limit of disk space for saving your files. For rise disk space use module File Storage'
				);
			} elseif ($count >= $maxFiles) {
				$message = array(
				    'success' => false,
				    'error' => 'You have exceeded max files limit!'
				);
			} else {
				$result = $uploader->handleUpload($tmpDir, $alias);


				// to pass data through iframe you will need to encode all html tags
				if (isset($result['success'])) {
					$fileExt = $result['filetype'];

					if ($result['success']) {
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						if (isset($fileExt)) {
							$values['ext'] = $fileExt;
						}
						$values['filesize'] = $uploader->getSize();
						$values['filename'] = $result['basename'];
//												
						$urls = $tmpDir . $result['filename'] . '.' . $result['filetype'];



						$tmpFilename = $tmpDir . 'tmp';
						$uploadedFilename = $tmpDir . $alias . '.' . $fileExt;

						$tmpflag = $tmpDir . 'flaguploader';
						$tmpflag_count = 0;
						while (file_exists($tmpflag) && $tmpflag_count < 10) {
							sleep(1);
							$tmpflag_count++;
						}

						if ($tmpflag_count >= 10) {
							$result['error'] = "System busy";
						}


						if (empty($result['error'])) {

							$text = "fileUploader Access";
							$fp = fopen($tmpflag, "w");
							fwrite($fp, $text);
							fclose($fp);


							if (false === copy($uploadedFilename, $tmpFilename)) {
								umask($umask);
								$result['error'] = 'Can`t copy file';
								$result['success'] = false;
							} else {
								switch ($type) {
									case "images":
										$this->resizeImage($alias, $section, $item->isCategory);
										if ((isset($moduleSettings['images']['keepOriginal']) ? $moduleSettings['images']['keepOriginal'] : false)) {
											$dir = $class::dir($alias, 'original', $section);
											if (!file_exists($dir)) {
												mkdir($dir, 0777, true);
											}
											$fileName = $dir . $alias . '.' . $fileExt;
											if (!file_exists($dir)) {
												mkdir($dir, 0777, true);
											}
											copy($tmpFilename, $fileName);
											Image::setCopyright($fileName);
										}
										list ($imgWidth, $imgHeight, $typeImg, $attr) = getimagesize($tmpFilename);
										$resolution = $imgWidth . 'x' . $imgHeight;
										$values['resolution'] = $resolution;
										unset($values['filesize']);
										unset($values['filename']);
										$urls = $class::src2($alias, 'fullsize', $section);
										break;
									case "attachments" || "audios":
										$umask = umask(0);
										$dir = $class::dir($alias, $section);
										if (!file_exists($dir)) {
											mkdir($dir, 0777, true);
										}
										$fileName = $dir . $alias . '.' . $fileExt;
										if (false === copy($tmpFilename, $fileName)) {
											umask($umask);
											throw new Exception('File %s cannot be moved.');
										}
										break;
								}

								$values2 = $values;
								$values2['itemId'] = 0;
								$createItem = $class::create($values2);
								$values['id'] = $createItem->id;
							}
						}

						if (file_exists($tmpflag)) {
							unlink($tmpflag);
						}


						$form = $this->initForm($moduleSettings[$type], $type . '_' . $alias);
//						$this->view->scripts = array('/js/directory.js');
//						$this->view->links = array('/css/libs/fileuploader.css');
						$this->addPlugins($moduleSettings[$type]);
						$this->view->scripts = array();
						$this->view->links = array();

						$form->attribute('onSubmit', 'return uploader.submitItem(this);');
						$form->attribute('action', Request::generateUri('admin/directory', 'saveuploadedfiles/' . $alias, false));
						$form->fieldset->name = 'default';
						$form->html('fileName', 'File name', $result['basename']);


						$style = "";
						$script = "";
						if (!empty($this->view->links))
							$style = $this->view->links;
						if (!empty($this->view->scripts))
							$script = $this->view->scripts;


						$message = array(
						    'success' => true,
						    'token' => $alias,
//						    'id' => '',
						    'ext' => $values['ext'],
						    'url' => $urls,
//						    'isImage' => $isImage,
						    'type' => $type,
						    'name' => $result['basename'],
						    'style' => $style,
						    'scrypt' => $script
						);
						$values['param'] = $message;
						$_SESSION['uploadFiles'][$class][$alias] = $values;
						unlink($uploadedFilename);

						$view = new View('admin/parts/short-form', array_merge(array('form' => $form), $message));
						$message['form'] = (string) $view;
					}
				} else {
					$result['success'] = false;
					if (!isset($result['error']) || strlen($result['error']) < 2) {
						$result['error'] = 'Something went wrong.';
					}
				}


				if (!$result['success']) {
					$message = $result;
				}
			}


			$this->response->body = json_encode($message);
			return false;
		}
	}

	public function actionSaveUploadedFiles($aliasFile) {
		if (!empty($_SESSION['uploadFiles'])) {
			foreach ($_SESSION['uploadFiles'] as $key => $files) {
				$class = $key;
				foreach ($files as $alias => $item) {
					if ($alias == $aliasFile) {


						$section = strtolower($item['section']);
						$itemId = $item['itemId'];
						$type = substr($key, 15) . 's';
						$param = $item['param'];




						$this->checkSection($section);
						$getitem = Model_Directoryitem::getItem($itemId, $section);
						$this->getPermissions($section, $getitem, false);
						$this->setCategoriesCrumbs($section, $getitem->parentId, true);

						$moduleSettings = $this->itemModuleSettings;
						$form = $this->initForm($moduleSettings[$type], $type . '_' . $alias);


						$this->view->scripts = array();
						$this->view->links = array();
						$this->addPlugins($moduleSettings[$type]);


						$form->attribute('onSubmit', 'return uploader.submitItem(this);');
						$form->attribute('action', Request::generateUri('admin/directory', 'saveuploadedfiles/' . $alias, false));



						if (is_object($form)) {
							if (Request::$method == 'POST') {
								$this->autoRender = false;
								if ($form->validate()) {
									$values = $form->getValues();
									$values = array_merge($values, $item);
									unset($values['param']);

									$class::update($values, $values['id']);

									$message['success'] = true;
									$message['form'] = $type . "_" . $alias;
									unset($_SESSION['uploadFiles'][$key][$alias]);
								} else {
									$message['success'] = false;
									$message['form'] = $type . "_" . $alias;
									$view = new View('admin/parts/short-form', array_merge(array('form' => $form), $item['param']));
									$message['form_data'] = (string) $view;
								}

								$this->response->body = json_encode($message);
							}
						}
					}
				}
			}
		}
	}

	public function actionRemoveUploadFile($aliasFile) {
//		dump($_SESSION['uploadFiles'],1);
		$this->autoRender = false;
		$message['success'] = $_SESSION;
		if (!empty($_SESSION['uploadFiles'])) {
			foreach ($_SESSION['uploadFiles'] as $key => $files) {
				$class = $key;
				foreach ($files as $alias => $item) {


					$type = substr($key, 15) . 's';
					if ($alias == $aliasFile) {
						$class::remove($item['id']);

						$message['success'] = true;
						$message['form'] = $type . "_" . $alias;
						unset($_SESSION['uploadFiles'][$key][$alias]);
						break(2);
					} else {
						$message['success'] = false;
					}
				}
			}
		}
		$this->response->body = json_encode($message);
		return false;
	}

	public function actionEditImage($section, $id, $itemId = null) {
		$section = strtolower($section);
		$this->checkSection($section);

		if (Request::get('modal', false)) {
			$this->view = new View('admin/template-iframe');
			$this->view->content = $content = new View('admin/parts/simple-form');
		} else {
			$this->view->content = $content = new View('admin/form');
		}

		$content->type = "images";
		$content->section = $section;
		$content->itemId = $itemId;

		if ($id) {
			$image = Model_Directoryimage::getById($id, $section);
			$itemId = $image->itemId;
		}

		$item = Model_Directoryitem::getItem($itemId, $section);
		$type = $item->isCategory ? 'categories' : 'items';

		$this->getPermissions($section, $item, false);
		$this->setCategoriesCrumbs($section, $item->parentId, true);

		if ((!$id && !$this->itemData['permissions']['images']['add']) || ($id && !$this->itemData['permissions']['images']['edit'])) {
			throw new ForbiddenException('Maximum files');
		}

		$moduleSettings = $this->itemModuleSettings;
		$backUrl = Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=images';

		$itemLabel = isset($moduleSettings[$type]['title']) ? $moduleSettings[$type]['title'] : 'name';
		$itemTitle = $item->$itemLabel ? $item->$itemLabel : ($type == 'items' ? 'Item' : 'Category');

		$this->view
			->crumbs($itemTitle)
			->crumbs('Images list', $backUrl);
		$this->view->active = $section;


		$form = $this->initForm($moduleSettings['images'], 'images');
		$this->addPlugins($moduleSettings['images']);


		if ($id) {
			$this->view->crumbs('Edit image');
			$content->multiUpload = false;
		} else {
			$this->view->crumbs('Add new image');
			$form->elements['file']->attribute('multiple');
			$content->multiUpload = true;
			$form->elements['submit']->attribute('onClick', 'return uploader.submitHeadForm();');
			$content->maxSize = $moduleSettings['images']['maxSize'];
			$content->ext = $moduleSettings['images']['extension'];
		}


		if (is_object($form)) {
			if ($id) {
				$form->loadValues($image->getValues());
			} else {
				$form->elements['file']->rule('required');
				$form->elements['file']->attribute('required', 'required');
			}
			if (Request::$method == 'POST') {
				if ($form->validate()) {
					if ($id) {
						$alias = $image->alias;
					} else {
						$cnt = 0;
						while (true === Model_Directoryimage::exists('alias', ($alias = Text::random('alphanuml', 6))) && $cnt < 7) {
							$cnt++;
						}
					}
					if (isset($form->elements['file']->files[0])) {
						$files = $form->elements['file']->files;
						$file = $files[0];
						$fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
						$umask = umask(0);
						$tmpDir = Model_Directoryimage::$path . '_tmp/';
						if (!file_exists($tmpDir)) {
							mkdir($tmpDir, 0777, true);
						}
						$tmpFilename = $tmpDir . 'tmp';
						if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
							umask($umask);
							throw new Exception('File %s cannot be moved.');
						} else {
							$this->resizeImage($alias, $section, $item->isCategory);
							if ((isset($moduleSettings['images']['keepOriginal']) ? $moduleSettings['images']['keepOriginal'] : false)) {
								$dir = Model_Directoryimage::dir($alias, 'original', $section);
								$fileName = $dir . $alias . '.' . $fileExt;
								if (!file_exists($dir)) {
									mkdir($dir, 0777, true);
								}
								copy($tmpFilename, $fileName);
								Image::setCopyright($fileName);
							}
						}
						list ($imgWidth, $imgHeight, $type, $attr) = getimagesize($tmpFilename);
						$resolution = $imgWidth . 'x' . $imgHeight;
					}
					if ($id) {
						$values = $form->getModified();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						if (isset($resolution)) {
							$values['resolution'] = $resolution;
						}
						if (isset($fileExt)) {
							$values['ext'] = $fileExt;
						}
						Model_Directoryimage::update($values, $id);

						$this->message('Image has been updated');
					} else {
						$values = $form->getValues();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						if (isset($resolution)) {
							$values['resolution'] = $resolution;
						}
						if (isset($fileExt)) {
							$values['ext'] = $fileExt;
						}
						Model_Directoryimage::create($values);

						$this->message('Image has been created');
					}

					if (Request::get('modal', false)) {
						$this->view->content = new View('admin/parts/success-upload');
					} else {
						$this->response->redirect($backUrl);
					}
				}
			}
		}

		$content->form = $form;

		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/html');
			$this->response->body = $content;

			return false;
		} else {
			$content->backUrl = $backUrl;
			$content->showed = 'images';
			$content->section = $section;
			$content->modSettings = $moduleSettings;
			$content->item = $item;
		}
	}

//	public function actionAddHotspot($section, $imageId)
//	{
//		$section = strtolower($section);
//		$this->checkSection($section);
//		$image = $this->model->getImage($imageId, $section);
//		if (!$imageId) {
//			throw new InvalidArgumentException('Invalid image ID.');
//		} else {
//			$this->actionEditHotspot($section, null, $imageId);
//		}
//	}
//
//	public function actionEditHotspot($section, $id, $imageId = null)
//	{
//		$section = strtolower($section);
//		$this->checkSection($section);
//		$this->template->content = new View('admin/form');
//		if ($id) {
//			$hotspot = $this->model->getHotspot($id, $section);
//			$imageId = $hotspot['imageId'];
//		}
//		$image = $this->model->getImage($imageId, $section);
//		$item = $this->model->getItem($image['itemId'], $section);
//		$moduleSettings = $this->moduleSettings[$section];
//		$level = ($item['parentId'] > 0 ? (string)$this->categories[$item['parentId']]['level'] : '0');
//		if (isset($moduleSettings['levelRules'][$level])) {
//			$moduleSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level]);
//		}
//		$type = ($item['isCategory'] ? 'categories' : 'items');
//		$fields = isset($moduleSettings[$type]['hotspots']['fields']) ? $moduleSettings[$type]['hotspots']['fields'] : array();
//		$this->template->content->section = $section;
//		$this->template->content->title = 'Add new hotspot';
//		$form = new Form('hotspot');
//		if ((in_array('name', $fields) || isset($fields['name']))) {
//			$form->text('name', (isset($fields['name'])? $fields['name'] : 'Name'))
//				->attribute('size', 40)
//				->rule('maxLength', 128);
//		}
//		$form->file('file', 'Image file')->attribute('size', 35)->rule('extension', array('jpg', 'jpeg', 'png', 'gif'));
//		for ($cnt = 1; $cnt <= 1; $cnt++) {
//			if (in_array('text' . $cnt, $fields) || isset($fields['text' . $cnt])) {
//				$form->textarea('text' . $cnt, (isset($fields['text' . $cnt])? $fields['text' . $cnt] : 'Description'))
//					->attribute('cols', 100)->attribute('rows', 15);
//			}
//		}
//		$form->submit('submit', 'Save');
//		if (isset($moduleSettings[$type]['hotspots']['required'])) {
//			foreach ($moduleSettings[$type]['hotspots']['required'] as $field) {
//				if (isset($form->elements[$field])) {
//					$form->elements[$field]->rule('required');
//					$form->elements[$field]->attribute('required', 'required');
//				}
//			}
//		}
//		if ($id) {
//			$this->template->content->title = 'Edit hotspot';
//			$form->loadValues($hotspot);
//		}
//		if (Request::$method == 'POST') {
//			if ($form->validate()) {
//				if ($id) {
//					$token = $hotspot['token'];
//				} else {
//					$cnt = 0;
//					Model_Table::instance('directoryHotspots')->keys['string'] = 'token';
//					while (true === Model_Table::instance('directoryHotspots')->exists(($token = Text::random('alphanuml', 6))) && $cnt < 3) {
//						$cnt++;
//					}
//				}
//				if (isset($form->elements['file']->files[0])) {
//					$files = $form->elements['file']->files;
//					$file = $files[0];
//					$umask = umask(0);
//					$tmpDir = $this->pathHotspots . '_tmp/';
//					if (!file_exists($tmpDir)) {
//						mkdir($tmpDir, 0777, true);
//					}
//					$tmpFilename = $tmpDir . 'tmp';
//					if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
//						umask($umask);
//						throw new Exception('File %s cannot be moved.');
//					} else {
//						$this->resizeHotspot($token, $section, $item['isCategory']);
//						if ((isset($moduleSettings[$type]['hotspots']['keepOriginal']) ? $moduleSettings[$type]['hotspots']['keepOriginal'] : false)) {
//							$fileName = $this->hotspotPath($token, 'original', $section);
//							if (!file_exists($hotspotDir = $this->hotspotDir($token, 'original', $section))) {
//								mkdir($hotspotDir, 0777, true);
//							}
//							copy($this->pathHotspots . '_tmp/tmp', $fileName);
//						}
//					}
//					list ($imgWidth, $imgHeight, $type, $attr) = getimagesize($tmpFilename);
//					$resolution = $imgWidth . 'x' . $imgHeight;
//				}
//				if ($id) {
//					$values = $form->getModified();
//					$values['token'] = $token;
//					$values['imageId'] = $imageId;
//					$values['section'] = $section;
//					if (isset($resolution)) {
//						$values['resolution'] = $resolution;
//					}
//					Model_Table::instance('directoryHotspots')->update($values, $id);
//				} else {
//					$values = $form->getValues();
//					$values['token'] = $token;
//					$values['imageId'] = $imageId;
//					$values['section'] = $section;
//					if (isset($resolution)) {
//						$values['resolution'] = $resolution;
//					}
//					Model_Table::instance('directoryHotspots')->insert($values);
//				}
//				$this->response->redirect(Request::$controller . 'hotspots/' . $section  . '/' . $image['id'] . '/');
//			}
//		}
//		$this->template->content->backUrl = Request::$controller . 'hotspots/' . $section  . '/' . $image['id'] . '/';
//		if (isset($formError)) {
//		    $form = $formError;
//		}
//		$this->template->content->form = $form;
//	}
//
//
//	public function actionDefineHotspot($section, $id)
//	{
//		$section = strtolower($section);
//		$this->checkSection($section);
//		$this->addScript('/scripts/swfobject.js');
//		$content = $this->template->content = new View('admin/hotspotDefine');
//		if ($id) {
//			$hotspot = $this->model->getHotspot($id, $section);
//			$imageId = $hotspot['imageId'];
//		}
//		$content->image = $image = $this->model->getImage($imageId, $section);
//		$content->item = $item = $this->model->getItem($image['itemId'], $section);
//		$content->currentId = $id;
//		$moduleSettings = $this->moduleSettings[$section];
//		$level = ($item['parentId'] > 0 ? (string)$this->categories[$item['parentId']]['level'] : '0');
//		if (isset($moduleSettings['levelRules'][$level])) {
//			$moduleSettings = array_replace_recursive($moduleSettings, $moduleSettings['levelRules'][$level]);
//		}
//		$type = ($item['isCategory'] ? 'categories' : 'items');
//		$form = new Form('points');
//		$form->hidden('points');
//		if (Request::$method == 'POST') {
//			if ($form->validate()) {
//				$values = $form->getValues();
//				Model_Table::instance('directoryHotspots')->update(array('points' => $values['points']), (int)$id);
//				$this->response->redirect(Request::$controller . 'hotspots/' . $section  . '/' . $image['id'] . '/');
//			}
//		}
//		$this->template->content->backUrl = Request::$controller . 'hotspots/' . $section  . '/' . $image['id'] . '/';
//		$this->template->content->form = $form;
//	}

	public function actionAddVideo($section, $itemId) {
		$section = strtolower($section);
		$this->checkSection($section);

		$this->actionEditVideo($section, null, $itemId);
	}

	public function actionEditVideo($section, $id, $itemId = null) {
		$section = strtolower($section);
		$this->checkSection($section);

		if (Request::get('modal', false)) {
			$this->view = new View('admin/template-iframe');
			$this->view->content = $content = new View('admin/parts/simple-form');
		} else {
			$this->view->content = $content = new View('admin/form');
		}

		if ($id) {
			$video = Model_Directoryvideo::getById($id, $section);
			$itemId = $video->itemId;
		}

		$item = Model_Directoryitem::getItem($itemId, $section);
		$type = $item->isCategory ? 'categories' : 'items';

		$this->getPermissions($section, $item, false);
		$this->setCategoriesCrumbs($section, $item->parentId, true);

		if ((!$id && !$this->itemData['permissions']['videos']['add']) || ($id && !$this->itemData['permissions']['videos']['edit'])) {
			throw new ForbiddenException('Maximum files');
		}

		$moduleSettings = $this->itemModuleSettings;
		$backUrl = Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=videos';

		$itemLabel = isset($moduleSettings[$type]['title']) ? $moduleSettings[$type]['title'] : 'name';
		$itemTitle = $item->$itemLabel ? $item->$itemLabel : ($type == 'items' ? 'Item' : 'Category');

		$this->view
			->crumbs($itemTitle)
			->crumbs('Videos list', $backUrl);
		$this->view->active = $section;

		if ($id) {
			$this->view->crumbs('Edit video');
		} else {
			$this->view->crumbs('Add new video');
		}

		$form = $this->initForm($moduleSettings['videos'], 'videos');
		$this->addPlugins($moduleSettings['videos']);

		$previewSize = (isset($moduleSettings['videos']['preview']) && $moduleSettings['videos']['preview']) ? $moduleSettings['videos']['preview'] : false;

		if (is_object($form)) {
			if ($id) {
				$video->url = Model_Directoryvideo::url($video);
				if ($previewSize) {
					$video->fileDefault = $video->imgCustom ? 0 : 1;
				}

				$form->loadValues($video->getValues());

				if ($form->elements['type']->value) {
					$form->elements['url']->attribute('disabled', false);
				}
			}

			if (Request::$method == 'POST') {
				if ($form->validate()) {
					$values = $form->getValues();

					if ($id) {
						$alias = $video->alias;
					} else {
						$cnt = 0;
						while (true === Model_Directoryvideo::exists('alias', ($alias = Text::random('alphanuml', 6))) && $cnt < 3) {
							$cnt++;
						}
					}

					switch ($values['type']) {
						case VIDEO_TYPE_YOUTUBE:
							preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $values['url'], $match);
							$videoId = $match[1];
							break;
						case VIDEO_TYPE_VIMEO:
							preg_match('/\/(\d+).*/', $values['url'], $match);
							$videoId = $match[1];
							break;
						default:
							throw new Exception('Video type not found');
							break;
					}

					$values['videoId'] = $videoId;

					$error = false;

					if ($previewSize) {
						$dir = Model_Directoryvideo::dir($alias, $section);

						if (!file_exists($dir)) {
							mkdir($dir, 0777, true);
						}

						if ($values['fileDefault']) {
							$originalFile = $dir . 'original.jpg';
							$umask = umask(0);
							switch ($values['type']) {
								case VIDEO_TYPE_YOUTUBE:
									if (!copy("http://img.youtube.com/vi/$videoId/0.jpg", $originalFile)) {
										umask($umask);
										$error = 'Can`t upload file from youtube server';
									}
									break;
								case VIDEO_TYPE_VIMEO:
									try {
										$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$videoId.php"));
									} catch (Exception $e) {
										$hash = false;
									}

									if (empty($hash) || !isset($hash[0]['thumbnail_large']) || !copy($hash[0]['thumbnail_large'], $originalFile)) {
										umask($umask);
										$error = 'Can`t upload file from server';
									}
									break;
								default:
									break;
							}
							if (!$error)
								Image::setCopyright($originalFile);
							$values['imgCustom'] = 0;
						} else {
							$files = $form->elements['file']->files;
							$file = $files[0];
							$umask = umask(0);

							$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
							$originalFile = $dir . 'original.' . $ext;
							if (false === move_uploaded_file($file['tmp_name'], $originalFile)) {
								umask($umask);
								$error = 'File can`t be moved';
							}
							if (!$error)
								Image::setCopyright($originalFile);
							$values['imgCustom'] = 1;
						}

						if (!$error) {
							$values['ext'] = pathinfo($originalFile, PATHINFO_EXTENSION);
							$values['filesize'] = filesize($originalFile);
							$size = $this->sizes[$previewSize];
							$ext = (isset($size['options']['outputExtension']) ? $size['options']['outputExtension'] : 'jpg');
							$fileName = $dir . $previewSize . '.' . $ext;

							Image::Resize($originalFile, $size['size'], array_merge($size['options'], array('filename' => $fileName)));
						}
					}

					if (!$error) {
						unset($values['url'], $values['fileDefault']);

						if ($id) {
							$values['alias'] = $alias;
							$values['itemId'] = $itemId;
							$values['section'] = $section;

							Model_Directoryvideo::update($values, $id);

							$this->message('Video has been updated');
						} else {
							$values['alias'] = $alias;
							$values['itemId'] = $itemId;
							$values['section'] = $section;

							Model_Directoryvideo::create($values);

							$this->message('Video has been created');
						}

						if (Request::get('modal', false)) {
							$this->view->content = new View('admin/parts/success-upload');
						} else {
							$this->response->redirect($backUrl);
						}
					} else {
						$form->elements['url']->error($error);
					}
				}
			}
		}

		$content->form = $form;

		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/html');
			$this->response->body = $content;

			return false;
		} else {
			$content->backUrl = $backUrl;
			$content->showed = 'videos';
			$content->section = $section;
			$content->modSettings = $moduleSettings;
			$content->item = $item;
		}
	}

	public function actionAddAttachment($section, $itemId) {
		$section = strtolower($section);
		$this->checkSection($section);

		$this->actionEditAttachment($section, null, $itemId);
	}

	public function actionEditAttachment($section, $id, $itemId = null) {
		$section = strtolower($section);
		$this->checkSection($section);

		if (Request::get('modal', false)) {
			$this->view = new View('admin/template-iframe');
			$this->view->content = $content = new View('admin/parts/simple-form');
		} else {
			$this->view->content = $content = new View('admin/form');
		}

		$content->type = "attachments";
		$content->section = $section;
		$content->itemId = $itemId;

		if ($id) {
			$attachment = Model_Directoryattachment::getById($id, $section);
			$itemId = $attachment->itemId;
		}

		$item = Model_Directoryitem::getItem($itemId, $section);
		$type = $item->isCategory ? 'categories' : 'items';

		$this->getPermissions($section, $item, false);
		$this->setCategoriesCrumbs($section, $item->parentId, true);

		if ((!$id && !$this->itemData['permissions']['attachments']['add']) || ($id && !$this->itemData['permissions']['attachments']['edit'])) {
			throw new ForbiddenException('Maximum files');
		}

		$moduleSettings = $this->itemModuleSettings;
		$backUrl = Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=attachments';

		$itemLabel = isset($moduleSettings[$type]['title']) ? $moduleSettings[$type]['title'] : 'name';
		$itemTitle = $item->$itemLabel ? $item->$itemLabel : ($type == 'items' ? 'Item' : 'Category');

		$this->view
			->crumbs($itemTitle)
			->crumbs('Attachments list', $backUrl);
		$this->view->active = $section;

		$form = $this->initForm($moduleSettings['attachments'], 'attachments');
		$this->addPlugins($moduleSettings['attachments']);

		if ($id) {
			$this->view->crumbs('Edit attachment');
		} else {
			$this->view->crumbs('Add new attachment');
			$form->elements['file']->attribute('multiple');
			$content->multiUpload = true;
			$form->elements['submit']->attribute('onClick', 'return uploader.submitHeadForm();');
			$content->maxSize = $moduleSettings['attachments']['maxSize'];
			$content->ext = $moduleSettings['attachments']['extension'];
		}



		if (is_object($form)) {
			if ($id) {
				$form->loadValues($attachment->getValues());
			} else {
				$form->elements['file']->rule('required');
				$form->elements['file']->attribute('required', 'required');
			}
			if (Request::$method == 'POST') {
				if ($form->validate()) {
					if ($id) {
						$alias = $attachment->alias;
					} else {
						$cnt = 0;
						while (true === Model_Directoryattachment::exists('alias', ($alias = Text::random('alphanuml', 6))) && $cnt < 3) {
							$cnt++;
						}
					}
					if (isset($form->elements['file']->files[0])) {
						$files = $form->elements['file']->files;
						$file = $files[0];
						$umask = umask(0);
						$dir = Model_Directoryattachment::dir($alias, $section);
						if (!file_exists($dir)) {
							mkdir($dir, 0777, true);
						}
						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
						$tmpFilename = $dir . $alias . '.' . $ext;
						if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
							umask($umask);
							throw new Exception('File %s cannot be moved.');
						}
						$filename = $file['name'];
						$filesize = filesize($tmpFilename);
					}
					if ($id) {
						$values = $form->getModified();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						if (isset($filesize)) {
							$values['filesize'] = $filesize;
						}
						if (isset($filename)) {
							$values['filename'] = $filename;
						}
						if (isset($ext)) {
							$values['ext'] = $ext;
						}
						Model_Directoryattachment::update($values, $id);

						$this->message('Attachment has been updated');
					} else {
						$values = $form->getValues();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						$values['filesize'] = $filesize;
						$values['filename'] = $filename;
						$values['ext'] = $ext;
						Model_Directoryattachment::create($values);

						$this->message('Attachment has been created');
					}

					if (Request::get('modal', false)) {
						$this->view->content = new View('admin/parts/success-upload');
					} else {
						$this->response->redirect($backUrl);
					}
				}
			}
		}

		$content->form = $form;

		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/html');
			$this->response->body = $content;

			return false;
		} else {
			$content->backUrl = $backUrl;
			$content->showed = 'attachments';
			$content->section = $section;
			$content->modSettings = $moduleSettings;
			$content->item = $item;
		}
	}

	public function actionAddAudio($section, $itemId) {
		$section = strtolower($section);
		$this->checkSection($section);

		$this->actionEditAudio($section, null, $itemId);
	}

	public function actionEditAudio($section, $id, $itemId = null) {
		$section = strtolower($section);
		$this->checkSection($section);

		if (Request::get('modal', false)) {
			$this->view = new View('admin/template-iframe');
			$this->view->content = $content = new View('admin/parts/simple-form');
		} else {
			$this->view->content = $content = new View('admin/form');
		}

		$content->type = "audios";
		$content->section = $section;
		$content->itemId = $itemId;

		if ($id) {
			$audio = Model_Directoryaudio::getById($id, $section);
			$itemId = $audio->itemId;
		}

		$item = Model_Directoryitem::getItem($itemId, $section);
		$type = $item->isCategory ? 'categories' : 'items';

		$this->getPermissions($section, $item, false);
		$this->setCategoriesCrumbs($section, $item->parentId, true);

		if ((!$id && !$this->itemData['permissions']['audios']['add']) || ($id && !$this->itemData['permissions']['audios']['edit'])) {
			throw new ForbiddenException('Maximum files');
		}

		$moduleSettings = $this->itemModuleSettings;
		$backUrl = Request::$controller . 'item/' . $section . '/' . $item->id . '/?edit=audios';

		$itemLabel = isset($moduleSettings[$type]['title']) ? $moduleSettings[$type]['title'] : 'name';
		$itemTitle = $item->$itemLabel ? $item->$itemLabel : ($type == 'items' ? 'Item' : 'Category');

		$this->view
			->crumbs($itemTitle)
			->crumbs('Audios list', $backUrl);
		$this->view->active = $section;

		$form = $this->initForm($moduleSettings['audios'], 'audios');
		$this->addPlugins($moduleSettings['audios']);

		if ($id) {
			$this->view->crumbs('Edit audio');
		} else {
			$this->view->crumbs('Add new audio');
			$form->elements['file']->attribute('multiple');
			$content->multiUpload = true;
			$form->elements['submit']->attribute('onClick', 'return uploader.submitHeadForm();');
			$content->maxSize = $moduleSettings['audios']['maxSize'];
			$content->ext = $moduleSettings['audios']['extension'];
		}

		if (is_object($form)) {
			if ($id) {
				$form->loadValues($audio->getValues());
			} else {
				$form->elements['file']->rule('required');
				$form->elements['file']->attribute('required', 'required');
			}
			if (Request::$method == 'POST') {
				if ($form->validate()) {
					if ($id) {
						$alias = $audio->alias;
					} else {
						$cnt = 0;
						while (true === Model_Directoryaudio::exists('alias', ($alias = Text::random('alphanuml', 6))) && $cnt < 7) {
							$cnt++;
						}
					}

					if (isset($form->elements['file']->files[0])) {
						$files = $form->elements['file']->files;
						$file = $files[0];
						$umask = umask(0);
						$dir = Model_Directoryaudio::dir($alias, $section);
						if (!file_exists($dir)) {
							mkdir($dir, 0777, true);
						}

						$ext = pathinfo($file['name'], PATHINFO_EXTENSION);

						$tmpFilename = $dir . $alias . '.' . $ext;
						if (false === move_uploaded_file($file['tmp_name'], $tmpFilename)) {
							umask($umask);
							throw new Exception('File %s cannot be moved.');
						}
						$filename = $file['name'];
						$filesize = filesize($tmpFilename);
					}
					if ($id) {
						$values = $form->getModified();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						if (isset($filesize)) {
							$values['filesize'] = $filesize;
						}
						if (isset($ext)) {
							$values['ext'] = $ext;
						}
						if (isset($filename)) {
							$values['filename'] = $filename;
						}

						Model_Directoryaudio::update($values, $id);

						$this->message('Audio has been updated');
					} else {
						$values = $form->getValues();
						$values['alias'] = $alias;
						$values['itemId'] = $itemId;
						$values['section'] = $section;
						$values['filesize'] = $filesize;
						$values['filename'] = $filename;
						$values['ext'] = $ext;

						Model_Directoryaudio::create($values);

						$this->message('Audio has been created');
					}

					if (Request::get('modal', false)) {
						$this->view->content = new View('admin/parts/success-upload');
					} else {
						$this->response->redirect($backUrl);
					}
				}
			}
		}

		$content->form = $form;

		if (Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/html');
			$this->response->body = $content;

			return false;
		} else {
			$content->backUrl = $backUrl;
			$content->showed = 'audios';
			$content->section = $section;
			$content->modSettings = $moduleSettings;
			$content->item = $item;
		}
	}

	private function resizeImage($alias, $section, $isCategory = false) {
		$moduleSettings = $this->moduleSettings[$section];
		$type = ($isCategory ? 'categories' : 'items');
		$sizes = (isset($moduleSettings[$type]['images']['sizes']) ? $moduleSettings[$type]['images']['sizes'] : array());
		if (!in_array('tiny', $sizes)) {
			$sizes[count($sizes)] = 'tiny';
		}
		if (!in_array('fullsize', $sizes)) {
			$sizes[count($sizes)] = 'fullsize';
		}
		foreach ($this->sizes as $size => $options) {
			$extension = (isset($options['options']['outputExtension']) ? $options['options']['outputExtension'] : 'jpg');
			if (count($sizes) == 0 || in_array($size, $sizes)) {
				$dir = Model_Directoryimage::dir($alias, $size, $section);
				$fileName = $dir . $alias . '.' . $extension;
				if (!file_exists($dir)) {
					mkdir($dir, 0777, true);
				}
				Image::Resize(Model_Directoryimage::$path . '_tmp/tmp', $options['size'], array_merge($options['options'], array('filename' => $fileName)));
			}
		}
	}

//	private function resizeHotspot($alias, $section, $isCategory = false)
//	{
//		$moduleSettings = $this->moduleSettings[$section];
//		$type = ($isCategory ? 'categories' : 'items');
//		$sizes = (isset($moduleSettings[$type]['hotspots']['sizes']) ? $moduleSettings[$type]['hotspots']['sizes'] : array());
//		if (!in_array('tiny', $sizes)) {
//			$sizes[count($sizes)] = 'tiny';
//		}
//		foreach ($this->sizes as $size => $options) {
//			$extension = (isset($options['options']['outputExtension']) ? $options['options']['outputExtension'] : 'jpg');
//			if (count($sizes) == 0 || in_array($size, $sizes)) {
//				$fileName = $this->hotspotPath($alias, $size, $section, $extension);
//				if (!file_exists($hotspotDir = $this->hotspotDir($alias, $size, $section))) {
//					mkdir($hotspotDir, 0777, true);
//				}
//				Image::Resize($this->pathHotspots . '_tmp/tmp', $options['size'], array_merge($options['options'], array('filename' => $fileName)));
//			}
//		}
//	}

	public function actionRemoveCategory($section, $categoryId, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$this->getPermissions($section, $categoryId, false);

		$category = $this->categories[$categoryId];
		$childrens = Model_Directoryitem::getChildrenCategories($categoryId, $section, 'id', 'DESC', false);

		if (count($childrens['data'])) {
			foreach ($childrens['data'] as $cat) {
				$this->actionRemoveCategory($section, $cat->id, true);
			}
		}

		$items = Model_Directoryitem::getCategoryItems($category->id, $section, 'id', 'DESC', false);
		if (count($items['data'])) {
			foreach ($items['data'] as $item) {
				$this->actionRemoveItem($section, $item->id, true);
			}
		}

		Model_Directoryitem::remove($category->id);

		if (!$internal) {
			$this->message('Category has been removed');
			$this->response->redirect(Request::$controller . 'browse/' . $section . '/' . $category->parentId . '/');
		}
	}

	public function actionRemoveItem($section, $id, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryitem::getItem($id, $section);
		$itemData = $this->getPermissions($section, $item, false);

		$stat = $itemData['stat']['count'];
		if (isset($stat['images']) && $stat['images']) {
			Model_Directoryimage::removeByParent($item, $this->sizes);
		}
		if (isset($stat['attachments']) && $stat['attachments']) {
			Model_Directoryattachment::removeByParent($item);
		}
		if (isset($stat['audios']) && $stat['audios']) {
			Model_Directoryaudio::removeByParent($item);
		}
		if (isset($stat['videos']) && $stat['videos']) {
			$modSettings = $this->itemModuleSettings;
			$size = isset($modSettings['videos']['preview']) ? $modSettings['videos']['preview'] : false;
			$options = $size ? $this->sizes[$modSettings['videos']['preview']] : false;
			Model_Directoryvideo::removeByParent($item, $size, $options);
		}

		Model_Directoryitem::remove($item->id);

		if (!$internal) {
			$this->message('Item has been removed');
			$this->response->redirect(Request::$controller . 'browse/' . $section . '/' . ($item->parentId ? $item->parentId : 0) . '/?show=items');
		}
	}

	public function actionRemoveAttachment($section, $id, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryattachment::getById($id, $section);
		if (empty($item)) {
			throw new NotFoundException('Attachment not found');
		}
		$parent = Model_Directoryitem::getItem($item->itemId, $section);
		if (empty($parent)) {
			throw new NotFoundException('Attachment item not found');
		}
		$this->getPermissions($section, $parent, false);

		if (isset($this->itemData['permissions']['attachments']) && !$this->itemData['permissions']['attachments']['delete']) {
			throw new NotFoundException('Permissions denied');
		}

		Model_Directoryattachment::removeItem($item);

		if (!$internal) {
			$this->message('Attachment has been removed');
			$this->response->redirect(Request::$controller . 'item/' . $section . '/' . $item->itemId . '/?edit=attachments');
		}
	}

	public function actionRemoveAudio($section, $id, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryaudio::getById($id, $section);
		if (empty($item)) {
			throw new NotFoundException('Audio not found');
		}
		$parent = Model_Directoryitem::getItem($item->itemId, $section);
		if (empty($parent)) {
			throw new NotFoundException('Audio item not found');
		}
		$this->getPermissions($section, $parent, false);

		if (isset($this->itemData['permissions']['audios']) && !$this->itemData['permissions']['audios']['delete']) {
			throw new NotFoundException('Permissions denied');
		}

		Model_Directoryaudio::removeItem($item);

		if (!$internal) {
			$this->message('Audio has been removed');
			$this->response->redirect(Request::$controller . 'item/' . $section . '/' . $item->itemId . '/?edit=audios');
		}
	}

	public function actionRemoveImage($section, $id, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryimage::getById($id, $section);
		if (empty($item)) {
			throw new NotFoundException('Image not found');
		}
		$parent = Model_Directoryitem::getItem($item->itemId, $section);
		if (empty($parent)) {
			throw new NotFoundException('Image item not found');
		}
		$this->getPermissions($section, $parent, false);

		if (isset($this->itemData['permissions']['images']) && !$this->itemData['permissions']['images']['delete']) {
			throw new NotFoundException('Permissions denied');
		}

		Model_Directoryimage::removeItem($item, $this->sizes);

		if (!$internal) {
			$this->message('Image has been removed');
			$this->response->redirect(Request::$controller . 'item/' . $section . '/' . $item->itemId . '/?edit=images');
		}
	}

//	public function actionRemoveHotspot($section, $hotspotId, $internal = false)
//	{
//		$moduleSettings = $this->moduleSettings[$section];
//		$section = strtolower($section);
//		$this->checkSection($section);
//		$hotspot = $this->model->getHotspot($hotspotId, $section);
//		$sizes = $this->sizes;
//		$sizes['original'] = array();
//		foreach ($sizes as $size => $options) {
//			$extension = (isset($options['options']['outputExtension']) ? $options['options']['outputExtension'] : 'jpg');
//			$fileName = $this->hotspotPath($hotspot['token'], $size, $section, $extension);
//			if (file_exists($fileName)) {
//				unlink($fileName);
//				if (FileSystem::isDirEmpty($hotspotDir = $this->hotspotDir($hotspot['token'], $size, $section))) {
//					rmdir($hotspotDir);
//				}
//			}
//		}
//		Model_Table::instance('directoryHotspots')->delete((int)$hotspot['id']);
//		if (!$internal) {
//			$this->response->redirect(Request::$controller . 'hotspots/' . $section  . '/' . $hotspot['imageId'] . '/');
//		}
//	}

	public function actionRemoveVideo($section, $videoId, $internal = false) {
		$section = strtolower($section);
		$this->checkSection($section);

		$item = Model_Directoryvideo::getById($videoId, $section);
		if (empty($item)) {
			throw new NotFoundException('Image not found');
		}
		$parent = Model_Directoryitem::getItem($item->itemId, $section);
		if (empty($parent)) {
			throw new NotFoundException('Video item not found');
		}
		$this->getPermissions($section, $parent, false);

		if (isset($this->itemData['permissions']['videos']) && !$this->itemData['permissions']['videos']['delete']) {
			throw new NotFoundException('Permissions denied');
		}

		$modSettings = $this->itemModuleSettings;
		$size = isset($modSettings['videos']['preview']) ? $modSettings['videos']['preview'] : false;
		$options = $size ? $this->sizes[$modSettings['videos']['preview']] : false;

		Model_Directoryvideo::removeItem($item, $size, $options);

		if (!$internal) {
			$this->message('Video has been removed');
			$this->response->redirect(Request::$controller . 'item/' . $section . '/' . $item->itemId . '/?edit=videos');
		}
	}

}

?>