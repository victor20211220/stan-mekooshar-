<?php
require_once APPLICATION_PATH . 'controllers/connections.php';

class Updates_Controller extends Controller_User
{

	protected $subactive = 'updates';

	public function  before() {
        $auth = Auth::getInstance();
        $Connections_Controller = new Connections_Controller();
        $Connections_Controller->updateConnectionsUser($auth->getIdentity()->id);

        parent::before();

        $this->view->script('/js/libs/fileuploader.js');
        $this->view->script('/js/uploader.js');

	}

	public function actionIndex()
	{
		$this->view->title = 'Updates';

		$countInSearch = Model_ConnectionSearchResult::countInSearchResult($this->user->id);
		$countVisits = Model_Visits::countVisits($this->user->id);
		$connectionsMayKnow = Model_Connections::getListMayKnowConnectionsByUser($this->user->id);
		$myVisits = Model_Visits::getListMyVisits($this->user->id);

		$f_Updates_AddUpdate = new Form_Updates_AddUpdate();

		$isError = false;
		if(Request::isPost()) {
			if($f_Updates_AddUpdate->form->validate()) {
				$timeline = Updates::newUpdate($f_Updates_AddUpdate, $this->user);

				$f_Updates_AddUpdate->form->clearValues();

				if(Request::$isAjax ) {
					$newTimeline = Model_Timeline::getItemById($timeline->id, $this->user->id);
					$content = View::factory('pages/updates/item-update', array(
						'timeline' => $newTimeline,
						'isUsernameLink' => TRUE
					));

					$this->autoRender = false;
					$this->response->setHeader('Content-Type', 'text/json');
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string)$content,
							'target' => '.block-list-updates > .list-items > li:first-child',
							'function_name' => 'updateClear',
							'data' => array(

							)
						)
					));
					return;
				}
			} else {
				$isError = true;
			}
		} else {
			unset($_SESSION['uploader-list']);
		}

		if($isError) {
			$content = View::factory('pages/updates/block-create-updates', array(
				'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
				'initUploader' => FALSE
			));

			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');
			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'submitError',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-create-updates'
				)
			));
		} else {
			$timelines = Model_Timeline::getListByUserId($this->user->id);

			if(Request::get('pagedown', false) && Request::$isAjax) {
				$this->autoRender = false;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach($timelines['data'] as $timeline) {
					$view .= View::factory('pages/updates/item-update', array(
						'timeline' => $timeline,
						'isUsernameLink' => TRUE
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'controller' => Request::generateUri('updates', 'index'),
							'isBand' => TRUE,
							'autoScroll' => TRUE
						) + $timelines['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string)$view,
						'target' => '.block-list-updates > .list-items > li:last-child'
					)
				));
				return;
			} else {
				$jobsYouMayLike = Model_Jobs::getJobsYouMayLike($this->user->id);
				$groupsYouMayLike = Model_Groups::getListInterestedByUserid($this->user->id);

				$view = new View('pages/updates/index', array(
					// Left top panel
					'f_Updates_AddUpdate' => $f_Updates_AddUpdate,

					// Left down panel
					'timelines' => $timelines,

					// Right panel
					'countInSearch' => $countInSearch,
					'countVisits' => $countVisits,
					'connectionsMayKnow' => $connectionsMayKnow,
					'myVisits' => $myVisits,
					'jobsYouMayLike' => $jobsYouMayLike,
					'groupsYouMayLike' => $groupsYouMayLike
				));
			}
			$this->view->content = $view;
		}

	}

	public function actionLoadUrlData()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$link = Request::get('url', false);

			$status = false;
			$result = false;
			if($link) {
				if(substr($link, 0, 7) == 'http://' || substr($link, 0, 8) == 'https://') {
					$host = substr($link, 0, strpos($link, '/', 9));
				} elseif(substr($link, 0, 4) == 'www.') {
					$host = 'http://' . substr($link, 0, strpos($link, '/'));
				} else {
					$host = false;
				}

				if($host) {
					$html = file_get_contents($link);

					if(!empty($html)) {
						$status = true;
						$images = array();
						$title = array();
						$description = array();


						// Get info from OG Tags
						$metas = explode('<meta', $html);
//						dump($metas, 17px);
						foreach($metas as $key => $meta){
							$metas[$key] = substr($meta, 0, strpos($meta, '>'));
						}
						foreach($metas as $meta) {
							if(strpos($meta, 'og:image') > 0 && strpos($meta, 'content="') > 0) {
								$src = substr($meta, strpos($meta, 'content="') + 9);
								$src = substr($src, 0, strpos($src, '"'));
//								$src = substr($src, 0, strpos($src, '?'));
//								$src = substr($src, 0, strpos($src, '&'));
//								$ext = pathinfo($src, PATHINFO_EXTENSION);
//								if(in_array($ext, array('jpg', 'png', 'gif', 'jpeg'))) {
									if(substr($src, 0, 7) == 'http://' || substr($src, 0, 8) == 'https://') {
										$images[] = $src;
									} elseif(substr($src, 0, 1) == '/') {
										$images[] = $host . $src;
									} elseif(substr($src, 0, 4) == 'www.') {
										$images[] = 'http://' . $src;
									}
//								}
							}
							if(strpos($meta, 'og:title') > 0 && strpos($meta, 'content="') > 0) {
								$content = substr($meta, strpos($meta, 'content="') + 9);
								$content = substr($content, 0, strpos($content, '"'));
								if(!empty($content)) {
									$title[] = $this->removeSpecialShars($content);
								}
							}
							if(strpos($meta, 'og:description') > 0 && strpos($meta, 'content="') > 0) {
								$content = substr($meta, strpos($meta, 'content="') + 9);
								$content = substr($content, 0, strpos($content, '"'));
								if(!empty($content)) {
									$description[] = $this->removeSpecialShars($content);
								}
							}
						}
						unset($metas);

						// If no OG Image get all images from html
//						if(empty($images)) {
							$imgs = explode('<img', $html);

							foreach($imgs as $key => $img){
								$imgs[$key] = substr($img, 0, strpos($img, '>'));
							}

							foreach($imgs as $key => $img){

								if(strpos($img, 'src="') > 0) {
									$src = substr($img, strpos($img, 'src="') + 5);
									$src = substr($src, 0, strpos($src, '"'));

//									$src = substr($src, 0, strpos($src, '?'));
//									$src = substr($src, 0, strpos($src, '&'));
//									$ext = pathinfo($src, PATHINFO_EXTENSION);
//									if(in_array(strtolower($ext), array('jpg', 'png', 'gif', 'jpeg'))) {
										if(substr($src, 0, 7) == 'http://' || substr($src, 0, 8) == 'https://') {
											$images[] = $src;
										} elseif(substr($src, 0, 1) == '/') {
											$images[] = $host . $src;
										} elseif(substr($src, 0, 4) == 'www.') {
											$images[] = 'http://' . $src;
										}

//									}
								}
							}
//						dump($images, 1);
//						}

						// If no OG Title get all images from html
						if(empty($title)) {
							$text = substr($html, strpos($html, '<title') + 7);
							$text = substr($text, 0, strpos($text, '</title>'));
							if(!empty($text)) {
								$title[] = $this->removeSpecialShars($text);
							}

							if(empty($title)) {
								$h1s = explode('<h1', $html);

								foreach($h1s as $key => $h1){
									$h1s[$key] = substr($h1, 0, strpos($h1, '</h1>'));
								}
								foreach($h1s as $key => $h1){
									$text = substr($h1, strpos($h1, '>'));
									$text = strip_tags($text);
									if(!empty($text)) {
										$title[] = $this->removeSpecialShars($text);
									}
								}
							}
						}

						// If no OG Description get all images from html
						if(empty($description)) {
								$ps = explode('<p', $html);

								foreach($ps as $key => $p){
									$tmp = substr($p, 0, strpos($p, '</p>'));
									$tmp = substr($tmp, strpos($tmp, '>'));
									$ps[$key] =$this->removeSpecialShars(strip_tags($tmp));
								}

								$description[0] = '';
								foreach($ps as $key => $p){
									$description[0] .= $p;
									if(strlen($description[0]) >= 500) {
										$description[0] = substr($description[0], 0, strpos($description[0], ' ', 500)) . '...';
										break;
									}
								}
						}


						// Get images fro url and create template
						$imageObj = array();
						if(!empty($images)) {

							foreach($images as $key => $url) {
								$data = array(
									'url' => $url,
									'minWidth' => 50,
									'minHeight' => 50
								);
								$result = Model_Files::upload(FILE_UPDATES, 0, $this->user, false, null, $data);
								if($result) {
									$imageObj[] = $result;
								}
							}

						}

						$_SESSION['updates']['isLink'] = $link;

						$result = array(
							'images' => $imageObj,
							'title' => $title,
							'description' => $description,
							'type' => POST_TYPE_WEB
						);
					} else {
						$status = TRUE;
						$result = array(
							'images' => array(),
							'title' => $host,
							'description' => $host,
							'type' => POST_TYPE_WEB
						);
					}
				}
			}

			$this->response->body = json_encode(array(
				'status' => $status,
				'content' => $result
//				'function_name' => 'changeContent',
//				'data' => array(
//					'content' => (string)$view,
//					'target' => '.block-list-updates > .list-items > li:last-child'
//				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionClearType()
	{
		if(Request::$isAjax){
			unset($_SESSION['updates']);
			if(isset($_SESSION['uploader-list']) && !empty($_SESSION['uploader-list'])) {
				$idS = array();
				foreach($_SESSION['uploader-list'] as $image_id => $tmp_value) {
					$idS[] = $image_id;
				}

				if(!empty($idS)) {
					Model_Files::removeByIds($idS, FILE_UPDATES, $this->user->id);
				}
				unset($_SESSION['uploader-list']);
			}

			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');
			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionEdit($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id, $this->user->id);

			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->edit($timeline);


			$isError = false;
			if(Request::isPost()){
				if($f_Updates_AddUpdate->form->validate()){
					$values = $f_Updates_AddUpdate->form->getValues();

					switch($timeline->type){
						case TIMELINE_TYPE_POST:
							$post = $f_Updates_AddUpdate->post;

							switch($post->typePost) {
								case POST_TYPE_TEXT:
								case POST_TYPE_IMAGE:
								case POST_TYPE_DOC:
								case POST_TYPE_PDF:
									Model_Posts::update(array(
										'text' => trim($values['text'])
									), $timeline->post_id);
									$timeline->postText = $values['text'];

									break;
								case POST_TYPE_WEB:
									Model_Posts::update(array(
										'text' => trim($values['urltext']),
										'title' => $values['title']
									), $timeline->post_id);
									$timeline->postText = $values['urltext'];
									$timeline->postTitle = $values['title'];
									break;
							}


							break;
						case TIMELINE_TYPE_SHAREPOST:
							$timeline->content = trim($values['text']);
							$timeline->save();
					}

					$content = View::factory('pages/updates/item-update', array(
						'timeline' => $timeline
					));
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'changeContent',
						'data' => array(
							'content' => (string)$content,
							'target' => 'li[data-id="timeline_' . $timeline->id . '"]'
						)
					));
					return;

				} else {
					$isError = true;
				}
			}

			$content = $f_Updates_AddUpdate->form;
			if($isError) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'submitError',
					'data' => array(
						'content' => (string)$content,
						'target' => '#' . $f_Updates_AddUpdate->form->attributes['id']
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'editBlock',
					'data' => array(
						'content' => (string)$content,
						'target' => 'li[data-id="timeline_' . $timeline_id . '"] .update-textblock'
					)
				));
			}
			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}


	public function actionDelete($timeline_id){
		$timeline = Model_Timeline::getItemById($timeline_id, $this->user->id);

		switch($timeline->type){
			case TIMELINE_TYPE_POST:
				if($timeline->postUserId == $this->user->id || $timeline->companyUserId == $this->user->id) {
					Model_Posts::remove($timeline->post_id);
				} else {
					$timeline->delete();
				}
				break;
			case TIMELINE_TYPE_SHAREPOST:
				$parentTimelineShare = Model_Timeline_Shares::getParentTimelineByCurrentTimelineId($this->user->id, $timeline->id);
				if($parentTimelineShare) {
					$parentTimeline = Model_Timeline::getItemByOnlyId($parentTimelineShare->parentTimeline_id);
					$parentTimeline->countShare -= 1;
					$parentTimeline->save();
				}
				$timeline->delete();
				break;
			case TIMELINE_TYPE_UPDATEPHOTO:
			case TIMELINE_TYPE_LIKE:
				$timeline->delete();
				break;
		}

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$removeTimelinesId = array($timeline->id => TRUE);
			$result = Model_Timeline::getChildrenByTimelime($timeline->id);
			while(!empty($result['data'])) {
				$timelineIds = array();
				foreach($result['data'] as $item) {
					$removeTimelinesId[$item->id] = true;
					$timelineIds[] = $item->id;
				}
				$result = Model_Timeline::getChildrenByTimelime($timelineIds);
			}

			$removeId = array();
			foreach($removeTimelinesId as $id => $value){
				$removeId[] = '"li[data-id="timeline_' . $id . '"]"';
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'removeItem',
				'data' => array(
					'target' => $removeId
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());

	}

	public function actionLike($timeline_id)
	{
		$timeline = Model_Timeline::getItemById($timeline_id);

		$result = Updates::like($timeline, $this->user);
		$timeline_updated = Model_Timeline::getItemById($timeline_id);

		$view = View::factory('pages/updates/block-who_like', array(
			'showLike' => TRUE,
			'showShare' => TRUE,
			'timeline' => $timeline_updated
		));

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => $result['resultFunction'],
				'data' => array(
					'target' => $result['target'],
					'function_name' => $result['resultFunction2'],
					'data' => array(
						'target' => $result['target2'],
						'function_name' => $result['resultFunction3'],
						'data' => array(
							'target' => $result['target3'],
							'class' => 'active',
							'function_name' => $result['resultFunction4'],
							'data' => array(
								'target' => $result['target4'],
								'class' => 'active',
								'function_name' => 'changeContent',
								'data' => array(
									'content' => (string)$view,
									'target' => '.list-items li[data-id="timeline_' . $timeline->id . '"] .update-who_likes'
								)
							)
						)
					)
				)
			));
			return;
		} else {
			$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
		}
	}

	public function actionShowLikeList($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id);
			$listLikes = Model_Timeline_Likes::getListByTimeline($timeline);

			$content = View::factory('parts/pbox-form', array(
				'title' => 'People who like this',
				'content' => $view = View::factory('popups/updates/list-likespeople', array(
						'listLikes' => $listLikes
					))
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'content' => (string)$content
			));
			return;
		}
		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionShowShareList($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id);
			$listShare = Model_Timeline_Shares::getListByTimeline($timeline);

			$content = View::factory('parts/pbox-form', array(
				'title' => 'People who share this',
				'content' => $view = View::factory('popups/updates/list-sharespeople', array(
						'listShare' => $listShare
					))
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'content' => (string)$content
			));
			return;
		}
		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionComments($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById($timeline_id, FALSE, FALSE, FALSE, $this->user->id);

			switch($timeline->type) {
				case TIMELINE_TYPE_COMMENTS:
				case TIMELINE_TYPE_LIKE:
					$comments = Model_Timeline_Comments::getListByTimelineId($timeline->parent_id, $this->user->id);
					break;
				default:
					$comments = Model_Timeline_Comments::getListByTimelineId($timeline->id, $this->user->id);
			}

			if(!is_null($timeline->postGroupId) && is_null($timeline->groupMemberUserId)) {
				$f_Updates_AddUpdateComments = FALSE;
			} else {
				$f_Updates_AddUpdateComments = new Form_Updates_AddUpdateComments($timeline->id);
			}

			if(Request::isPost()) {
				if($f_Updates_AddUpdateComments->form->validate()) {
					$values = $f_Updates_AddUpdateComments->form->getValues();

					$results = Updates::comments($timeline, $values, $this->user);

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string) $results['view'],
							'target' => 'li[data-id="timeline_' . $timeline->id . '"] .block-list-updatecomments > .list-items > li:first-child',
							'function_name' => 'addCount',
							'data' => array(
								'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-comments div',
								'function_name' => ($results['target2']) ? 'addBlock' : null,
								'data' => array(
									'target' => ($results['target2']) ? $results['target2'] : null
								)
							)
						)
					));
