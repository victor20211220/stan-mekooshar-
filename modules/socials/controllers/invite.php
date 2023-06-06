<?php

class Invite_Controller extends Controller_Common
{
	public function  before() {
		parent::before();
		define('OAUTH_YAHOO', MODULES_PATH . 'socials/library/socials/yahoo/');
		define('OAUTH', MODULES_PATH . 'socials/library/oauth/');

		if (!isset($_SESSION)) {
			session_start();
		}
	}

	public function actionIndex()
	{
		$this->view->title = 'Invite page';

		$content = '';
		if(isset($_SESSION['invite'])) {
			foreach($_SESSION['invite'] as $mail) {
				$content .= $mail['email'] . '<br>';
			}
			unset($_SESSION['invite']);
		}

		// Google
		$clientid = $this->config->socials->google->clientid;
		$redirecturi = $this->config->socials->google->invite->callback;

		$content .= '<a style="color: #000" href="https://accounts.google.com/o/oauth2/auth?client_id=
' . $clientid . '&redirect_uri=' . $redirecturi . '
&scope=https://www.google.com/m8/feeds/&response_type=code">
Invite Friends From Gmail</a><br>';

		$content .= '<a style="color: #000" href="' . Request::generateUri(false, 'yahoo') . '">Invite Friends From Yahoo</a><br>';

		$this->view->content = $content;
	}

	public function actionGoogle()
	{
		$this->view->title = 'Index page';

		$clientid = $this->config->socials->google->clientid;
		$clientsecret = $this->config->socials->google->clientsecret;
		$redirecturi = $this->config->socials->google->invite->callback;
		$maxresults = $this->config->socials->google->invite->maxresults;


		if(isset($_GET['code'])) {
			$authcode = $_GET["code"];
			$fields=array(
				'code'=> urlencode($authcode),
				'client_id'=> urlencode($clientid),
				'client_secret'=> urlencode($clientsecret),
				'redirect_uri'=> urlencode($redirecturi),
				'grant_type'=> urlencode('authorization_code') );

			$fields_string = '';
			foreach($fields as $key=>$value){ $fields_string .= $key.'='.$value.'&'; }
			$fields_string = rtrim($fields_string,'&');

			$ch = curl_init();//open connection
			curl_setopt($ch, CURLOPT_URL,'https://accounts.google.com/o/oauth2/token');
			curl_setopt($ch, CURLOPT_POST,5);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($result);
			$accesstoken = $response->access_token;
			if( $accesstoken!='')
				$_SESSION['token']= $accesstoken;
			$xmlresponse= file_get_contents('https://www.google.com/m8/feeds/contacts/default/full?max-results='.$maxresults.'&oauth_token='. $_SESSION['token']);
			$xml= new SimpleXMLElement($xmlresponse);
			$xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');

			$email = (string) $xml->id;

			$result = $xml->xpath('//gd:email');
			$emails = array();
			foreach ($result as $title) {
				$emails['data'][] = (string) $title->attributes()->address;
			}
			$emails['user'] = $email;


			$_SESSION['invite'] = $emails;
			$this->response->redirect('https://' . Request::$host . $this->config->socials->google->invite->successredirect);
		}

	}

