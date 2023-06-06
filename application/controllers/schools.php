
<?php

class Schools_Controller extends Controller_User
{

	protected $subactive = 'schools';

	public function  before() {
		parent::before();
		$this->view->script('/js/libs/fileuploader.js');
		$this->view->script('/js/uploader.js');
	}

	public function __call($action, $params)
	{
		$this->actionIndex($action);
	}


	public function actionIndex($school_id = false)
	{
		if(!$school_id) {
			$this->response->redirect(Request::generateUri('schools', 'updates'));
			die();
		}

		$school = Model_Universities::getItemById($school_id, $this->user->id);

		if($school->isAgree == 0) {
			$this->response->redirect(Request::generateUri('schools', 'edit', $school->id));
			die();
		}

		$timelinesSchool = Model_Timeline::getListByUserIdSchoolId($this->user->id, $school->id);
		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($timelinesSchool['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesSchool['paginator']) . '</li>';

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
			unset($_SESSION['uploader-list']);
		}




		if($school->user_id != $this->user->id) {
//			Model_Visits::create(array(
//				'user_id' => $this->user->id,
//				'company_id' => $company->id
//			));
			$f_Updates_AddUpdate = false;
		} else {
			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();
			$f_Updates_AddUpdate->setUpdateFromSchool($school->id);
		}


		$notableAlumni = Model_Profile_Education::getListBySchoolId($school->id, array('isNotableAlumni' => TRUE), TRUE, FALSE);
		$staffMember = Model_Profile_Experience::getListStaffMemberBySchoolid($school->id, SCHOOL_TYPEMEMBER_STAFF);
		$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $this->user->id);
		$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);

		$f_Schools_SelectTypeInSchool = new Form_Schools_SelectTypeInSchool($school);

		$this->view->title = 'View school "' . $school->name . '"';

