<?php
require_once APPLICATION_PATH . 'controllers/invites.php';
require_once APPLICATION_PATH . 'controllers/connections.php';
class Connections_Controller extends Controller_User
{

	protected $subactive = 'connections';
    protected  $Connections_Controller = '';
	public function  before() {
		parent::before();
        $this->Connections_Controller = new Connections_Controller();

	}

	public function actionIndex()
	{
		$this->view->title = 'Connections';

		$query = array(
			'tag' => Request::get('tag', false),
			'company' => Request::get('company', false),
			'region' => Request::get('region', false)
		);

		$connections = Model_Connections::getListConnectionsInfoByUser($this->user->id, $query);

		$tags = Model_Tags::getListByUser($this->user->id);
		$companies = Model_Connections::getListCompaniesFromUserProfile($this->user->id);
		$regions = Model_Connections::getListRegionsFromUserProfile($this->user->id);
		$countReceived = Model_Connections::getCountNewReceived($this->user->id);

		$this->view->content = View::factory('pages/connections/index', array(
			'left' => View::factory('pages/connections/menu-connections', array(
					'tags' => $tags,
					'companies' => $companies,
					'regions' => $regions,
					'query' => $query,
					'countReceived' => $countReceived
			)),
			'right' => View::factory('pages/connections/list-connections', array(
					'connections' => $connections,
					'countReceived' => $countReceived
			))
		));
	}

	public function actionReceivedInvitations ()
	{
		$this->view->title = 'Received Invitations';
		$receiveds = Model_Connections::getListReceived($this->user->id);
		$countReceived = Model_Connections::getCountNewReceived($this->user->id);

		$this->view->content = View::factory('pages/connections/index', array(
			'left' => View::factory('pages/connections/menu-connections', array(
					'tags' => false,
					'companies' => false,
					'regions' => false,
					'query' => array(),
					'countReceived' => $countReceived
				)),
			'right' => View::factory('pages/connections/list-received-invitations', array(
					'receiveds' => $receiveds,
					'countReceived' => $countReceived
				))
		));
	}


	public function actionSentInvitations ()
	{
		$this->view->title = 'Sent Invitations';
		$sentInvitations = Model_Connections::getListSent($this->user->id);
		$countReceived = Model_Connections::getCountNewReceived($this->user->id);

		$this->view->content = View::factory('pages/connections/index', array(
			'left' => View::factory('pages/connections/menu-connections', array(
					'tags' => false,
					'companies' => false,
					'regions' => false,
					'query' => array(),
					'countReceived' => $countReceived
				)),
			'right' => View::factory('pages/connections/list-sent-invitations', array(
					'sentInvitations' => $sentInvitations,
					'countReceived' => $countReceived
				))
		));
	}

	public function actionAddConnectionsFromUserAvaBlock($profiles_id)
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$connection = $this->actionAddConnections($profiles_id, false, 'UserAvaBlock');