	public function actionYahoo()
	{
		require_once(OAUTH_YAHOO . 'globals.php');
		require_once(OAUTH_YAHOO . 'oauth_helper.php');

		$consumer_key = $this->config->socials->yahoo->consumerkey;
		$consumer_secret = $this->config->socials->yahoo->consumersecret;
		$callback = $this->config->socials->yahoo->invite->callback;
		$maxresults = $this->config->socials->yahoo->invite->maxresults;

		if(!isset($_GET['oauth_verifier'])) {
			/* Get the request token using HTTP GET and HMAC-SHA1 signature*/
			$retarr = get_request_token($consumer_key, $consumer_secret,
				$callback, false, true, true);

			if (! empty($retarr)){
				list($info, $headers, $body, $body_parsed) = $retarr;

				if ($info['http_code'] == 200 && !empty($body)) {
					$_SESSION['request_token']  = $body_parsed['oauth_token'];
					$_SESSION['request_token_secret']  = $body_parsed['oauth_token_secret']; $_SESSION['oauth_verifier'] = $body_parsed['oauth_token'];

					echo $yahoo_link =  urldecode($body_parsed['xoauth_request_auth_url']);
					$this->response->redirect($yahoo_link);
					return;
				}
			}

		} else {
			$request_token               =   $_SESSION['request_token'];
			$request_token_secret   =   $_SESSION['request_token_secret'];
			$oauth_verifier                =   $_GET['oauth_verifier'];

			/* Get the access token using HTTP GET and HMAC-SHA1 signature */
			$retarr = get_access_token_yahoo($consumer_key, $consumer_secret,
				$request_token, $request_token_secret,
				$oauth_verifier, false, true, true);
			if (! empty($retarr)) {
				list($info, $headers, $body, $body_parsed) = $retarr;
				if ($info['http_code'] == 200 && !empty($body)) {
					$guid = $body_parsed['xoauth_yahoo_guid'];
					$access_token  = rfc3986_decode($body_parsed['oauth_token']) ;
					$access_token_secret  = $body_parsed['oauth_token_secret'];
					/* Call Contact API */
					$emails['data'] = callcontact_yahoo($consumer_key, $consumer_secret,
						$guid, $access_token, $access_token_secret,
						false, true, $maxresults);
					$profile = callprofile_yahoo($consumer_key, $consumer_secret,
						$guid, $access_token, $access_token_secret,
						false, true, $maxresults);
				}
			} else {
				$emails['data'] = array();
			}
			$emails['user'] = $profile->emails[0]->handle;

			$_SESSION['invite'] = $emails;
			$this->response->redirect('https://' . Request::$host . $this->config->socials->yahoo->invite->successredirect);
		}

return;

// ===============================
//
//		require(MODULES_PATH . "socials/library/socials/yahoo/OAuth.php");
//		$cc_key  = $consumerkey;
//		$cc_secret = $consumersecret;
//		$url = 'https://query.yahooapis.com/v1/public/yql';
//		$args = array();
////		$args["format"] = "json";
////		$args["diagnostics"] = "true";
////		$args["q"] = 'show%20tables';
////		$args["callback"] = '';
//
//		$consumer = new OAuthConsumer($cc_key, $cc_secret);
//		$request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);
//		$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
//		$url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));
////		dump($url, 1);
////		$ch = curl_init();
//		$headers = array($request->to_header());
//		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//		curl_setopt($ch, CURLOPT_URL, $url);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//		$rsp = curl_exec($ch);
//		$results = json_decode($rsp);
//		dump($url);
//		dump($results, 1);
//		return;
//// ================================
//
//
//		if(isset($_GET['code'])) {
//			$authcode = $_GET["code"];
//			$fields=array(
//				'code'=> urlencode($authcode),
//				'client_id'=> urlencode($clientid),
//				'client_secret'=> urlencode($clientsecret),
//				'redirect_uri'=> urlencode($redirecturi),
//				'grant_type'=> urlencode('authorization_code') );
//
//			$fields_string = '';
//			foreach($fields as $key=>$value){ $fields_string .= $key.'='.$value.'&'; }
//			$fields_string = rtrim($fields_string,'&');
//
//			$ch = curl_init();//open connection
//			curl_setopt($ch, CURLOPT_URL,'https://query.yahooapis.com/o/oauth2/token');
//			curl_setopt($ch, CURLOPT_POST,5);
//			curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//			$result = curl_exec($ch);
//			curl_close($ch);
//
//			$response = json_decode($result);
//			$accesstoken = $response->access_token;
//			if( $accesstoken!='')
//				$_SESSION['token']= $accesstoken;
//
//
//			$query = 'select * from social.profile where guid=me';
//
//// insert the query into the full URL
//			$url = 'https://query.yahooapis.com/v1/public/yql?format=json&q=' . urlencode($query);
//
//// set up the cURL
//			$c = curl_init();
//			curl_setopt($c, CURLOPT_URL, $url);
//			curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
//			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
//
//// execute the cURL
//			$rawdata = curl_exec($c);
//			curl_close($c);
//
//// Convert the returned JSON to a PHP object
//			$data = json_decode($rawdata);
//
//// Show us the data
//			echo '<pre>';
//			print_r($data);
//			echo '</pre>';
//			return;
//		}

	}
}