//					dump($_SESSION, 1);
					return;
				}
			}

			if(Request::get('pagedown', false)) {
				$view = '';
				foreach($comments['data'] as $comment) {
					$view .= View::factory('pages/updates/item-comment', array(
						'comment' => $comment
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('updates', 'comments', $timeline->id),
						'isBand' => TRUE
					) + $comments['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string)$view,
						'target' => 'li[data-id="timeline_' . $timeline->id . '"] .block-list-updatecomments > .list-items > li:last-child'
					)
				));
				return;
			} else {
				$view = View::factory('pages/updates/list-comments', array(
					'comments' => $comments,
					'f_Updates_AddUpdateComments' => $f_Updates_AddUpdateComments,
					'timeline_id' => $timeline->id
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'addBlock',
					'data' => array(
						'content' => (string)$view,
						'target' => 'li[data-id="timeline_' . $timeline->id . '"] .update-comments'
					)
				));
				return;
			}

		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}


	public function actionDeleteComment($comment_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$check = Updates::deleteComment($comment_id, $this->user);

			if($check) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => 'li[data-id="comment_' . $check->id . '"]',
						'function_name' => 'negativeComments',
						'data' => array(
							'target' => 'li[data-id="timeline_' . $check->timeline_id . '"] .i-comments div'
						)
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => false,
				));
			}


			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionShare($timeline_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$timeline = Model_Timeline::getItemById_WithoutUserid($timeline_id, $this->user->id);

			$f_Updates_ShareUpdate = new Form_Updates_ShareUpdate($timeline->id);

			if(!empty($timeline->parent_id)) {
				$parentId = $timeline->parent_id;
			} else {
				$parentId = $timeline->id;
			}
			$check = Model_Timeline_Shares::checkIsset($this->user->id, $parentId);

			$isError = false;
			if(Request::isPost() && !$check){
				if($f_Updates_ShareUpdate->form->validate()){
					$values = $f_Updates_ShareUpdate->form->getValues();

					$newTimeline = Updates::share($timeline, $values, $this->user);

					$view = View::factory('pages/updates/item-update', array(
						'timeline' => $newTimeline
					));

					$timeline_updated = Model_Timeline::getItemById($timeline_id);
					$view_share = View::factory('pages/updates/block-who_share', array(
						'showLike' => TRUE,
						'showShare' => TRUE,
						'timeline' => $timeline_updated
					));
					$view_like = View::factory('pages/updates/block-who_like', array(
						'showLike' => TRUE,
						'showShare' => TRUE,
						'timeline' => $timeline_updated
					));
//					dump((string)$view);
//					dump((string)$view_like, 1);


					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string) $view,
							'target' => '.block-list-updates > .list-items > li:first-child',
							'function_name' => 'addCount',
								'data' => array(
								'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-replay div',
								'function_name' => 'addClass',
								'data' => array(
									'target' => 'li[data-id="timeline_' . $timeline->id . '"] .i-replay',
									'class' => 'active',
									'function_name' => 'changeContent',
									'data' => array(
										'content' => (string)$view_share,
										'target' => '.list-items li[data-id="timeline_' . $timeline->id . '"] .update-who_shares',
										'function_name' => 'changeContent',
										'data' => array(
											'content' => (string)$view_like,
											'target' => '.list-items li[data-id="timeline_' . $timeline->id . '"] .update-who_like'
										)
									)
								)
							)
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			if($check){
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'popupShow',
					'data' => array(
						'title' => 'Message',
						'content' => 'This post has been shared in your update!'
					)
				));
				return;
			}

			$content = $f_Updates_ShareUpdate->form;
			if($isError) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'submitError',
					'data' => array(
						'content' => (string)$content,
						'target' => '#' . $f_Updates_ShareUpdate->form->attributes['id']
					)
				));
			} else {
				$content = View::factory('parts/pbox-form', array(
					'title' => 'Share ',
					'content' => View::factory('popups/updates/shareupdate', array(
							'f_Updates_ShareUpdate' => $f_Updates_ShareUpdate->form
						))
				));

				$this->response->body = json_encode(array(
					'status' => (!$isError),
					'content' => (string)$content
				));
			}
			return;

		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function actionClick($post_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			Model_Posts::clearPostClicks();
			$post = new Model_Posts($post_id);

			$isError = true;
			if(!is_null($post->company_id)) {
				if(!isset($_SESSION['posts_click'][$post->id])) {
					$_SESSION['posts_click'][$post->id] = time();

					$check = Model_Company_Post_Clicks::checkIsset($this->user->id, $post->id);
					if(!$check) {
						Model_Company_Post_Clicks::create(array(
							'user_id' => $this->user->id,
							'post_id' => $post->id,
							'company_id' => $post->company_id
						));
						$post->countClicks += 1;
						$post->save();
					}
					$isError = false;
				}
			}

			$this->response->body = json_encode(array(
				'status' => (!$isError)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('updates', 'index') . Request::getQuery());
	}

	public function removeSpecialShars($text)
	{
		return preg_replace("/&#?[a-z0-9]+;/i","",$text);
	}
}