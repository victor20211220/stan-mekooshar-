<?php

class Profile_Controller extends Controller_User
{
    protected $subactive = 'profile';

    public function  before() {
        parent::before();
    }

    public function __call($user_id, $args)
    {
        $this->actionIndex($user_id);
    }

    public function actionIndex($profile_id = false)
    {
        if(!$profile_id) {
            $user_id = $this->user->id;
            $this->view->title = 'Profile page';
            $this->view->active = 'profile';
            $profile = $this->user;
            $isConnections = false;
        } else {
            $this->view->active = 'profile';
            $this->subactive = 'other_profile';

            $converted = (int)$profile_id;
            if(strlen($profile_id) == strlen($converted)) {
                $profile = new Model_User($profile_id);
                $profile = new Model_User($profile_id);
                $profile = Model_User::getById_withComplaint($profile_id);
            } else {
                $profile = Model_User::getByAlias_withComplaint($profile_id);
            }
            $user_id = $profile->id;
            $this->view->title = $profile->firstName . ' ' . $profile->lastName;

            $isConnections = Model_Connections::getConnectinsWithUsers($this->user->id, $profile->id);
            if($this->user->id != $profile->id){
                Model_Visits::create(array(
                    'user_id' => $this->user->id,
                    'profile_id' => $profile->id
                ));

                Model_Notifications::createViewProfileNotification($this->user->id, $profile->id);
            }
        }
        $banners = Model_Pages::getListBanners($profile);

        $f_findInProfile = new Form_FindInProfile($profile->id);
        $items_experience = Model_Profile_Experience::getListByUser($user_id);
        $items_languages = Model_Profile_Language::getListByUser($user_id);
        $items_skills = Model_Profile_Skills::getListByUser($user_id);
        $items_educations = Model_Profile_Education::getListByUser($user_id);
        $items_projects = Model_Profile_Project::getListByUser($user_id);
        $items_testscores = Model_Profile_TestScore::getListByUser($user_id);
        $items_certifications = Model_Profile_Certification::getListByUser($user_id);
        $items_connections = Model_Connections::getListConnectionsByUser($user_id);
        $follow_companies = Model_Company_Follow::getListByUserId($user_id);
        $follow_groups = Model_Group_Members::getListByUserId($user_id);


        $userinfo_experience = $items_experience;
        $userinfo_education = Model_Profile_Education::getOneLastByUser($user_id);

        $countInSearch = Model_ConnectionSearchResult::countInSearchResult($this->user->id);
        $countVisits = Model_Visits::countVisits($this->user->id);
        $connectionsMayKnow = Model_Connections::getListMayKnowConnectionsByUser($this->user->id);
        $connectionsAlsoViewed = Model_Visits::getListAlsoViewedConnectionsByUser($this->user->id);

        $keys_skill = array();
        foreach($items_skills['data'] as $skill) {
            $keys_skill[$skill->skill_id] = true;
        }
        $keys_skill = array_keys($keys_skill);
        $endorsements = array();

        if(!empty($keys_skill)) {
            $skill_endorsement = Model_SkillsEndorsement::getListByProfileidSkillskey($user_id, $keys_skill);
            foreach($skill_endorsement['data'] as $endorsement) {
                $endorsements[$endorsement->skill_id][$endorsement->userId] = $endorsement;
            }
        }





        $address = array();
        if($profile) {
            !empty($profile->address) ? $address[] = $profile->address : null;
            !empty($profile->city) ? $address[] = $profile->city : null;
            if(!empty($user->state)) {
                if(!empty($user->country) && $user->country == 'US' && isset($states[$user->state])) {
                    $address[] = $states[$user->state];
                } else {
                    $address[] = $user->state;
                }
            }
            !empty($profile->zip) ? $address[] = $profile->zip : null;
            !empty($profile->country) ? $address[] = t('countries.' . $profile->country) : null;
        }
        $profile->fullAddress = implode(', ', $address);

        $view = new View('pages/profile/index', array(
            // Left top panel
            'profile' => $profile,
            'isConnections' => $isConnections,
            'userinfo_experience' => $userinfo_experience,
            'userinfo_education' => $userinfo_education,

            // Left down panel
            'f_findInProfile' => $f_findInProfile,
            'items_experience' => $items_experience,
            'items_languages' => $items_languages,
            'items_skills' => $items_skills,
            'items_educations' => $items_educations,
            'items_projects' => $items_projects,
            'items_testscores' => $items_testscores,
            'items_certifications' => $items_certifications,
            'items_connections' => $items_connections,
            'follow_companies' => $follow_companies,
            'follow_groups' => $follow_groups,
            'skill_endorsement' => $endorsements,

            // Right panel
            'countInSearch' => $countInSearch,
            'countVisits' => $countVisits,
            'connectionsMayKnow' => $connectionsMayKnow,
            'connectionsAlsoViewed' => $connectionsAlsoViewed,

            'banners' => $banners,
        ));
        $this->view->content = $view;
    }

    public function actionEdit()
    {
        $this->view->title = 'Edit profile page';
        $this->view->active = 'profile';

        $items_experience = Model_Profile_Experience::getListByUser($this->user->id);
        $items_languages = Model_Profile_Language::getListByUser($this->user->id);
        $items_skills = Model_Profile_Skills::getListByUser($this->user->id);
        $items_educations = Model_Profile_Education::getListByUser($this->user->id);
        $items_projects = Model_Profile_Project::getListByUser($this->user->id);
        $items_testscores = Model_Profile_TestScore::getListByUser($this->user->id);
        $items_certifications = Model_Profile_Certification::getListByUser($this->user->id);
        $items_connections = Model_Connections::getListConnectionsByUser($this->user->id);


        $userinfo_experience = $items_experience;
        $userinfo_education = Model_Profile_Education::getOneLastByUser($this->user->id);

        $countInSearch = Model_ConnectionSearchResult::countInSearchResult($this->user->id);
        $countVisits = Model_Visits::countVisits($this->user->id);
        $connectionsMayKnow = Model_Connections::getListMayKnowConnectionsByUser($this->user->id);
        $connectionsAlsoViewed = Model_Visits::getListAlsoViewedConnectionsByUser($this->user->id);


        $keys_skill = array();
        foreach($items_skills['data'] as $skill) {
            $keys_skill[$skill->skill_id] = true;
        }
        $keys_skill = array_keys($keys_skill);
        $endorsements = array();

        if(!empty($keys_skill)) {
            $skill_endorsement = Model_SkillsEndorsement::getListByProfileidSkillskey($this->user->id, $keys_skill);
            foreach($skill_endorsement['data'] as $endorsement) {
                $endorsements[$endorsement->skill_id][$endorsement->userId] = $endorsement;
            }
        }

        $address = array();
        if($this->user) {
            !empty($this->user->address) ? $address[] = $this->user->address : null;
            !empty($this->user->city) ? $address[] = $this->user->city : null;
            !empty($this->user->state) ? $address[] = (!empty($this->user->country) && $this->user->country == 'US') ? t('states.' . $this->user->state) : $this->user->state : null;
            !empty($this->user->zip) ? $address[] = $this->user->zip : null;
            !empty($this->user->country) ? $address[] = t('countries.' . $this->user->country) : null;
        }
        $this->user->fullAddress = implode(', ', $address);

        $view = new View('pages/profile/edit', array(
            // Left top panel
            'profile' => $this->user,
            'isConnections' => FALSE,
            'items_connections' => $items_connections,
            'userinfo_experience' => $userinfo_experience,
            'userinfo_education' => $userinfo_education,

            // Left down panel
            'items_experience' => $items_experience,
            'items_languages' => $items_languages,
            'items_skills' => $items_skills,
            'items_educations' => $items_educations,
            'items_projects' => $items_projects,
            'items_testscores' => $items_testscores,
            'items_certifications' => $items_certifications,
            'skill_endorsement' => $endorsements,

            // Right panel
            'countInSearch' => $countInSearch,
            'countVisits' => $countVisits,
            'connectionsMayKnow' => $connectionsMayKnow,
            'connectionsAlsoViewed' => $connectionsAlsoViewed
        ));
        $this->view->content = $view;
        $this->view->script('/js/libs/fileuploader.js');
        $this->view->script('/js/uploader.js');
    }

