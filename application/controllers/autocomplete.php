<?php

class AutoComplete_Controller extends Controller_User
{

	protected $subactive = '';

	public function  before() {
		parent::before();
	}

	public function actionGetListSkills($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);

			$results = Model_Skills::getList_OrderCountUsed($text);

			if(isset($_COOKIE['search_jobs_skills_autocomplete'])) {
				$without_skills = explode('_', $_COOKIE['search_jobs_skills_autocomplete']);
			} else {
				$without_skills = array();
			}
			foreach($without_skills as $keys) {
				unset($results['data'][$keys]);
			}

			$skills = array();
			foreach($results['data'] as $id=>$value) {
				$skills[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $skills
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}

	public function actionGetListCertifications($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_Certifications::getList_OrderCountUsed($text);

			$certificats = array();
			foreach($results['data'] as $id=>$value) {
				$certificats[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $certificats
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}


	public function actionGetListTestScopes($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_TestScores::getList_OrderCountUsed($text);

			$testscopes = array();
			foreach($results['data'] as $id=>$value) {
				$testscopes[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $testscopes
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}


	public function actionGetListProjects($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_Projects::getList_OrderCountUsed($text);

			$projects = array();
			foreach($results['data'] as $id=>$value) {
				$projects[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $projects
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}

	public function actionGetListUniversities($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_Universities::getList_OrderCountUsed($text);

			$universities = array();
			foreach($results['data'] as $id=>$value) {
				$universities[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $universities
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}


	public function actionGetListExperience($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_Companies::getList_OrderCountUsed($text);

			$experience = array();
			foreach($results['data'] as $id=>$value) {
				$experience[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $experience
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}



	public function actionGetListLanguages($text = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$page = Request::get('page', FALSE);
			$results = Model_Languages::getList_OrderCountUsed($text);

			$languages = array();
			foreach($results['data'] as $id=>$value) {
				$languages[] = array(
					'value' => $id,
					'text' => trim($value->name),
					'itemorder' => $value->countUsed,
					'itemtitle' => $value->name
				);
			}

			$this->response->body = json_encode(array(
				'status' => true,
				'selectize' => array(
					'clear_data' => (($page === FALSE) ? false : true),
					'next_page' => (!empty($results['paginator']['next']) ? $results['paginator']['next'] : null),
					'data' => $languages
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('profile', 'index'));
	}
}