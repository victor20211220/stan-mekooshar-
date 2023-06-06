<?php

	class Search_Controller extends Controller_User
	{
		protected $subactive = 'search';

		public function  before()
		{
			parent::before();

			$_SESSION['search']['last_open'] = time();
		}

		public function actionIndex()
		{
			$this->title = 'Search all';

			$searchname = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('all');
//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$searchname = $values['searchall'];
				}
//			}

			if (!$searchname) {
				$searchname = Request::get('searchall', FALSE);
			}

			$query = array(
				'filter'          => Request::get('filter', FALSE),

				'connection'      => Request::get('connection', FALSE),
				'region'          => Request::get('region', FALSE),
				'company'         => Request::get('company', FALSE),
				'industrypeople'  => Request::get('industrypeople', FALSE),
				'school'          => Request::get('school', FALSE),
				'skill'           => Request::get('skill', FALSE),

				'employer'        => Request::get('employer', FALSE),
				'typecompany'     => Request::get('typecompany', FALSE),
				'industrycompany' => Request::get('industrycompany', FALSE),

				'access'          => Request::get('access', FALSE),

				'typeschool'      => Request::get('typeschool', FALSE),

				'peoplename'      => $searchname,
				'companyname'     => $searchname,
				'groupname'       => $searchname,
				'schoolname'      => $searchname
			);
			$_SESSION['search']['allquery'] = $query;

			if ($query['connection'] || $query['region'] || $query['company'] || $query['industrypeople'] || $query['school'] || $query['skill']) {
				$query['filter'] = 'people';
			} elseif ($query['employer'] || $query['typecompany'] || $query['industrycompany']) {
				$query['filter'] = 'company';
			} elseif ($query['access']) {
				$query['filter'] = 'group';
			} elseif ($query['typeschool']) {
				$query['filter'] = 'school';
			} else {
				$query['filter'] = 'all';
			}

			$active_menu = $query['filter'];

			$industrycompany = array('data' => array());
			$type = array('data' => array());
			$employer = array('data' => array());
			$companies = array('data' => array());
			$regions = array('data' => array());
			$industrypeople = array('data' => array());
			$university = array('data' => array());
			$skill = array('data' => array());

			switch ($query['filter']) {
				case 'people':
					$results = Model_User::getListSearchPeople($this->user->id, $query);
					$companies = Model_Connections::getListCompaniesForSearchPeople($this->user->id);
					$regions = Model_Connections::getListRegionsForSearchPeople($this->user->id);
					$industrypeople = Model_Connections::getListIndustryForSearchPeople($this->user->id);
					$university = Model_Connections::getListUniversityForSearchPeople($this->user->id);
					break;

				case 'company':
					$results = Model_Companies::getListSearchCompany($this->user->id, $query);
					$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
					$industrycompany = Model_Companies::getListIndustryForSearchCompany($this->user->id, $isFollow);
					$type = Model_Companies::getListTypeForSearchCompany($this->user->id, $isFollow);
					$employer = Model_Companies::getListEmployerForSearchCompany($this->user->id, $isFollow);
					break;

				case 'group':
					$results = Model_Groups::getListSearchGroups($this->user->id, $query);
					break;

				case 'school':
					$results = Model_Universities::getListSearchSchool($this->user->id, $query);
					break;

				default:
					$results1 = Model_User::getListSearchPeople($this->user->id, $query, 'createDate');
					$results2 = Model_Companies::getListSearchCompany($this->user->id, $query, 'createDate');
					$results3 = Model_Groups::getListSearchGroups($this->user->id, $query, 'createDate');
					$results4 = Model_Universities::getListSearchSchool($this->user->id, $query, 'createDate');

					$results = array();
					$tmp = array();
					foreach ($results1['data'] as $result1) {
						$id = FALSE;
						$i = -1;
						while ($id == FALSE && !isset($tmp[$id])) {
							$i++;
							$id = (strtotime($result1->createDate)) . $i;
						}
						$tmp[$id] = $result1;
					}

					foreach ($results2['data'] as $result2) {
						$id = FALSE;
						$i = -1;
						while ($id == FALSE && !isset($tmp[$id])) {
							$i++;
							$id = (strtotime($result2->createDate)) . $i;
						}
						$tmp[$id] = $result2;
					}
					foreach ($results3['data'] as $result3) {
						$id = FALSE;
						$i = -1;
						while ($id == FALSE && !isset($tmp[$id])) {
							$i++;
							$id = (strtotime($result3->createDate)) . $i;
						}
						$tmp[$id] = $result3;
					}
					foreach ($results4['data'] as $result4) {
						$id = FALSE;
						$i = -1;
						while ($id == FALSE && !isset($tmp[$id])) {
							$i++;
							$id = (strtotime($result4->createDate)) . $i;
						}
						$tmp[$id] = $result4;
					}
					krsort($tmp);

					$i = 0;
					foreach ($tmp as $date => $item) {
						$i++;
						$results['data'][$date] = $item;

						if ($i >= 10) {
							break;
						}
					}

					if(!isset($results['data'])) {
						$results['data'] = array();
					}

					$results['paginator']['count'] = $results1['paginator']['count'] + $results2['paginator']['count'] + $results3['paginator']['count'] + $results4['paginator']['count'];
					if($results['paginator']['count'] > 0) {
						$results['paginator']['next'] = Request::generateUri(FALSE, FALSE) . Request::getQuery('pagedown', strtotime(end($results['data'])->createDate));
					} else {
						$results['paginator']['next'] = '';
					}

					if ($results['paginator']['count'] > 10) {
						$results['paginator']['isLast'] = FALSE;
					} else {
						$results['paginator']['isLast'] = TRUE;
					}


					$companies = Model_Connections::getListCompaniesForSearchPeople($this->user->id);
					$regions = Model_Connections::getListRegionsForSearchPeople($this->user->id);
					$industrypeople = Model_Connections::getListIndustryForSearchPeople($this->user->id);
					$university = Model_Connections::getListUniversityForSearchPeople($this->user->id);


					$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
					$industrycompany = Model_Companies::getListIndustryForSearchCompany($this->user->id, $isFollow);
					$type = Model_Companies::getListTypeForSearchCompany($this->user->id, $isFollow);
					$employer = Model_Companies::getListEmployerForSearchCompany($this->user->id, $isFollow);
			}

			Model_ConnectionSearchResult::insertShowResult($results);


			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/all/item-search-results', array(
						'result' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_all  > .list-items > li:last-child'
					)
				));

				return;
			}

			unset($_GET['connection'], $_GET['region'], $_GET['company'], $_GET['industrypeople'], $_GET['school'], $_GET['skill'], $_GET['searchall']);
			unset($_GET['employer'], $_GET['typecompany'], $_GET['industrycompany'], $_GET['access'], $_GET['typeschool'], $_GET['filter']);

			$f_Search_FilterPeople = new Form_Search_FilterPeople($searchname);
			$f_Search_FilterPeople->inSearchAll();
			$f_Search_FilterPeople->generateConnection($query['connection']);
			$f_Search_FilterPeople->generateCompany($companies, $query['company']);
			$f_Search_FilterPeople->generateIndustry($industrypeople, $query['industrypeople']);
			$f_Search_FilterPeople->generateRegion($regions, $query['region']);
			$f_Search_FilterPeople->generateSchool($university, $query['school']);
			$f_Search_FilterPeople->generateSkills($skill, $query['skill']);

			$f_Search_FilterCompany = new Form_Search_FilterCompany($searchname);
			$f_Search_FilterCompany->inSearchAll();
			$f_Search_FilterCompany->generateIndustry($industrycompany, $query['industrycompany']);
			$f_Search_FilterCompany->generateType($type, $query['typecompany']);
			$f_Search_FilterCompany->generateEmployer($employer, $query['employer']);

			$f_Search_FilterGroup = new Form_Search_FilterGroup($searchname);
			$f_Search_FilterGroup->inSearchAll();
			$f_Search_FilterGroup->generateAccess(t('group_access_search_filter'), $query['access']);

			$f_Search_FilterSchool = new Form_Search_FilterSchool($searchname);
			$f_Search_FilterSchool->inSearchAll();
			$f_Search_FilterSchool->generateType(t('school_type'), $query['typeschool']);

			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/all/menu-filter', array(
					'f_Search_FilterPeople'  => $f_Search_FilterPeople,
					'f_Search_FilterCompany' => $f_Search_FilterCompany,
					'f_Search_FilterGroup'   => $f_Search_FilterGroup,
					'f_Search_FilterSchool'  => $f_Search_FilterSchool,
					'query'                  => $query,
					'active_menu'            => $active_menu
				)),
				'right' => View::factory('pages/search/all/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}

		public function actionPeople()
		{
			$this->title = 'Search people';

			$peoplename = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('people');
//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$peoplename = $values['searchpeople'];
				}
//			}

			if (!$peoplename) {
				$peoplename = Request::get('searchpeople', FALSE);
			}

			$query = array(
				'connection'     => Request::get('connection', FALSE),
				'region'         => Request::get('region', FALSE),
				'company'        => Request::get('company', FALSE),
				'industrypeople' => Request::get('industrypeople', FALSE),
				'school'         => Request::get('school', FALSE),
				'skill'          => Request::get('skill', FALSE),
				'peoplename'     => $peoplename
			);
			$_SESSION['search']['peoplequery'] = $query;

			$results = Model_User::getListSearchPeople($this->user->id, $query);
			Model_ConnectionSearchResult::insertShowResult($results);

			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/people/item-search-results', array(
						'result' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_people > .list-items > li:last-child'
					)
				));

				return;
			}

			unset($_GET['connection'], $_GET['region'], $_GET['company'], $_GET['industrypeople'], $_GET['school'], $_GET['searchpeople'], $_GET['skill']);

			$companies = Model_Connections::getListCompaniesForSearchPeople($this->user->id);
			$regions = Model_Connections::getListRegionsForSearchPeople($this->user->id);
			$industry = Model_Connections::getListIndustryForSearchPeople($this->user->id);
			$university = Model_Connections::getListUniversityForSearchPeople($this->user->id);
			$skills = array('data' => array(), 'paginator' => array());

			$f_Search_FilterPeople = new Form_Search_FilterPeople($peoplename);
			$f_Search_FilterPeople->generateConnection($query['connection']);
			$f_Search_FilterPeople->generateCompany($companies, $query['company']);
			$f_Search_FilterPeople->generateIndustry($industry, $query['industrypeople']);
			$f_Search_FilterPeople->generateRegion($regions, $query['region']);
			$f_Search_FilterPeople->generateSchool($university, $query['school']);
			$f_Search_FilterPeople->generateSkills($skills, $query['skill']);

			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/people/menu-filter', array(
					'f_Search_FilterPeople' => $f_Search_FilterPeople,
					'query'                 => $query
				)),
				'right' => View::factory('pages/search/people/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}

		public function actionCompany()
		{
			$this->title = 'Search companies';

			$companyname = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('company');
//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$companyname = $values['searchcompany'];
				}
//			}

			if (!$companyname) {
				$companyname = Request::get('searchcompany', FALSE);
			}

			$query = array(
				'employer'        => Request::get('employer', FALSE),
				'typecompany'     => Request::get('typecompany', FALSE),
				'industrycompany' => Request::get('industrycompany', FALSE),
				'companyname'     => $companyname
			);
			$_SESSION['search']['companyquery'] = $query;

			$results = Model_Companies::getListSearchCompany($this->user->id, $query);

			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/company/item-search-results', array(
						'company' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_company > .list-items > li:last-child'
					)
				));

				return;
			}

			unset($_GET['employer'], $_GET['typecompany'], $_GET['industrycompany'], $_GET['searchcompany']);


			$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
			$industry = Model_Companies::getListIndustryForSearchCompany($this->user->id, $isFollow);
			$type = Model_Companies::getListTypeForSearchCompany($this->user->id, $isFollow);
			$employer = Model_Companies::getListEmployerForSearchCompany($this->user->id, $isFollow);

			$f_Search_FilterCompany = new Form_Search_FilterCompany($companyname);
			$f_Search_FilterCompany->generateIndustry($industry, $query['industrycompany']);
			$f_Search_FilterCompany->generateType($type, $query['typecompany']);
			$f_Search_FilterCompany->generateEmployer($employer, $query['employer']);

			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/company/menu-filter', array(
					'f_Search_FilterCompany' => $f_Search_FilterCompany,
					'query'                  => $query
				)),
				'right' => View::factory('pages/search/company/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}

		public function actionGroup()
		{
			$this->title = 'Search groups';

			$groupname = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('group');
//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$groupname = $values['searchgroup'];
				}
//			}

			if (!$groupname) {
				$groupname = Request::get('searchgroup', FALSE);
			}
			$query = array(
				'access'    => Request::get('access', FALSE),
				'groupname' => $groupname
			);
			$_SESSION['search']['groupquery'] = $query;

			$results = Model_Groups::getListSearchGroups($this->user->id, $query);

			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/groups/item-search-results', array(
						'group' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_group > .list-items > li:last-child'
					)
				));

				return;
			}

			unset($_GET['access'], $_GET['searchgroup']);


			$f_Search_FilterGroup = new Form_Search_FilterGroup($groupname);
			$f_Search_FilterGroup->generateAccess(t('group_access_search_filter'), $query['access']);

			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/groups/menu-filter', array(
					'f_Search_FilterGroup' => $f_Search_FilterGroup,
					'query'                => $query
				)),
				'right' => View::factory('pages/search/groups/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}

		public function actionSchool()
		{
			$this->title = 'Search schools';

			$schoolname = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('school');

//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$schoolname = $values['searchschool'];
				}
//			}

			if (!$schoolname) {
				$schoolname = Request::get('searchschool', FALSE);
			}

			$query = array(
				'typeschool' => Request::get('typeschool', FALSE),
				'schoolname' => $schoolname
			);

			$_SESSION['search']['schoolquery'] = $query;
			$results = Model_Universities::getListSearchSchool($this->user->id, $query);

			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/school/item-search-results', array(
						'school' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_school > .list-items > li:last-child'
					)
				));

				return;
			}


			unset($_GET['typeschool'], $_GET['searchcompany']);


			$f_Search_FilterSchool = new Form_Search_FilterSchool($schoolname);
			$f_Search_FilterSchool->generateType(t('school_type'), $query['typeschool']);

			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/school/menu-filter', array(
					'f_Search_FilterSchool' => $f_Search_FilterSchool,
					'query'                 => $query
				)),
				'right' => View::factory('pages/search/school/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}


		public function actionJob()
		{
			$this->title = 'Search job';

			$jobname = FALSE;
			$f_FindPanel = new Form_FindPanel();
			$f_FindPanel->setFindType('job');

//			if (Request::isPost()) {
				if ($f_FindPanel->form->validate()) {
					$values = $f_FindPanel->form->getValues();
					$jobname = $values['searchjob'];
				}
//			}

			if (!$jobname) {
				$jobname = Request::get('searchjob', FALSE);
			}

			$query = array(
				'industryjob' => Request::get('industryjob', FALSE),
				'regionjob' => Request::get('regionjob', FALSE),
				'skilljob' => Request::get('skilljob', FALSE),
				'jobname' => $jobname
			);

			$_SESSION['search']['jobquery'] = $query;
			$results = Model_Jobs::getListSearch($this->user->id, $query, true);


			if (Request::get('pagedown', FALSE) && Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$view = '';
				foreach ($results['data'] as $result) {
					$view .= View::factory('pages/search/job/item-search-results', array(
						'job' => $result
					));
				}
				$view .= '<li>' . View::factory('common/default-pages', array(
							'isBand'     => TRUE,
							'autoScroll' => TRUE
						) + $results['paginator']) . '</li>';

				$this->response->body = json_encode(array(
					'status'        => TRUE,
					'function_name' => 'changeContent',
					'data'          => array(
						'content' => (string)$view,
						'target'  => '.search-list_job .list-items > li:last-child'
					)
				));

				return;
			}


			unset($_GET['regionjob'], $_GET['industryjob'], $_GET['searchjob'], $_GET['skilljob']);

			$industry = array('data' => array(), 'paginator' => array());
			$regions = array('data' => array(), 'paginator' => array());
			$skills = array('data' => array(), 'paginator' => array());
			$f_Search_FilterJob = new Form_Search_FilterJob($jobname);
			$f_Search_FilterJob->generateIndustry($industry, $query['industryjob']);
			$f_Search_FilterJob->generateRegion($regions, $query['regionjob']);
			$f_Search_FilterJob->generateSkills($skills, $query['skilljob']);



			$view = View::factory('parts/parts-right_big', array(
				'left'  => View::factory('pages/search/job/menu-filter', array(
					'f_Search_FilterJob' => $f_Search_FilterJob,
					'query'                 => $query
				)),
				'right' => View::factory('pages/search/job/list-search-results', array(
					'results' => $results
				))
			));
			$this->view->content = $view;
		}

		public function actionAddSearchCompany()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$companies = Model_Connections::getListCompaniesForSearchPeople($this->user->id);
				$without_companies = array();

				foreach($companies['data'] as $company) {
					$without_companies[] = $company->companyName;
				}
				if (isset($_SESSION['search']['people']['company'])) {
					$without_companies = array_merge($without_companies, array_keys($_SESSION['search']['people']['company']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateCompany($without_companies, Request::generateUri('search', 'AddSearchCompany'));

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
						$f_Search_FilterPeople->generateCompany($companies, $query['company']);

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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}


		public function actionAddSearchRegion()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$regions = Model_Connections::getListRegionsForSearchPeople($this->user->id);

				$without_region = array();
				foreach($regions['data'] as $region) {
					$without_region[] = $region->userCountry;
				}
				if (isset($_SESSION['search']['people']['region'])) {
					$without_region = array_merge($without_region, array_keys($_SESSION['search']['people']['region']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateRegion($without_region, Request::generateUri('search', 'AddSearchRegion'));

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
						$f_Search_FilterPeople->generateRegion($regions, $query['region']);

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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}

		public function actionAddSearchIndustry()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$industries = Model_Connections::getListIndustryForSearchPeople($this->user->id);

				$without_industry = array();
				foreach($industries['data'] as $industry) {
					$without_industry[] = $industry->userIndustry;
				}
				if (isset($_SESSION['search']['people']['industry'])) {
					$without_industry = array_merge($without_industry, array_keys($_SESSION['search']['people']['industry']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateIndustry($without_industry, Request::generateUri('search', 'AddSearchIndustry'));

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
						$f_Search_FilterPeople = new Form_Search_FilterPeople('');
						$f_Search_FilterPeople->generateIndustry($industries, $query['industrypeople']);

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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}

		public function actionAddSearchSchool()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$schools = Model_Connections::getListUniversityForSearchPeople($this->user->id);

				$without_school = array();
				foreach($schools['data'] as $school) {
					$without_school[] = $school->universityName;
				}
				if (isset($_SESSION['search']['people']['school'])) {
					$without_school = array_merge($without_school, array_keys($_SESSION['search']['people']['school']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateSchool($without_school, Request::generateUri('search', 'AddSearchSchool'));

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
						$f_Search_FilterPeople->generateSchool($schools, $query['school']);

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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}

		public function actionAddSearchSkill()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$skills = Model_Connections::getListSkillsForSearchPeople($this->user->id);

				$without_skills = array();
				foreach($skills['data'] as $skill) {
					$without_skill[] = $skill->skillName;
				}
				if (isset($_SESSION['search']['people']['skill'])) {
					$without_skill = array_merge($without_skill, array_keys($_SESSION['search']['people']['skill']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateSkill($without_skill, Request::generateUri('search', 'AddSearchSkill'));

				if (!$isSet) {
					$message = 'You can not add more skill!';
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
						$f_Search_FilterPeople->generateSchool($schools, $query['school']);

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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}


		public function actionAddSearchCompanyIndustry()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
				$industry = Model_Companies::getListIndustryForSearchCompany($this->user->id, $isFollow);

				$without_industry = array_keys($industry['data']);
				if (isset($_SESSION['search']['company']['industry'])) {
					$without_industry = array_merge($without_industry, array_keys($_SESSION['search']['company']['industry']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateIndustry($without_industry, Request::generateUri('search', 'AddSearchCompanyIndustry'));

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

						$_SESSION['search']['company']['industry'][$values['industry']] = t('industries.' . $values['industry']);

						if (!isset($_SESSION['search']['companyquery'])) {
							$query = array(
								'employer' => array()
							);
						} else {
							$query = $_SESSION['search']['companyquery'];
						}
						$f_Search_FilterCompany = new Form_Search_FilterCompany('');
						$f_Search_FilterCompany->generateIndustry($industry, $query['industrycompany']);

						ob_start();
						$f_Search_FilterCompany->form->render('industry');
						$content = ob_get_clean();

						$this->response->body = json_encode(array(
							'status'        => TRUE,
							'function_name' => 'changeContent',
							'data'          => array(
								'content' => (string)$content,
								'target'  => '#' . $f_Search_FilterCompany->form->attributes['id'] . '-fieldset-industry'
							)
						));

						return;
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add industry to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'company'));
		}


		public function actionAddSearchCompanyType()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
				$type = Model_Companies::getListTypeForSearchCompany($this->user->id, $isFollow);

				$without_type = array_keys($type['data']);
				if (isset($_SESSION['search']['company']['type'])) {
					$without_type = array_merge($without_type, array_keys($_SESSION['search']['company']['type']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateCompanyType($without_type, Request::generateUri('search', 'AddSearchCompanyType'));

				if (!$isSet) {
					$message = 'You can not add more company type!';
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

						$_SESSION['search']['company']['type'][$values['type']] = t('company_type.' . $values['type']);

						if (!isset($_SESSION['search']['companyquery'])) {
							$query = array(
								'employer' => array()
							);
						} else {
							$query = $_SESSION['search']['companyquery'];
						}
						$f_Search_FilterCompany = new Form_Search_FilterCompany('');
						$f_Search_FilterCompany->generateType($type, $query['typecompany']);

						ob_start();
						$f_Search_FilterCompany->form->render('type');
						$content = ob_get_clean();

						$this->response->body = json_encode(array(
							'status'        => TRUE,
							'function_name' => 'changeContent',
							'data'          => array(
								'content' => (string)$content,
								'target'  => '#' . $f_Search_FilterCompany->form->attributes['id'] . '-fieldset-type'
							)
						));

						return;
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add industry to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'company'));
		}


		public function actionAddSearchCompanyEmployer()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$isFollow = Model_Company_Follow::checkFollowOtherCompanies($this->user->id);
				$employer = Model_Companies::getListEmployerForSearchCompany($this->user->id, $isFollow);

				$without_employer = array_keys($employer['data']);
				if (isset($_SESSION['search']['company']['employer'])) {
					$without_employer = array_merge($without_employer, array_keys($_SESSION['search']['company']['employer']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateCompanyEmployer($without_employer, Request::generateUri('search', 'AddSearchCompanyEmployer'));

				if (!$isSet) {
					$message = 'You can not add more company employer!';
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

						$_SESSION['search']['company']['employer'][$values['employer']] = t('company_number_of_employer.' . $values['employer']);

						if (!isset($_SESSION['search']['companyquery'])) {
							$query = array(
								'employer' => array()
							);
						} else {
							$query = $_SESSION['search']['companyquery'];
						}
						$f_Search_FilterCompany = new Form_Search_FilterCompany('');
						$f_Search_FilterCompany->generateEmployer($employer, $query['employer']);

						ob_start();
						$f_Search_FilterCompany->form->render('employer');
						$content = ob_get_clean();

						$this->response->body = json_encode(array(
							'status'        => TRUE,
							'function_name' => 'changeContent',
							'data'          => array(
								'content' => (string)$content,
								'target'  => '#' . $f_Search_FilterCompany->form->attributes['id'] . '-fieldset-employer'
							)
						));

						return;
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add industry to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'company'));
		}

		public function actionAddSearchJobIndustry()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$industry = array('data' => array(), 'paginator' => array());

				$without_industry = array_keys($industry['data']);
				if (isset($_SESSION['search']['job']['industry'])) {
					$without_industry = array_merge($without_industry, array_keys($_SESSION['search']['job']['industry']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateIndustry($without_industry, Request::generateUri('search', 'AddSearchJobIndustry'));

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

						$_SESSION['search']['job']['industry'][$values['industry']] = t('industries.' . $values['industry']);

						if (!isset($_SESSION['search']['jobquery'])) {
							$query = array(
								'industry' => array()
							);
						} else {
							$query = $_SESSION['search']['jobquery'];
						}
						$f_Search_FilterJob = new Form_Search_FilterJob('');
						$f_Search_FilterJob->generateIndustry($industry, $query['industryjob']);

						ob_start();
						$f_Search_FilterJob->form->render('industry');
						$content = ob_get_clean();

						$this->response->body = json_encode(array(
							'status'        => TRUE,
							'function_name' => 'changeContent',
							'data'          => array(
								'content' => (string)$content,
								'target'  => '#' . $f_Search_FilterJob->form->attributes['id'] . '-fieldset-industry'
							)
						));

						return;
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add industry to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'job'));
		}

		public function actionAddSearchJobRegion()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

//				$regions = Model_Connections::getListRegionsForSearchPeople($this->user->id);
				$regions = array('data' => array(), 'paginator' => array());

				$without_region = array();
				foreach($regions['data'] as $region) {
					$without_region[] = $region->userCountry;
				}
				if (isset($_SESSION['search']['job']['region'])) {
					$without_region = array_merge($without_region, array_keys($_SESSION['search']['job']['region']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateRegion($without_region, Request::generateUri('search', 'AddSearchJobRegion'));

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
							$_SESSION['search']['job']['region'][$values['region']] = $countries[$values['region']];
						} else {
							$_SESSION['search']['job']['region'][$values['region']] = $values['region'];
						}

						if (!isset($_SESSION['search']['jobquery'])) {
							$query = array(
								'region' => array()
							);
						} else {
							$query = $_SESSION['search']['jobquery'];
						}

						$f_Search_FilterJob = new Form_Search_FilterJob('');
						$f_Search_FilterJob->generateRegion($regions, $query['regionjob']);

						ob_start();
						$f_Search_FilterJob->form->render('region');
						$content = ob_get_clean();

						$this->response->body = json_encode(array(
							'status'        => TRUE,
							'function_name' => 'changeContent',
							'data'          => array(
								'content' => (string)$content,
								'target'  => '#' . $f_Search_FilterJob->form->attributes['id'] . '-fieldset-region'
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

			$this->response->redirect(Request::generateUri('search', 'job'));
		}

		public function actionAddSearchJobSkill()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$skill = array('data' => array(), 'paginator' => array());

				$without_skill = array_keys($skill['data']);
				if (isset($_SESSION['search']['job']['skill'])) {
					$without_skill = array_merge($without_skill, array_keys($_SESSION['search']['job']['skill']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateSkill($without_skill, Request::generateUri('search', 'AddSearchJobSkill'));

				if (!$isSet) {
					$message = 'You can not add more skills!';
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

						$skillname = Model_Skills::checkItemById($values['skill']);
						if($skillname) {
							$_SESSION['search']['job']['skill'][$values['skill']] = $skillname->name;

							if (!isset($_SESSION['search']['jobquery'])) {
								$query = array(
									'skilljob' => array()
								);
							} else {
								$query = $_SESSION['search']['jobquery'];
							}
							$f_Search_FilterJob = new Form_Search_FilterJob('');
							$f_Search_FilterJob->generateSkills($skill, $query['skilljob']);

							ob_start();
							$f_Search_FilterJob->form->render('skill');
							$content = ob_get_clean();

							$this->response->body = json_encode(array(
								'status'        => TRUE,
								'function_name' => 'changeContent',
								'data'          => array(
									'content' => (string)$content,
									'target'  => '#' . $f_Search_FilterJob->form->attributes['id'] . '-fieldset-skill'
								)
							));

							return;
						} else {
							$f_Search_People_AddField->form->elements['skill']->errors[] = 'Not finded!';
							$isError = TRUE;
						}
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add skill to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'job'));
		}



		public function actionAddSearchPeopleSkill()
		{
			if (Request::$isAjax) {
				$this->autoRender = FALSE;
				$this->response->setHeader('Content-Type', 'text/json');

				$skill = array('data' => array(), 'paginator' => array());

				$without_skill = array_keys($skill['data']);
				if (isset($_SESSION['search']['people']['skill'])) {
					$without_skill = array_merge($without_skill, array_keys($_SESSION['search']['people']['skill']));
				}

				$f_Search_People_AddField = new Form_Search_People_AddField();
				$isSet = $f_Search_People_AddField->generateSkill($without_skill, Request::generateUri('search', 'AddSearchPeopleSkill'));

				if (!$isSet) {
					$message = 'You can not add more skills!';
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

						$skillname = Model_Skills::checkItemById($values['skill']);
						if($skillname) {
							$_SESSION['search']['people']['skill'][$values['skill']] = $skillname->name;

							if (!isset($_SESSION['search']['peoplequery'])) {
								$query = array(
									'skillpeople' => array()
								);
							} else {
								$query = $_SESSION['search']['peoplequery'];
							}
							if(!isset($query['skillpeople'])) {
								$query['skillpeople'] = array();
							}
							$f_Search_FilterPeople = new Form_Search_FilterPeople('');
							$f_Search_FilterPeople->generateSkills($skill, $query['skillpeople']);

							ob_start();
							$f_Search_FilterPeople->form->render('skill');
							$content = ob_get_clean();

							$this->response->body = json_encode(array(
								'status'        => TRUE,
								'function_name' => 'changeContent',
								'data'          => array(
									'content' => (string)$content,
									'target'  => '#' . $f_Search_FilterPeople->form->attributes['id'] . '-fieldset-skill'
								)
							));

							return;
						} else {
							$f_Search_People_AddField->form->elements['skill']->errors[] = 'Not finded!';
							$isError = TRUE;
						}
					} else {
						$isError = TRUE;
					}
				}

				$content = View::factory('parts/pbox-form', array(
					'title'   => 'Add skill to the search filter',
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

			$this->response->redirect(Request::generateUri('search', 'people'));
		}
	}