			return;
		}

		$this->response->redirect(Request::generateUri('profile', $profile_id));
	}

	public function actionAddConnectionsFromSearch($profiles_id)
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$connection = $this->actionAddConnections($profiles_id, false, 'FromSearch');

			return;
		}

		$this->response->redirect(Request::generateUri('profile', $profile_id));
	}

	public function actionAddConnections($profile_id, $redirectToProfile = true, $from = false)
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$profile = Model_User::getItemByUserid_withoutError($profile_id);

			// When I is in block list user's
			$isBlocked = Model_Profile_Blocked::checkIsIInBlockListUser($profile_id);
			if($isBlocked) {
				$message = 'You cannot send invitations to this user. You are in block list.';
				$content = View::factory('parts/pbox-form', array(
					'title' => 'Message',
					'content' => View::factory('popups/message', array(
						'message' => $message
					))
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'content' => (string)$content
				));
				return;
			}

			// When user is more 3 ignores from future friend
			$countBan = Model_ConnectionBan::countConnectionBan($this->user->id, $profile->id);
			if($countBan >= 3) {
				$message = 'You cannot send invitations to this user anymore.';
				$content = View::factory('parts/pbox-form', array(
					'title' => 'Message',
					'content' => View::factory('popups/message', array(
							'message' => $message
						))
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'content' => (string)$content
				));
				return;
			}

			if(Model_Connections::exists(array('user_id = ? AND friend_id = ? AND typeApproved in (0,1)',$this->user->id, $profile->id))){
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('profile', $profile->id)
					)
				));
				return;
			}

			$f_Connections_AddConnections = new Form_Connections_AddConnections($profile);
			if($from == 'UserAvaBlock') {
				$f_Connections_AddConnections->setFromUserAvaBlock($profile->id);
			}
			if($from == 'FromSearch') {
				$f_Connections_AddConnections->setFromSearch($profile->id);
			}

			$isError = false;
			if(Request::isPost()) {
				if($f_Connections_AddConnections->form->validate()) {
					$values = $f_Connections_AddConnections->form->getValues();

					// Check is received for this user
					$isActiveReceived = false;
					$receiveds = Model_Connections::getConnectinsWithUsers($profile_id, $this->user->id);
					foreach($receiveds['data'] as $received) {
						if($received->typeApproved == 0) {
							$isActiveReceived = true;
						}
					}

					if($isActiveReceived){
						$connection = Model_Connections::create(array(
							'user_id' => $this->user->id,
							'friend_id' => $profile->id,
							'typeApproved' => 1,
						));
						Model_Connections::update(array(
							'typeApproved' => 1
						), array('user_id = ? AND friend_id = ? AND typeApproved = 0', $profile->id, $this->user->id));

						Model_Notifications::createNewConnectionNotification($this->user->id, $profile->id);
						Model_Timeline::createNewConnectionsTimeline($this->user->id, $profile->id);
						Model_User::update(array(
								'isUpdatedConnections' => 1
							), array('`id` = ? OR `id` = ?', $this->user->id, $profile->id)
						);
//						Model_User_Friends::addFriends($this->user->id, $profile->id);
//						Model_User::updateOneUsersCountConnections($this->user);
//						Model_User::updateOneUsersCountConnections($profile);
					} else {
						$connection = Model_Connections::create(array(
							'user_id' => $this->user->id,
							'friend_id' => $profile->id,
							'message' => $values['message'],
						));

						// Send notification for another user (profile)
//						$mail = new Mailer('notifications/new_connections');
//						$mail->firstName = $profile->firstName;
//						$mail->ouser = $profile;
//						$mail->user = $this->user;
//						$mail->connection_id = $connection->id;
//						$mail->send($profile->email);
					}

					$tags = Model_Tags::getListByUser($this->user->id);
					foreach($values as $key => $value) {
						if(!empty($value) && substr($key, 0, 4) == 'tags') {
							$id = substr($key, 5);
							if(isset($tags['data'][$id])) {
								Model_ConnectionTags::create(array(
									'connection_id' => $connection->id,
									'tag_id' => $id
								));
							}
						}
					}

					Model_User::update(array(
							'isUpdatedConnections' => 1
						), array($connection->friend_id, $connection->user_id)
					);

					if($redirectToProfile) {
						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'redirect',
							'data' => array(
								'url' => Request::generateUri('profile', $profile->id)
							)
						));
					}
					if($from == 'UserAvaBlock') {
						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'addClass',
							'data' => array(
								'target' => '[data-id="profile_' . $profile_id . '"] .userava-add_connection',
								'class' => 'hidden',
								'function_name' => 'removeClass',
								'data' => array(
									'target' => '[data-id="profile_' . $profile_id . '"] .userava-invitation_sent',
									'class' => 'hidden'
								)
							)
						));
					}
					if($from == 'FromSearch') {
						$profile = Model_User::getItemByUserid($profile->id);
						$profile->connectionApproved = ADDCONNECTION_SEND;
						$view = View::factory('pages/search/people/item-search-results', array(
							'result' => $profile
						));

						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'changeContent',
							'data' => array(
								'content' => (string) $view,
								'target' => 'li[data-id="profile_' . $profile->id . '"]'
							)
						));
					}
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Invite ' . $profile->firstName . ' ' . $profile->lastName,
				'content' => View::factory('popups/connections/addconnections', array(
						'profile' => $profile,
						'f_Connections_AddConnections' => $f_Connections_AddConnections->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', $profile_id));
	}


	public function actionManageTags()
	{
		if(Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$tags = Model_Tags::getListByUser($this->user->id);
			$f_Connections_ManageTags = new Form_Connections_ManageTags($tags);

			$isError = false;
			if(Request::isPost()) {
				if($f_Connections_ManageTags->form->validate()) {
					$values = $f_Connections_ManageTags->form->getValues();

					$new_tags = $f_Connections_ManageTags->getPost($values);

					// Remove old tags
					foreach($tags['data'] as $key => $tag) {
						$isFounded = false;
						foreach($new_tags as $new_tag) {
							if($tag->id == $new_tag['id']) {
								$isFounded =TRUE;
								break;
							}
						}
						if(!$isFounded) {
							Model_Tags::remove($tag->id);
						}
					}

					// Add new tags and change value
					foreach($new_tags as $key => $new_tag) {
						$isFounded = false;
						foreach($tags['data'] as $tag) {
							if($tag->id == $new_tag['id']) {
								if($tag->name != $new_tag['name']) {
									Model_Tags::update(array('name' => $new_tag['name']), $tag->id);
								}
								$isFounded = TRUE;
								break;
							}
						}
						if(!$isFounded) {
							$created_tag = Model_Tags::create(array(
								'user_id' => $this->user->id,
								'name' => $new_tag['name']
							));
							$new_tags[$key]['id'] = $created_tag->id;
						}
					}

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'redirect',
						'data' => array(
							'url' => Request::generateUri('connections', 'index')
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Manage tags ',
				'content' => View::factory('popups/connections/managetags', array(
						'f_Connections_ManageTags' => $f_Connections_ManageTags->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', $profile_id));
	}

	public function actionEditTags($connection_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$connection = new Model_Connections($connection_id);
			if($connection->typeApproved != 1 && $connection->user_id != $this->user->id){
				$this->response->redirect(Request::generateUri('connections', 'index'));
			}
			$profile = new Model_User($connection->friend_id);

			$tags = Model_Tags::getListByUser($this->user->id);
			$f_Connections_SetTagForConnection = new Form_Connections_SetTagForConnection($connection, $tags);
			$f_Connections_SetTagForConnection->setAction(Request::generateUri('connections', 'editTags', $connection->id));

			$isError = false;
			if(Request::isPost()){
				if($f_Connections_SetTagForConnection->form->validate()){
					$values = $f_Connections_SetTagForConnection->form->getValues();
					Model_ConnectionTags::remove(array('connection_id = ?', $connection->id));

					foreach($values as $key => $value) {
						if(substr($key, 0, 4) == 'tags' && !empty($value) && isset($tags['data'][substr($key, 5)])) {
							Model_ConnectionTags::create(array(
								'connection_id' => $connection->id,
								'tag_id' => substr($key, 5),
							));
						}
					}

					$connections = Model_Connections::getListConnectionsInfoByUser($this->user->id);
					$countReceived = Model_Connections::getCountNewReceived($this->user->id);
					$content = View::factory('/pages/connections/list-connections', array(
						'connections' => $connections,
						'countReceived' => $countReceived
					));
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'changeContent',
						'data' => array(
							'content' => (string)$content,
							'target' => '.connections-list_connections'
						)
					));
					return;
				} else {
					$isError = true;
				}
			} else {
				$connectionTags = Model_ConnectionTags::getListConnectionTags($connection->id);
				$f_Connections_SetTagForConnection->setTags($connectionTags);
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Set tags for ' . $profile->firstName . ' ' . $profile->lastName,
				'content' => View::factory('popups/connections/settagsforconnection', array(
						'profile' => $profile,
						'f_Connections_SetTagForConnection' => $f_Connections_SetTagForConnection->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$this->response->redirect(Request::generateUri('connections', 'index'));
	}

	public function actionDeleteConnection($connection_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$connection = new Model_Connections($connection_id);
			if($connection->typeApproved != 1 && $connection->user_id != $this->user->id){
				$this->response->redirect(Request::generateUri('connections', 'index'));
			}

			Model_Connections::remove($connection->id);
			Model_Connections::remove(array('user_id = ? AND friend_id = ?', $connection->friend_id, $connection->user_id));
			Model_Timeline::remove(array(
				'((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)) AND type = ?',
				$connection->friend_id, $connection->user_id,
				$connection->user_id, $connection->friend_id,
				TIMELINE_TYPE_NEWCONNECTION,
			));
			Model_User::update(array(
				'isUpdatedConnections' => 1
				), array($connection->friend_id, $connection->user_id)
			);

			$profile = new Model_User($connection->friend_id);
////			Model_User_Friends::removeFriends($connection->friend_id, $connection->user_id);
//			Model_User::updateOneUsersCountConnections($this->user);
//			Model_User::updateOneUsersCountConnections($profile);
            $this->Connections_Controller ->updateConnectionsUser($_SESSION['identity']->id);
            Auth::getInstance()->updateIdentity($this->user->id, TRUE);


            $connections = Model_Connections::getListConnectionsInfoByUser($this->user->id);
			$countReceived = Model_Connections::getCountNewReceived($this->user->id);
			$content = View::factory('/pages/connections/list-connections', array(
				'connections' => $connections,
				'countReceived' => $countReceived
			));
			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.connections-list_connections'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('connections', 'index'));
	}

	public function actionAcceptReceived($connection_id)
	{
        if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$connection = new Model_Connections($connection_id);
			$profile = new Model_User($connection->user_id);

			if($connection->typeApproved != 0 || $connection->friend_id != $this->user->id) {
				$this->response->redirect(Request::generateUri('connections', 'receivedInvitations'));
			}

			$tags = Model_Tags::getListByUser($this->user->id);
			$friendCheckedTags = Model_ConnectionTags::getListConnectionTagsWithName($connection->id);
			$setTags = array();

			$f_Connections_SetTagForConnection = new Form_Connections_SetTagForConnection($connection, $tags);
			foreach($friendCheckedTags['data'] as $friendcheckedTag) {
				foreach($tags['data'] as $tag) {
					if($tag->name == $friendcheckedTag->tagName) {
						$setTags[] = $tag->id;
					}
				}
			}

			$f_Connections_SetTagForConnection->setTagsById($setTags);

			$isError = false;
			if(Request::isPost()){
                if($f_Connections_SetTagForConnection->form->validate()){
					$values = $f_Connections_SetTagForConnection->form->getValues();

					Model_Connections::update(array('typeApproved' => 1), $connection->id);
					$myConnection = Model_Connections::create(array(
						'user_id' => $this->user->id,
						'friend_id' => $connection->user_id,
						'typeApproved' => 1
					));
					Model_ConnectionBan::remove(array(
						'(user_id = ? AND friend_id = ?) OR (friend_id = ? AND user_id = ?)', $connection->user_id, $this->user->id, $connection->user_id, $this->user->id
					));
					Model_User::update(array(
							'isUpdatedConnections' => 1
						), array('`id` = ? OR `id` = ?', $connection->friend_id, $connection->user_id)
					);
					// Update level Connections

                    $this->Connections_Controller ->updateConnectionsUser($_SESSION['identity']->id);

////					Model_User_Friends::addFriends($connection->friend_id, $connection->user_id);
//					Model_User::updateOneUsersCountConnections($this->user);
//					Model_User::updateOneUsersCountConnections($profile);
					Auth::getInstance()->updateIdentity($this->user->id, TRUE);
					// Set tags for connection
					foreach($values as $key => $value) {
						if(substr($key, 0, 4) == 'tags' && !empty($value) && isset($tags['data'][substr($key, 5)])) {
							Model_ConnectionTags::create(array(
								'connection_id' => $myConnection->id,
								'tag_id' => substr($key, 5),
							));
						}
					}

					$receiveds = Model_Connections::getListReceived($this->user->id);

					Model_Notifications::createNewConnectionNotification($this->user->id, $connection->user_id);
					Model_Timeline::createNewConnectionsTimeline($this->user->id, $connection->user_id);
					$countReceived = Model_Connections::getCountNewReceived($this->user->id);

					// Send notification for another user (profile)
//					$mail = new Mailer('notifications/accept_connections');
//					$mail->firstName = $profile->firstName;
//					$mail->ouser = Model_User::getItemByUserid_withoutError($this->user->id);
//					$mail->user = $profile;
//					$mail->connection_id = $connection->id;
//					$mail->send($profile->email);

					$content = View::factory('pages/connections/list-received-invitations', array(
						'receiveds' => $receiveds,
						'countReceived' => $countReceived
					));
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'changeContent',
						'data' => array(
							'content' => (string)$content,
							'target' => '.connections-list_receivedinvitations',
							'function_name' => 'negativeCount',
							'data' => array(
								'target' => '.userpanel-control a.active .userpanel-counter',
								'function_name' => 'negativeCount',
								'data' => array(
									'target' => '.connections-countreceived span'
								)
							)
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Set tags for ' . $profile->firstName . ' ' . $profile->lastName,
				'content' => View::factory('popups/connections/settagsforconnection', array(
						'profile' => $profile,
						'f_Connections_SetTagForConnection' => $f_Connections_SetTagForConnection->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}

		$_SESSION['ajaxBox_ret'] = Url::current();
		$this->response->redirect(Request::generateUri('connections', 'receivedInvitations'));
	}


	public function actionIgnoreReceived($connection_id)
	{
		$connection = new Model_Connections($connection_id);
		if($connection->typeApproved != 0 || $connection->friend_id != $this->user->id){
			$this->response->redirect(Request::generateUri('connections', 'receivedInvitations'));
		}


		Model_Connections::update(array('typeApproved' => 2), $connection->id);
		Model_ConnectionBan::create(array(
			'user_id' => $connection->user_id,
			'friend_id' => $connection->friend_id
		));

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$countReceived = Model_Connections::getCountNewReceived($this->user->id);
			if($countReceived == 0) {
				$content = View::factory('pages/connections/list-received-invitations', array(
					'countReceived' => $countReceived,
					'receiveds' => array(
						'data' => array(),
						'paginator' => array()
					),
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string)$content,
						'target' => '.connections-list_receivedinvitations',
						'function_name' => 'negativeCount',
						'data' => array(
							'target' => '.connections-leftpanel .connections-countreceived span',
							'function_name' => 'negativeCount',
							'data' => array(
								'target' => '.userpanel-control .userpanel-new_connections'
							)
						)
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => '.connections-list_receivedinvitations .list-items li[data-id="profile_' . $connection->user_id . '"]',
						'function_name' => 'negativeCount',
						'data' => array(
							'target' => '.connections-leftpanel .connections-countreceived span',
							'function_name' => 'negativeCount',
							'data' => array(
								'target' => '.userpanel-control .userpanel-new_connections'
							)
						)
					)
				));
			}

			return;
		} else {
			$this->response->redirect(Request::generateUri('connections', 'receivedInvitations'));
		}
	}

	public function actionDeleteInvitation($connection_id)
	{
		return $this->actionDiscartInvitation($connection_id);
	}

	public function actionDiscartInvitation($connection_id)
	{
		$connection = new Model_Connections($connection_id);
		if($connection->typeApproved == 1 || $connection->user_id != $this->user->id){
			$this->response->redirect(Request::generateUri('connections', 'sentInvitations'));
		}

		Model_Connections::remove($connection->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$sentInvitations = Model_Connections::getListSent($this->user->id);

			$content = View::factory('pages/connections/list-sent-invitations', array(
				'sentInvitations' => $sentInvitations,
			));
			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.connections-list_sentinvitations'
				)
			));
			return;
		} else {
			$this->response->redirect(Request::generateUri('connections', 'sentInvitations'));
		}
	}

	public function actionResentInvitation($connection_id)
	{
		$connection = new Model_Connections($connection_id);
		if($connection->typeApproved != 2 || $connection->user_id != $this->user->id){
			$this->response->redirect(Request::generateUri('connections', 'sentInvitations'));
		}

		Model_Connections::create(array(
			'user_id' => $connection->user_id,
			'friend_id' => $connection->friend_id,
			'message' => $connection->message,
			'typeApproved' => 0
		));
		Model_Connections::remove($connection->id);

		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$sentInvitations = Model_Connections::getListSent($this->user->id);

			$content = View::factory('pages/connections/list-sent-invitations', array(
				'sentInvitations' => $sentInvitations,
			));
			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.connections-list_sentinvitations'
				)
			));
			return;
		} else {
			$this->response->redirect(Request::generateUri('connections', 'sentInvitations'));
		}
	}

	public function actionInvite()
	{

		$this->view->title = 'Invite new connection';
		$this->subactive = 'invite';

		$type = Request::get('type', false);
        $Invites_Controller = new Invites_Controller();

        $followers = $Invites_Controller->index();
        $inviter = $Invites_Controller->getInviter(Auth::getInstance()->getIdentity()->id);

		$countInSearch = Model_ConnectionSearchResult::countInSearchResult($this->user->id);
		$countVisits = Model_Visits::countVisits($this->user->id);
		$connectionsMayKnow = Model_Connections::getListMayKnowConnectionsByUser($this->user->id);
		$connectionsAlsoViewed = Model_Visits::getListAlsoViewedConnectionsByUser($this->user->id);


		$view = new View('pages/connections/invite', array(
			// Left top panel
			'followers' =>  $followers,
			'inviter' => $inviter,
			'countInSearch' => $countInSearch,
			'countVisits' => $countVisits,
			'connectionsMayKnow' => $connectionsMayKnow,
			'connectionsAlsoViewed' => $connectionsAlsoViewed
		));
		$this->view->content = $view;
	}

	public function actionAddRegistered($ids = false)
	{
		$f_Connections_FindNewConnection = new Form_Connections_FindNewConnection('');
		if(Request::isPost() && isset($_FILES['file']['tmp_name'][0]) && !empty($_FILES['file']['tmp_name'][0]))
		{
			if(!$f_Connections_FindNewConnection->form->validate())
			{
				$this->message('Bad file!');
				$this->response->redirect(Request::generateUri('connections', 'invite'));
			}

			$values = $f_Connections_FindNewConnection->form->getValues();
			$file = file_get_contents($_FILES['file']['tmp_name'][0]);
			$file = preg_replace(	"/[^-.@a-z_ 0-9]/", ' ', $file);
			$file = explode('@', $file);

			$emails = array();
			$part_email = '';
			foreach($file as $item){
				if(!empty($part_email)){
					$end = strpos($item, ' ');
					$part_email .= '@' . substr($item, 0, $end);
					if (filter_var($part_email, FILTER_VALIDATE_EMAIL)) {
						$emails[trim($part_email)] = true;
					}
				}

				$start = strrpos($item, ' ', -1);
				$part_email = substr($item, $start + 1);
			}
			$users_email = $_SESSION['invite']['data'] = array_keys($emails);

		} elseif($ids && !empty($ids)){

			$connections = Model_User::getListProfile_WithoutMyConnections($this->user->id, $ids);

			foreach($connections['data'] as $connection	) {
				$connection = Model_Connections::create(array(
					'user_id' => $this->user->id,
					'friend_id' => $connection->id,
					'message' => NULL,
				));
			}

			$this->message('You send invite to ' . count($connections['data']) . ' connections on Mekooshar!');
			$this->response->redirect(Request::generateUri('connections', 'sendInvitations'));
			return;
		}

		if(!isset($_SESSION['invite']) || empty($_SESSION['invite']['data'])) {
			unset($_SESSION['invite']);
			$this->message('There are not find email(s)!');
			$this->response->redirect(Request::generateUri('connections', 'invite'));
			return;
		}

		$this->subactive = 'invite';
		$users_email = $_SESSION['invite']['data'];

		$connections = Model_User::getListNewConnectionsByUseremail($this->user->id, $users_email);

		$view = new View('pages/connections/block-invite-add_registred', array(
			'connections' => $connections
		));
		$this->view->content = $view;
	}

	public function actionSendInvitations($emails = false)
	{
		if(!isset($_SESSION['invite'])) {
			$this->response->redirect(Request::generateUri('connections', 'invite'));
		}
		$this->subactive = 'invite';


		$users_email = $_SESSION['invite']['data'];

		$is_emails = array();
		$connections = Model_User::getListConnectionsByUseremail($this->user->id, $users_email);
		foreach($connections['data'] as $connection) {
			$is_emails[] = $connection->userEmail;
		}
		$no_registered = array_diff($users_email, $is_emails);



		$old_sends = Model_InviteConnections::getByEmails($this->user->id, $no_registered);

		$is_old_send_emails = array();
		foreach($old_sends['data'] as $old_send) {
			$is_old_send_emails[] = $old_send->email;
		}
		$no_registered_no_sended = array_diff($no_registered, $is_old_send_emails);


		if($emails) {
			$emails = explode(',', $emails);
			if(!empty($emails)) {
				$sended_list = array_intersect($emails, $no_registered_no_sended);

				foreach($sended_list as $email) {
//					$mail = new Mailer('sent_invitation');
//					$mail->set('firstName', $this->user->firstName);
//					$mail->set('lastName', $this->user->lastName);
//					$mail->send($email);

					Model_InviteConnections::create(array(
						'user_id' => $this->user->id,
						'email' => $email
					));
				}

				$this->message('You send ' . count($sended_list) . ' invite(s)!');
				unset($_SESSION['invite']);
				$this->response->redirect(Request::generateUri('connections', 'invite'));
				return;
			}
		}


		$view = new View('pages/connections/block-invite-sent_invitations', array(
			'emails' => $no_registered_no_sended
		));
		$this->view->content = $view;
	}

	public function userConnections($userID)
    {
	    return Model_Connections::getConnectionsByUserId($userID);
    }

    public function updateConnectionsUser($userId)
    {
        $userConnections = Model_User::getListSearchPeople($userId, array('connection'     => '1' ))['paginator']['count'];
        $userConnections2 = Model_User::getListSearchPeople($userId, array('connection'     => '2' ))['paginator']['count'];
        $userConnections3 = Model_User::getListSearchPeople($userId, array('connection'     => '3' ))['paginator']['count'];

        Model_User::update(array('countConnections' => $userConnections , 'countConnections2' => $userConnections2, 'countConnections3' => $userConnections3),$userId);
    }


}