<?php

class Jobs_Controller extends Controller_User
{

	protected $subactive = 'jobs';

	public function  before() {
		parent::before();
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/uploader.js');

		if(!in_array(strtolower(Request::$action), array('addjobskill', 'editjob', 'removejobskill'))) {
			unset($_SESSION['jobs_skills']);
		}
		$isMyCompanies = Model_Companies::getIsMyCompanies($this->user->id);
		View::$global->isMyCompanies = $isMyCompanies;
	}


	public function actionIndex()
	{
		$this->actionSearch();
	}

	public function actionSearch()
	{
		$this->view->title = 'Search jobs';

		$f_Jobs_SearchJob = new Form_Jobs_SearchJob();

		$values = array(
			'jobname' => Request::get('search', FALSE),
			'country' => Request::get('country', FALSE),
			'state' => Request::get('state', FALSE),
			'state1' => Request::get('state1', FALSE),
			'city' => Request::get('city', FALSE),
			'industries' => Request::get('industry', FALSE),
			'skills' => Request::get('skill', FALSE)
		);

		$is_request = FALSE;
		if(Request::get('search', 'none') != 'none') {
			$is_request = TRUE;
		}

		if($is_request) {
			$jobs = Model_Jobs::getListSearch($this->user->id, $values);

			$view = new View('pages/jobs/search', array(
				// Left top panel

				// Left down panel
				'jobs' => $jobs

				// Right panel
			));
			$this->view->content = $view;
			return;
		}


		$view = new View('pages/jobs/index', array(
			// Left top panel

			// Left down panel
			'f_Jobs_SearchJob' => $f_Jobs_SearchJob

			// Right panel
		));
		$this->view->content = $view;
	}


	public function actionNewJob()
	{
		$this->view->title = 'New job';
		$f_Jobs_NewJob = new Form_Jobs_NewJob();

		if(Request::isPost()) {
			if($f_Jobs_NewJob->form->validate()) {
				$values = $f_Jobs_NewJob->form->getValues();

				$job = Model_Jobs::create(array(
					'user_id' => $this->user->id,
					'company_id' => $values['company'],
					'industry' => $values['industry'],
					'country' => $values['country'],
					'state' => $values['state'],
					'city' => $values['city'],
					'title' => $values['title'],
					'description' => $values['description'],
					'about' => $values['about'],
					'employment' => $values['employment'],
					'receivedType' => $values['received'],
					'receivedEmail' => (isset($values['email']) ? $values['email'] : null),
                    'activateDate' => date('Y-m-d H:m:i', time()),
                    'expiredDate' => date('Y-m-d H:m:i', strtotime('+1 years'))
				));

				if(isset($_SESSION['jobs_skills'])) {
					$ids = array_keys($_SESSION['jobs_skills']);
					$skills = Model_Skills::getListByIds($ids);

					foreach($skills['data'] as $skill) {
						Model_Job_Skills::create(array(
							'job_id' => $job->id,
							'skill_id' => $skill->id
						));
					}
					unset($_SESSION['jobs_skills']);
				}

				$message = 'Your job has been created. You can activate it now.';
				$this->message($message);

				$this->response->redirect(Request::generateUri('jobs', 'myJobs')); //return to myJobPage
//                $this->response->redirect(Request::generateUri('jobs', 'activateJob', $job->id));

                return;
			}
		}

		$view = new View('pages/jobs/editjob', array(
			// Left top panel

			// Left down panel
			'f_Jobs_NewJob' => $f_Jobs_NewJob

			// Right panel
		));
		$this->view->content = $view;
	}


	public function actionEditJob($job_id)
	{
		$this->view->title = 'Edit job';
		$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);

		if(!Request::isPost()) {
			$ids = explode(', ', $job->skillsId);
			$names = explode(', ', $job->skillsName);
			$tmp = array();
			foreach($ids as $key => $id) {
				$tmp[$id] = $names[$key];
			}
			$_SESSION['jobs_skills'] = $tmp;
			unset($tmp);
		}


