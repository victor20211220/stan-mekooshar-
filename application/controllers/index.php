<?php
require_once APPLICATION_PATH . 'controllers/invites.php';

class Index_Controller extends Controller_Common
{
	public function  before() {
		parent::before();

	}

	public function  after() {
		// Get gallery

//		if(!Request::getUserAgent('mobile')) {
			$item_gallery = Model_Pages::getItemByType(PAGE_TYPE_GALLERY);

			if($item_gallery) {
				$galleries = Model_Pages::getGalleries($item_gallery->text);
				$galleries_id = array_keys($galleries);
			}

			if(!empty($galleries)) {
				$gallery_items = Model_Galleryitems::getAllGalleryItems(array_shift($galleries_id));
			} else {
				$gallery_items = false;
			}
			$this->view->galleries = $gallery_items;
//		} else {
//			$this->view->galleries = false;
//		}
		parent::after();

	}

	public function __call($user_id, $args)
	{
		$tmp = Request::execute('/profile/' . $user_id . '/');
		$this->response->body = $tmp->body;

	}

	public function actionRegistration()
	{
		$this->actionIndex(true);
	}

	public function actionSignIn()
	{
		$this->actionIndex(false, true);
	}

	public function actionIndex($isRegistration = false, $isLogin = false)
	{
		if(Auth::getInstance()->hasIdentity()) {
			$url = Request::generateUri('profile', 'index');
			if(Request::$isAjax){
				$this->autoRender = false;
				$this->response->setHeader('Content-Type', 'text/json');
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => $url
					)
				));