    public function actionConnections($profile_id = false)
    {
        if(!$profile_id) {
            $user_id = $this->user->id;
            $this->view->title = 'Profile connections page';
            $this->view->active = 'profile';
            $profile = $this->user;
            $isConnections = false;
        } else {
            $user_id = $profile_id;
            $this->view->active = 'profile';
            $this->subactive = 'other_profile';
            $profile = new Model_User($profile_id);
            $isShowConnections = Model_Connections::checkAllowMeToProffileConnections($profile, USER_LEVEL_ACCESS_SHOW_CONNECTIONS);
            if(!$isShowConnections) {
                $this->message('Sorry! Connections info is private!');
                $this->response->redirect(Request::generateUri('profile', $profile->id));
            }
            $isConnections = Model_Connections::getConnectinsWithUsers($this->user->id, $profile->id);
            if($this->user->id != $profile->id){
                Model_Visits::create(array(
                    'user_id' => $this->user->id,
                    'profile_id' => $profile->id
                ));

                Model_Notifications::createViewProfileNotification($this->user->id, $profile->id);
            }

            $this->view->title = $profile->firstName . ' ' . $profile->lastName . ' connections profile';
        }


        $items_connections = Model_Connections::getListConnectionsByUser($profile->id, array('limit' => 50));
        $items_experience = Model_Profile_Experience::getListByUser($profile->id);
        $userinfo_experience = $items_experience;
        $userinfo_education = Model_Profile_Education::getOneLastByUser($profile->id);

        $countInSearch = Model_ConnectionSearchResult::countInSearchResult($this->user->id);
        $countVisits = Model_Visits::countVisits($this->user->id);
        $connectionsMayKnow = Model_Connections::getListMayKnowConnectionsByUser($this->user->id);
        $connectionsAlsoViewed = Model_Visits::getListAlsoViewedConnectionsByUser($this->user->id);


        $address = array();
        if($profile) {
            !empty($profile->address) ? $address[] = $profile->address : null;
            !empty($profile->city) ? $address[] = $profile->city : null;
            !empty($profile->state) ? $address[] = (!empty($profile->country) && $profile->country == 'US') ? t('states.' . $profile->state) : $profile->state : null;
            !empty($profile->zip) ? $address[] = $profile->zip : null;
            !empty($profile->country) ? $address[] = t('countries.' . $profile->country) : null;
        }
        $profile->fullAddress = implode(', ', $address);

        $view = new View('pages/profile/connections', array(
            // Left top panel
            'profile' => $profile,
            'isConnections' => $isConnections,
            'userinfo_experience' => $userinfo_experience,
            'userinfo_education' => $userinfo_education,
            'items_connections' => $items_connections,

            // Left down panel


            // Right panel
            'countInSearch' => $countInSearch,
            'countVisits' => $countVisits,
            'connectionsMayKnow' => $connectionsMayKnow,
            'connectionsAlsoViewed' => $connectionsAlsoViewed
        ));
        $this->view->content = $view;
    }

    public function actionPrivacySettings()
    {
        $this->view->title = 'Privacy settings';
        $this->view->active = 'profile';

        $f_Profile_PrivacySettings = new Form_Profile_PrivacySettings();


        if(Request::isPost()) {
            if($f_Profile_PrivacySettings->form->validate()) {
                $values = $f_Profile_PrivacySettings->form->getValues();

                if(!empty($values['current']) && !empty($values['new'])) {
                    $password = Model_User::encryptPassword($values['new']);

                    Model_User::update(array(
                        'password' => $password
                    ), $this->user->id);

                    $mail = new Mailer('change-password');
                    $mail->firstName = $this->user->firstName;
                    $mail->send($this->user->email);
                }

                $current_blocked_list = Model_Profile_Blocked::getListBlockedUser();

                // Add blocked user
                if(isset($_SESSION['privacy_settings']['blocked']['add'])){
                    $ids = array_keys($_SESSION['privacy_settings']['blocked']['add']);

                    if(!empty($ids)) {

                        $profiles = Model_User::getByIds($ids);
                        foreach($ids as $id) {
                            if(!isset($current_blocked_list['data'][$id])) {
                                Model_Profile_Blocked::create(array(
                                    'user_id' => $this->user->id,
                                    'profile_id' => $id
                                ));


                                Model_Connections::deteteMyConnectionWithUser($id);
                                // Update level Connections
                                Model_User_Friends::removeFriends($id, $this->user->id);
//                                Model_User::updateOneUsersCountConnections($this->user);
//                                Model_User::updateOneUsersCountConnections($profiles['data'][$id]);
                                Auth::getInstance()->updateIdentity($this->user->id, TRUE);
                            }
                        }
                    }

                    unset($_SESSION['privacy_settings']['blocked']['add']);
                }


                // Remove blocked user
                if(isset($_SESSION['privacy_settings']['blocked']['remove'])){
                    $ids = array_keys($_SESSION['privacy_settings']['blocked']['remove']);

                    if(!empty($ids)) {
                        foreach($ids as $id) {
                            if(isset($current_blocked_list['data'][$id])) {
                                Model_Profile_Blocked::remove(array('user_id = ? AND profile_id = ?', $this->user->id, $id));
                            }
                        }
                    }

                    unset($_SESSION['privacy_settings']['blocked']['remove']);
                }
                unset($_SESSION['privacy_settings']);


                $value = array(
                    'shareActivityInActivityFeed' => $values['share_activity_in_activity_feed'],
                    'setInvisibleProfile' => $values['set_invisible_profile'],
//					'whoCanSeeActivity' => $values['can_see_activity'],
                    'whoCanSeeConnections' => $values['can_see_connections'],
                    'whoCanSeeContactInfo' => $values['can_see_contact_info'],
                );

                Model_User::update($value, $this->user->id);
                Auth::getInstance()->updateIdentity($this->user->id);
                $f_Profile_PrivacySettings->form->clearValues();
                $this->message('Privacy&Settings have been updated!');

                if(Request::isAjax()) {
                    $this->autoRender = false;
                    $this->response->setHeader('Content-Type', 'text/json');
                    $this->response->body = json_encode(array(
                        'status' => true
                    ));
                    return;
                }
            }
        } else {
            unset($_SESSION['privacy_settings']);
        }

        $f_Profile_PrivacySettings->setValues();

        $profile = Model_User::getItemByUserid($this->user->id);
        $blockedUsers = Model_Profile_Blocked::getListBlockedUser();

        $view = View::factory('pages/profile/privacy_settings', array(
            'profile' => $profile,
            'f_Profile_PrivacySettings' => $f_Profile_PrivacySettings,
            'blockedUsers' => $blockedUsers
        ));

        $this->view->content = $view;
    }

    public function actionUpgrade()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER'] ); //disable upgrade button
        exit;
        $this->view->title = 'Upgrade profile';
        $this->view->active = 'profile';

        $profile = Model_User::getItemByUserid($this->user->id);
        $view = View::factory('pages/profile/upgrade_profile', array(
            'profile' => $profile,
        ));