		$f_Jobs_NewJob = new Form_Jobs_NewJob();
		$f_Jobs_NewJob->edit($job);

		if(Request::isPost()) {
			if($f_Jobs_NewJob->form->validate()) {
				$values = $f_Jobs_NewJob->form->getValues();

				Model_Jobs::update(array(
					'company_id' => $values['company'],
					'industry' => $values['industry'],
					'country' => $values['country'],
					'state' => $values['state'],
					'city' => $values['city'],
					'title' => $values['title'],
					'description' => $values['description'],
					'about' => $values['about'],
					'employment' => $values['employment'],
					'receivedType' => $values['received'],
					'receivedEmail' => (isset($values['email']) ? $values['email'] : null),
				), $job->id);

				Model_Job_Skills::remove(array('job_id = ?', $job->id));
				if(isset($_SESSION['jobs_skills']) && !empty($_SESSION['jobs_skills'])) {
					$ids = array_keys($_SESSION['jobs_skills']);
					$skills = Model_Skills::getListByIds($ids);

					foreach($skills['data'] as $skill) {
						Model_Job_Skills::create(array(
							'job_id' => $job->id,
							'skill_id' => $skill->id
						));
					}
				}
				unset($_SESSION['jobs_skills']);

				$from = Request::get('from', FALSE);
				$id = $job->id;
				if(!$from) {
					$from = 'myJobs';
				}
				if(in_array($from, array('myJobs', 'search'))) {
					$id = false;
				}

				$this->response->redirect(Request::generateUri('jobs', $from, $id));
				return;
			}
		}