				return false;
			} else {
				$this->response->redirect($url);
			}
		}
		$this->view->title = 'Home page';

		$f_registration = new Form_Registration();
		$f_findshort = new Form_FindShort();



		if(Request::isPost()) {
			if($isRegistration === true) {
				if($f_registration->form->validate()) {

					$values = array(
                        'email' => $_SESSION['socials']['email'],
                        'firstName' => $_SESSION['socials']['firstName'],
                        'lastName' => $_SESSION['socials']['lastName'],
                        'isConfirmed' => '1',
                        'password' => $_SESSION['socials']['email'],
                        'role'  => 'user',
                        'accountType' => 1, // GOLD ACCOUNT
                        'city' => '',
                        'state' => '',
                        'zip' => '',
                        'country' => '',
                        'phone' => '',
                    );

 //					$values['isConfirmed'] = 0;
					$cnt = 0;
					while (true === Model_User::exists('token', ($token = Text::random('alphanuml', 32))) && $cnt < 10) {
						$cnt++;
					}
					if ($cnt == 10) {
						throw new ForbiddenException('Can not create token.');
					}
					$values['token'] = $token;
					$password = md5(mt_rand() . time());
					$values['password'] = Model_User::encryptPassword($values['password']);
                    //add follower_id to invite_by_key


					$user = Model_User::create($values);


					//Add follower to invite_by_ket
                    $invite = new Invites_Controller();

                    $inviteModel = $invite->getByKey($_SESSION['inviteKey']['key']);

                    $invite->addFollower($inviteModel['id'], $user->id);

                    $invite->addConnection( $user->id, (integer)$inviteModel['user_invite_id']);

                    $invite->addConnection((integer)$inviteModel['user_invite_id'], $user->id );

					// Create default tags for connections
					$tags = t('connections_default_tags');
					foreach($tags as $key => $name) {
						Model_Tags::create(array(
							'name' => $name,
							'user_id' => $user->id
						));
					}




                    $auth = new Auth();


                    $status = $auth->authenticateWithoutPassword($user->email, true, true);

                    $ava = new Model_Files();
                    $uploadFromUrl['url'] = $_SESSION['socials']['url'];

                    $ava->upload(FILE_USER_AVA, 0, $user, false, null, $uploadFromUrl);
//
//                    if($status) {
//                        unset($_SESSION['socials']);
////                        $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->login->successredirect);
//                    }
// else {

					if(Request::$isAjax){
						$f_registration->form->clearValues();
						//todo check this (user agreement field in form)
						$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_REGISTRATION_PEOPLE);
						if($useOfTerm) {
							$useOfTerm = $useOfTerm->text;
							$f_registration->setUseOfTerms($useOfTerm);
						}
						$this->autoRender = false;
						$this->response->setHeader('Content-Type', 'text/json');
						$this->response->body = json_encode(array(
							'status' => true,
							'redirect_url' => 'https://' . Request::$host . $this->config->socials->facebook->login->successredirect,
//							'content' => (string) new View('pages/home-form', array('f_registration' => $f_registration->form)),
//							'function_name' => 'popupShow',
//							'data' => array(
//								'title' => 'Message',
//								'content' => 'Thank you for registering! To confirm your registration, please follow the link sent to you at your email.'
//							)
						));

						return false;
					}
				} else {
					if(Request::$isAjax){
						$this->autoRender = false;
						//todo check this (user agreement field in form)
						$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_REGISTRATION_PEOPLE);
						if($useOfTerm) {
							$useOfTerm = $useOfTerm->text;
							$f_registration->setUseOfTerms($useOfTerm);
						}
						$this->response->setHeader('Content-Type', 'text/json');
						$this->response->body = json_encode(array(
							'status' => true,
							'content' => (string) new View('pages/home-form', array('f_registration' => $f_registration->form))
						));

						return false;
					}
				}


			} elseif ($isLogin === TRUE) {
				$f_login = new Form_Login();
				if($f_login->form->validate()){

					$user = Auth::getInstance()->getIdentity();
					if(in_array($user->role, array(USER_TYPE_WEBADMIN, USER_TYPE_WEBROOT))) {
						$url = Request::generateUri('admin', 'index');
					} else {
						$url = Request::generateUri('updates', 'index');
					}

					if(Request::$isAjax){
						$f_login->form->clearValues();

						$this->autoRender = false;
						$this->response->setHeader('Content-Type', 'text/json');
						$this->response->body = json_encode(array(
							'status' => true,
							'function_name' => 'redirect',
							'data' => array(
								'url' => $url
							)
						));

						return false;
					} else {
						$this->response->redirect($url);
					}
				} else {
					if(Request::$isAjax){
						$this->autoRender = false;
						$this->response->setHeader('Content-Type', 'text/json');
						$this->response->body = json_encode(array(
							'status' => true,
							'content' => (string) $f_login->form
						));

						return false;
					}
				}


			}else{
				if($f_findshort->form->validate()) {

				} else {

				}
			}
		}

		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_HOME);
		$galleries = Model_Pages::getGalleries($page->text);
		if(count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			foreach($images as $key => $image) {
				$info = unserialize($image->info);

				if(!isset($info['memberName']) || empty($info['memberName'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberTitle']) || empty($info['memberTitle'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberDescription']) || empty($info['memberDescription'])) {
					unset($images[$key]);
				}
			}
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_aboutus', $images);
		}


		$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_REGISTRATION_PEOPLE);
		if($useOfTerm) {
			$useOfTerm = $useOfTerm->text;
			$f_registration->setUseOfTerms($useOfTerm);
		}



		$view = New View('pages/home', array(
			'page' => $page,
			'f_registration' => $f_registration
		));
		$view->f_findshort = $f_findshort->form;

		$this->view->content = $view;
		$this->view->active = 'home';

		$this->view->f_registration = $f_registration->form;
