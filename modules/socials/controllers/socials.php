<?php
require_once APPLICATION_PATH . 'controllers/invites.php';
require_once '../vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;

class Socials_Controller extends Controller_Common
{
    public function before()
    {
        parent::before();
        define('OAUTH', MODULES_PATH . 'socials/library/oauth/');

        if (!isset($_SESSION)) {
            session_start();
        }
    }

    public function actionIndex()
    {
        $this->view->title = 'Socials page';

        $content = '';
        if (isset($_SESSION['socials'])) {
            $socials = $_SESSION['socials'];
            if (!isset($socials['error']) || $socials['error'] !== TRUE) {
                $content .= '<p>';
                $content .= 'ID: ' . $socials['id'] . '<br>';
                $content .= 'PROVIDER: ' . $socials['provider'] . '<br>';
                $content .= 'MAIL: ' . $socials['email'] . '<br>';
                $content .= 'FIRST NAME: ' . $socials['firstName'] . '<br>';
                $content .= 'LAST NAME: ' . $socials['lastName'] . '<br>';
                $content .= '</p>';
            }
            unset($_SESSION['socials']);
        }

        $content .= '<a style="color: #000" href="' . Request::generateUri(false, 'facebook') . '">Login in the Facebook</a><br>';
//		$content .= '<a style="color: #000" href="' . Request::generateUri(false, 'linkedin') . '">Login in the Linkedin</a><br>';

        $this->view->content = $content;
    }


    public function actionFacebook()
    {
        $this->response->redirect('https://' . Request::$host);

        $app_id = $this->config->socials->facebook->app_id;
        $app_secret = $this->config->socials->facebook->app_secret;

        $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret]);
        $permissions = ['public_profile', 'email'];

        $helper = $fb->getRedirectLoginHelper();
        $redirect_url = $helper->getLoginUrl('https://' . Request::$host . $this->config->socials->facebook->login->authfacebook, $permissions);
        $this->response->redirect($redirect_url);
    }

    public function actionAuthFacebook()
    {

        $app_id = $this->config->socials->facebook->app_id;
        $app_secret = $this->config->socials->facebook->app_secret;
        $fb = new Facebook\Facebook(['app_id' => $app_id, 'app_secret' => $app_secret]);
        $helper = $fb->getRedirectLoginHelper();
        $access_token = null;
        try {
            $access_token = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (empty($access_token)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            }
        }

        $res = $fb->get('/me', $access_token->getValue());
        $fbUser = $res->getDecodedBody();

        if (Model_User::exists('facebook_ID', $fbUser['id'])) {
            $auth = new Auth();
            $status = $auth->authenticateWithoutPassword($fbUser['id'], true, true);
            if ($status) {
                $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->login->successredirect);
            }
        }

        if (isset($_SESSION['inviteKey']['key'])) {
            $invite = new Invites_Controller();
            $inviting = $invite->getByKey($_SESSION['inviteKey']['key']);
            $isActiveKey = $invite->checkKey($_SESSION['inviteKey']['key']);

            if ($isActiveKey && $inviting['status']) {
                $user = Model_User::createUserFacebook($fbUser);
                $invite->addConnection($inviting['user_invite_id'], $user->id);
                $invite->addFollower($inviting['id'], $user->id);

                $auth = new Auth();
                $status = $auth->authenticateWithoutPassword($fbUser['id'], true, true);
                if ($status) {
                    $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->login->successredirect);
                }
            } else {
                unset($_SESSION['socials']);
                $_SESSION['socials']['error'] = true;
                $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->auth->errorredirect);
            }
        }

        unset($_SESSION['socials']);
        $_SESSION['socials']['error'] = true;
        $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->auth->errorredirect);
    }

    function actionAuthGoogle()
    {
        $clientID = $this->config->socials->google->app_id;
        $clientSecret = $this->config->socials->google->app_secret;
        $redirectUri = 'https://' . Request::$host . $this->config->socials->google->redirect_uri;

        $client = new Google_Client();
        $client->setClientId($clientID);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->addScope("email");
        $client->addScope("profile");

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);

            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();

            $this->authUser((array)$google_account_info);
        } else {
            $this->response->redirect($client->createAuthUrl());
        }
    }

    function authUser($data){
        if(!isset($data['id']))  $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->auth->errorredirect);

        if (Model_User::exists('facebook_ID', $data['id'])) {
            $auth = new Auth();
            $status = $auth->authenticateWithoutPassword($data['id'], true, true);
            if ($status) {
                $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->login->successredirect);
            }
        }

        if (1) {
//            $invite = new Invites_Controller();
//            $inviting = $invite->getByKey($_SESSION['inviteKey']['key']);
//            $isActiveKey = $invite->checkKey($_SESSION['inviteKey']['key']);


            if (1) {
                $user = Model_User::createUserFacebook($data);
//                $invite->addConnection($inviting['user_invite_id'], $user->id);
//                $invite->addFollower($inviting['id'], $user->id);


                $auth = new Auth();
                $status = $auth->authenticateWithoutPassword($user->facebook_ID, true, true);
                Model_Files::upload(FILE_USER_AVA, $parent_id = 0, Model_User::getById(454),  false,  null, array('url'=> $data['picture']));

                if ($status) {
                    $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->login->successredirect);
                }
            } else {
                unset($_SESSION['socials']);
                $_SESSION['socials']['error'] = true;
                $this->response->redirect('https://' . Request::$host . $this->config->socials->facebook->auth->errorredirect);
            }
        }
        $this->response->redirect('https://' . Request::$host);

    }

}