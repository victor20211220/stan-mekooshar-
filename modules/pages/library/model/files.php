<?php

/**
 *
 * EvaProperty
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://myevasystem.com/
 *
 */
class Model_Files extends Model {

	/**
	 * @var string Table name.
	 */
	public static $table = 'files';

//	public function url()
//	{
//                $url = self::$settings['url'];
//                $values = array();
//                $values['%type%'] = 'attachment';
//                $values['%token%'] = $this->token;
//                $values['%filename%'] = $this->filename;
//
//                $src = strtr($url, $values);
//                return $src;
//	}
	public static function getItemByJobAlias($job_id, $alias)
	{
		$file = new self(array(
			'where' => array('token = ? AND parent_id = ?', $alias, $job_id)
		));

		$file->takeUrls();
		return $file;
	}

	public static function getByIds($ids)
	{
		$result = array();
		foreach (self::query(array(
			'where' => array('id IN(?)', $ids)
		)) as $file) {
			$file = self::instance($file);
			$file->takeUrls();

			$result[$file->id] = $file;
		}

		return $result;
	}

	public static function getByToken($token) {
		return new self(array(
			'where' => array('token = ?', $token)
		));
	}

	public static function getPathByType($type) {
		$config = System::$global->config;

		$type = $config->fileTypes->$type;

		$path = $config->store->$type;

		return $path;
	}

	public static function getLoaded($fileType) {
		$auth = Auth::getInstance()->getIdentity();
		$result = array();

		if (!empty($auth)) {
			foreach (self::query(array(
				'where' => array('sender_type = ? AND sender_id = ? AND parent_id = 0 AND type = ?', $auth->type, $auth->id, $fileType)
			)) as $file) {
				$file = self::instance($file);
				$file->takeUrls();

				$result[$file->id] = $file;
			}
		}

		return $result;
	}

	/**
	 * @param $parentId
	 * @param $type
	 * @param $withOrder
	 * @return array
	 */
	public static function getByParentId($parentId, $type, $withOrder = false) {
		$result = array();
		foreach (self::query(array(
			'where' => array('type = ? AND parent_id IN(?)', $type, $parentId),
			'order' => '`position` ASC, id DESC'
		)) as $file) {
			$file = self::instance($file);
			$file->takeUrls();

			if(!$withOrder){
				if (is_array($parentId)) {
					$result[$file->parent_id][$file->id] = $file;
				} else {
					$result[$file->id] = $file;
				}
			} else {
				$result[] = $file;
			}
		}

		return $result;
	}

	public static function getByType($type) {
		$result = array();
		foreach (self::query(array(
			'where' => array('type = ? ', $type)
		)) as $file) {
			$file = self::instance($file);
			$file->takeUrls();

			$file->real_url = self::realUrl($file);

			$result[$file->id] = $file;
		}

		return $result;
	}

	public static function getListByApplicantidJobid($user_id, $job_id)
	{
		return self::getList(array(
			'where' => array('sender_id = ? AND parent_id = ? AND type = ?', $user_id, $job_id, FILE_JOB_APPLY)
		), false);
	}

	public static function realUrl(Model_Files $file) {

		$path = Model_Files::getPathByType($file->type);

		if ($file->isImage) {
			$url = Filesystem::compilePath($path . '/image', $file->token);
			$url .= 'original.' . $file->ext;
		} else {
			$url = Filesystem::compilePath($path . '/files', $file->token);
			$url .= $file->name;
		}

		return $url;
	}

	public function takeUrls() {
		$config = System::$global->config;

		$this->url = self::generateUrl($this->token, $this->ext, $this->type, $this->isImage, $this->name);
		$urls = array('url' => $this->url);

		if ($this->isImage) {
			$type = $this->type;
			$realType = $config->fileTypes->$type;

			if ($config->imageThumbs->__isset($realType)) {
				$sizes = $config->imageThumbs->$realType->arrayize();
				if (count($sizes)) {
					foreach ($sizes as $key => $size) {
						$urlKey = 'url_' . $key;
						$this->$urlKey = self::generateUrl($this->token, (isset($size['format']) ? $size['format'] : $this->ext), $this->type, $this->isImage, $this->name, $key);
						$urls[$urlKey] = $this->$urlKey;
					}
				}
			}
		}
		return $urls;
	}

