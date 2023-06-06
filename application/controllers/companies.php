<?php

class Companies_Controller extends Controller_User
{

	protected $subactive = 'companies';

	public function  before() {
		parent::before();
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/uploader.js');
	}

	public function __call($action, $params)
	{
		$this->actionIndex($action);
	}


	public function actionIndex($company_id = false)
	{
		if(!$company_id) {
			$this->response->redirect(Request::generateUri('companies', 'updates'));
			die();
		}

		$company = Model_Companies::getItemById($company_id, $this->user->id);

		if($company->isAgree == 0) {
			$this->response->redirect(Request::generateUri('companies', 'edit', $company->id));
			die();
		}

		$timelinesCompany = Model_Timeline::getListByUserIdCompanyId($this->user->id, $company->id);
		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($timelinesCompany['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesCompany['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-list-updates > .list-items > li:last-child'
				)
			));
			return;
		}




		if($company->user_id != $this->user->id) {
			Model_Visits::create(array(
				'user_id' => $this->user->id,
				'company_id' => $company->id
			));
			$f_Updates_AddUpdate = false;
		} else {
			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->setUpdateFromCompany($company->id);

		}

		$peopleAlsoViewed = Model_Visits::getListCompanyAlsoViewedConnectionsByUser($this->user->id);

		$this->view->title = 'View company "' . $company->name . '"';

		$view = new View('pages/companies/index', array(
			// Left top panel
			'company' => $company,

			// Left down panel
			'company' => $company,
			'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
			'timelinesCompany' => $timelinesCompany,

			// Right panel
			'peopleAlsoViewed' => $peopleAlsoViewed
		));
		$this->view->content = $view;

	}

	public function actionAnalytics($company_id)
	{
		$company = Model_Companies::getItemById($company_id, $this->user->id);

		if($company->isAgree == 0) {
			$this->response->redirect(Request::generateUri('companies', 'edit', $company->id));
			die();
		}

		if($company->user_id != $this->user->id) {
			$this->response->redirect(Request::generateUri('companies', 'updates'));
		}

		$companyUpdates = Model_Posts::getListByCompanyid($company->id);

		if(Request::get('page', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($companyUpdates['data'] as $post) {
				$view .= View::factory('pages/companies/item-analytic_update', array(
					'post' => $post
				));
			}
			$view .= '<li collspan="5">' . View::factory('common/default-pages', array(
							'isBand' => TRUE,
							'autoScroll' => FALSE
							) + $companyUpdates['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-company_analytics > .list-items > li:last-child'
				)
			));
			return;
		}


		$impressions = Model_Company_Post_Impressions::getListMonthStatistic($company->id);
		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
		}
		foreach($impressions['data'] as $impression){
			$tmp[$impression->id] += $impression->countItems;
		}
		$impressions = $tmp;


		$impressionsUnique = Model_Company_Post_Impressions::getListMonthStatisticUnique($company->id);
		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
		}
		foreach($impressionsUnique['data'] as $impression){
			$tmp[$impression->id] += $impression->countItems;
		}
		$impressionsUnique = $tmp;



		$clicks = Model_Company_Post_Clicks::getListMonthStatistic($company->id);
		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
		}
		foreach($clicks['data'] as $click){
			$tmp[$click->id] += $click->countItems;
		}
		$clicks = $tmp;




		$likes = Model_Company_Post_Likes::getListMonthStatistic($company->id);
		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
		}
		foreach($likes['data'] as $like){
			$tmp[$like->id] += $like->countItems;
		}
		$likes = $tmp;




		$comments = Model_Company_Post_Comments::getListMonthStatistic($company->id);
		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
		}
		foreach($comments['data'] as $comment){
			$tmp[$comment->id] += $comment->countItems;
		}
		$comments = $tmp;



		$tmp = array();
		for($i = 5; $i>=0; $i--) {
			$key = date('Y-m-01', time() - 60*60*24*31*$i);
			if($impressions[$key] != 0) {
				$tmp[$key] = round(($clicks[$key] + $likes[$key] + $comments[$key]) / 3 / $impressions[$key] * 100, 2);
			} else {
				$tmp[$key] = 0;
			}

		}
		$engagement = $tmp;


		$compareCompanies = Model_Companies::getListCompareCompanies($this->user->id, $company->industry, $company->size);


		$this->view->title = 'Company analytics "' . $company->name . '"';
		$view = new View('pages/companies/analytics', array(
			// Left top panel
			'company' => $company,

			// Left down panel
			'companyUpdates' => $companyUpdates,

			'impressionsUnique' => $impressionsUnique,
			'impressions' => $impressions,
			'clicks' => $clicks,
			'likes' => $likes,
			'comments' => $comments,
			'engagement' => $engagement,

			'compareCompanies' => $compareCompanies

			// Right panel
		));

		$this->view->script('http://www.google.com/jsapi');
		$this->view->content = $view;


	}


	public function actionFollowers($company_id)
	{
		$company = Model_Companies::getItemById($company_id, $this->user->id);

		if($company->isAgree == 0) {
			$this->response->redirect(Request::generateUri('companies', 'edit', $company->id));
		}

		if(is_null($company->followUserId)) {
			$this->response->redirect(Request::generateUri('companies', $company->id));
		}

		$followers = Model_Company_Follow::getListByCompanyId($company->id);
		$peopleAlsoViewed = Model_Visits::getListCompanyAlsoViewedConnectionsByUser($this->user->id);


		$this->view->title = 'Followers company "' . $company->name . '"';

		$view = new View('pages/companies/followers', array(
			// Left top panel
			'company' => $company,

			// Left down panel
			'company' => $company,
			'followers' => $followers,

			// Right panel
			'peopleAlsoViewed' => $peopleAlsoViewed
		));
		$this->view->content = $view;

	}

	public function actionUpdates()
	{
		$this->view->title = 'Companies updates';

		$followCompanies = Model_Company_Follow::getListCompanyIdByUserId($this->user->id);
		$companiesKey = array_keys($followCompanies['data']);

		$timelinesCompanies= false;
		if(!empty($companiesKey)) {
			$timelinesCompanies = Model_Timeline::getListByUserIdCompanyId($this->user->id, $companiesKey);
		}


		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($timelinesCompanies['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesCompanies['paginator']) . '</li>';

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.block-list-updates > .list-items > li:last-child'
				)
			));
			return;
		}


		$myCompanies = Model_Companies::getListByuserId($this->user->id);
		$youRecentlyVisit = Model_Visits::getListMyVisitsCompanies($this->user->id);

		$view = new View('pages/companies/updates', array(
			// Left top panel

			// Left down panel
			'timelinesCompanies' => $timelinesCompanies,

			// Right panel
			'myCompanies' => $myCompanies,
			'youRecentlyVisit' => $youRecentlyVisit
		));
		$this->view->content = $view;

	}

	public function actionFollowing()
	{
		$this->view->title = 'Companies following';

		$myCompanies = Model_Companies::getListByuserId($this->user->id);
		$youRecentlyVisit = Model_Visits::getListMyVisitsCompanies($this->user->id);
		$myFollowing = Model_Company_Follow::getListByUserId($this->user->id);

		$view = new View('pages/companies/following', array(
			// Left top panel

			// Left down panel
			'myFollowing' => $myFollowing,

			// Right panel
			'myCompanies' => $myCompanies,
			'youRecentlyVisit' => $youRecentlyVisit
		));
		$this->view->content = $view;

	}


	public function actionCreateCompany()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Companies_CreateCompany = new Form_Companies_CreateCompany();

			$isError = false;
			if(Request::isPost()){
				if($f_Companies_CreateCompany->form->validate()){
					$values = $f_Companies_CreateCompany->form->getValues();

					$company = Model_Companies::getByName($values['companyName']);
					if(!$company) {
						$company = Model_Companies::create(array(
							'name' => $values['companyName'],
                            'user_id' => $this->user->id,
                            'isAgree' => 1
						));
					}

					$confirm = serialize(array(
						'email' => $values['email'],
						'company_id' => $company->id
					));
					Confirmations::generate($this->user->id, Confirmations::USER, Confirmations::CREATECOMPANY, $confirm);

                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'redirect',
                        'data' => array(
                            'url' => Request::generateUri('companies', 'edit', $company->id)
                        )
                    ));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Create company page',
				'content' => View::factory('popups/companies/createcompany', array(
						'f_Companies_CreateCompany' => $f_Companies_CreateCompany->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content,
				'popupsize' => 'message'
			));
			return;
		}

		$this->response->redirect(Request::generateUri('companies', 'index'));
	}

	public function actionEdit($company_id)
	{
		$this->view->title = 'Edit company';
		$company = Model_Companies::getUserIdCompanyId($this->user->id, $company_id);

		$f_Companies_EditCompany = new Form_Companies_EditCompany($company);


		$isError = false;
		if(Request::isPost()) {
			if($f_Companies_EditCompany->form->validate()) {
				$values = $f_Companies_EditCompany->form->getValues();


				if($values['name'] == $company->name) {
					unset($values['name']);
					Model_Companies::update($values, $company->id);
					$company_id2 = $company->id;
				} else {
					$newCompany = Model_Companies::getByName($values['name']);

					if(!$newCompany) {
						$newCompany = Model_Companies::create(array(
							'name' => $values['name']
						));
					}

					$old_name = $company->name;
					$new_name = $newCompany->name;

					$newCompany->name = $old_name;
					$newCompany->save();
					$company->name = $new_name;
					$company->save();

					$company_id2 = $company->id;

					$isComnanies = Model_Profile_Experience::isInExperienceCompany($company->id, $newCompany->id);

					if(count($isComnanies['data']) == 2) {
						Model_Profile_Experience::changePlacesCompanyId($company->id, $newCompany->id);
					}
					if(count($isComnanies['data']) == 1) {
						if(isset($isComnanies['data'][$company->id])) {
							Model_Profile_Experience::update(array(
								'company_id' => $newCompany->id
							), array('company_id = ?', $company->id));
						}
						if(isset($isComnanies['data'][$newCompany->id])) {
							Model_Profile_Experience::update(array(
								'company_id' => $company->id
							), array('company_id = ?', $newCompany->id));
						}

					}
				}

				$this->message('Changes have been saved');
				$this->response->redirect(Request::generateUri('companies', $company_id2));

			} else {
				$isError = true;
			}
		} else {
			$f_Companies_EditCompany->setValues($company);
		}

		if(!$company->isAgree) {
			$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_CREATE_COMPANY);
			if($useOfTerm) {
				$useOfTerm = $useOfTerm->text;
				$f_Companies_EditCompany->setUseOfTerms($useOfTerm);
			}
		}


		$view = new View('pages/companies/edit', array(
			'f_Companies_EditCompany' => $f_Companies_EditCompany
		));

		$this->view->content = $view;
	}


	public function actionRemove($company_id)
	{
		$this->view->title = 'Remove company';
		$company = Model_Companies::getUserIdCompanyId($this->user->id, $company_id);


		Model_Companies::update(array(
			'user_id' => NULL,
			'createDate' => NULL,
			'domain' => NULL,
			'email' => NULL,
			'email2' => NULL,
			'url' => NULL,
			'year' => NULL,
			'type' => NULL,
			'size' => NULL,
			'phone' => NULL,
			'address' => NULL,
			'industry' => NULL,
			'companyStatus' => NULL,
			'description' => NULL,
			'avaToken' => NULL,
			'coverToken' => NULL,
			'isAgree' => 0
		), $company->id);

		Model_Company_Follow::remove(array('company_id = ?', $company->id));
		Model_Visits::remove(array('company_id = ?', $company->id));

		$this->message('Company has been succesfully removed!');
		$this->response->redirect(Request::generateUri('companies', 'updates'));
	}

	public function actionFollow($company_id)
	{
		$this->view->title = 'Follow/unfollow company';

		$company = $this->follow($company_id);

		$this->response->redirect(Request::generateUri('companies', $company->id));
	}

	public function actionFollowFromList($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = $this->follow($company_id);

			$view = View::factory('parts/companiesava-more', array(
				'company' => $company,
				'avasize' => 'avasize_52',
				'isCompanyNameLink' => TRUE,
				'isCompanyIndustry' => TRUE,
				'isFollowButton' => TRUE
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeInnerContent',
				'data' => array(
					'target' => 'li[data-id="company_' . $company->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('companies', $company_id));
	}

	public function actionFollowFromBlock($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = $this->follow($company_id);

			$view = View::factory('pages/companies/item-following', array(
				'company' => $company
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeInnerContent',
				'data' => array(
					'target' => 'li[data-id="company_block_' . $company->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('companies', $company_id));
	}

	public function actionFollowFromSearch($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = $this->follow($company_id);

			$view = View::factory('pages/search/company/item-search-results', array(
				'company' => $company
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'target' => 'li[data-id="company_' . $company->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('companies', $company_id));
	}

	protected function follow($company_id)
	{
		$company = Model_Companies::getItemById($company_id, $this->user->id);

		if(!is_null($company->followUserId)) {
			Model_Company_Follow::remove(array('company_id = ? AND user_id = ?', $company->id, $this->user->id));
			$company->followers -= 1;
			$company->save();
			$company->followUserId = NULL;
		} else {
			Model_Company_Follow::create(array(
				'company_id' => $company->id,
				'user_id' => $this->user->id
			));
			$company->followers += 1;
			$company->save();
			$company->followUserId = $this->user->id;
		}

		return $company;
	}

	public function actionCropAva($company_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = Model_Companies::getItemById($company_id, $this->user->id);

			$image = Model_Files::getByToken($company->avaToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('companies', 'edit', $company_id));
	}

	public function actionCropCover($company_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = Model_Companies::getItemById($company_id, $this->user->id);

			$image = Model_Files::getByToken($company->coverToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('companies', 'edit', $company_id));
	}

	public function actionRemoveAva($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = Model_Companies::getItemById($company_id, $this->user->id);
			Model_Files::removeByType($company->id, FILE_COMPANY_AVA);

			$company->avaToken = NULL;
			$company->save();

			$content = View::factory('pages/companies/block-ava_logo', array(
				'company' => $company
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-companyava'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', 'edit', $company_id));
	}

	public function actionRemoveCover($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = Model_Companies::getItemById($company_id, $this->user->id);
			Model_Files::removeByType($company->id, FILE_COMPANY_COVER);

			$company->coverToken = NULL;
			$company->save();

			$content = View::factory('pages/companies/block-ava_cover', array(
				'company' => $company
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-companycover'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('profile', 'edit', $company_id));
	}

	public function actionUpdate($company_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$company = Model_Companies::getItemById($company_id, $this->user->id);

			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();

			$isError = false;
			if(Request::isPost()) {
				if($f_Updates_AddUpdate->form->validate()) {
					$timeline = Updates::newUpdate($f_Updates_AddUpdate, $this->user, $company);

//					$timeline = Model_Timeline::getItemById($timeline->id, $this->user->id, $company->id);
//					Model_Posts::update(array(
//						'company_id' => $company_id
//					), $timeline->post_id);

					$f_Updates_AddUpdate->form->clearValues();

					$newTimeline = Model_Timeline::getItemById($timeline->id, $this->user->id, $company->id);
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
				} else {
					$isError = true;
				}
			}

			if($isError) {
//				$content = View::factory('pages/updates/block-create-updates', array(
//					'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
//					'initUploader' => FALSE
//				));
//
//				$this->autoRender = false;
//				$this->response->setHeader('Content-Type', 'text/json');
//				$this->response->body = json_encode(array(
//					'status' => true,
//					'function_name' => 'submitError',
//					'data' => array(
//						'content' => (string)$content,
//						'target' => '.block-create-updates'
//					)
//				));
			}
		}
		$this->response->redirect(Request::generateUri('companies', $company_id));
	}
}