//		$this->view->galleries = $gallery_items;
	}

	public function actionPolicy()
	{
		$this->view->title = 'Privacy policy';
		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_POLICY);

		$galleries = Model_Pages::getGalleries($page->text);
		if(count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			foreach($images as $key => $image) {
				$info = unserialize($image->info);

				if(!isset($info['memberName']) || empty($info['memberName'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberTitle']) || empty($info['memberTitle'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberDescription']) || empty($info['memberDescription'])) {
					unset($images[$key]);
				}
			}
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_aboutus', $images);
		}

		$view = New View('pages/policy', array(
			'page' => $page
		));
		$this->view->content = $view;
		$this->view->active = 'policy';
	}


	/**
	 * Page: Our Team
	 */
	public function actionTeam()
	{
		$this->view->title = 'Our team';

		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_OURTEAM);

		$galleries = Model_Pages::getGalleries($page->text);
		if (count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			foreach ($images as $key => $image) {
				$info = unserialize($image->info);

				if (!isset($info['memberName']) || empty($info['memberName'])) {
					unset($images[$key]);
				}
				if (!isset($info['memberTitle']) || empty($info['memberTitle'])) {
					unset($images[$key]);
				}
				if (!isset($info['memberDescription']) || empty($info['memberDescription'])) {
					unset($images[$key]);
				}
			}
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_aboutus', $images);
		}


		$view = New View('pages/team', array(
			'page' => $page
		));
		$this->view->content = $view;
		$this->view->active = 'team';
	}


	public function actionAbout()
	{
		$this->view->title = 'About us';
		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_ABOUTUS);

		$galleries = Model_Pages::getGalleries($page->text);
		if(count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			foreach($images as $key => $image) {
				$info = unserialize($image->info);

				if(!isset($info['memberName']) || empty($info['memberName'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberTitle']) || empty($info['memberTitle'])) {
					unset($images[$key]);
				}
				if(!isset($info['memberDescription']) || empty($info['memberDescription'])) {
					unset($images[$key]);
				}
			}
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_aboutus', $images);
		}


		$view = New View('pages/about', array(
			'page' => $page
		));
		$this->view->content = $view;
		$this->view->active = 'about';
	}


	public function actionAdvertiseWithUs()
	{
		$this->view->title = 'Advertise with us';
		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_ADVERTISE_WITH_US);

		$f_AdvertiseWithUs = new Form_AdvertiseWithUs();

		if(Request::isPost()) {
			if($f_AdvertiseWithUs->form->validate()) {
				$values = $f_AdvertiseWithUs->form->getValues();

				$admin_email = System::$global->settings['email'];

				$mail = new Mailer('advertise_with_us');
				$mail->set($values);
				$mail->send($admin_email);

				$message = 'Message is sent!';
				$this->message($message);
				$f_AdvertiseWithUs->form->clearValues();
			}
		}

		$galleries = Model_Pages::getGalleries($page->text);
		if(count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_advertise_with_us', $images);
		}


		$view = New View('pages/advertise_with_us', array(
			'page' => $page,
			'f_AdvertiseWithUs' => $f_AdvertiseWithUs
		));
		$this->view->content = $view;
		$this->view->active = 'advertise_with_us';
	}

	public function actionSupport()
	{
		$this->view->title = 'Support';
		$page = Model_Pages::getItemByCategory(PAGE_CATEGORY_SUPPORT);

		$f_Support = new Form_Support();

		if(Request::isPost()) {
			if($f_Support->form->validate()) {
				$values = $f_Support->form->getValues();

				$support_email = System::$global->settings['email'];
//				$support_email = System::$global->settings['support-email'];

				$mail = new Mailer('support');
				$mail->set($values);
				$mail->send($support_email);

				$message = 'Message is sent!';
				$this->message($message);
				$f_Support->form->clearValues();
			}
		}

		$galleries = Model_Pages::getGalleries($page->text);
		if(count($galleries) != 0) {
			$images = Model_Files::getByParentId(array_keys($galleries), FILE_PAGES, true);
			$page->text = Model_Pages::replaceGallery($page->text, 'parts/galleries/gallery_advertise_with_us', $images);
		}


		$view = New View('pages/support', array(
			'page' => $page,
			'f_Support' => $f_Support
		));
		$this->view->content = $view;
		$this->view->active = 'support';
	}

	public function actionSearchPeople()
	{
		$this->title = 'Search people';

		$peoplename = false;
		$f_findshort = new Form_FindShort();
		$f_findshort->onFindPage();
		if(Request::isPost()) {
			if($f_findshort->form->validate()) {
				$values = $f_findshort->form->getValues();
				$peoplename1 = HTML::chars(trim($values['firstName']));
				$peoplename2 = HTML::chars(trim($values['lastName']));
				$peoplename = $peoplename1 . ' ' . $peoplename2;
			}
		}

//		$f_FindPanel = new Form_FindPanel();
//		$f_FindPanel->setFindType('people');
//		if(Request::isPost()) {
//			if($f_FindPanel->form->validate()){
//				$values = $f_FindPanel->form->getValues();
//				$peoplename = $values['searchpeople'];
//			}
//		}

		if(Request::get('searchpeople', false)){
			$peoplename = Request::get('searchpeople', false);

			$tmp = explode(' ', $peoplename);
			$peoplename1 = $tmp[0];
			unset($tmp[0]);
			$peoplename2 = implode(' ', $tmp);
		} elseif(!$peoplename || $peoplename == ' '){
			$peoplename1 = HTML::chars(trim(Request::get('firstName', false)));
			$peoplename2 = HTML::chars(trim(Request::get('lastName', false)));
			$peoplename = $peoplename1 . ' ' . $peoplename2;
		}

		$query = array(
//			'connection' => Request::get('connection', false),
			'region' => Request::get('region', false),
			'company' => Request::get('company', false),
			'industrypeople' => Request::get('industrypeople', false),
			'school' => Request::get('school', false),
			'firstName' => $peoplename1,
			'lastName' => $peoplename2
		);
		$_SESSION['search']['peoplequery'] = $query;

		if($peoplename && $peoplename != ' ') {
			$results = Model_User::getListSearchPeople(-1, $query);
			Model_ConnectionSearchResult::insertShowResult($results);
		} else {
			$results = array('data' => array(), 'paginator' => array('count' => 0));
		}

		unset($_GET['searchpeople'], $_GET['region'], $_GET['company'], $_GET['industrypeople'], $_GET['school'], $_GET['searchpeople'], $_GET['firstName'], $_GET['lastName']);

		$companies = array('data' => array());
		$regions = array('data' => array());
		$industry = array('data' => array());
		$university = array('data' => array());

		$f_Search_FilterPeople = new Form_Search_FilterPeople($peoplename);
		$f_Search_FilterPeople->setForNoRegistredUser();
//		$f_Search_FilterPeople->generateConnection($query['connection']);
		$f_Search_FilterPeople->generateCompany($companies, $query['company']);
		$f_Search_FilterPeople->generateIndustry($industry, $query['industrypeople']);
		$f_Search_FilterPeople->generateRegion($regions, $query['region']);
		$f_Search_FilterPeople->generateSchool($university, $query['school']);

		$view = View::factory('parts/parts-right_big', array(
			'left' => View::factory('pages/search_people_as_public/menu-filter', array(
				'f_Search_FilterPeople' => $f_Search_FilterPeople,
				'query' => $query
			)),
			'right' => View::factory('pages/search_people_as_public/list-search-results', array(
				'results' => $results,
				'f_findshort' => $f_findshort,
				'query' => $query
			))
		));
		$this->view->content = $view;
	}


	public function actionAddSearchRegion()
	{
		if (Request::$isAjax) {
			$this->autoRender = FALSE;
			$this->response->setHeader('Content-Type', 'text/json');

			$without_region = array();
			if (isset($_SESSION['search']['people']['region'])) {
				$without_region = array_merge($without_region, array_keys($_SESSION['search']['people']['region']));
			}

			$f_Search_People_AddField = new Form_Search_People_AddField();
			$isSet = $f_Search_People_AddField->generateRegion($without_region, Request::generateUri('index', 'AddSearchRegion'));

			if (!$isSet) {
				$message = 'You can not add more region!';
				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Message',
					'content' => View::factory('popups/message', array(
						'message' => $message
					))
				));

				$this->response->body = json_encode(array(
					'status'  => TRUE,
					'content' => (string)$content
				));

				return;
			}

			$isError = FALSE;
			if (Request::isPost()) {
				if ($f_Search_People_AddField->form->validate()) {
					$values = $f_Search_People_AddField->form->getValues();

					$countries = t('countries');
					if (isset($countries[$values['region']])) {
						$_SESSION['search']['people']['region'][$values['region']] = $countries[$values['region']];
					} else {
						$_SESSION['search']['people']['region'][$values['region']] = $values['region'];
					}

					if (!isset($_SESSION['search']['peoplequery'])) {
						$query = array(
							'region' => array()
						);
					} else {
						$query = $_SESSION['search']['peoplequery'];
					}

					$f_Search_FilterPeople = new Form_Search_FilterPeople('');
					$f_Search_FilterPeople->setForNoRegistredUser();
					$f_Search_FilterPeople->generateRegion(array('data' => array()), $query['region']);

					ob_start();
					$f_Search_FilterPeople->form->render('region');
					$content = ob_get_clean();

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'changeContent',
						'data'          => array(
							'content' => (string)$content,
							'target'  => '#' . $f_Search_FilterPeople->form->attributes['id'] . '-fieldset-region'
						)
					));

					return;
				} else {
					$isError = TRUE;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title'   => 'Add region name to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
					'form' => $f_Search_People_AddField->form
				))
			));

			$this->response->body = json_encode(array(
				'status'  => (!$isError),
				'content' => (string)$content
			));

			return;
		}

		$this->response->redirect(Request::generateUri('index', 'SearchPeople'));
	}


	public function actionAddSearchCompany()
	{
		if (Request::$isAjax) {
			$this->autoRender = FALSE;
			$this->response->setHeader('Content-Type', 'text/json');

			$without_companies = array();
			if (isset($_SESSION['search']['people']['company'])) {
				$without_companies = array_merge($without_companies, array_keys($_SESSION['search']['people']['company']));
			}

			$f_Search_People_AddField = new Form_Search_People_AddField();
			$isSet = $f_Search_People_AddField->generateCompany($without_companies, Request::generateUri('index', 'AddSearchCompany'));

			if (!$isSet) {
				$message = 'You can not add more company!';
				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Message',
					'content' => View::factory('popups/message', array(
						'message' => $message
					))
				));

				$this->response->body = json_encode(array(
					'status'  => TRUE,
					'content' => (string)$content
				));

				return;
			}

			$isError = FALSE;
			if (Request::isPost()) {
				if ($f_Search_People_AddField->form->validate()) {
					$values = $f_Search_People_AddField->form->getValues();

					$company = Model_Companies::getByName($values['company']);
					if (!$company) {
						$university = Model_Universities::getByName($values['company']);

						if (!$university) {
							$company = Model_Companies::create(array('name' => $values['company']));
						}
					}

					if ($company) {
						$id = 'c' . $company->id;
						$name = $company->name;
					} else {
						$id = 'u' . $university->id;
						$name = $university->name;
					}

					$_SESSION['search']['people']['company'][$id] = $name;

					if (!isset($_SESSION['search']['peoplequery'])) {
						$query = array(
							'company' => array()
						);
					} else {
						$query = $_SESSION['search']['peoplequery'];
					}

					$f_Search_FilterPeople = new Form_Search_FilterPeople('');
					$f_Search_FilterPeople->setForNoRegistredUser();
					$f_Search_FilterPeople->generateCompany(array('data' => array()), $query['company']);

					ob_start();
					$f_Search_FilterPeople->form->render('company');
					$content = ob_get_clean();

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'changeContent',
						'data'          => array(
							'content' => (string)$content,
							'target'  => '#' . $f_Search_FilterPeople->form->attributes['id'] . '-fieldset-company'
						)
					));

					return;
				} else {
					$isError = TRUE;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title'   => 'Add company name to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
					'form' => $f_Search_People_AddField->form
				))
			));

			$this->response->body = json_encode(array(
				'status'  => (!$isError),
				'content' => (string)$content
			));

			return;
		}

		$this->response->redirect(Request::generateUri('index', 'SearchPeople'));
	}



	public function actionAddSearchIndustry()
	{
		if (Request::$isAjax) {
			$this->autoRender = FALSE;
			$this->response->setHeader('Content-Type', 'text/json');

			$without_industry = array();
			if (isset($_SESSION['search']['people']['industry'])) {
				$without_industry = array_merge($without_industry, array_keys($_SESSION['search']['people']['industry']));
			}

			$f_Search_People_AddField = new Form_Search_People_AddField();
			$isSet = $f_Search_People_AddField->generateIndustry($without_industry, Request::generateUri('index', 'AddSearchIndustry'));

			if (!$isSet) {
				$message = 'You can not add more industry!';
				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Message',
					'content' => View::factory('popups/message', array(
						'message' => $message
					))
				));

				$this->response->body = json_encode(array(
					'status'  => TRUE,
					'content' => (string)$content
				));

				return;
			}

			$isError = FALSE;
			if (Request::isPost()) {
				if ($f_Search_People_AddField->form->validate()) {
					$values = $f_Search_People_AddField->form->getValues();

					$_SESSION['search']['people']['industry'][$values['industry']] = t('industries.' . $values['industry']);

					if (!isset($_SESSION['search']['peoplequery'])) {
						$query = array(
							'industrypeople' => array()
						);
					} else {
						$query = $_SESSION['search']['peoplequery'];
					}
//					dump($query, 1);
					$f_Search_FilterPeople = new Form_Search_FilterPeople('');
					$f_Search_FilterPeople->setForNoRegistredUser();
					$f_Search_FilterPeople->generateIndustry(array('data' => array()), $query['industrypeople']);

					ob_start();
					$f_Search_FilterPeople->form->render('industry');
					$content = ob_get_clean();

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'changeContent',
						'data'          => array(
							'content' => (string)$content,
							'target'  => '#' . $f_Search_FilterPeople->form->attributes['id'] . '-fieldset-industry'
						)
					));

					return;
				} else {
					$isError = TRUE;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title'   => 'Add industry name to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
					'form' => $f_Search_People_AddField->form
				))
			));

			$this->response->body = json_encode(array(
				'status'  => (!$isError),
				'content' => (string)$content
			));

			return;
		}

		$this->response->redirect(Request::generateUri('index', 'SearchPeople'));
	}



	public function actionAddSearchSchool()
	{
		if (Request::$isAjax) {
			$this->autoRender = FALSE;
			$this->response->setHeader('Content-Type', 'text/json');


			$without_school = array();
			if (isset($_SESSION['search']['people']['school'])) {
				$without_school = array_merge($without_school, array_keys($_SESSION['search']['people']['school']));
			}

			$f_Search_People_AddField = new Form_Search_People_AddField();
			$isSet = $f_Search_People_AddField->generateSchool($without_school, Request::generateUri('index', 'AddSearchSchool'));

			if (!$isSet) {
				$message = 'You can not add more schools!';
				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Message',
					'content' => View::factory('popups/message', array(
						'message' => $message
					))
				));

				$this->response->body = json_encode(array(
					'status'  => TRUE,
					'content' => (string)$content
				));

				return;
			}

			$isError = FALSE;
			if (Request::isPost()) {
				if ($f_Search_People_AddField->form->validate()) {
					$values = $f_Search_People_AddField->form->getValues();

					$university = Model_Universities::getByName($values['school']);
					if (!$university) {
						$university = Model_Universities::create(array('name' => $values['school']));
					}

					$_SESSION['search']['people']['school'][$university->id] = $university->name;

					if (!isset($_SESSION['search']['peoplequery'])) {
						$query = array(
							'school' => array()
						);
					} else {
						$query = $_SESSION['search']['peoplequery'];
					}

					$f_Search_FilterPeople = new Form_Search_FilterPeople('');
					$f_Search_FilterPeople->setForNoRegistredUser();
					$f_Search_FilterPeople->generateSchool(array('data' => array()), $query['school']);

					ob_start();
					$f_Search_FilterPeople->form->render('school');
					$content = ob_get_clean();

					$this->response->body = json_encode(array(
						'status'        => TRUE,
						'function_name' => 'changeContent',
						'data'          => array(
							'content' => (string)$content,
							'target'  => '#' . $f_Search_FilterPeople->form->attributes['id'] . '-fieldset-school'
						)
					));

					return;
				} else {
					$isError = TRUE;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title'   => 'Add school name to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
					'form' => $f_Search_People_AddField->form
				))
			));

			$this->response->body = json_encode(array(
				'status'  => (!$isError),
				'content' => (string)$content
			));

			return;
		}

		$this->response->redirect(Request::generateUri('index', 'SearchPeople'));
	}

	public function createUser()
    {
        Model_Users::insert([
            'email' => $_SESSION['socials']['email'],
            'firstName' => $_SESSION['socials']['firstName'],
            'lastName' => $_SESSION['socials']['lastName'],
            'isConfirmed' => '1',
        ]);
    }


}