	public static function generateUrl($token, $ext, $type, $isImage, $name = false, $isThumb = false) {
		$path = self::getPathByType($type);

		if ($isImage) {
			$dest = Filesystem::compilePath($path . '/image', $token);
			Filesystem::buildPath($dest);
		} else {
			$dest = Filesystem::compilePath($path . '/files', $token);
			Filesystem::buildPath($dest);
		}

//		$path = '/' . self::getPathByType($type);
		if ($isImage) {
			if (!empty($token)) {
				$path = $dest . ($isThumb ? $isThumb : 'original') . '.' . $ext;
			} else {
				$path = self::getCap($type, $isThumb);
			}
		} else {
//			$path = $dest . $name;
			$path = $dest . $token . '.' . $ext;
		}


		return '/' . $path;
	}

	public static function generateRealUrl($token, $ext, $type, $isImage, $name = false, $isThumb = false) {

		$path = Model_Files::getPathByType($type);
		$url = '';
		if ($isImage) {
			$url = Filesystem::compilePath($path . '/image', $token);
			$url .= ($isThumb ? $isThumb : 'original') . '.' . $ext;
		} else {
			$url = Filesystem::compilePath($path . '/files', $token);
			if ($name) {
				$url .= $name;
			}
		}

		return $url;
	}

	public static function getCap($type, $thumb = false, $edit = false) {
		$thumb = $thumb ? $thumb : 'original';
		switch ($type) {
			case FILE_AVATAR :
				$cap = "/images/caps/avatar_{$thumb}.png";
				break;
			case CUBE_FILE_AVA :
				$cap = "/images/caps/cubeAva_{$thumb}.jpg";
				break;
			default:
				$cap = ((Request::$action == 'edit' || Request::$action == 'add' || $edit) && $thumb == 'preview') ? "/images/avatar_{$thumb}_edit.jpg" : "/images/avatar_{$thumb}.jpg";
		}

		return $cap;
	}

	public static function setParentId($ids, $parentId) {
		$whereId = '';
		if (is_array($ids)) {
			$whereId = array(0 => '');
			$first = true;
			foreach ($ids as $id) {
				if (!$first) {
					$whereId[0] .= ' OR ';
				} else {
					$first = false;
				}
				$whereId[0] .= 'id = ?';
				$whereId[] = $id;
			}
		} else {
			$whereId = $ids;
		}
		return self::update(array('parent_id' => $parentId), $whereId);
	}