		$view = new View('pages/schools/index', array(
			// Left top panel
			'school' => $school,

			// Left down panel
			'school' => $school,
			'f_Updates_AddUpdate' => $f_Updates_AddUpdate,
			'timelinesSchool' => $timelinesSchool,

			// Right panel
			'notableAlumni' => $notableAlumni,
			'profile_experiance' => $profile_experiance,
			'profile_education' => $profile_education,
			'staffMember' => $staffMember,
			'f_Schools_SelectTypeInSchool' => $f_Schools_SelectTypeInSchool
		));
		$this->view->content = $view;

	}


	public function actionEdit($school_id)
	{
		$this->view->title = 'Edit school';
		$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);

		$f_Schools_EditSchool = new Form_Schools_EditSchool($school);
		$notanleAlumni = Model_Profile_Education::getListBySchoolId($school->id, array('isNotableAlumni' => TRUE), TRUE, FALSE);
		$staffMember = Model_Profile_Experience::getListStaffMemberBySchoolid($school->id);


		$isError = false;
		if(Request::isPost()) {
			if($f_Schools_EditSchool->form->validate()) {
				$values = $f_Schools_EditSchool->form->getValues();

				Model_Profile_Education::update(array(
					'isNotableAlumni' => NULL
				), array('university_id = ?', $school->id));

				if(isset($_SESSION['edit_school'][$school->id]) && !empty($_SESSION['edit_school'][$school->id])) {
					$keys_students = array_keys($_SESSION['edit_school'][$school->id]);
					if(!empty($keys_students)) {
						Model_Profile_Education::update(array(
							'isNotableAlumni' => 1
						), array('university_id = ? AND user_id in (?)', $school->id, $keys_students));
					}
				}

				if(isset($_SESSION['edit_school_member'][$school->id]) && !empty($_SESSION['edit_school_member'][$school->id])) {

					$keys_allow_member = array();
					$keys_deny_member = array();
					foreach($_SESSION['edit_school_member'][$school->id] as $id => $status){
						if($status === TRUE) {
							$keys_allow_member[] = $id;
							continue;
						}
						if($status === FALSE) {
							$keys_deny_member[] = $id;
							continue;
						}
					}

					if(!empty($keys_allow_member)) {
						Model_Profile_Experience::update(array(
							'isSchoolMember' => 1
						), array('university_id = ? AND user_id in (?)', $school->id, $keys_allow_member));
					}
					if(!empty($keys_deny_member)) {
						Model_Profile_Experience::update(array(
							'isSchoolMember' => NULL
						), array('university_id = ? AND user_id in (?)', $school->id, $keys_deny_member));
					}
				}

				if($values['name'] == $school->name) {
					$values['yearFounded'] = $values['year'];
					unset($values['name'], $values['year'], $values['notable_alumni']);
					Model_Universities::update($values, $school->id);
					$school_id2 = $school->id;
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
				$this->response->redirect(Request::generateUri('schools', $school_id2));

			} else {
				$isError = true;
			}
		} else {
			$f_Schools_EditSchool->setValues($school);

			unset($_SESSION['edit_school']);
			$notable_alumni = array();
			foreach($notanleAlumni['data'] as $student) {
				$notable_alumni[$student->userId] = TRUE;
			}
			$_SESSION['edit_school'][$school->id] = $notable_alumni;




			unset($_SESSION['edit_school_member']);
			$staff_member = array();
			foreach($staffMember['data'] as $member) {
				if($member->profileSchoolMember == 1) {
					$staff_member[$member->userId] = TRUE;
					continue;
				}
				if($member->profileSchoolMember === '0') {
					$staff_member[$member->userId] = NULL;
					continue;
				}
			}
			$_SESSION['edit_school_member'][$school->id] = $staff_member;
		}

		if(!$school->isAgree) {
			$useOfTerm = Model_Pages::getItemByCategory(POPUP_CATEGORY_CREATE_SCHOOL);
			if($useOfTerm) {
				$useOfTerm = $useOfTerm->text;
				$f_Schools_EditSchool->setUseOfTerms($useOfTerm);
			}
		}

		$view = new View('pages/schools/edit', array(
			'f_Schools_EditSchool' => $f_Schools_EditSchool,
			'school' => $school,
			'notanleAlumni' => $notanleAlumni,
			'staffMember' => $staffMember
		));

		$this->view->content = $view;
	}


	public function actionFollowers($school_id)
	{
		$school = Model_Universities::getItemById($school_id, $this->user->id);

		if($school->isAgree == 0) {
			$this->response->redirect(Request::generateUri('schools', 'edit', $school->id));
		}

		if(is_null($school->followUserId)) {
			$this->response->redirect(Request::generateUri('schools', $school->id));
		}

		$followers = Model_University_Follow::getListBySchoolId($school->id);
		$staffMember = Model_Profile_Experience::getListStaffMemberBySchoolid($school->id, SCHOOL_TYPEMEMBER_STAFF);
		$notableAlumni = Model_Profile_Education::getListBySchoolId($school->id, array('isNotableAlumni' => TRUE), TRUE, FALSE);
		$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $this->user->id);
		$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);

		$f_Schools_SelectTypeInSchool = new Form_Schools_SelectTypeInSchool($school);

		$this->view->title = 'Followers school "' . $school->name . '"';

		$view = new View('pages/schools/followers', array(
			// Left top panel
			'school' => $school,

			// Left down panel
			'school' => $school,
			'followers' => $followers,

			// Right panel
			'profile_experiance' => $profile_experiance,
			'profile_education' => $profile_education,
			'notableAlumni' => $notableAlumni,
			'staffMember' => $staffMember,
			'f_Schools_SelectTypeInSchool' => $f_Schools_SelectTypeInSchool
		));
		$this->view->content = $view;
	}


	public function actionSelectTypeInSchool($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);
			$f_Schools_SelectTypeInSchool = new Form_Schools_SelectTypeInSchool($school);

			if($f_Schools_SelectTypeInSchool->form->validate())
			{
				$values = $f_Schools_SelectTypeInSchool->form->getValues();

				switch($values['type']) {
					case 0:
						Model_Profile_Experience::update(array(
								'isSchoolMember' => NULL
							), array(
							'user_id = ? AND university_id = ? AND isSchoolMember IS NOT NULL', $this->user->id, $school->id
						));
						Model_Profile_Education::remove(array(
							'user_id = ? AND university_id = ?', $this->user->id, $school->id
						));

						// remove count for auto complite
						$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);
						$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $this->user->id);
						$countDelete = 0;
						if($profile_education) {
							$countDelete ++;
						}
						if($profile_experiance) {
							$countDelete ++;
						}
						if($countDelete > 0 ) {
							Model_Universities::update(array(
								'countUsed' => $school->countUsed - $countDelete
							), $school->id);
						}

						$message = 'Saved!';
						break;
					case 1:
						$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);

						if($profile_education) {
							Model_Profile_Education::update(array(
//								'yearTo' => NULL
								'isTypeInSchool' => 1
							), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
						} else {
							Model_Profile_Education::create(array(
								'user_id' => $this->user->id,
								'university_id' => $school->id,
//								'yearTo' => NULL,
								'isTypeInSchool' => 1
							));
							Model_Universities::update(array(
								'countUsed' => $school->countUsed + 1
							), $school->id);
						}
						Model_Profile_Experience::update(array(
							'isSchoolMember' => NULL
						), array(
							'user_id = ? AND university_id = ? AND isSchoolMember IS NOT NULL', $this->user->id, $school->id
						));
						$message = 'Saved. You are student in this school!';
						break;
					case 2:
						$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);

						if($profile_education) {
							if($profile_education->yearTo >= date('Y') || is_null($profile_education->yearTo)) {
								Model_Profile_Education::update(array(
//									'yearTo' => (date('Y') - 1),
									'isTypeInSchool' => 2
								), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
							}
						} else {
							Model_Profile_Education::create(array(
								'user_id' => $this->user->id,
								'university_id' => $school->id,
//								'yearTo' => (date('Y') - 1),
								'isTypeInSchool' => 2
							));
							Model_Universities::update(array(
								'countUsed' => $school->countUsed + 1
							), $school->id);
						}
						Model_Profile_Experience::update(array(
							'isSchoolMember' => NULL
						), array(
							'user_id = ? AND university_id = ? AND isSchoolMember IS NOT NULL', $this->user->id, $school->id
						));
						$message = 'Saved. You are alumni in this school!';
						break;
					case 3:
						$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $this->user->id);

						if($profile_experiance) {
							Model_Profile_Experience::update(array(
								'isSchoolMember' => 0
							), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
						} else {
							Model_Profile_Experience::create(array(
								'user_id' => $this->user->id,
								'university_id' => $school->id,
								'isSchoolMember' => 0
							));
							Model_Universities::update(array(
								'countUsed' => $school->countUsed + 1
							), $school->id);
						}
						$message = 'Saved. Request to staff member is sent!';
						break;
					case 4:
						$message = 'Saved. Request to staff member is sent!';
						break;
				}


			}

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'popupShow',
				'data' => array(
					'title' => 'Message',
					'content' => $message
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', $school_id));
	}


	public function actionStudentsAlumni($school_id)
	{
		$school = Model_Universities::getItemById($school_id, $this->user->id);

		if($school->isAgree == 0) {
			$this->response->redirect(Request::generateUri('schools', 'edit', $school->id));
		}

		if(is_null($school->followUserId)) {
			$this->response->redirect(Request::generateUri('schools', $school->id));
		}

		$f_Schools_FindStudentsAlumni = new Form_Schools_FindStudentsAlumni($school->id);
		if(Request::isAjax()) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$query = Request::get('findstudentsalumni', false);
			$students = Model_Profile_Education::getListBySchoolId($school->id, $query);

			$view = '';
			foreach($students['data'] as $student) {
				$view .= View::factory('pages/schools/list-students_alumni', array(
					'student' => $student
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('schools', 'studentsAlumni', $school_id),
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $students['paginator']) . '</li>';



			if(!Request::get('pagedown', false)) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeInnerContent',
					'data' => array(
						'content' => (string) $view,
						'target' => '.block-students_alumni .students_alumni_result .list-items',
						'function_name' => 'changeInnerContent',
						'data' => array(
							'content' => $students['paginator']['count'],
							'target' => '.block-students_alumni .text-bgtitle span'
						)
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $view,
						'target' => '.students_alumni_result > .list-items > li:last-child'
					)
				));
			}
			return false;
		}

		$students = Model_Profile_Education::getListBySchoolId($school->id);
		$notableAlumni = Model_Profile_Education::getListBySchoolId($school->id, array('isNotableAlumni' => TRUE), TRUE, FALSE);


		$this->view->title = 'Students and alumni school "' . $school->name . '"';

		$view = new View('pages/schools/students_alumni', array(
			// Left top panel
			'school' => $school,

			// Left down panel
			'school' => $school,
			'students' => $students,
			'f_Schools_FindStudentsAlumni' => $f_Schools_FindStudentsAlumni,
			'notableAlumni' => $notableAlumni

			// Right panel

		));
		$this->view->content = $view;
	}


	public function actionViewYourSchool()
	{

		$f_Schools_FindStudentsAlumni = new Form_Schools_FindStudentsAlumni(false);
		$f_Schools_FindStudentsAlumni->setAllschool();
		if(Request::isAjax()) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$query = Request::get('findstudentsalumni', false);
			if(!isset($query['school']) || $query['school'] == 'all') {
				$query['school'] = array_keys($f_Schools_FindStudentsAlumni->schools);
			}

			$students = Model_Profile_Education::getListBySchoolId($query['school'], $query);

			$view = '';
			foreach($students['data'] as $student) {
				$view .= View::factory('pages/schools/item-students_school', array(
					'student' => $student
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'controller' => Request::generateUri('schools', 'viewYourSchool'),
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $students['paginator']) . '</li>';



			if(!Request::get('pagedown', false)) {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeInnerContent',
					'data' => array(
						'content' => (string) $view,
						'target' => '.block-students_schools .students_schools_result .list-items',
						'function_name' => 'changeInnerContent',
						'data' => array(
							'content' => $students['paginator']['count'],
							'target' => '.block-students_schools .text-bgtitle span'
						)
					)
				));
			} else {
				$this->response->body = json_encode(array(
					'status' => true,
					'function_name' => 'changeContent',
					'data' => array(
						'content' => (string) $view,
						'target' => '.students_schools_result > .list-items > li:last-child'
					)
				));
			}
			return false;
		}

		$ids = array_keys($f_Schools_FindStudentsAlumni->schools);
		if(!empty($ids)) {
			$students = Model_Profile_Education::getListBySchoolId($ids);
		} else {
			$students = array(
				'data' => array(),
				'paginator' => array()
			);
		}

		$this->view->title = 'Schools';

		$view = new View('pages/schools/schools', array(
			// Left top panel

			// Left down panel
			'students' => $students,
			'f_Schools_FindStudentsAlumni' => $f_Schools_FindStudentsAlumni

			// Right panel

		));
		$this->view->content = $view;
	}

	public function actionUpdates()
	{
		$this->view->title = 'Schools updates';

		$schoolsManage = Model_Universities::getListMySchools($this->user->id);

		$followSchools = Model_University_Follow::getListSchoolsIdByUserId($this->user->id);
		$interestedSchool = Model_Universities::getListInterestedSchoolByUserid($this->user->id);
		if(count($interestedSchool['data']) < 6) {
			$interestedSchool2 = Model_Universities::getListInterestedSchoolByUserid_WithoutNyFriendsFollow($this->user->id);
			$count = count($interestedSchool['data']);
			foreach($interestedSchool2['data'] as $key => $interest) {
				$interestedSchool['data'][$key] = $interest;

				if(count($interestedSchool['data']) >= 6) {
					break;
				}
			}

		}

		$schoolsKey = array_keys($followSchools['data']);

		$timelinesSchools= false;
		if(!empty($schoolsKey)) {
			$timelinesSchools = Model_Timeline::getListByUserIdSchoolId($this->user->id, $schoolsKey);
		}


		if(Request::get('pagedown', false) && Request::$isAjax) {
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$view = '';
			foreach($timelinesSchools['data'] as $timeline) {
				$view .= View::factory('pages/updates/item-update', array(
					'timeline' => $timeline,
					'isUsernameLink' => TRUE
				));
			}
			$view .= '<li>' . View::factory('common/default-pages', array(
						'isBand' => TRUE,
						'autoScroll' => TRUE
					) + $timelinesSchools['paginator']) . '</li>';

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


		$view = new View('pages/schools/updates', array(
			// Left top panel

			// Left down panel
			'timelinesSchools' => $timelinesSchools,

			// Right panel
			'schoolsManage' => $schoolsManage,
			'followSchools' => $followSchools,
			'interestedSchool' => $interestedSchool
		));
		$this->view->content = $view;

	}


	public function actionCreateSchool()
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$f_Schools_CreateSchool = new Form_Schools_CreateSchool();

			$isError = false;
			if(Request::isPost()){
				if($f_Schools_CreateSchool->form->validate()){
					$values = $f_Schools_CreateSchool->form->getValues();

					$school = Model_Universities::getByName($values['schoolName']);
					if(!$school) {
						$school = Model_Universities::create(array(
							'name' => $values['schoolName'],
                            'isRegistered' => 1,
                            'user_id' => $this->user->id,
                            'isAgree' => 1
                        ));
					}

					$confirm = serialize(array(
						'email' => $values['email'],
						'school_id' => $school->id
					));
					Confirmations::generate($this->user->id, Confirmations::USER, Confirmations::CREATESCHOOL, $confirm);

                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'redirect',
                        'data' => array(
                            'url' => Request::generateUri('schools', 'edit', $school->id)
                        )
                    ));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Create schools page',
				'content' => View::factory('popups/schools/createschool', array(
						'f_Schools_CreateSchool' => $f_Schools_CreateSchool->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content,
				'popupsize' => 'message'
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', 'index'));
	}

	public function actionRemoveAva($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);
			Model_Files::removeByType($school->id, FILE_SCHOOL_AVA);

			$school->avaToken = NULL;
			$school->save();

			$content = View::factory('pages/schools/block-ava_logo', array(
				'school' => $school
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-schoolava'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', 'edit', $school_id));
	}

	public function actionRemoveCover($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);
			Model_Files::removeByType($school->id, FILE_SCHOOL_COVER);

			$school->coverToken = NULL;
			$school->save();

			$content = View::factory('pages/schools/block-ava_cover', array(
				'school' => $school
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$content,
					'target' => '.block-schoolcover'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('school', 'edit', $school_id));
	}

	public function actionCropAva($school_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);

			$image = Model_Files::getByToken($school->avaToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('schools', 'edit', $school_id));
	}

	public function actionCropCover($school_id, $isSave = FALSE)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);

			$image = Model_Files::getByToken($school->coverToken);
			$message = Model_Files::cropImage($image->id, $isSave);

			$this->response->body = json_encode($message);
			return;

		}

		$this->response->redirect(Request::generateUri('schools', 'edit', $school_id));
	}

	public function actionRemove($school_id)
	{
		$this->view->title = 'Remove school';
		$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);

		Model_Files::update(array(
			'parent_id' => 0
		), array('token = ? AND type = ? AND parent_id = ?', $school->avaToken, FILE_SCHOOL_AVA, $school->id));
		Model_Files::update(array(
			'parent_id' => 0
		), array('token = ? AND type = ? AND parent_id = ?', $school->coverToken, FILE_SCHOOL_COVER, $school->id));
        if(! Model_Universities::isOwnerSchool($this->user->id, $school->id)){
            $this->message('School hasn\'t been removed!');
            $this->response->redirect(Request::generateUri('schools', 'updates'));
        }
        Model_Universities::remove($school->id);
		Model_University_Follow::remove(array('univercity_id = ?', $school->id));

		$this->message('School has been succesfully removed!');
		$this->response->redirect(Request::generateUri('schools', 'updates'));
	}


	public function actionUpdate($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getItemById($school_id, $this->user->id);

			$f_Updates_AddUpdate = new Form_Updates_AddUpdate();


			$isError = false;
			if(Request::isPost()) {
				if($f_Updates_AddUpdate->form->validate()) {
					$timeline = Updates::newUpdate($f_Updates_AddUpdate, $this->user, false, false, $school);

//					$timeline = Model_Timeline::getItemById($timeline->id, $this->user->id, $company->id);
//					Model_Posts::update(array(
//						'company_id' => $company_id
//					), $timeline->post_id);

					$f_Updates_AddUpdate->form->clearValues();

					$newTimeline = Model_Timeline::getItemById($timeline->id, $this->user->id, FALSE, FALSE, FALSE, $school->id);
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

//	public function actionSetStaffMember($school_id)
//	{
//		$school = Model_Universities::getItemById($school_id, $this->user->id);
//		$profile_experiance = Model_Profile_Experience::checkUniversityBySchoolidUserid($school->id, $this->user->id);
//
//		if($profile_experiance) {
//			Model_Profile_Experience::update(array(
//				'isSchoolMember' => 0
//			), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
//		} else {
//			Model_Profile_Experience::create(array(
//				'user_id' => $this->user->id,
//				'university_id' => $school->id,
//				'isSchoolMember' => 0
//			));
//		}
//		$this->actionIndex($school_id);
//	}

//	public function actionSetStudent($school_id)
//	{
//		$school = Model_Universities::getItemById($school_id, $this->user->id);
//		$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);
//
//		if($profile_education) {
//			Model_Profile_Education::update(array(
//				'yearTo' => NULL
//			), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
//		} else {
//			Model_Profile_Education::create(array(
//				'user_id' => $this->user->id,
//				'university_id' => $school->id,
//				'yearTo' => NULL
//			));
//		}
//		$this->actionIndex($school_id);
//	}

//	public function actionSetAlumni($school_id)
//	{
//		$school = Model_Universities::getItemById($school_id, $this->user->id);
//		$profile_education = Model_Profile_Education::checkUniversityBySchoolidUserid($school->id, $this->user->id);
//
//		if($profile_education) {
//			if($profile_education->yearTo >= date('Y') || is_null($profile_education->yearTo)) {
//				Model_Profile_Education::update(array(
//					'yearTo' => (date('Y') - 1)
//				), array('user_id = ? AND university_id = ?', $this->user->id, $school->id));
//			}
//		} else {
//			Model_Profile_Education::create(array(
//				'user_id' => $this->user->id,
//				'university_id' => $school->id,
//				'yearTo' => (date('Y') - 1)
//			));
//		}
//		$this->actionIndex($school_id);
//	}


	public function actionFollow($school_id)
	{
		$this->view->title = 'Follow/unfollow school';

		$school = $this->follow($school_id);

		$this->response->redirect(Request::generateUri('schools', $school->id));
	}

	public function actionAddNotableAlumni($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);

			$f_Schools_AddNotableAlumni = new Form_Schools_AddNotableAlumni($school);
			if($f_Schools_AddNotableAlumni->countStudents == 0) {
				$message = 'There are no students of your school yet. Please, find students first and then choose notable alumni.';
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
			if(Request::isPost()){
				if($f_Schools_AddNotableAlumni->form->validate()){
					$values = $f_Schools_AddNotableAlumni->form->getValues();

					$student = Model_Profile_Education::getItemStudentByIdSchoolid($values['selectedStudent'], $school->id);

					$edit_school = array();
					if(isset($_SESSION['edit_school'])) {
						$edit_school = $_SESSION['edit_school'];
					}
					$edit_school[$school->id][$student->id] = true;
					$_SESSION['edit_school'] = $edit_school;

					$view = View::factory('pages/schools/item-notablealumni_in_settings', array(
						'student' => $student,
						'school' => $school
					));
					$this->response->body = json_encode(array(
						'status' => true,
						'content' => '',
						'function_name' => 'addBlock',
						'data' => array(
							'content' => (string)$view,
							'target' => '.editschool-notable_alumni > .list-items > li:first-child'
						)
					));
					return;
				} else {
					$isError = true;
				}
			}

			$content = View::factory('parts/pbox-form', array(
				'title' => 'Add notable alumni',
				'content' => View::factory('popups/schools/addnotablealumni', array(
						'f_Schools_AddNotableAlumni' => $f_Schools_AddNotableAlumni->form
					))
			));

			$this->response->body = json_encode(array(
				'status' => (!$isError),
				'content' => (string)$content,
				'popupsize' => 'message'
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', $school_id));
	}



	public function actionRemoveNotableAlumni($school_id, $profile_education_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);
			$student = Model_Profile_Education::getItemStudentByIdSchoolid($profile_education_id, $school->id);

			$edit_school = array();
			if(isset($_SESSION['edit_school'])) {
				$edit_school = $_SESSION['edit_school'];
			}
			unset($edit_school[$school->id][$student->id]);
			$_SESSION['edit_school'] = $edit_school;

			$this->response->body = json_encode(array(
				'status' => true,
				'content' => '',
				'function_name' => 'removeItem',
				'data' => array(
					'target' => '.editschool-notable_alumni li[data-id="student_' . $student->userId . '"]'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', $school_id));
	}


	public function actionStaffMemberApply($school_id, $profile_experiance_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);
			$member = Model_Profile_Experience::getItemMemeberByIdSchoolid($profile_experiance_id, $school->id);

			$edit_school = array();
			if(isset($_SESSION['edit_school_member'])) {
				$edit_school = $_SESSION['edit_school_member'];
			}

			$edit_school[$school->id][$member->userId] = TRUE;
			$_SESSION['edit_school_member'] = $edit_school;


			$member->profileSchoolMember = 1;
			$view = View::factory('pages/schools/item-staffmember_in_settings', array(
				'member' => $member,
				'school' => $school
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'content' => '',
				'function_name' => 'changeContent',
				'data' => array(
					'content' => (string)$view,
					'target' => '.editschool-staff_member li[data-id="member_' . $member->userId . '"]'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', $school_id));
	}

	public function actionStaffMemberDelete($school_id, $profile_experiance_id)
	{
		$this->actionStaffMemberDeny($school_id, $profile_experiance_id);
	}

	public function actionStaffMemberDeny($school_id, $profile_experiance_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = Model_Universities::getUserIdSchoolId($this->user->id, $school_id);
			$member = Model_Profile_Experience::getItemMemeberByIdSchoolid($profile_experiance_id, $school->id);

			$edit_school = array();
			if(isset($_SESSION['edit_school_member'])) {
				$edit_school = $_SESSION['edit_school_member'];
			}

			$edit_school[$school->id][$member->userId] = FALSE;
			$_SESSION['edit_school_member'] = $edit_school;

			$this->response->body = json_encode(array(
				'status' => true,
				'content' => '',
				'function_name' => 'removeItem',
				'data' => array(
					'target' => '.editschool-staff_member li[data-id="member_' . $member->userId . '"]'
				)
			));
			return;
		}

		$this->response->redirect(Request::generateUri('schools', $school_id));
	}

	protected function follow($school_id)
	{
		$school = Model_Universities::getItemById($school_id, $this->user->id);

		if(!is_null($school->followUserId)) {
			Model_University_Follow::remove(array('univercity_id = ? AND user_id = ?', $school->id, $this->user->id));
			$school->countFollowers -= 1;
			$school->save();
			$school->followUserId = NULL;
		} else {
			Model_University_Follow::create(array(
				'univercity_id' => $school->id,
				'user_id' => $this->user->id
			));
			$school->countFollowers += 1;
			$school->save();
			$school->followUserId = $this->user->id;
		}

		return $school;
	}

	public function actionFollowFromSearch($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$school = $this->follow($school_id);

			$view = View::factory('pages/search/school/item-search-results', array(
				'school' => $school
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeContent',
				'data' => array(
					'target' => 'li[data-id="school_' . $school->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('schools', $school_id));
	}

	public function actionFollowFromList($school_id)
	{
		if(Request::$isAjax){
			$this->autoRender = false;
			$this->response->setHeader('Content-Type', 'text/json');

			$this->follow($school_id);
			$school = Model_Universities::getItemById($school_id, $this->user->id);

			$view = View::factory('parts/schoolava-more', array(
				'school' => $school,
				'avasize' => 'avasize_52',
				'isCustomInfo' => TRUE,
				'isLinkProfile' => FALSE,
				'isSchoolNameLink' => TRUE,
				'isFollowButton' => TRUE
			));

			$this->response->body = json_encode(array(
				'status' => true,
				'function_name' => 'changeInnerContent',
				'data' => array(
					'target' => 'li[data-id="school_' . $school->id . '"]',
					'content' => (string) $view
				)
			));
			return;
		}
		$this->response->redirect(Request::generateUri('groups', $group_id));
	}


}