        $this->view->content = $view;
    }

    public function actionDisupgradeProfile()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            Model_User::update(array(
                'accountType' => ACCOUNT_TYPE_BASIC,
                'updateExp' => date('Y-m-d H:i:s', time() - 60)
            ), $this->user->id);
            Auth::getInstance()->updateIdentity($this->user->id);

            $profile = Model_User::getItemByUserid($this->user->id);
            $view = View::factory('pages/profile/upgrade_profile', array(
                'profile' => $profile,
            ));

            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$view,
                    'target' => '.user-upgrade_profile'
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'upgrade'));
    }

    public function actionStatistic()
    {
        $this->view->title = 'Statistic profile';
        $this->view->active = 'profile';

        $items_connections = Model_Connections::getListConnectionsByUser($this->user->id);
        $userinfo_experience = Model_Profile_Experience::getListByUser($this->user->id);
        $userinfo_education = Model_Profile_Education::getOneLastByUser($this->user->id);
        $connectionWhoVisitMyProfile = Model_Visits::getListWhoVisitMyProfileByUserId($this->user->id);

        $statisticByMonth = Model_Visits::getListMonthStatisticByUser($this->user->id);
        $statisticByDay = Model_Visits::getListDaysStatisticByUser($this->user->id);


        $tmp = array();
        for($i = 5; $i>=0; $i--) {
            $tmp[date('Y-m-01', time() - 60*60*24*31*$i)] = 0;
        }
        foreach($statisticByMonth['data'] as $statistic){
            $tmp[$statistic->id] += $statistic->countItems;
        }
        $statisticByMonth = $tmp;


        $i = 0;
        $j = 1;
        $statisticByWeek = array(
            4 => 0, 3 => 0, 2 => 0, 1 => 0,
        );
        foreach($statisticByDay['data'] as $statistic) {
            $i++;
            $statisticByWeek[$j] = $statistic->countItems;
            if($i == 7) {
                $i = 0;
                $j++;
            }
        }

//		dump($statisticByDay, 1);
        $i = 0;
        $tmp = array();
        for($i = 6; $i>=0; $i--) {
            $tmp[date('Y-m-d', time()-60*60*24*$i)] = 0;
        }
        foreach($statisticByDay['data'] as $statistic) {
            if(isset($tmp[$statistic->id])) {
                $tmp[$statistic->id] += $statistic->countItems;
            }
        }
        $statisticByDay = $tmp;
//		dump($tmp, 1);

        $f_Profile_ChangeGpaphStatistic = new Form_Profile_ChangeGpaphStatistic();

        $view = View::factory('pages/profile/statistic', array(
            'profile' => $this->user,
            'isConnections' => false,
            'userinfo_experience' => $userinfo_experience,
            'userinfo_education' => $userinfo_education,
            'items_connections' => $items_connections,

            'connectionWhoVisitMyProfile' => $connectionWhoVisitMyProfile,

            'statisticByMonth' => $statisticByMonth,
            'statisticByWeek' => $statisticByWeek,
            'statisticByDay' => $statisticByDay,
            'f_Profile_ChangeGpaphStatistic' => $f_Profile_ChangeGpaphStatistic
        ));


        $this->view->script('http://www.google.com/jsapi');
        $this->view->content = $view;
    }

    public function actionEditUserName()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditUserName = new Form_Profile_EditUserName();

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditUserName->form->validate()) {
                    $values = $f_Profile_EditUserName->form->getValues();
                    Model_User::update($values, $this->user->id);

                    // Update session
                    $auth = Auth::getInstance();
                    $auth->updateIdentity($this->user->id, true);
                    $this->user = $auth->getIdentity();
                    View::$global->user = $this->user;

                    $userinfo_experience = Model_Profile_Experience::getListByUser($this->user->id);;
                    $userinfo_education = Model_Profile_Education::getOneLastByUser($this->user->id);
                    $items_connections = Model_Connections::getListConnectionsByUser($this->user->id);

                    $content = View::factory('/pages/profile/edit/user-info', array(
                        'userinfo_experience' => $userinfo_experience,
                        'userinfo_education' => $userinfo_education,
                        'profile' => $this->user,
                        'items_connections' => $items_connections
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-userinfo'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
                $values = array(
                    'firstName' => $this->user->firstName,
                    'lastName' => $this->user->lastName
                );
                $f_Profile_EditUserName->form->loadValues($values);
            }

            $content = $f_Profile_EditUserName->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditUserName->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.userinfo-name'
                    )
                ));
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionEditHeadline()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditHeadline = new Form_Profile_EditHeadline();

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditHeadline->form->validate()) {
                    $values = $f_Profile_EditHeadline->form->getValues();

                    $country = t('countries');
                    $industries = t('industries');
                    if(!isset($country[$values['country']])){
                        $values['country'] = '';
                    }
                    if(!isset($industries[$values['industry']])){
                        $values['industry'] = NULL;
                    }

                    Model_User::update($values, $this->user->id);

                    // Update session
                    $auth = Auth::getInstance();
                    $auth->updateIdentity($this->user->id, true);
                    $this->user = $auth->getIdentity();
                    View::$global->user = $this->user;

                    $userinfo_experience = Model_Profile_Experience::getListByUser($this->user->id);;
                    $userinfo_education = Model_Profile_Education::getOneLastByUser($this->user->id);
                    $items_connections = Model_Connections::getListConnectionsByUser($this->user->id);

                    $content = View::factory('/pages/profile/edit/user-info', array(
                        'userinfo_experience' => $userinfo_experience,
                        'userinfo_education' => $userinfo_education,
                        'profile' => $this->user,
                        'items_connections' => $items_connections
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-userinfo'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
                $values = array(
                    'professionalHeadline' => $this->user->professionalHeadline,
                    'country' => $this->user->country,
                    'industry' => $this->user->industry
                );
                $f_Profile_EditHeadline->form->loadValues($values);
            }

            $content = $f_Profile_EditHeadline->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditHeadline->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.userinfo-headline'
                    )
                ));
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionEditUserInfo()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_form_Profile_Userinfo = new Form_Profile_Userinfo();

            $isError = false;
            if(Request::isPost()) {
                if($f_form_Profile_Userinfo->form->validate()) {
                    $values = $f_form_Profile_Userinfo->form->getValues();


                    $profile = $f_form_Profile_Userinfo->getPost($values);
                    $profile['websites'] = serialize($profile['websites']);

                    Model_User::update($profile, $this->user->id);

                    // Update session
                    $auth = Auth::getInstance();
                    $auth->updateIdentity($this->user->id, true);
                    $this->user = $auth->getIdentity();
                    View::$global->user = $this->user;

                    $userinfo_experience = Model_Profile_Experience::getListByUser($this->user->id);;
                    $userinfo_education = Model_Profile_Education::getOneLastByUser($this->user->id);
                    $items_connections = Model_Connections::getListConnectionsByUser($this->user->id);

                    $content = View::factory('/pages/profile/edit/user-info', array(
                        'userinfo_experience' => $userinfo_experience,
                        'userinfo_education' => $userinfo_education,
                        'profile' => $this->user,
                        'items_connections' => $items_connections
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-userinfo'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
                $f_form_Profile_Userinfo->setValue();
            }

            $content = $f_form_Profile_Userinfo->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_form_Profile_Userinfo->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.userinfo-contactinfo'
                    )
                ));
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }

    public function actionAddSummary()
    {
        return $this->actionEditSummary();
    }

    public function actionEditSummary()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditSummary = new Form_Profile_EditSummary();

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditSummary->form->validate()) {
                    $values = $f_Profile_EditSummary->form->getValues();

                    Model_User::update($values, $this->user->id);

                    // Update session
                    $auth = Auth::getInstance();
                    $auth->updateIdentity($this->user->id, true);
                    $this->user = $auth->getIdentity();
                    View::$global->user = $this->user;

                    $content = View::factory('/pages/profile/edit/block-summary', array(
                        'isEdit' => true,
                        'profile' => $this->user
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-summary'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
                $values = array(
                    'summaryText' => $this->user->summaryText
                );
                $f_Profile_EditSummary->form->loadValues($values);
            }

            $content = $f_Profile_EditSummary->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditSummary->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.summary-text'
                    )
                ));
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionAddExperience()
    {
        $this->actionEditExperience();
    }

    public function actionEditExperience($id = false)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditExperience = new Form_Profile_EditExperience($id);
            if($id) {
                $item = Model_Profile_Experience::getItemById($id, $this->user->id);
                $f_Profile_EditExperience->setValue($item);
            }

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditExperience->form->validate()) {
                    $values = $f_Profile_EditExperience->getPost();
                    $profile = array();

                    if(substr($values['company'], 0, 1) == 'c'){
                        $profile['company_id'] = substr($values['company'], 1);
                        $profile['university_id'] = NULL;
                    } else {
                        $profile['company_id'] = NULL;
                        $profile['university_id'] = substr($values['company'], 1);
                    }

                    $profile['user_id'] = $this->user->id;
                    $profile['title'] = $values['title'];
                    $profile['location'] = $values['location'];
                    $profile['dateFrom'] = date("Y-m-t", strtotime($values['yearFrom'] . '-' . $values['monthFrom'] . '-01'));
                    if(!empty($values['isCurrent'])){
                        $profile['dateTo'] = NULL;
                    } else {
                        $profile['dateTo'] = date("Y-m-t", strtotime($values['yearTo'] . '-' . $values['monthTo'] . '-01'));
                    }
                    $profile['isCurrent'] = $values['isCurrent'];
                    if($id) {
                        Model_Profile_Experience::update($profile, $item->id);

                        if(!empty($item->company_id) && substr($values['company'], 0, 1) == 'c' && ('c' .$item->company_id) != $values['company']){
                            $company = Model_Companies::checkItemById_withUniversity('c' . $item->company_id);
                            Model_Companies::update(array(
                                'countUsed' => $company->countUsed - 1
                            ), $item->company_id);
                            Model_Companies::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        } elseif(!empty($item->company_id) && substr($values['company'], 0, 1) != 'c'){
                            $company = Model_Companies::checkItemById_withUniversity('c' . $item->company_id);
                            Model_Companies::update(array(
                                'countUsed' => $company->countUsed - 1
                            ), $item->company_id);
                            Model_Universities::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        } elseif(!empty($item->university_id) && substr($values['company'], 0, 1) == 'u' && ('u' .$item->university_id) != $values['company']){
                            $university = Model_Companies::checkItemById_withUniversity('u' . $item->university_id);
                            Model_Universities::update(array(
                                'countUsed' => $university->countUsed - 1
                            ), $item->university_id);
                            Model_Universities::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        } elseif(!empty($item->university_id) && substr($values['company'], 0, 1) != 'u'){
                            $university = Model_Companies::checkItemById_withUniversity('u' . $item->university_id);
                            Model_Universities::update(array(
                                'countUsed' => $university->countUsed - 1
                            ), $item->university_id);
                            Model_Companies::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        }
                    } else {
                        $new_id = Model_Profile_Experience::create($profile);
                        Model_Timeline::createNewJob($this->user->id, $new_id->id, $values['title'], $values['company']);

                        if(substr($values['company'], 0, 1) == 'c') {
                            Model_Companies::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        } elseif (substr($values['company'], 0, 1) == 'u') {
                            Model_Universities::update(array(
                                'countUsed' => $f_Profile_EditExperience->experience->countUsed + 1
                            ), substr($values['company'], 1));
                        }
                    }

                    $items_experience = Model_Profile_Experience::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-experience', array(
                        'items_experience' => $items_experience,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-experience'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
//				$values = array(
//					'summaryText' => $this->user->summaryText
//				);
//				$f_Profile_EditExperience->form->loadValues($values);
            }

            $content = $f_Profile_EditExperience->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditExperience->form->attributes['id']
                    )
                ));
            } else {
                if($id) {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'editBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-experience li[data-id="' . $id . '"] > div'
                        )
                    ));
                } else {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-experience .profile-title'
                        )
                    ));
                }
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionRemoveExperience($id)
    {
        $item = new Model_Profile_Experience($id);

        if(!empty($item->company_id)){
            $company = Model_Companies::checkItemById_withUniversity('c' . $item->company_id);
            Model_Companies::update(array(
                'countUsed' => $company->countUsed - 1
            ), $item->company_id);
        } elseif(!empty($item->university_id)) {
            $university = Model_Companies::checkItemById_withUniversity('u' . $item->university_id);
            Model_Universities::update(array(
                'countUsed' => $university->countUsed - 1
            ), $item->university_id);
        }
        Model_Profile_Experience::remove($item->id);

        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');
            $items_experience = Model_Profile_Experience::getListByUser($this->user->id);
            $content = View::factory('/pages/profile/edit/block-experience', array(
                'items_experience' => $items_experience,
                'isEdit' => TRUE
            ));
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.block-experience'
                )
            ));
            return;
        } else {
            $this->response->redirect(Request::generateUri('profile', 'edit'));
        }
    }



    public function actionAddLanguage()
    {
        $this->actionEditLanguage();
    }

    public function actionEditLanguage()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $items = Model_Profile_Language::getListByUser($this->user->id);
            $f_Profile_EditLanguages = new Form_Profile_EditLanguages($items);

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditLanguages->form->validate()) {
                    $values = $f_Profile_EditLanguages->form->getValues();

                    $languages = $f_Profile_EditLanguages->getPost($values);

                    $ids_remove = array();
                    foreach($items['data'] as $key => $item) {
                        $isFinded = FALSE;
                        foreach($languages as $key => $language){
                            if($language['id'] == $item->language_id){
                                $isFinded = TRUE;
                                break;
                            }
                        }
                        if(!$isFinded) {
                            $ids_remove[] = $item->language_id;
                            Model_Languages::update(array(
                                'countUsed' => $item->languageCountUsed - 1
                            ), $item->language_id);
                        }
                    }
                    if(!empty($ids_remove)) {
                        Model_Profile_Language::remove(array('user_id = ? AND language_id in (?)', $this->user->id, $ids_remove));
                    }


                    // Change in profile
                    $firstKey = array_keys(t('language_level'));
                    $firstKey = $firstKey[0];

                    foreach($languages as $key => $language){
                        if(empty($language['levelType']) || $language['levelType'] == 0) {
                            $language['levelType'] = $firstKey;
                        }

                        foreach($items['data'] as $key => $item) {
                            if($language['levelType'] != $item->levelType && $language['id'] == $item->language_id){
                                $isChanged = TRUE;
                                Model_Profile_Language::update(array(
                                    'levelType' => $language['levelType']
                                ), array('user_id = ? AND language_id = ?', $this->user->id, $item->language_id));
                                break;
                            }
                        }
                    }


                    // Create new in profile
                    $arrayCreated = array();
                    foreach($languages as $key => $language){
                        $isFinded = FALSE;

                        if(in_array($language['id'], $arrayCreated)) {
                            continue;
                        }

                        foreach($items['data'] as $key => $item) {
                            if($language['id'] == $item->language_id){
                                $isFinded = TRUE;
                                break;
                            }
                        }
                        if(!$isFinded) {
                            $arrayCreated[] = $language['id'];
                            Model_Profile_Language::create(array(
                                'user_id' => $this->user->id,
                                'language_id' => $language['id'],
                                'levelType' => empty($language['levelType']) ? $firstKey : $language['levelType']
                            ));
                            Model_Languages::update(array(
                                'countUsed' => $language['countUsed'] + 1
                            ), $language['id']);
                        }
                    }
                    $items_languages = Model_Profile_Language::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-languages', array(
                        'items_languages' => $items_languages,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-landuages'
                        )
                    ));
                    return;

                } else {
                    $isError = true;
                }
            }


            $content = $f_Profile_EditLanguages->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditLanguages->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.landuages-bloks'
                    )
                ));

            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionAddSkills()
    {
        $this->actionEditSkills();
    }

    public function actionEditSkills()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $items = Model_Profile_Skills::getListByUser($this->user->id);
            $f_Profile_EditSkills = new Form_Profile_EditSkills($items);


            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditSkills->form->validate()) {
                    $values = $f_Profile_EditSkills->form->getValues();
                    $skills = $f_Profile_EditSkills->getPost($values);

                    // Get Skills from db
                    $skills_ids = array();
                    foreach($skills as $skill) {
                        $skills_ids[] = $skill['id'];
                    }
                    $skills_from_db = Model_Skills::getListByIds($skills_ids);

//					// Add new skills and get id for name
//					foreach($skills as $key => $skill) {
//						$isFinded = false;
//						foreach($skills_from_db['data'] as $skill_db){
//							if($skill_db->name == $skill['name']) {
//								$isFinded = true;
//								$skills[$key]['id'] = $skill_db->id;
//								continue;
//							}
//						}
//
//						if(!$isFinded) {
//							$new_id = Model_Skills::create(array('name' => $skill['name']));
//							$skills[$key]['id'] = $new_id->id;
//						}
//					}

                    // Add skills to profile
                    foreach($skills as $skill){
                        $isInProfile = false;
                        foreach($items['data'] as $skill_profile){
                            if($skill_profile->skill_id == $skill['id']){
                                $isInProfile = true;
                                continue;
                            }
                        }

                        if(!$isInProfile){
                            Model_Profile_Skills::create(array(
                                'user_id' => $this->user->id,
                                'skill_id' => $skill['id']
                            ));
                            Model_Skills::update(array(
                                'countUsed' => $skills_from_db['data'][$skill['id']]->countUsed + 1
                            ), $skill['id']);
                        }
                    }

                    // Remove skills from profile
                    foreach($items['data'] as $skill_profile){
                        $isSetSkill = false;
                        foreach($skills as $skill){
                            if($skill_profile->skill_id == $skill['id']){
                                $isSetSkill = true;
                                continue;
                            }
                        }

                        if(!$isSetSkill){
                            Model_Skills::update(array(
                                'countUsed' => $skill_profile->skillCountUsed - 1
                            ), $skill_profile->skill_id);

                            Model_Profile_Skills::remove(array('user_id = ? AND skill_id = ?', $this->user->id, $skill_profile->skill_id ));
                            Model_SkillsEndorsement::remove(array('owner_id = ? AND skill_id = ?', $this->user->id, $skill_profile->skill_id ));
                        }
                    }

                    // Get Updated data
                    $items_skills = Model_Profile_Skills::getListByUser($this->user->id);
                    $keys_skill = array();
                    foreach($items_skills['data'] as $skill) {
                        $keys_skill[$skill->skill_id] = true;
                    }
                    $keys_skill = array_keys($keys_skill);
                    $endorsements = array();

                    if(!empty($keys_skill)) {
                        $skill_endorsement = Model_SkillsEndorsement::getListByProfileidSkillskey($this->user->id, $keys_skill);
                        foreach($skill_endorsement['data'] as $endorsement) {
                            $endorsements[$endorsement->skill_id][$endorsement->userId] = $endorsement;
                        }
                    }


                    $content = View::factory('/pages/profile/edit/block-skills', array(
                        'items_skills' => $items_skills,
                        'skill_endorsement' => $endorsements,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-skills'
                        )
                    ));
                    return;

                } else {
                    $isError = true;
                }
            }

            $content = $f_Profile_EditSkills->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditSkills->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.skills_block_inner'
                    )
                ));

            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionAddEducation()
    {
        $this->actionEditEducation();
    }

    public function actionEditEducation($id = false)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $universityName = false;
            if($id) {
                $item = Model_Profile_Education::getItemById($id, $this->user->id);
                $universityName = $item->universityName;
            }

            $f_Profile_EditEducation = new Form_Profile_EditEducation($id, $universityName);
            if($id) {
                $f_Profile_EditEducation->setValue($item);
            }

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditEducation->form->validate()) {
                    $values = $f_Profile_EditEducation->getPost();
                    $profile = array();

//					$university = Model_Universities::getByName($values['university']);
//					if(!$university){
//						$university = Model_Universities::create(array('name' => $values['university']));
//					}

                    $profile['university_id'] = $values['university'];
                    $profile['user_id'] = $this->user->id;
                    $profile['yearFrom'] = $values['yearFrom'];
                    $profile['yearTo'] = $values['yearTo'];
                    $profile['fieldOfStudy'] = $values['fieldOfStudy'];
                    $profile['degree'] = $values['degree'];
                    $profile['grade'] = $values['grade'];
//					$profile['description'] = $values['description'];
                    $profile['activitiesAndSocieties'] = $values['activitiesAndSocieties'];
                    if(empty($profile['yearFrom'])) {
                        $profile['yearFrom'] = NULL;
                    }
                    if(empty($profile['yearTo'])) {
                        $profile['yearTo'] = NULL;
                    }


                    if($id) {
                        Model_Profile_Education::update($profile, $item->id);

                        if($item->university_id != $values['university']) {
                            $university = Model_Universities::checkItemById($item->university_id);
                            Model_Universities::update(array(
                                'countUsed' => $university->countUsed - 1
                            ), $university->id);
                            Model_Universities::update(array(
                                'countUsed' => $f_Profile_EditEducation->university->countUsed + 1
                            ), $values['university']);
                        }
                    } else {
                        $new_id = Model_Profile_Education::create($profile);

                        Model_Universities::update(array(
                            'countUsed' => $f_Profile_EditEducation->university->countUsed + 1
                        ), $values['university']);
                    }

                    $items_educations = Model_Profile_Education::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-educations', array(
                        'items_educations' => $items_educations,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-educations'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            }

            $content = $f_Profile_EditEducation->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditEducation->form->attributes['id']
                    )
                ));
            } else {
                if($id) {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'editBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-educations li[data-id="' . $id . '"] > div'
                        )
                    ));
                } else {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-educations .profile-title'
                        )
                    ));
                }
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionRemoveEducation($id)
    {
        $item = new Model_Profile_Education($id);

        $university = Model_Universities::checkItemById($item->university_id);
        Model_Universities::update(array(
            'countUsed' => $university->countUsed - 1
        ), $university->id);
        Model_Profile_Education::remove($item->id);

        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');
            $items_educations = Model_Profile_Education::getListByUser($this->user->id);
            $content = View::factory('/pages/profile/edit/block-educations', array(
                'items_educations' => $items_educations,
                'isEdit' => TRUE
            ));
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.block-educations'
                )
            ));
            return;
        } else {
            $this->response->redirect(Request::generateUri('profile', 'edit'));
        }
    }



    public function actionEditAdditional()
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditAdditional = new Form_Profile_EditAdditional();

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditAdditional->form->validate()) {
                    $values = $f_Profile_EditAdditional->form->getValues();

                    $maritel_status = t('maritel_status');
                    if(!isset($maritel_status[$values['maritalStatus']])){
                        $values['maritalStatus'] = 0;
                    }


                    if(!empty($values['birthdayDate'])) {
                        $values['birthdayDate'] = date('Y-m-d H:i:s', strtotime($values['birthdayDate']));
                    } else {
                        $values['birthdayDate'] = NULL;
                    }

                    Model_User::update($values, $this->user->id);

                    // Update session
                    $auth = Auth::getInstance();
                    $auth->updateIdentity($this->user->id, true);
                    $this->user = $auth->getIdentity();
                    View::$global->user = $this->user;

                    $content = View::factory('/pages/profile/edit/block-addition_information', array(
                        'isEdit' => true,
                        'profile' => $this->user
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-addition_information'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            } else {
                $f_Profile_EditAdditional->setValue($this->user);
            }

            $content = $f_Profile_EditAdditional->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditAdditional->form->attributes['id']
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'editBlock',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '.block-addition_information-inner'
                    )
                ));
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionAddProject()
    {
        $this->actionEditProject();
    }

    public function actionEditProject($id = false)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');


            $f_Profile_EditProject = new Form_Profile_EditProject($id);

            if(count($f_Profile_EditProject->occupations) == 0) {
                $message = 'You can\'t add a project. First add Exrerience or Education please';

                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'popupShow',
                    'data' => array(
                        'content' => $message,
                        'title' => 'Message',
                        'function_name' => 'cancelEditBlock',
                        'data' => array()
                    )
                ));
                return;
            }

            if($id) {
                $item = Model_Profile_Project::getItemById($id, $this->user->id);
                $f_Profile_EditProject->setValue($item);
            }

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditProject->form->validate()) {
                    $values = $f_Profile_EditProject->getPost();
                    $profile = array();

//					$project = Model_Projects::getByName($values['project']);
//					if(!$project){
//						$project = Model_Projects::create(array('name' => $values['project']));
//					}

                    $profile['project_id'] = $values['project'];
                    $profile['user_id'] = $this->user->id;
                    if(substr($values['occupation'], 0, 2) == 'ex') {
                        $profile['occupation_experience_id'] = substr($values['occupation'], 2);
                    } else {
                        $profile['occupation_education_id'] = substr($values['occupation'], 2);
                    }
                    $profile['url'] = $values['url'];
                    $profile['dateFrom'] = date("Y-m-t", strtotime($values['yearFrom'] . '-' . $values['monthFrom'] . '-01'));
                    if(!empty($values['isCurrent'])){
                        $profile['dateTo'] = NULL;
                    } else {
                        $profile['dateTo'] = date("Y-m-t", strtotime($values['yearTo'] . '-' . $values['monthTo'] . '-01'));
                    }
                    $profile['isCurrent'] = $values['isCurrent'];
//					$profile['description'] = $values['description'];

                    if($id) {
                        Model_Profile_Project::update($profile, $item->id);

                        if($item->project_id != $values['project']) {
                            $project = Model_Projects::checkItemById($item->project_id);
                            Model_Projects::update(array(
                                'countUsed' => $project->countUsed - 1
                            ), $project->id);
                            Model_Projects::update(array(
                                'countUsed' => $f_Profile_EditProject->project->countUsed + 1
                            ), $values['project']);
                        }
                    } else {
                        $new_id = Model_Profile_Project::create($profile);
                        Model_Projects::update(array(
                            'countUsed' => $f_Profile_EditProject->project->countUsed + 1
                        ), $values['project']);
                    }

                    $items_projects = Model_Profile_Project::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-projects', array(
                        'items_projects' => $items_projects,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-projects'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            }

            $content = $f_Profile_EditProject->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditProject->form->attributes['id']
                    )
                ));
            } else {
                if($id) {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'editBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-projects li[data-id="' . $id . '"] > div'
                        )
                    ));
                } else {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-projects .profile-title'
                        )
                    ));
                }
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionRemoveProject($id)
    {
        $item = new Model_Profile_Project($id);

        $project = Model_Projects::checkItemById($item->project_id);
        Model_Projects::update(array(
            'countUsed' => $project->countUsed - 1
        ), $project->id);
        Model_Profile_Project::remove($item->id);

        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');
            $items_projects = Model_Profile_Project::getListByUser($this->user->id);
            $content = View::factory('/pages/profile/edit/block-projects', array(
                'items_projects' => $items_projects,
                'isEdit' => TRUE
            ));
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.block-projects'
                )
            ));
            return;
        } else {
            $this->response->redirect(Request::generateUri('profile', 'edit'));
        }
    }


    public function actionAddTestScore()
    {
        $this->actionEditTestScore();
    }

    public function actionEditTestScore($id = false)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditTestScore = new Form_Profile_EditTestScore($id);
            if($id) {
                $item = Model_Profile_TestScore::getItemById($id, $this->user->id);
                $f_Profile_EditTestScore->setValue($item);
            }

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditTestScore->form->validate()) {
                    $values = $f_Profile_EditTestScore->getPost();
                    $profile = array();

//					$testscore = Model_TestScores::getByName($values['testscore']);
//					if(!$testscore){
//						$testscore = Model_TestScores::create(array('name' => $values['testscore']));
//					}

                    $profile['testscore_id'] = $values['testscore'];
                    $profile['user_id'] = $this->user->id;
                    $profile['occupation'] = $values['occupation'];
                    $profile['score'] = $values['score'];
                    if(!empty($values['dateScore'])) {
                        $profile['dateScore'] = date('Y-m-d', strtotime($values['dateScore']));
                    } else {
                        $profile['dateScore'] = NULL;
                    }
                    $profile['url'] = $values['url'];
                    $profile['description'] = $values['description'];

                    if($id) {
                        Model_Profile_TestScore::update($profile, $item->id);

                        if($item->testscore_id != $values['testscore']) {
                            $testscore = Model_TestScores::checkItemById($item->testscore_id);
                            Model_TestScores::update(array(
                                'countUsed' => $testscore->countUsed - 1
                            ), $testscore->id);
                            Model_TestScores::update(array(
                                'countUsed' => $f_Profile_EditTestScore->testscore->countUsed + 1
                            ), $values['testscore']);
                        }
                    } else {
                        $new_id = Model_Profile_TestScore::create($profile);
                        Model_TestScores::update(array(
                            'countUsed' => $f_Profile_EditTestScore->testscore->countUsed + 1
                        ), $values['testscore']);
                    }

                    $items_testscores = Model_Profile_TestScore::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-testscore', array(
                        'items_testscores' => $items_testscores,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-testscore'
                        )
                    ));
                    return;
                } else {
                    $isError = true;
                }
            }

            $content = $f_Profile_EditTestScore->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditTestScore->form->attributes['id']
                    )
                ));
            } else {
                if($id) {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'editBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-testscore li[data-id="' . $id . '"] > div'
                        )
                    ));
                } else {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-testscore .profile-title'
                        )
                    ));
                }
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionRemoveTestScore($id)
    {
        $item = new Model_Profile_TestScore($id);

        $testscore = Model_TestScores::checkItemById($item->testscore_id);
        Model_TestScores::update(array(
            'countUsed' => $testscore->countUsed - 1
        ), $testscore->id);
        Model_Profile_TestScore::remove($item->id);

        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');
            $items_testscores = Model_Profile_TestScore::getListByUser($this->user->id);
            $content = View::factory('/pages/profile/edit/block-testscore', array(
                'items_testscores' => $items_testscores,
                'isEdit' => TRUE
            ));
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.block-testscore'
                )
            ));
            return;
        } else {
            $this->response->redirect(Request::generateUri('profile', 'edit'));
        }
    }

    public function actionAddCertification()
    {
        $this->actionEditCertification();
    }

    public function actionEditCertification($id = false)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_EditCertification = new Form_Profile_EditCertification($id);
            if($id) {
                $item = Model_Profile_Certification::getItemById($id, $this->user->id);
                $f_Profile_EditCertification->setValue($item);
            }

            $isError = false;
            if(Request::isPost()) {
                if($f_Profile_EditCertification->form->validate()) {
                    $values = $f_Profile_EditCertification->getPost();
                    $profile = array();

//					$certification = Model_Certifications::getByName($values['certification']);
//					if(!$certification){
//						$certification = Model_Certifications::create(array('name' => $values['certification']));
//					}

                    $authority = Model_CertificationAuthorities::getByName($values['authority']);
                    if(!$authority){
                        $authority = Model_CertificationAuthorities::create(array('name' => $values['authority']));
                    }

                    $profile['certification_id'] = $values['certification'];
                    $profile['certification_authority_id'] = $authority->id;
                    $profile['user_id'] = $this->user->id;
                    $profile['number'] = '';
                    $profile['dateFrom'] = date("Y-m-t", strtotime($values['yearFrom'] . '-' . $values['monthFrom'] . '-01'));
                    if(!empty($values['isCurrent'])){
                        $profile['dateTo'] = NULL;
                    } else {
                        $profile['dateTo'] = date("Y-m-t", strtotime($values['yearTo'] . '-' . $values['monthTo'] . '-01'));
                    }
                    $profile['isCurrent'] = $values['isCurrent'];
                    $profile['url'] = $values['url'];

                    if($id) {
                        Model_Profile_Certification::update($profile, $item->id);

                        if($item->certification_id != $values['certification']) {
                            $certification = Model_Certifications::checkItemById($item->certification_id);
                            Model_Certifications::update(array(
                                'countUsed' => $certification->countUsed - 1
                            ), $certification->id);
                            Model_Certifications::update(array(
                                'countUsed' => $f_Profile_EditCertification->certification->countUsed + 1
                            ), $values['certification']);
                        }
                    } else {
                        $new_id = Model_Profile_Certification::create($profile);
                        Model_Certifications::update(array(
                            'countUsed' => $f_Profile_EditCertification->certification->countUsed + 1
                        ), $values['certification']);
                    }

                    $items_certifications = Model_Profile_Certification::getListByUser($this->user->id);
                    $content = View::factory('/pages/profile/edit/block-certifications', array(
                        'items_certifications' => $items_certifications,
                        'isEdit' => true
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'changeContent',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-certifications'
                        )
                    ));
                    return;
                } else {
//					var_dump('ggg');exit;
                    $isError = true;
                }
            }

            $content = $f_Profile_EditCertification->form;
            if($isError) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'submitError',
                    'data' => array(
                        'content' => (string)$content,
                        'target' => '#' . $f_Profile_EditCertification->form->attributes['id']
                    )
                ));
            } else {
                if($id) {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'editBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-certifications li[data-id="' . $id . '"] > div'
                        )
                    ));
                } else {
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$content,
                            'target' => '.block-certifications .profile-title'
                        )
                    ));
                }
            }
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }


    public function actionRemoveCertification($id)
    {
        $item = new Model_Profile_Certification($id);

        $certification = Model_Certifications::checkItemById($item->certification_id);
        Model_Certifications::update(array(
            'countUsed' => $certification->countUsed - 1
        ), $certification->id);
        Model_Profile_Certification::remove($item->id);

        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');
            $items_certifications = Model_Profile_Certification::getListByUser($this->user->id);
            $content = View::factory('/pages/profile/edit/block-certifications', array(
                'items_certifications' => $items_certifications,
                'isEdit' => TRUE
            ));
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.block-certifications'
                )
            ));
            return;
        } else {
            $this->response->redirect(Request::generateUri('profile', 'edit'));
        }
    }

    public function actionCropAva($isSave = FALSE)
    {
        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $image = Model_Files::getByToken($this->user->avaToken);
            $message = Model_Files::cropImage($image->id, $isSave);

            $this->response->body = json_encode($message);
            return;

        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }

    public function actionRemoveAva()
    {
        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            Model_Files::removeByType($this->user->id, FILE_USER_AVA);
            Model_User::update(array(
                'avaToken' => NULL
            ), $this->user->id);


            $auth = Auth::getInstance();
            $auth->updateIdentity($this->user->id);
            $user = $auth->getIdentity();

            $content = View::factory('pages/profile/edit/ava-block', array(
                'profile' => $user
            ));

            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => '.userinfo-editfoto'
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }

    public function actionEndorseSkill($profile_id, $skill_id)
    {
        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $profile = Model_User::getRealUser($profile_id);
            $skill = Model_Profile_Skills::checkIssetChecked($profile_id, $skill_id, $this->user->id);

            $isConnections = Model_Connections::getConnectinsWithUsers($this->user->id, $profile->id);

            $isConnected = FALSE;
            foreach($isConnections['data'] as $isConnection){
                if($isConnection->typeApproved == 1) {
                    $isConnected = true;
                    break;
                }
            }

            if(!$skill || $profile->id == $this->user->id) {
                $this->response->body = json_encode(array(
                    'status' => false
                ));
                return;
            } elseif(is_null($skill->endorsementSkill)) {
                if(!$isConnected) {
                    $this->response->body = json_encode(array(
                        'status' => false
                    ));
                    return;
                }

                Model_SkillsEndorsement::create(array(
                    'user_id' => $this->user->id,
                    'owner_id' => $profile->id,
                    'skill_id' => $skill->skill_id,
                ));
                Model_Notifications::createEndorseSkillNotification($this->user->id, $profile->id, $skill->skill_id, $skill->skillName);
                Model_Profile_Skills::update(array(
                    'countEndorse' => ($skill->countEndorse + 1)
                ), array('user_id = ? AND skill_id = ?', $skill->user_id, $skill->skill_id));
            } else {
                Model_SkillsEndorsement::remove(array('user_id = ? AND owner_id = ? AND skill_id = ?', $this->user->id, $profile->id, $skill->skill_id));
                Model_Profile_Skills::update(array(
                    'countEndorse' => ($skill->countEndorse - 1)
                ), array('user_id = ? AND skill_id = ?', $skill->user_id, $skill->skill_id));
            }

            $item_skill = Model_Profile_Skills::getItemByUser($profile->id, $skill->skill_id);
            $skill_endorsement = Model_SkillsEndorsement::getListByProfileidSkillskey($profile->id, $skill->skill_id);
            $endorsements = array();
            foreach($skill_endorsement['data'] as $endorsement) {
                $endorsements[$endorsement->skill_id][$endorsement->userId] = $endorsement;
            }

            $content = View::factory('pages/profile/edit/item-skills', array(
                'skill' => $item_skill,
                'skill_endorsement' => $endorsements,
                'isConnected' => $isConnected
            ));

            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$content,
                    'target' => 'li[data-id="skill_' . $skill->skill_id . '"]',
                    'function_name' => 'initGallery',
                    'data' => array(
                        'gallery' => '.user-gallery'
                    )
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'edit'));
    }

    public function actionAddBlockUser()
    {
        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $f_Profile_AddBlockUser = new Form_Profile_AddBlockUser();
            $isError = FALSE;
            if(Request::isPost()) {
                if($f_Profile_AddBlockUser->form->validate()){

                    $user = $f_Profile_AddBlockUser->finded_profile;

                    $isBlockedInDB = Model_Profile_Blocked::checkIsBlockedUser($user->id);
                    if(!$isBlockedInDB) {
                        $_SESSION['privacy_settings']['blocked']['add'][$user->id] = TRUE;
                    } else {
                        if(isset($_SESSION['privacy_settings']['blocked']['remove'][$user->id])) {
                            unset($_SESSION['privacy_settings']['blocked']['remove'][$user->id]);
                        }
                    }


                    $_SESSION['privacy_settings']['blocked']['add'][$user->id] = TRUE;


                    $view = View::factory('pages/profile/item-blocked_user', array(
                        'user' => $user
                    ));
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'content' => '',
                        'function_name' => 'addBlock',
                        'data' => array(
                            'content' => (string)$view,
                            'target' => '.privacy_settings-block_users > .list-items > li:first-child'
                        )
                    ));

                    return;
                } else {
                    $isError = TRUE;
                }
            }

            $content = View::factory('parts/pbox-form', array(
                'title' => 'Add user to block',
                'content' => View::factory('popups/profile/addblockuser', array(
                    'f_Profile_AddBlockUser' => $f_Profile_AddBlockUser->form
                ))
            ));

            $this->response->body = json_encode(array(
                'status' => (!$isError),
                'content' => (string)$content,
                'popupsize' => 'message'
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'privacySettings'));
    }

    public function actionRemoveBlockUser($profile_id)
    {
        if(Request::$isAjax){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $isBlockedInDB = Model_Profile_Blocked::checkIsBlockedUser($profile_id);
            if($isBlockedInDB) {
                $_SESSION['privacy_settings']['blocked']['remove'][$profile_id] = TRUE;
            } else {
                if(isset($_SESSION['privacy_settings']['blocked']['add'][$profile_id])) {
                    unset($_SESSION['privacy_settings']['blocked']['add'][$profile_id]);
                }
            }

            $this->response->body = json_encode(array(
                'status' => true,
                'content' => '',
                'function_name' => 'removeItem',
                'data' => array(
                    'target' => '.privacy_settings-block_users li[data-id="profile_' . $profile_id . '"]'
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', 'privacySettings'));
    }


    public function actionComplaint($profile_id)
    {
        if(Request::$isAjax && $profile_id != $this->user->id){
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            if(Model_Profile_Complaint::checkIsComplaint($profile_id)) {
                $message = 'You have already complaint on this user';
                $this->response->body = json_encode(array(
                    'status' => true,
                    'content' => '',
                    'function_name' => 'popupShow',
                    'data' => array(
                        'title' => 'Message',
                        'content' => $message
                    )
                ));

                return;
            }

            $profile = new Model_User($profile_id);
            $f_Profile_AddComplaint = new Form_Profile_AddComplaint($profile);

            $isError = FALSE;
            if(Request::isPost()){
                if($f_Profile_AddComplaint->form->validate()) {
                    $values = $f_Profile_AddComplaint->form->getValues();

                    Model_Profile_Complaint::create(array(
                        'user_id' => $this->user->id,
                        'profile_id' => $profile->id,
                        'description' => $values['description']
                    ));

                    $message = 'Your complaint was sent to Administrator';
                    $this->response->body = json_encode(array(
                        'status' => true,
                        'content' => '',
                        'function_name' => 'popupShow',
                        'data' => array(
                            'title' => 'Message',
                            'content' => $message,
                            'function_name' => 'removeItem',
                            'data' => array(
                                'target' => '.userinfo-complain'
                            )
                        )
                    ));

                    return;
                }
            }

            $content = View::factory('parts/pbox-form', array(
                'title' => 'Complaint on the user',
                'content' => View::factory('popups/profile/complaint', array(
                    'f_Profile_AddComplaint' => $f_Profile_AddComplaint->form
                ))
            ));

            $this->response->body = json_encode(array(
                'status' => (!$isError),
                'content' => (string)$content,
                'popupsize' => 'message'
            ));

            return;
        }

        $this->response->redirect(Request::generateUri('profile', $profile_id));
    }

    public function actionGetFollowUserGroups($profile_id)
    {
        if(Request::$isAjax && $profile_id != $this->user->id) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $profile = Model_User::getItemByUserid($profile_id);
            $follow_groups = Model_Group_Members::getListByUserId($profile_id);

            $view = '';
            foreach($follow_groups['data'] as $follow_group) {
                $view .= '<li data-id="group_' . $follow_group->id . '">' . View::factory('parts/groupsava-more', array(
                        'group' => $follow_group,
                        'avasize' => 'avasize_52',
                        'isGroupNameLink' => TRUE,
                        'isFollowButton' => ($profile->id == $this->user->id) ? TRUE : FALSE
                    )) . '</li>';
            }
            $view .= '<li>' . View::factory('common/default-pages', array(
                        'controller' => Request::generateUri('profile', 'getFollowUserGroups', $profile->id),
                        'isBand' => TRUE,
                        'autoScroll' => FALSE
                    ) + $follow_groups['paginator']) . '</li>';

            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$view,
                    'target' => '.block-groups-and-following_groups > li:last-child'
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', $profile_id));
    }

    public function actionGetFollowUserCompanies($profile_id)
    {
        if(Request::$isAjax && $profile_id != $this->user->id) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $profile = Model_User::getItemByUserid($profile_id);
            $follow_companies = Model_Company_Follow::getListByUserId($profile_id);

            $view = '';
            foreach($follow_companies['data'] as $follow_company) {
                $view .= '<li data-id="company_' . $follow_company->id . '">' . View::factory('parts/companiesava-more', array(
                        'company' => $follow_company,
                        'avasize' => 'avasize_52',
                        'isCompanyIndustry' => TRUE,
                        'isCompanyNameLink' => TRUE,
                        'isFollowButton' => ($profile->id == $this->user->id) ? TRUE : FALSE
                    )) . '</li>';
            }
            $view .= '<li>' . View::factory('common/default-pages', array(
                        'controller' => Request::generateUri('profile', 'getFollowUserCompanies', $profile->id),
                        'isBand' => TRUE,
                        'autoScroll' => FALSE
                    ) + $follow_companies['paginator']) . '</li>';

            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'changeContent',
                'data' => array(
                    'content' => (string)$view,
                    'target' => '.block-groups-and-following_companies > li:last-child'
                )
            ));
            return;
        }

        $this->response->redirect(Request::generateUri('profile', $profile_id));
    }

    public function actionGetListProfileConnection($profile_id)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            $profile = Model_User::getItemByUserid($profile_id);
            $f_findInProfile = new Form_FindInProfile($profile->id);

            $name_if_search = 'connections &nbsp;&nbsp;';
            $values = array();
            if($f_findInProfile->form->validate()) {
                $values = $f_findInProfile->form->getValues();
                $values['find'] = trim($values['find']);
                if(!empty($values['find'])) {
                    $name_if_search = 'finded &nbsp;&nbsp;';
                }
            }

            $items_connections = Model_Connections::getListConnectionsByUser($profile->id, $values);

            $view = '';
            foreach($items_connections['data'] as $connection) {
                $view .= View::factory('pages/profile/view/item-profile_connections', array(
                    'connection' => $connection
                ));
            }
            $view .= '<li>' . View::factory('common/default-pages', array(
                        'controller' => Request::generateUri('profile', 'getListProfileConnection', $profile->id),
                        'isBand' => TRUE,
                        'autoScroll' => FALSE
                    ) + $items_connections['paginator']) . '</li>';

            if($items_connections['paginator']['count'] == 0) {
                $view = '<li class="list-item-empty">Nothing found</li>';
            }


            if(!empty($values) && !isset($_GET['page'])) {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'changeInnerContent',
                    'data' => array(
                        'content' => (string)$view,
                        'target' => '.block-all_connections-connections',
                        'function_name' => 'changeInnerContent',
                        'data' => array(
                            'target' => '.block-all_connections .text-bgtitle',
                            'content' => $name_if_search . '<span>' . $items_connections['paginator']['count'] . '</span>'
                        )
                    )
                ));
            } else {
                $this->response->body = json_encode(array(
                    'status' => true,
                    'function_name' => 'changeContent',
                    'data' => array(
                        'content' => (string)$view,
                        'target' => '.block-all_connections-connections > li:last-child, .profile_connections_all > li:last-child',
                        'function_name' => 'changeInnerContent',
                        'data' => array(
                            'target' => '.block-all_connections .text-bgtitle',
                            'content' => $name_if_search . '<span>' . $items_connections['paginator']['count'] . '</span>'
                        )
                    )
                ));
            }

            return;
        }

        $this->response->redirect(Request::generateUri('profile', $profile_id));
    }
}