	public static function upload($type, $parent_id = 0, $user = 0, $photoSettings = false, $group = null, $uploadFromUrl = false) {
	    $config = System::$global->config;
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$multiple = false;
		$imgExtensions = $config->files->allowed->imgExt->arrayize();
		$allowedExtensionsFull = array_merge($imgExtensions, $config->files->allowed->fileExt->arrayize());

		// max file size in bytes
		$sizeLimit = $config->files->allowed->maxSize;

		$realType = $config->fileTypes->$type;

		$allowedExtensions = array();
		if ($config->fileCondition->__isset($realType)) {
			if ($config->fileCondition->$realType->__isset('onlyImgs') && $config->fileCondition->$realType->onlyImgs) {
				$allowedExtensions = $imgExtensions;
			} elseif ($config->fileCondition->$realType->__isset('fileExt') && $config->fileCondition->$realType->fileExt) {
				$allowedExtensions = $config->fileCondition->$realType->fileExt->arrayize();
			}
			if ($config->fileCondition->$realType->__isset('maxSize')) {
				$sizeLimit = $config->fileCondition->$realType->maxSize;
			}
			if ($config->fileCondition->$realType->__isset('multiple')) {
				$multiple = $config->fileCondition->$realType->multiple;
			}
		}

		if(empty($allowedExtensions)) {
			$allowedExtensions = $allowedExtensionsFull;
		}

		$tmpDir = $config->store->temp . '/';
		if (!file_exists($tmpDir)) {
			mkdir($tmpDir, 0777, true);
		}
		$cnt = 0;
		while (true === Model_Files::exists('token', ($token = Text::random('alphanuml', 8))) && $cnt < 10) {
			$cnt++;
		}
		if ($cnt >= 10) {
			return array(
				'success' => false,
				'error' => 'Can not create token for file.'
			);
		}

		if($uploadFromUrl) {
			$url = $uploadFromUrl['url'];
			$ext = pathinfo($url, PATHINFO_EXTENSION);
			if(empty($ext)) {
				$ext = 'jpg';
			}
			$file = $tmpDir . $token . '.' . $ext;
			$size = file_put_contents($file, fopen($url, 'r'));

			$info = getimagesize($url);
			if(isset($uploadFromUrl['minWidth']) && $uploadFromUrl['minWidth'] > $info[0]) {
				return false;
			}
			if(isset($uploadFromUrl['minHeight']) && $uploadFromUrl['minHeight'] > $info[1]) {
				return false;
			}
			$result = array(
				'success' => ($size > 0) ? TRUE : FALSE,
				'basename' => basename($url),
				'filename' => $token,
				'filetype' => $ext,
				'filesize' => $size
			);
		} else {
			$uploader = new fileuploader($allowedExtensions, $sizeLimit);
			$result = $uploader->handleUpload($tmpDir, $token);
		}
		// to pass data through iframe you will need to encode all html tags
		if (isset($result['success'])) {
			$isImage = in_array(strtolower($result['filetype']), $imgExtensions) ? 1 : 0;

			if (!$user instanceof Model_User)
				$user = new Model_User($user->id);

			$values = array(
				//'sender_type' => !empty($user) ? $user->getType() : 0,
				'sender_id' => !empty($user) ? $user->id : 0,
				'parent_id' => $parent_id,
				'type' => $type,
				'token' => $token,
				'name' => $result['filename'],
				'size' => $result['filesize'],
				'ext' => $result['filetype'],
				'group' => $group,
				'isImage' => $isImage,
					//'adminId' => !empty(System::$global->user) ? System::$global->user->id : null
			);

			$path = self::getPathByType($type);

			if ($isImage) {
				$dest = Filesystem::compilePath($path . '/image', $token);
				Filesystem::buildPath($dest);
			} else {
				$dest = Filesystem::compilePath($path . '/files', $token);
				Filesystem::buildPath($dest);
			}
            if (copy($tmpDir . $values['token'] . '.' . $values['ext'], $dest . $values['name'])) {
				unlink($tmpDir . $values['token'] . '.' . $values['ext']);

				if ($isImage) {
                    $values['ext'] = strtolower($values['ext']);
					rename($dest . $values['name'], $dest . 'original.' . $values['ext']);
					if ($error = self::makeThumbs($path, $values['token'], $values['ext'], $type, $photoSettings)) {
						// error
						$result['error'] = $error;
						$result['success'] = false;
					}
				} else {
					$values['ext'] = strtolower($values['ext']);
					rename($dest . $values['name'], $dest . $token . '.' . $values['ext']);
				}



				if ($result['success']) {
					$attach = Model_Files::create($values);
					$urls = $attach->takeUrls();
					$message = array(
						'success' => true,
						'token' => $token,
						'id' => $attach->id,
						'ext' => $values['ext'],
						'url' => $urls['url'],
						'isImage' => $isImage,
						'name' => $result['filename']
					);

					if ($multiple === true) {
						$message['html'] = (string) new View('parts/list-one-image', array(
							'image' => $attach,
							'imageType' => $type,
							'cubeCanEdit' => true
								)
						);
					}
					//Module content
					switch ($type) {
						case FILE_PHOTOS:
							$attach->urls = $urls;
							$message['html'] = (string) new View('admin/parts/gallery/gallery-image', array(
								'image' => $attach,
								'imageType' => $type
									)
							);
							break;
						case FILE_USER_AVA:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							Model_User::update(array(
								'avaToken' => $attach->token
							), $user->id);

                            $auth->updateIdentity($user->id);
							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.userinfo-editfoto',
								'content' => (string) View::factory('pages/profile/edit/ava-block', array(
									'profile' => $user
								))
							);
							Model_Timeline::createUpdatePhoto($user->id);

							break;
						case FILE_UPDATES:
							$attach->urls = $urls;
							if(!$isImage) {
								if($attach->ext == 'doc' || $attach->ext == 'docx') {

									$source =  realpath(NULL) . $attach->url;
									$destination = realpath(NULL) . '/' . $tmpDir;
									$command = 'libreoffice --headless --convert-to pdf ' . $source . ' --outdir ' . $destination;
									shell_exec('export HOME=/tmp && ' . $command);

									$tmp_img = 'doc' . rand(0,999999);
									$source =  realpath(NULL) . '/' . $tmpDir . $attach->token . '.pdf';
									$destination = realpath(NULL) . '/' . $tmpDir  . $tmp_img . '.jpg';
									$command = 'convert "' . $source . '" -alpha off -colorspace RGB -resize 1200x1200 -background white -quality 100 "' . $destination . '"';
									exec($command);

									$files = scandir(realpath(NULL) . '/' . $tmpDir);
									$messages = array();
									foreach($files as $pdfFile) {
										if(substr($pdfFile, 0, strlen($tmp_img)) == $tmp_img) {
											$uploadFromUrl['url'] = $_SERVER['DOCUMENT_ROOT'] . '/' . $tmpDir . $pdfFile;
											$messages[] = self::upload($type, $parent_id, $user, $photoSettings, $group, $uploadFromUrl);
										}
									}

									$_SESSION['updates']['isDoc'] = $attach->id;

								} elseif($attach->ext == 'pdf') {
									$tmp_img = 'pdf' . rand(0,999999);
									$source =  $_SERVER['DOCUMENT_ROOT'] . $attach->url;
									$destination = $_SERVER['DOCUMENT_ROOT'] . '/' . $tmpDir  . $tmp_img . '.jpg';
									$command = 'convert "' . $source . '" -alpha off -colorspace RGB -resize 1200x1200 -background white -quality 100 "' . $destination . '"';
									exec($command);

									$files = scandir($_SERVER['DOCUMENT_ROOT'] . '/' . $tmpDir);
									$messages = array();
									foreach($files as $pdfFile) {
										if(substr($pdfFile, 0, strlen($tmp_img)) == $tmp_img) {
											$uploadFromUrl['url'] = $_SERVER['DOCUMENT_ROOT'] . '/' . $tmpDir . $pdfFile;
											$messages[] = self::upload($type, $parent_id, $user, $photoSettings, $group, $uploadFromUrl);
										}
									}

									$_SESSION['updates']['isPdf'] = $attach->id;
								}

								$message['html'] = $messages;
								$message['function_name'] = 'updateAddFileLink';
								$message['data'] = array(
									'url' => $message['url'],
									'name' => $message['name']
								);
							} else {
								$message['html'] = (string) new View('parts/gallery/gallery-upload', array(
										'image' => $attach,
										'imageType' => $type
									)
								);
								$message['html'] = $message['html'];
								$_SESSION['uploader-list'][$attach->id] = true;
							}

							break;
						case FILE_COMPANY_AVA:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$company = Model_Companies::getItemById($parent_id, $user->id);

							$company->avaToken = $attach->token;
							$company->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-companyava',
								'content' => (string) View::factory('pages/companies/block-ava_logo', array(
										'company' => $company
									))
							);

							break;
						case FILE_COMPANY_COVER:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$company = Model_Companies::getItemById($parent_id, $user->id);

							$company->coverToken = $attach->token;
							$company->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-companycover',
								'content' => (string) View::factory('pages/companies/block-ava_cover', array(
										'company' => $company
									))
							);

							break;
						case FILE_GROUP_EMBLEM:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$group = Model_Groups::getItemById($parent_id, $user->id);

							$group->avaToken = $attach->token;
							$group->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-groupemblem',
								'content' => (string) View::factory('pages/groups/block-ava_emblem', array(
										'group' => $group
									))
							);

							break;
						case FILE_GROUP_COVER:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$group = Model_Groups::getItemById($parent_id, $user->id);

							$group->coverToken = $attach->token;
							$group->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-groupcover',
								'content' => (string) View::factory('pages/groups/block-ava_cover', array(
										'group' => $group
									))
							);

