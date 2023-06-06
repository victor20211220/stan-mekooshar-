<?php

/**
 *
 * UaModna
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://uamodna.com
 *
 */
class Uploader_Files_Controller extends Controller_Admin_Template {

	protected $resource = 'profile';

	public function before() {
		parent::before();
	}

	public function actionGetGalleryId() {
		if (Request::$isAjax) {
			$page_id = (isset($_POST['page_id']) && $_POST['page_id'] != 0) ? $_POST['page_id'] : NULL;
			$gallery = Model_Gallery::create(array('page_id' => $page_id));
			$this->response->body = json_encode(array('id' => $gallery->id));
			$_SESSION['gallery_new'][] = $gallery->id;
			return false;
		}
	}

	public function actionRemoveGallery($itemId) {
		if (Request::isPost() && Request::isAjax()) {
			$gallery_id = Request::get('gallery_id', false);
			$_SESSION['gallery_remove'][$gallery_id] = true;
//			if ($gallery_id) {
//				Model_Galleryitems::removeGalleryItems($gallery_id);
//			}
			$this->response->body = json_encode(array(
				'status' => 'success'
			));
			return;
		}


//		if (Request::isPost() && Request::isAjax()) {
//			$item = new Model_Pages($itemId);
//			$group = Request::get('gallery_id', false);
//
//			$_SESSION['gallery_remove'][] = array('itemId' => $itemId, 'group' => $group);
//
////
////			$content = Model_Pages::replaceGallery($item->text);
////			Model_Pages::update(array('text' => $content), array('id = ?', $itemId));
//
//			$this->response->body = json_encode(array(
//				'status' => 'success'
//			));
//			return;
//		}
	}

	public function actionSortImages($gallery_id) {
		if (Request::$isAjax && Request::isPost()) {
			$post = Request::get('images', false);

			if ($post) {
//				$i = 0;
//				foreach ($post as $key => $v) {
//					Model_Galleryitems::update(array('position' => ++$i), array('file_id = ? AND gallery_id = ?', $v, $gallery_id));
//				}
				$_SESSION['gallery_items_position'][$gallery_id] = $post;
			}

			$this->response->body = json_encode(array(
				'status' => 'success'
			));
			return;
		}

	}

	public function actionUpload($type, $parent_id = 0) {
		if (Request::$isAjax or (Request::$method == 'POST' and isset($_FILES['qqfile']))) {
			$this->autoRender = false;
			$gallery_id = Request::get('gallery_id', false);
			$page_id = Request::get('page_id', false);

			if ($page_id) {
				$parent_id = $page_id;
			}

			if ($gallery_id) {
				$photoSettings = Model_Gallery::photoSettings($gallery_id);
				$message = Model_Files::upload($type, $gallery_id, $this->user, $photoSettings);
			} else {
				$message = Model_Files::upload($type, $parent_id, $this->user);
			}

			if ($gallery_id)
				Model_Galleryitems::create(array('gallery_id' => $gallery_id, 'file_id' => $message['id']));

			if ($message['success'] && $parent_id) {
				switch ($type) {
					default:
						$url = false;
						break;
				}
				$message['setMainUrl'] = $url;
			}

			$this->response->body = json_encode($message);
			return false;
		}

	}

	public static function realUrl(Model_File $file, $sizeName = false, $ext = false)
	{
		$path = Model_File::getPathByType($file->type);

		if ($file->isImage) {
			$url = Filesystem::compilePath($path . '/image', $file->token);
			$url .= ($sizeName ? $sizeName : 'crop') . '.' . ($ext ? $ext : $file->ext);
		} else {
			$url = Filesystem::compilePath($path . '/files', $file->token);
			$url .= $file->name;
		}

		return $url;
	}

	public static function getPathByType($type)
	{
		$config = System::$global->config;

		$type = $config->fileTypes->$type;

		$path = $config->store->$type;

		return $path;
	}