		$view = new View('pages/jobs/editjob', array(
			// Left top panel

			// Left down panel
			'f_Jobs_NewJob' => $f_Jobs_NewJob

			// Right panel
		));
		$this->view->content = $view;
	}

	public function actionMyJobs()
	{
		$this->view->title = 'My jobs';
		$jobs = Model_Jobs::getListByUserid($this->user->id);

		$view = new View('pages/jobs/myjobs', array(
			// Left top panel

			// Left down panel
			'jobs' => $jobs

			// Right panel
		));
		$this->view->content = $view;
	}


	public function actionApplications()
	{
		$this->view->title = 'My applications';
		$jobs = Model_Job_Apply::getListByUserid($this->user->id);
//		dump($jobs, 1);

		$view = new View('pages/jobs/applications', array(
			// Left top panel

			// Left down panel
			'jobs' => $jobs

			// Right panel
		));
		$this->view->content = $view;
	}

	public function actionApplicants($job_id)
	{
		$this->view->title = 'My job applicants';
		$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
		$applicants = Model_Job_Apply::getListApplicantByJobid($job->id);

		$view = new View('pages/jobs/applicants', array(
			// Left top panel

			// Left down panel
			'job' => $job,
			'applicants' => $applicants

			// Right panel
		));
		$this->view->content = $view;
	}

	public function actionApplicant($job_id, $user_id)
	{
		$this->view->title = 'My job applicant';
		$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
		$applicant = Model_Job_Apply::getItemApplicantByUseridJobid($user_id, $job->id);
		if(is_null($applicant->id)) {
			$this->response->redirect(Request::generateUri('jobs', 'applicants', $job->id));
		}

		if($applicant->jobapplyIsViewed == 0){
			Model_Job_Apply::update(array(
				'isViewed' => 1
			), array('user_id = ? AND job_id = ?', $user_id, $job->id));
			$job->countNewApplicants --;
			$job->save();
		}

		$files = Model_Files::getListByApplicantidJobid($user_id, $job->id);

		$view = new View('pages/jobs/applicant', array(
			// Left top panel

			// Left down panel
			'job' => $job,
			'applicant' => $applicant,
			'files' => $files

			// Right panel
		));
		$this->view->content = $view;
	}

	public function actionActivateJob($job_id)
	{
        $this->view->title = 'Activate job';
		$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);

		if(strtotime($job->expiredDate) > time()) {
			$this->message('Job is actived!');
			$this->response->redirect(Request::generateUri('jobs', 'myJobs'));
		}else{ // if job no activated + 365 days
            Model_Jobs::setActivate($job->getData()['id'], 365); // if job no activated + 365 days
            $this->message('Job is actived!'); // if job no activated + 365 days
            $this->response->redirect(Request::generateUri('jobs', 'myJobs')); // if job no activated + 365 days
        } // if job no activated + 365 daysapplication/controllers/jobs.php

		$from = Request::get('from', FALSE);
		if(!$from) {
			$from = 'myJobs';
		}
		$_SESSION['jobs_from'] = $from;
		$_SESSION['jobs_id'] = $job->id;

		$this->response->redirect(Request::generateUri('commerce', 'activateJob',  $job->id));
	}

	public function actionJob($job_id)
	{
		$job = Model_Jobs::getItemByIdUserid($job_id);

		if($job->user_id != $this->user->id) {
			$apply = Model_Job_Apply::getItemByUseridJobid($this->user->id, $job->id);

			if(!$apply && $job->expiredDate < CURRENT_DATETIME) {
				$this->response->redirect(Request::generateUri('jobs', 'index'));
				return;
			}
		}

		$this->view->title = 'Job - ' . $job->title;

		$view = new View('pages/jobs/job', array(
			// Left top panel

			// Left down panel
			'job' => $job

			// Right panel
		));
		$this->view->content = $view;
	}

	public function actionApply($job_id)
	{
		$job = Model_Jobs::getItemByIdUserid($job_id);
		if($job->expiredDate < CURRENT_DATETIME) {
			$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
			return;
		}

		$apply = Model_Job_Apply::getItemByUseridJobid($this->user->id, $job->id);

		$from = Request::get('from', FALSE);
		$id = $job->id;
		if(!$from) {
			$from = 'myJobs';
		}
		if(in_array($from, array('myJobs', 'search'))) {
			$id = false;
		}

		if($apply) {
			$this->message('Your application are already sended!');
			$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
		}


		$this->view->title = 'Apply job - ' . $job->title;
		$f_Jobs_ApplyJob = new Form_Jobs_ApplyJob($job);

		if(Request::isPost()) {
			if($f_Jobs_ApplyJob->form->validate()) {
				$values = $f_Jobs_ApplyJob->form->getValues();

				if(!empty($job->receivedEmail) && !is_null($job->receivedEmail)) {

					$mail = new Mailer('job-apply');
					$mail->firstName = $job->ownerCompanyFirstName;
					$mail->company_name = $job->companyName;
					$mail->job_title = $job->title;
					$mail->applicant_full_name = $this->user->firstName . ' ' . $this->user->lastName;
					$mail->job_id = $job->id;
					$mail->applicant_id = $this->user->id;
					$mail->send($job->receivedEmail);

				}

				Model_Job_Apply::create(array(
					'user_id' => $this->user->id,
					'job_id' => $job->id,
					'coverLetter' => $values['cover_letter'],
				));
				$job->countApplicants ++;
				$job->countNewApplicants ++;
				$job->save();


				$tokens = explode(',', $values['files']);
				Model_Files::update(array(
					'parent_id' => $job->id
				), array('parent_id = 0 AND sender_id = ? AND type = ? AND token in (?)', $this->user->id, FILE_JOB_APPLY, $tokens));

				$this->message('Success. The company has received your application!');

				$this->response->redirect(Request::generateUri('jobs', $from, $id) . (($from == 'search') ? Request::getQuery() : null));
				return;
			}
		}

		$view = new View('pages/jobs/apply', array(
			// Left top panel

			// Left down panel
			'job' => $job,
			'f_Jobs_ApplyJob' => $f_Jobs_ApplyJob

			// Right panel
		));
		$this->view->content = $view;
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/uploader.js');
	}


	public function actionCloseJob($job_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);

			if(strtotime($job->expiredDate) < time()) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'popupShow',
					'data' => array(
						'title' => 'Message',
						'content' => 'Job is closed!'
					)
				));
			}

			$job->expiredDate = date('Y-m-d H:m:i', (time() - 60*60*24));
			$job->save();

			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}

			if($from == 'myJobs') {
				$content = View::factory('pages/jobs/item-my_jobs', array(
					'job' => $job
				));
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => 'li[data-id="job_' . $job->id . '"]'
					)
				));
			} else {
				$content = View::factory('pages/jobs/block-job_buttons', array(
					'job' => $job
				));
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-job_view .block-job_buttons'
					)
				));
			}
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}

	public function actionDeleteJob($job_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			Model_Jobs::update(array(
				'isRemoved' => 1
			), $job->id);

			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}

			if($from == 'myJobs') {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => 'li[data-id="job_' . $job->id . '"]'
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('jobs', 'myJobs')
					)
				));
			}
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}


	public function actionAddJobSkill()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Jobs_AddField = new Form_Jobs_AddField();
			$isSet = $f_Jobs_AddField->generateSkillsForJob();

			if(!$isSet) {
				$message = 'You can not add more skills!';
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

			$isError = false;
			if(Request::isPost()) {
				if($f_Jobs_AddField->form->validate()) {
					$values = $f_Jobs_AddField->form->getValues();


					if(isset($_SESSION['jobs_skills'])) {
						$jobs_skills = $_SESSION['jobs_skills'];
					} else {
						$jobs_skills = array();
					}

					$jobs_skills[$values['skill']] = true;
					$_SESSION['jobs_skills'] = $jobs_skills;


					$f_Jobs_NewJob = new Form_Jobs_NewJob();
					$f_Jobs_NewJob->generateSkills();

					$content = View::factory('form-element', array(
						'inline' => FALSE,
						'el' => $f_Jobs_NewJob->form->elements['skill_' . $values['skill']],
						'form' => $f_Jobs_NewJob->form,
						'labelWidth' => '',
						'listMargin' => '',
						'combineErrors' => FALSE
					));

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string)$content,
							'target' => '#newjob-fieldset-fields3 > ol > li:last-child'
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Add skill to the job',
				'content' => View::factory('popups/search/addnewfield', array(
						'form' => $f_Jobs_AddField->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}


	public function actionAddSearchIndustry()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Jobs_AddField = new Form_Jobs_AddField();
			$isSet = $f_Jobs_AddField->generateIndustry();

			if(!$isSet) {
				$message = 'You can not add more skills!';
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

			$isError = false;
			if(Request::isPost()) {
				if($f_Jobs_AddField->form->validate()) {
					$values = $f_Jobs_AddField->form->getValues();

					if(isset($_COOKIE['search_jobs_industries'])) {
						$search_jobs_industry = explode('_', $_COOKIE['search_jobs_industries']);
						unset($_COOKIE['search_jobs_industries']);
					} else {
						$search_jobs_industry = array();
					}

//					$search_jobs_industry = array();
					$isFinded = FALSE;
					foreach($search_jobs_industry as $item){
						if($item == $values['industry']) {
							$isFinded = TRUE;
							break;
						}
					}
					if(!$isFinded) {
						$search_jobs_industry[] = $values['industry'];
					}
					setcookie('search_jobs_industries', implode('_',$search_jobs_industry), 0, '/jobs/');
					$_COOKIE['search_jobs_industries'] = implode('_',$search_jobs_industry);

					$f_Jobs_SearchJob = new Form_Jobs_SearchJob();

					$content = View::factory('form-element', array(
						'inline' => FALSE,
						'el' => $f_Jobs_SearchJob->form->elements['industry_' . $values['industry']],
						'form' => $f_Jobs_SearchJob->form,
						'labelWidth' => '',
						'listMargin' => '',
						'combineErrors' => FALSE
					));

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string)$content,
							'target' => '#searchjob-fieldset-fields3 > ol > li:last-child'
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Add industry to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
						'form' => $f_Jobs_AddField->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}





	public function actionAddSearchSkill()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Jobs_AddField = new Form_Jobs_AddField();
//			$isSet = $f_Jobs_AddField->generateSkills();
			$isSet = $f_Jobs_AddField->generateSkillsForJobAsText();

			if(!$isSet) {
				$message = 'You can not add more skills!';
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

			$isError = false;
			if(Request::isPost()) {
				if($f_Jobs_AddField->form->validate()) {
					$values = $f_Jobs_AddField->form->getValues();

					if(isset($_COOKIE['search_jobs_skills'])) {
						$search_jobs_skills = explode('_', $_COOKIE['search_jobs_skills']);
						unset($_COOKIE['search_jobs_skills']);
					} else {
						$search_jobs_skills = array();
					}

					$isFinded = FALSE;
					foreach($search_jobs_skills as $item){
						if($item == $values['skill']) {
							$isFinded = TRUE;
							break;
						}
					}
					if(!$isFinded) {
						$search_jobs_skills[] = $values['skill'];
					}
					setcookie('search_jobs_skills', implode('_',$search_jobs_skills), 0, '/jobs/');
					setcookie('search_jobs_skills_autocomplete', implode('_',$search_jobs_skills), 0, '/autoComplete/');
					$_COOKIE['search_jobs_skills'] = implode('_',$search_jobs_skills);
					$f_Jobs_SearchJob = new Form_Jobs_SearchJob();

					$content = View::factory('form-element', array(
						'inline' => FALSE,
						'el' => $f_Jobs_SearchJob->form->elements['skills_' . $values['skill']],
						'form' => $f_Jobs_SearchJob->form,
						'labelWidth' => '',
						'listMargin' => '',
						'combineErrors' => FALSE
					));

					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string)$content,
							'target' => '#searchjob-fieldset-fields5 > ol > li:last-child'
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Add skill to the search filter',
				'content' => View::factory('popups/search/addnewfield', array(
						'form' => $f_Jobs_AddField->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}


	public function actionRemoveSearchIndustry($industry_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$industries = t('industries');
			$industry = (isset($industries[$industry_id]) ? : FALSE);

			if(!$industry) {
				$this->response->body = json_encode(array(
					'status' => false
				));
			}


			if(isset($_COOKIE['search_jobs_industries'])) {
				$search_jobs_industries = explode('_', $_COOKIE['search_jobs_industries']);
				unset($_COOKIE['search_jobs_industries']);
			} else {
				$search_jobs_industries = array();
				$this->response->body = json_encode(array(
					'status' => false
				));
				return;
			}


			foreach($search_jobs_industries as $key => $item){
				if($industry_id == $item) {
					unset($search_jobs_industries[$key]);
					setcookie('search_jobs_industries', implode('_',$search_jobs_industries), 0, '/jobs/');
					$_COOKIE['search_jobs_industries'] = implode('_',$search_jobs_industries);
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'removeParentItem',
						'data' => array(
							'target' => '.checkbox-control-select1[data-id="' . $industry_id . '"]',
							'parent' => 'li'
						)
					));
					return;
					break;
				}
			}

			$this->response->body = json_encode(array(
				'status' => false
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}

	public function actionRemoveSearchSkill($skill_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$skill = Model_Skills::getItemById($skill_id);
			if(isset($_COOKIE['search_jobs_skills'])) {
				$search_jobs_skills = explode('_', $_COOKIE['search_jobs_skills']);
				unset($_COOKIE['search_jobs_skills']);
			} else {
				$search_jobs_skills = array();
				$this->response->body = json_encode(array(
					'status' => false
				));
				return;
			}


			foreach($search_jobs_skills as $key => $item){
				if($skill->id == $item) {
					unset($search_jobs_skills[$key]);
					setcookie('search_jobs_skills', implode('_',$search_jobs_skills), 0, '/jobs/');
					setcookie('search_jobs_skills_autocomplete', implode('_',$search_jobs_skills), 0, '/autoComplete/');
					$_COOKIE['search_jobs_skills'] = implode('_',$search_jobs_skills);
					$this->response->body = json_encode(array(
						'status' => true,
						'function_name' => 'removeParentItem',
						'data' => array(
							'target' => '.checkbox-control-select2[data-id="' . $skill->id . '"]',
							'parent' => 'li'
						)
					));
					return;
					break;
				}
			}

			$this->response->body = json_encode(array(
				'status' => false
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
	}

	public function actionRemoveJobSkill($skill_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$skill = Model_Skills::getItemById($skill_id);
			if(isset($_SESSION['jobs_skills'])) {
				$jobs_skills = $_SESSION['jobs_skills'];
			} else {
				$jobs_skills = array();
				$this->response->body = json_encode(array(
					'status' => false
				));
				return;
			}
//			dump(isset($jobs_skills[$skill->id]), 1);
			if(isset($jobs_skills[$skill->id])) {
				unset($jobs_skills[$skill->id]);
				$_SESSION['jobs_skills'] = $jobs_skills;
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeParentItem',
					'data' => array(
						'target' => 'div[data-id="skill_' . $skill->id . '"]',
						'parent' => 'li'
					)
				));
				return;
			}

			$this->response->body = json_encode(array(
				'status' => false
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'index'));
	}



	public function actionApplyDelete($job_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id);
			$apply = Model_Job_Apply::getItemByUseridJobid($this->user->id, $job->id);

//			if($apply->isInvited == JOBAPPLY_ANSWER_NULL) {
//				$this->response->redirect(Request::generateUri('jobs', 'job', $job_id));
//				return;
//			}

			Model_Job_Apply::update(array(
				'isRemovedJobApplicant' => 1
			), array('user_id = ? AND job_id = ?', $this->user->id, $job->id));

			$job = Model_Jobs::getItemByIdUserid($job_id);
			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}
			if($from == 'search') {
				$content = View::factory('pages/jobs/item-search_result', array(
					'job' => $job
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-search_result li[data-id="job_' . $job->id . '"]',
					)
				));
				return;
			}
			if($from == 'job') {
				$content = View::factory('pages/jobs/block-job_buttons', array(
					'job' => $job,
					'from' => $from
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-job_buttons',
					)
				));
				return;
			}
			if($from == 'applications') {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => '.block-applications li[data-id="job_' . $job->id . '"]',
					)
				));
				return;
			}


			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'index'));
	}

	public function actionApplyCancel($job_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id);
			$apply = Model_Job_Apply::getItemByUseridJobid($this->user->id, $job->id);
			if($apply->isViewed == 0) {
				$job->countNewApplicants --;
			}
			$job->countApplicants --;
			$job->save();
			Model_Job_Apply::remove(array('user_id = ? AND job_id = ?', $this->user->id, $job->id));


			$job = Model_Jobs::getItemByIdUserid($job_id);
			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}
			if($from == 'search') {
				$content = View::factory('pages/jobs/item-search_result', array(
					'job' => $job
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-search_result li[data-id="job_' . $job->id . '"]',
					)
				));
				return;
			}
			if($from == 'job') {
				$content = View::factory('pages/jobs/block-job_buttons', array(
					'job' => $job,
					'from' => $from
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-job_buttons',
					)
				));
				return;
			}
			if($from == 'applications') {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => '.block-applications li[data-id="job_' . $job->id . '"]',
					)
				));
				return;
			}


			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'index'));
	}


	public function actionApplicantInvite($job_id, $user_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			$apply = Model_Job_Apply::getItemByUseridJobid($user_id, $job->id);
			if($apply->isViewed == 0) {
				$job->countNewApplicants --;
				$job->save();
			}

			Model_Job_Apply::update(array(
				'isInvited' => JOBAPPLY_ANSWER_APPROVE,
				'isViewed' => 1
			), array('user_id = ? AND job_id = ?', $user_id, $job->id));

			Model_Notifications::createApproveApplicant($user_id, $job);


			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			$applicant = Model_Job_Apply::getItemApplicantByJobidProfileid($job_id, $user_id);
			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}
			if($from == 'applicants') {
				$content = View::factory('pages/jobs/item-applicants', array(
					'applicant' => $applicant,
					'job' => $job
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-applicants li[data-id="applicant_' . $user_id . '"]',
					)
				));
				return;
			}
			if($from == 'applicant') {
				$content = View::factory('pages/jobs/block-applicant_buttons', array(
					'applicant' => $applicant,
					'job' => $job,
					'from' => 'applicants'
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-job_buttons',
					)
				));
				return;
			}



			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'applicant', $job_id));
	}


	public function actionApplicantDeny($job_id, $user_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			$apply = Model_Job_Apply::getItemByUseridJobid($user_id, $job->id);
			if($apply->isViewed == 0) {
				$job->countNewApplicants --;
				$job->save();
			}

			Model_Job_Apply::update(array(
				'isInvited' => JOBAPPLY_ANSWER_DENY,
				'isViewed' => 1
			), array('user_id = ? AND job_id = ?', $user_id, $job->id));

			Model_Notifications::createDenyApplicant($user_id, $job);


			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			$applicant = Model_Job_Apply::getItemApplicantByJobidProfileid($job_id, $user_id);
			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}
			if($from == 'applicants') {
				$content = View::factory('pages/jobs/item-applicants', array(
					'applicant' => $applicant,
					'job' => $job
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-applicants li[data-id="applicant_' . $user_id . '"]',
					)
				));
				return;
			}
			if($from == 'applicant') {
				$content = View::factory('pages/jobs/block-applicant_buttons', array(
					'applicant' => $applicant,
					'job' => $job,
					'from' => 'applicants'
				));

				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $content,
						'target' => '.block-job_buttons',
					)
				));
				return;
			}



			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'applicant', $job_id));
	}




	public function actionApplicantDelete($job_id, $user_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);
			$apply = Model_Job_Apply::getItemByUseridJobid($user_id, $job->id);

			if($apply->isInvited != JOBAPPLY_ANSWER_DENY) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('jobs', 'applicant', array($job->id, $user_id))
					)
				));
				return;
			}

			if($apply->isViewed == 0) {
				$job->countNewApplicants --;
			}
			$job->countApplicants --;
			$job->save();

			Model_Job_Apply::update(array(
				'isInvited' => JOBAPPLY_ANSWER_DENY,
				'isViewed' => 1,
				'isRemovedJobOwner' => 1
			), array('user_id = ? AND job_id = ?', $user_id, $job->id));

			Model_Notifications::createDenyApplicant($user_id, $job);



			$from = Request::get('from', FALSE);
			if(!$from) {
				$from = 'myJobs';
			}
			if($from == 'applicants') {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'removeItem',
					'data' => array(
						'target' => '.block-applicants li[data-id="applicant_' . $user_id . '"]',
					)
				));
				return;
			}
			if($from == 'applicant') {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'redirect',
					'data' => array(
						'url' => Request::generateUri('jobs', 'applicants', $job->id)
					)
				));
				return;
			}

			$this->response->body = json_encode(array(
				'status' => true
			));
			return;
		}
		$this->response->redirect(Request::generateUri('jobs', 'applicant', $job_id));
	}

}