							break;
						case FILE_JOB_APPLY:
							$message['function_name'] = 'addBlock';
							$message['data'] = array(
								'target' => '.uploader-list > li:last-child',
								'content' => (string) View::factory('pages/jobs/item-apply_file', array(
										'attach' => $attach
									))
							);
							break;
						case FILE_SCHOOL_AVA:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$school = Model_Universities::getItemById($parent_id, $user->id);

							$school->avaToken = $attach->token;
							$school->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-schoolava',
								'content' => (string) View::factory('pages/schools/block-ava_logo', array(
										'school' => $school
									))
							);

							break;
						case FILE_SCHOOL_COVER:
							$auth = Auth::getInstance();
							$user = $auth->getIdentity();
							$school = Model_Universities::getItemById($parent_id, $user->id);

							$school->coverToken = $attach->token;
							$school->save();

							$user = $auth->getIdentity();
							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.block-schoolcover',
								'content' => (string) View::factory('pages/schools/block-ava_cover', array(
										'school' => $school
									))
							);

							break;
						case FILE_BANNER:
							$view = View::factory('admin/file_one', array(
								'parent_id' => $parent_id,
								'src' => $urls['url']
							));
							$_SESSION['uploaded_banner'] = $attach->id;

							$message['function_name'] = 'changeContent';
							$message['data'] = array(
								'target' => '.admin_uploader',
								'content' => (string)$view
							);


							break;
					}

					self::clear();

					if ($isImage) {
						$message = array_merge($message, $urls);
					} else {
						$message['directory'] = '/' . self::getPathByType($type) . '/files/' . $token . '/';
					}
				}
			} else {
				$result['error'] = 'Can`t move file';
				$result['success'] = false;
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


		return $message;
	}

	public static function makeThumbs($path, $token, $ext, $type, $photoSettings) {
//		$dest = realpath(NULL) . '/' . Filesystem::compilePath($path . '/image', $token);
		$dest = Filesystem::compilePath($path . '/image', $token);
		$config = System::$global->config;
//		$path = realpath(NULL) . '/' . $path;
		$type = $config->fileTypes->$type;

		if ($config->imageThumbs->__isset($type)) {
			$sizes = $config->imageThumbs->$type->arrayize();
			if (count($sizes)) {
				$result = Images::resize($sizes, $dest, 'original', $ext, null, $photoSettings);
				if ($result !== true) {
					return $result;
				}
			}
		}

		return false;
	}

	/**
	 * Regenerate image sizes to new config
	 *
	 * @param $type File type (FILE_UNIT_IMG, ...)
	 * @param bool $size Size of type (fullsize, preview, ...)
	 * @return bool Return true or false
	 */
	public static function regenerateThumbs($type, $size = false) {
		$result = true;

		$files = self::query(array(
					'where' => array('type = ?', $type)
		));

		$path = self::getPathByType($type);
		$config = System::$global->config;
		$type = $config->fileTypes->$type;

		foreach ($files as $file) {
			$dest = Filesystem::compilePath($path . '/image', $file->token);

			if ($config->imageThumbs->__isset($type)) {
				$sizes = $config->imageThumbs->$type->arrayize();
				if (count($sizes)) {
					$sizes = $size && isset($sizes[$size]) ? array($size => $sizes[$size]) : $sizes;
					$result = Images::resize($sizes, $dest, 'original', $file->ext);
					if ($result !== true) {
						$result = false;
					}
				}
			}
		}

		return $result;
	}

	public function cropThumb($x, $y, $w, $h, $cropAreaWidth, $cropSizes) {
		$type = $this->type;
		$dest = Filesystem::compilePath($this->getPathByType($type) . '/image', $this->token);
		if ($cropAreaWidth) {
			$resolution = @getimagesize($dest . 'original.' . $this->ext);

			$ratio = $resolution[0] / $cropAreaWidth;
		} else {
			$ratio = 1;
		}

		$x = round($x * $ratio);
		$y = round($y * $ratio);
		$w = round($w * $ratio);
		$h = round($h * $ratio);

		$config = System::$global->config;

		$type = $config->fileTypes->$type;

		if ($config->imageThumbs->__isset($type)) {
			$sizes = $config->imageThumbs->$type->arrayize();
			if (count($sizes)) {
				foreach ($sizes as $key => $size) {
					if (in_array($key, $cropSizes)) {
						$size['method'] = 'cropCustom';
						$size['geometry'] = Images::geometry(array($w, $h), array($x, $y));
						$size['offset'] = ['x'=>$x , 'y'=>$y];
						$size['sizes'] = ['width'=>$w, 'height'=>$h];
 						Images::resize(array($key => $size), $dest, 'original', $this->ext);
					}
				}
			}
		}

		return true;
	}

	public static function removeJustLoaded($type) {
		$user = Auth::getInstance()->getIdentity();
		if ($user) {
			foreach (self::query(array(
				'where' => array('parent_id = ? AND type =? AND sender_id =? AND sender_type = ?', 0, $type, $user->id, $user->type)
			)) as $file) {
				$file = self::instance($file);
				$file->removeFile();
			};
		}

		return isset($file) ? true : false;
	}

	public function removeFile($keepDbRecord = false) {
		$config = System::$global->config;

		if ($this->isImage) {
			$root = self::getPathByType($this->type);
			$tmp_type = $this->type;
			$type = $config->fileTypes->$tmp_type;
			if ($config->imageThumbs->__isset($type)) {
				$path = Filesystem::compilePath($root . '/image', $this->token);

				$original = $path . 'original' . '.' . $this->ext;
				if (file_exists($original)) {
					unlink($original);
				}

				$sizes = $config->imageThumbs->$type->arrayize();

				if (count($sizes)) {
					foreach ($sizes as $key => $size) {
						$filename = $path . $key . '.' . (isset($size['format']) ? $size['format'] : $this->ext);

						if (file_exists($filename)) {
							unlink($filename);
						}
					}
				}
				if (FileSystem::isDirEmpty($path)) {
					rmdir($path);
				}
			}
		} else {
			$root = self::getPathByType($this->type);

			$path = Filesystem::compilePath($root . '/files', $this->token);
			$file = $path . $this->name;

			if (file_exists($file)) {
				unlink($file);
			}

			if ($this->type == FILE_AUDIO) {
				$file = $path . 'audio.mp3';
				if (file_exists($file)) {
					unlink($file);
				}
				$file = $path . 'audio.ogg';
				if (file_exists($file)) {
					unlink($file);
				}
			}


			if (FileSystem::isDirEmpty($path)) {
				rmdir($path);
			}
		}
		if (!$keepDbRecord) {
			$status = $this->remove($this->id);
			return $status;
		}
	}

	public static function clear() {
		set_time_limit(0);
		$query = array(
			'where' => array('`parent_id` = ? AND `date` < (NOW() - INTERVAL 3 HOUR)', 0)
		);

		$files = array();
		foreach (self::query($query) as $v) {
			$files[] = self::instance($v);
		};

		if (!empty($files)) {
			foreach ($files as $file) {
				$file->removeFile();
			}
		}

		$config = System::$global->config;
		$tmpDir = $config->store->temp . '/';
		$files = scandir($_SERVER['DOCUMENT_ROOT'] . '/' . $tmpDir);
		foreach($files as $file){
			if(is_file($tmpDir . $file)) {
				if(date('U', filemtime($tmpDir . $file)) < (time() - 60*60*3)){
					unlink($tmpDir . $file);
				}
			}
		}
	}

	/**
	 * Remove files of item
	 *
	 * @var mixed $parent_id Item id
	 * @var integer $fileType File type
	 * @return integer
	 */
	public static function removeByType($parent_id, $fileType) {
		if (is_array($parent_id)) {
			$query = array(
				'where' => array('`parent_id` IN(' . implode(',', $parent_id) . ') AND `type` = ? ', $fileType)
			);
		} else {
			$query = array(
				'where' => array('`parent_id` = ? AND `type` = ? ', $parent_id, $fileType)
			);
		}

		$files = array();
		$idS = array();
		foreach (self::query($query) as $v) {
			$files[] = self::instance($v);
			$idS[] = $v->id;
		};

		if (!empty($idS)) {
			self::remove(array('id IN(' . implode(',', $idS) . ')'));
		}

		if (!empty($files)) {
			foreach ($files as $file) {
				$file->removeFile(true);
			}
		}
	}


	public static function removeByIds($idS, $fileType, $user_id) {
		$query = array(
			'where' => array('`id` IN(?) AND `type` = ? AND sender_id = ?', $idS, $fileType, $user_id)
		);

		$files = array();
		$idS2 = array();
		foreach (self::query($query) as $v) {
			$files[] = self::instance($v);
			$idS2[] = $v->id;
		};

		if (!empty($idS2)) {
			self::remove(array('id IN(?)', $idS2));
		}

		if (!empty($files)) {
			foreach ($files as $file) {
				$file->removeFile(true);
			}
		}
	}

	public static function removeByLandlordId($landLordId) {
		$query = array(
			'where' => array('`adminId` = ?', $landLordId)
		);

		$files = array();
		$idS = array();
		foreach (self::query($query) as $v) {
			$files[] = self::instance($v);
			$idS[] = $v->id;
		};

		if (!empty($idS)) {
			self::remove(array('id IN(' . implode(',', $idS) . ')'));
		}

		if (!empty($files)) {
			foreach ($files as $file) {
				$file->removeFile(true);
			}
		}
	}

	public static function cropImage($imageId, $save = FALSE) {

		$file = new Model_UploadFiles($imageId);

		if ($save != 1) {
			$fileSrc = Model_UploadFiles::generateUrl($file->token, $file->ext, $file->type, true, false, 'crop');
			if (!is_file($fileSrc)) {
				$fileSrc = Model_UploadFiles::generateUrl($file->token, $file->ext, $file->type, true, false);
			}

			$message = array(
				'url' => $fileSrc,
				'cropArea' => !empty($file->cropArea) ? unserialize($file->cropArea) : false,
				'download' => Request::generateUri('files', 'download', $file->token),
			);
		} else {
			$sizes = Request::get('sizes');
//			dump($sizes, 1);
			$cropAreaWidth = Request::get('cropAreaWidth');

			$x = Request::get('x');
			$y = Request::get('y');
			$w = Request::get('w');
			$h = Request::get('h');

			$file->cropArea = serialize(array(
				'x' => $x,
				'y' => $y,
				'x2' => $x + $w,
				'y2' => $y + $h
			));

			$file->save();

			$status = false;

			if (count($sizes)) {
				$status = $file->cropThumb($x, $y, $w, $h, $cropAreaWidth, $sizes);
			}

			$message = array(
				'success' => $status ? true : false
			);
		}

		return $message;
	}

}