	public function actionDownloadAlbum($gallery_id)
	{
		$urls     = array();
		$type     = FILE_PHOTOS;
		$config   = System::$global->config;
		$realType = $config->fileTypes->$type;
		$sizes    = $config->imageThumbs->$realType->arrayize();

		$neededSizes = array();

		foreach ($sizes as $size => $sizearray) {
			if (array_key_exists('for_album', $sizearray) && array_key_exists('priority', $sizearray) && $sizearray['for_album']) {
				$neededSizes[$sizearray['priority']] = $size;
			}
		}

		krsort($neededSizes);

		$items = Model_Content_Galleryitems::getAllGalleryItems($gallery_id);
		$gallery = new Model_Content_Gallery((int)$gallery_id);
		$item = Model_Content_Article::getItem($gallery->page_id,$this->lang);


		foreach ($items as $image) {

			$file = Model_UploadFiles::getByToken($image->token);
			$path = Model_UploadFiles::getPathByType($file->type);

			$files[] = $file;

			foreach ($neededSizes as $size) {
				$url = Filesystem::compilePath($path . '/image', $image->token);
				$url .= $size.'.' . $file->ext;

				if (file_exists($url))  $neededUrl = $url;
			}

			$urls[] = $neededUrl;
		}

		$folder = '/tmp/uamodna_tmp_zip_files_'.substr(md5(rand(0,1000)),0,10);
		exec('mkdir '.$folder);

		for($i=0; $i<count($urls); $i++) {
			$e = explode('.',$urls[$i]);
			exec('cp "'.$urls[$i].'" "'.$folder.'/'.($i+1).'.'.end($e).'"');
		}

		$zip = '/tmp/uamodna_tmp_zip_archive_'.substr(md5(rand(0,1000)),0,10).'.zip';
		exec("zip -9 -r -j ".$zip." ".$folder);
		exec('rm -rf '.$folder);

		$rFile = @fopen($zip, 'r');
		$rOutput = fopen('php://output', 'w');
		if ($rFile) {
			header('Content-Disposition: attachment; filename="' . ucfirst(Url::Translit($item->name)) . '.zip"');
			header("Accept-Ranges: bytes");
			header("Content-Length: " . filesize($zip));
			header('Content-Type: application/zip ; charset: UTF-8');
			stream_copy_to_stream($rFile, $rOutput);
			exit();
		} else {
			throw new NotFoundException('File not exist');
		}
	}

	public function actionClearZipAlbums()
	{
		// removing ziped albums created more then 6 hours ago
		exec('find /tmp -type f -name "*uamodna_tmp_zip_archive*" -cmin +360 -exec rm -rf {} \; 2>/dev/null');
	}

	public function actionDownload($token, $size = 'original')
	{
		$this->autoRender = false;

		$file = Model_UploadFiles::getByToken($token);

		$path = Model_UploadFiles::getPathByType($file->type);
		if ($file->isImage) {
			$url = Filesystem::compilePath($path . '/image', $token);
			$url .= $size.'.' . $file->ext;
		} else {
			$url = Filesystem::compilePath($path . '/files', $token);
			$url .= $file->name;
		}

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

	public function actionDetails($id, $type) {
		if (Request::$isAjax) {
			$this->autoRender = false;

			$file = new Model_UploadFiles($id);
			if ($file->type != $type) {
				throw new NotFoundExceptiond('File not found');
			}

			$message = array(
				'success' => true,
				'id' => $id,
				'token' => $file->token,
				'ext' => $file->ext,
				'url' => Model_UploadFiles::generateUrl($file->token, $file->ext, $type, $file->isImage, $file->name),
				'isImage' => $file->isImage,
				'name' => $file->name
			);

			$config = System::$global->config;
			$realType = $config->fileTypes->$type;

			if ($file->isImage && $config->imageThumbs->__isset($realType)) {
				$sizes = $config->imageThumbs->$realType->arrayize();

				if (count($sizes)) {
					foreach ($sizes as $key => $size) {
						$ext = isset($size['format']) ? $size['format'] : $file->ext;
						$message['url_' . $key] = Model_UploadFiles::generateUrl($file->token, $file->ext, $type, $file->isImage, false, $key);
					}
				}
			}

			$this->response->body = json_encode($message);
			return false;
		}
	}

	public function actionRemove($token) {
		if (Request::$isAjax) {
			$this->autoRender = false;

			$file = Model_UploadFiles::getByToken($token);
			if ($file->sender_id && $file->sender_id != $this->user->id) {
				$message = array(
					'succes' => false
				);
			} else {
				$fileId = $file->id;
				$status = $file->removeFile();

				$message = array(
					'succes' => $status,
					'id' => $fileId
				);
			}

			$this->response->body = json_encode($message);
			return false;
		}
	}

	public function actionRemoveById($id) {
		if (Request::$isAjax || Request::isRemote()) {
			$this->autoRender = false;

			$file = new Model_UploadFiles($id);

			if ($this->user->getType() != USER_TYPE_ADMIN && (($file->sender_id && $file->sender_id != $this->user->id))) {
				$message = array(
					'status' => false
				);
			} else {
				$fileId = $file->id;
				$status = $file->removeFile();

				if(isset($_SESSION['uploader-list'][$id])) {
					unset($_SESSION['uploader-list'][$id]);
				}

				$message = array(
					'status' => $status,
					'id' => $fileId,
				);
			}

			$this->response->body = json_encode($message);

			return false;
		}
	}

//  TODO! Not Secure for public action
//	public function actionCrop($imageId, $save = 0) {
//		if (Request::$isAjax) {
//			$this->autoRender = false;
//
//			$file = new Model_UploadFiles($imageId);
//
//			if ($save != 1) {
//				$fileSrc = Model_UploadFiles::generateUrl($file->token, $file->ext, $file->type, true, false, 'crop');
//				if (!is_file($fileSrc)) {
//					$fileSrc = Model_UploadFiles::generateUrl($file->token, $file->ext, $file->type, true, false);
//				}
//				$message = array(
//					'url' => $fileSrc,
//					'cropArea' => !empty($file->cropArea) ? unserialize($file->cropArea) : false,
//					'download' => Request::generateUri('files', 'download', $file->token),
//				);
//			} else {
//				$sizes = Request::get('sizes');
//
//				$cropAreaWidth = Request::get('cropAreaWidth');
//
//				$x = Request::get('x');
//				$y = Request::get('y');
//				$w = Request::get('w');
//
//				$file->cropArea = serialize(array(
//					'x' => $x,
//					'y' => $y,
//					'x2' => $x + $w,
//					'y2' => $y + $w
//				));
//				$file->save();
//
//				$status = false;
//
//				if (count($sizes)) {
//					$status = $file->cropThumb($x, $y, $w, $cropAreaWidth, $sizes);
//				}
//
//				$message = array(
//					'success' => $status ? true : false
//				);
//			}
//
//			$this->response->body = json_encode($message);
//			return false;
//		}
//	}

	/**
	 * Remove image from gallery
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function actionRemoveFromList($id) {
		if (Request::$isAjax) {
			$this->autoRender = false;

			$file = new Model_Files($id);

			//remove record from Content_Galleryitems table
//			Model_Galleryitems::remove(array('file_id = ?', $id));
			$_SESSION['gallery_remove_item'][$file->id] = true;

			$fileId = $file->id;
//			$status = $file->removeFile();

			$message = array(
				'succes' => true,
				'id' => $fileId
			);

			$this->response->body = json_encode($message);
			return false;
		}
		return;

//		if (Request::$isAjax) {
//			$this->autoRender = false;
//
//			$file = new Model_UploadFiles($id);
//			$_SESSION['gallery_remove_item'][$file->id] = true;
//
//			$fileId = $file->id;
////			$status = $file->removeFile();
//			$message = array(
//				'succes' => $status,
//				'id' => $fileId
//			);
//
//			$this->response->body = json_encode($message);
//			return false;
//		}
	}

	public function __RemoveImage($id) {
		$file = new Model_UploadFiles($id);

		//remove record from Content_Galleryitems table
		Model_Content_Galleryitems::remove(array('file_id = ?', $id));

		//remove record from content_images table with Image info
		Model_Content_Image::remove(array('file_id = ?', $id));

		$file->removeFile();
	}

	public function actionEditImage($fileId) {
//		$info = Model_Content_Image::getByFileId($fileId);
		$file = new Model_UploadFiles($fileId);
		$page = Model_Pages::getPageByFileid($file->id);

		$pageCategory = FALSE;
		if($page){
			$pageCategory = $page->category;
		}

		/* if ($file->adminId !== $this->cubeAdmin->id) {
		  throw new ForbiddenException('You are not allowed to edit this image info');
		  } */

		$formObj = new Form_Gallery_ImageInfo();

		switch($pageCategory) {
			case PAGE_CATEGORY_ABOUTUS:
			case PAGE_CATEGORY_HOME:
			case PAGE_CATEGORY_POLICY:
				$formObj->addMemberFiled();
				break;
		}

		if (!empty($file->info)) {
			$info = unserialize($file->info);
		} else {
			$info = array(
				'title' => '',
				'text' => '',
				'alternative' => ''
			);
		}
		$formObj->edit($info);

		$form = $formObj->form;
		$view = $this->view;

		if (Request::isPost()) {
			if ($form->validate()) {
				$values = $form->getValues();
//				dump($values, 1);
				$values = serialize($values);
				$file->info = $values;
				$file->save();

				$this->message('Saved');
//				if ($info) {
//					Model_Content_Image::setKey('file_id');
//					Model_Content_Image::update($values, $info->file_id);
//					$this->message(t('cube_image_info_changed'));
//				} else {
//					$values['file_id'] = $fileId;
//					Model_Content_Image::create($values);
//					$this->message(t('cube_image_info_added'));
//				}

				//$this->response->redirect(Request::$referrer);
				$this->response->body = json_encode(array(
					'status' => true
				));
				return;
			}
		}



//		if (Request::isAjax()) {

//			$this->autoRender = false;
//			$this->response->setHeader('Content-Type', 'text/json');
//			$this->response->body = json_encode(array(
//				'status' => false,
//				'content' => (string) $content
//				'content' => (string) new View('parts/popup', array('content' => $content))
//			));

		if(Request::get('modal', false)) {
			$this->view = new View('admin/template-iframe');
			$this->view->content = $content = new View('admin/parts/simple-form');
		} else {
			$this->view->content = $content = new View('admin/form');
		}

		$content->form = $form;
		$content->showed = 'images';


		$this->autoRender = false;
		$this->response->setHeader('Content-Type', 'text/html');
		$this->response->body = $content;

		$this->view->style('/css/autoform.css');
//		return false;

//			$this->response->body = $content;

//			return;
//		} else {
//			dump(11111,1);
//			$view->content = $content;
//		}

//		$view->content = $form;
//
//		$view->style('/css/autoform.css');
		//$this->getMessages();
	}

}
