<?php
require_once 'PaypalRequestSend.php';

class Commerce_Controller extends Controller_User
{
    protected $resource = 'public';
    protected $subactive = 'jobs';

    public function before()
    {
        parent::before();

        if (!isset($_SESSION)) {
            session_start();
        }

        $isMyCompanies = Model_Companies::getIsMyCompanies($this->user->id);
        View::$global->isMyCompanies = $isMyCompanies;
    }

    public function actionDeleteItem($section, $token)
    {
        $result = false;
        if (isset($_SESSION['cart'][$section]) ? count($_SESSION['cart'][$section]) : false) {
            $items = &$_SESSION['cart'][$section];
            foreach($items as $k => $v) {
                if ($v['token'] == $token) {
                    unset($items[$k]);
                    $result = true;
                    break;
                }
            }
        }

        if(Request::$isAjax) {
            return $result;
        } else {
            $this->response->redirect(Request::$controller . 'checkout/');
        }
    }

    public function actionCheckout()
    {
        $this->view->title = 'Checkout';
        $this->view->content = $content = new View('checkout');

        $cart = Cartitems::instance();
        $items = $cart->getItems();

        if (count($items)) {
            $form = new Form('payment');
            $form->checkbox('delivery', '', 'Delivery ($'.$this->settings['delivery'].')', $cart->delivery() ? 1 : 0);

            if($this->settings['paypalType'] == 'paypalPro') {
                $form->radio('method', array('creditcard' => '', 'paypal' => ''))->required();
            }

            $form->submit('submit', 'Continue');

            $content->form = $form;

            if (Request::$method == 'POST') {
                if ($form->validate()) {
                    $values = $form->getValues();
                    if ($values['delivery'] && !$cart->isDigital()) {
                        $_SESSION['cartDelivery'] = $this->settings['delivery'];
                    } else {
                        $_SESSION['cartDelivery'] = false;
                    }

                    if ($this->settings['paypalType'] == 'paypalPro' && $values['method'] == 'creditcard') {
                        $this->response->redirect(Request::$controller . 'creditcard/');
                    } else {
                        $this->response->redirect(Request::$controller . 'gopaypal/');
                    }
                }
            }
        }

        $content->items = $items;
    }

    public function actionConfirmation($result = 'ok')
    {
        $this->view->title = 'Checkout';
        if ($result == 'ok') {
            $this->view->content = 'Thank you.';
        } elseif ($result == 'cancel') {
            $this->view->content = 'Try next time. Thank you.';
        }
    }


    public function actionActivateJob($job_id)
    {
        $this->view->title = 'Activate job';
        $job = Model_Jobs::getItemByIdUserid($job_id, $this->user->id);

        $content = new View('payment/creditcard');

        $f_Payment = new Form_Payment();
        $f_Payment->setTypeActivateJob($job);


        $form = $f_Payment->form;

        $card = Cartitems::instance();

        if ($form->validate() && Request::isPost()) {
            $values = $form->getValues();
            $plans = Model_Plans::getListPlans();

            unset($_SESSION['cart']);
            $_SESSION['cart'][] = array(
                'id' 		=> $job->id,
                'user_id'	=> $this->user->id,
                'job_id'	=> $job->id,
                'plan_id'	=> $values['plan'],
                'name' 		=> $job->title,
                'price' 	=> $plans['data'][$values['plan']]->price,
                'token' 	=> $values['plan'],
                'text' 		=> $job->description,
                'quantity' 	=> 1,
                'section' 	=> 'jobs'
            );
            $card = $card::instance();
            $items = $card->getItems();

            $values['acct'] 		= str_ireplace('-', '', $values['acct']);
            $values['expDate'] 		= $values['expMonth'] . $values['expYear'];
            $values['paymentAction']= 'sale';
            $values['countryCode']	= 'US';
            $values['currencyCode']	= 'USD';
            $values['amt'] = $card->getAmount();

            $paypalSend = new PaypalRequestSend(
                $values['acct'],
                strtolower($values['creditCardType']),
                $values['expMonth'],
                $values['expYear'],
                $values['cvv2'],
                $values['firstName'],
                $values['lastName'],
                $plans['data'][$values['plan']]->price,
                'USD'
            );

            $response = $paypalSend->sendPay();

            if (isset($response->state) && $response->state == 'approved') {
                $order = array();


                $order['user_id']				= $this->user->id;
                $order['paypalMethod']			= 'CreditCard';
                $order['paypalAck']				= $response->state;
                $order['transactionid']			= $response->id;
                $order['paypalTimestamp']		= $response->create_time;
                $order['dateTimePaid']			= date('Y-m-d h:i');
                $order['customer']              = $values['customerCustomer'];
                $order['email']                 = $values['customerEmail'];
                $order['state']                 = $values['state'];
                $order['zip']                   = $values['customerZip'];
                $order['city']                  = $values['city'];
                $order['address']               = $values['customerAddress'];
                $order['phone']                 = $values['customerPhone'];
                $order['comment']               = $values['customerComment'];



                $order = $card->createOrder($order);
                $card::setPaid($order->id);
                Model_Jobs::setActivate($job->id, $plans['data'][$values['plan']]->countDays);

                $this->flushSession();
                $_SESSION['payment_type'] = 'job';
                $this->response->redirect(Request::$controller . 'success/' . $order->token . '/');

            } else {

                $content->errors = array('Can\'t connect to processing center');

                $content->form = false;
            }
        }

        $content->form = new View('payment/creditcard-form', array('form' => $form));
        $this->view->content = View::factory('pages/jobs/activate', array(
            'content' => $content,
            'job' => $job
        ));
    }


    public function actionUpgradeProfile()
    {
        $this->view->title = 'Upgrade profile';

        $profile = Model_User::getItemByUserid($this->user->id);

        $content = new View('payment/creditcard');

        $f_Payment = new Form_Payment();
        $f_Payment->setTypeUpgradeProfile();


        $form = $f_Payment->form;

        $card = Cartitems::instance();

        if ($form->validate() && Request::isPost()) {
            $values = $form->getValues();

            $plans = Model_Plans::getListPlans(CATEGORY_PLAN_PROFILE);

            unset($_SESSION['cart']);
            $_SESSION['cart'][] = array(
                'id' 		=> $this->user->id,
                'user_id'	=> $this->user->id,
                'job_id'	=> NULL,
                'plan_id'	=> $values['plan'],
                'name' 		=> 'Upgrade profile ' . $plans['data'][$values['plan']]->name,
                'price' 	=> $plans['data'][$values['plan']]->price,
                'token' 	=> $values['plan'],
                'text' 		=> '',
                'quantity' 	=> 1,
                'section' 	=> 'profile'
            );
            $card = $card::instance();
            $items = $card->getItems();

            $values['acct'] 		= str_ireplace('-', '', $values['acct']);
            $values['expDate'] 		= $values['expMonth'] . $values['expYear'];
            $values['paymentAction']= 'sale';
            $values['countryCode']	= 'US';
            $values['currencyCode']	= 'USD';
            $values['amt'] = $card->getAmount();


            $paypalSend = new PaypalRequestSend(
                $values['acct'],
                strtolower($values['creditCardType']),
                $values['expMonth'],
                $values['expYear'],
                $values['cvv2'],
                $values['firstName'],
                $values['lastName'],
                $plans['data'][$values['plan']]->price,
                'USD'
            );

            $response = $paypalSend->sendPay();

            if (isset($response->state) && $response->state == 'approved') {
                $order = array();

                $order['user_id']				= $this->user->id;
                $order['paypalMethod']			= 'CreditCard';
                $order['paypalAck']				= $response->state;
                $order['transactionid']			= $response->id;
                $order['paypalTimestamp']		= $response->create_time;
                $order['dateTimePaid']			= date('Y-m-d h:i');
                $order['customer']              = $values['customerCustomer'];
                $order['email']                 = $values['customerEmail'];
                $order['state']                 = $values['state'];
                $order['zip']                   = $values['customerZip'];
                $order['city']                  = $values['city'];
                $order['address']               = $values['customerAddress'];
                $order['phone']                 = $values['customerPhone'];
                $order['comment']               = $values['customerComment'];

                $order = $card->createOrder($order);
                $card::setPaid($order->id);
                Model_User::setUpgradeProfile($plans['data'][$values['plan']]->countDays);

                $this->flushSession();

                $_SESSION['payment_type'] = 'profile';
                $this->response->redirect(Request::$controller . 'success/' . $order->token . '/');
            } else {
                $content->errors = array('Can\'t connect to processing center');

                $content->form = false;
            }
        }

        $content->form = new View('payment/creditcard-form', array('form' => $form));
        $this->view->content = View::factory('pages/profile/upgrade_profile_payment', array(
            'profile' => $profile,
            'content' => $content
        ));
    }

    public function actionGoPayPal()
    {
        if($this->settings['paypalType'] == 'paypalPro') {
            $card = Cartitems::instance();

            $values['amt'] = $card->getAmount();
            $values['desc'] = 'Website payment';
            $values['ReturnUrl'] = Url::site(Request::$controller . 'paypal/');
            $values['CancelUrl'] = Url::site(Request::$controller . 'checkout/');
//			if (!$this->settings['isSandbox']) {
//				$values['ReturnUrl'] = Url::site(Request::$controller . 'paypal/', 'https');
//				$values['CancelUrl'] = Url::site(Request::$controller . 'checkout/', 'https');
//			}
            $values['PaymentAction'] = 'Sale';
            $values['CurrencyCode'] = 'USD';
            $response = Paypal::getInstance()->request('SetExpressCheckout', $values);

            if (isset($response['ack']) ? (strtolower($response['ack']) == 'success' || strtolower($response['ack']) == 'successwithwarning') : false) {
                if (isset($response['token'])) {
                    $url = Paypal::getInstance()->setExpressCheckoutUrl($response['token']);
                    $this->response->redirect($url);
                }
            } else {
                $this->view->title = 'Error occured while set up paypal payment';
                $this->view->content = $content = new View('payment/result-error');
                $errors = array();
                foreach ($response as $k => $v) {
                    if (preg_match_all('/^l_shortmessage(\d+)$/i', $k, $matches, PREG_SET_ORDER)) {
                        if ($response['l_shortmessage' . $matches[0][1]] != 'Invalid Parameter') {
                            $errors[] = View::factory('payment/error', array('title' => $response['l_shortmessage' . $matches[0][1]], 'message' => $response['l_longmessage' . $matches[0][1]]));
                        }
                    }
                }
                if (!isset($response['ack'])) {
                    $content->errors = array(0 => 'Can\'t connect to processing center');
                } else {
                    $content->errors = $errors;
                }
            }
        } else {
            $this->actionPaypalStandart();
        }
    }

    public function actionPaypalStandart()
    {
        $this->view->title = 'Pay with Paypal';
        $this->view->content = $content = new View('payment/creditcard');

        $form = new Form('payment');
        $form->labelWidth = '150px';

        $form->text('customer', 'Full name')
            ->attribute('size', 40)
            ->rule('required');
        $form->text('email', 'Email')
            ->attribute('size', 40)
            ->rule('required')
            ->rule('email');
        $form->text('address', 'Address')
            ->attribute('size', 40)
            ->rule('maxLength', 127)
            ->rule('required');
        $form->text('city', 'City')
            ->attribute('size', 40)
            ->rule('maxLength', 63)
            ->rule('required');
        $form->select('state', array_merge(array('' => ' '), Text::get('states')), 'State')
            ->rule('required');
        $form->text('zip', 'ZIP-code')
            ->attribute('size', 10)
            ->rule('required')
            ->rule('integer')
            ->rule('maxLength', 5);
        $form->text('phone', 'Phone')
            ->attribute('size', 40)
            ->rule('maxLength', 31);
        $form->textarea('comment', 'Comments')
            ->attribute('cols', 25)
            ->attribute('rows', 10);

        $form->fieldset('submit');
        $form->submit('submit', 'Pay now')
            ->attribute('style', 'width: 100px; height: 40px;');

        if (isset($this->user) && $this->user) {
            $form->elements['customer']->value = $this->user->firstName . ' ' . $this->user->lastName;
            $form->elements['email']->value    = $this->user->email;
            $form->elements['address']->value  = $this->user->address1;
            $form->elements['city']->value     = $this->user->city;
            $form->elements['state']->value    = $this->user->state;
            $form->elements['zip']->value      = $this->user->zip;
            $form->elements['phone']->value    = $this->user->phone;
        }

        $card = Cartitems::instance();
        $items = $card->getItems();

        if (count($items)) {
            if ($card->isDigital()) {
                unset($form->elements['state']);
                unset($form->elements['zip']);
                unset($form->elements['city']);
                unset($form->elements['address']);
            }

            if ($form->validate()) {
                $values = $form->getValues();

                $order = $card->createOrder($values);

                $paypalLink = $card->getPaypalLink($order);
                Model_Cartorders::update(array('paypalLink' => $paypalLink), $order->id);

                $this->flushSession();

                $this->response->redirect($paypalLink);
            }

            $content->form = new View('payment/paypal-form', array('form' => $form));
        } else {
            $this->view->content = $content = new View('cart/message');

            $content->title = 'Shopping cart.';
            $content->message = 'Your shopping cart is empty.';
        }

        $this->view->style('/css/autoform.css');
    }

    public function actionPayPal()
    {
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else {
            throw new ForbiddenException();
        }

        $values['token'] = $token;
        $response = Paypal::getInstance()->request('GetExpressCheckoutDetails', $values);
        if (isset($response['ack']) ? (strtolower($response['ack']) == 'success' || strtolower($response['ack']) == 'successwithwarning') : false) {
            $_SESSION['paypalResponse'] = $response;
            $this->response->redirect(Request::$controller . 'paypaldetails/');
        } else {
            $this->template->content = new View('payment/result-error');
            $errors = array();
            foreach ($response as $k => $v) {
                if (preg_match_all('/^l_shortmessage(\d+)$/i', $k, $matches, PREG_SET_ORDER)) {
                    if ($response['l_shortmessage' . $matches[0][1]] != 'Invalid Parameter') {
                        $errors[] = View::factory('payment/error', array('title' => $response['l_shortmessage' . $matches[0][1]], 'message' => $response['l_longmessage' . $matches[0][1]]));
                    }
                }
            }
            if (!isset($response['ack'])) {
                $this->template->content->errors = array(0 => 'Can\'t connect to processing center');
            } else {
                $this->template->content->errors = $errors;
            }
        }
    }

    public function actionPayPalDetails()
    {
        if (!isset($_SESSION['paypalResponse'])) {
            throw new ForbiddenException();
        }

        $this->view->title = 'Payment with PayPal';
        $this->view->content = $content = new View('payment/paypal-form');

        $form = new Form('payment');
        $form->labelWidth = '150px';

        $fullName = (isset($_SESSION['paypalResponse']['firstname']) ? $_SESSION['paypalResponse']['firstname'] : '') . ' ' . (isset($_SESSION['paypalResponse']['firstname']) ? $_SESSION['paypalResponse']['lastname'] : '');
        $fullName = trim($fullName);

        $form->text('customerCustomer', 'Full name', $fullName)
            ->attribute('size', 40)
            ->rule('required');
        $form->text('customerEmail', 'Email', $_SESSION['paypalResponse']['email'])
            ->attribute('size', 40)
            ->rule('required')
            ->rule('email');
        $form->select('customerState', array_merge(array('' => ''), Text::get('states')), 'State', (isset($_SESSION['paypalResponse']['shiptostate']) ? $_SESSION['paypalResponse']['shiptostate'] : ''))
            ->rule('required');
        $form->text('customerZip', 'ZIP-code', (isset($_SESSION['paypalResponse']['shiptozip']) ? $_SESSION['paypalResponse']['shiptozip'] : ''))
            ->attribute('size', 10)
            ->rule('required');
        $form->text('customerCity', 'City', (isset($_SESSION['paypalResponse']['shiptocity']) ? $_SESSION['paypalResponse']['shiptocity'] : ''))
            ->attribute('size', 40)
            ->rule('required');
        $form->text('customerAddress', 'Address', (isset($_SESSION['paypalResponse']['shiptostreet']) ? $_SESSION['paypalResponse']['shiptostreet'] : ''))
            ->attribute('size', 40)
            ->rule('required');
        $form->text('customerPhone', 'Phone')
            ->attribute('size', 40);
        $form->textarea('customerComment', 'Comments')
            ->attribute('cols', 30)
            ->attribute('rows', 10);
        $form->submit('submit', 'Pay now')
            ->attribute('style', 'width: 100px; height: 40px;');

        $card = Cartitems::instance();
        $items = $card->getItems();

        if (count($items)) {
            if ($card->isDigital()) {
                unset($form->elements['customerState']);
                unset($form->elements['customerZip']);
                unset($form->elements['customerCity']);
                unset($form->elements['customerAddress']);
            }

            $content->form = $form;

            if (Request::$method == 'POST') {
                if ($form->validate()) {
                    $values = $form->getValues();
                    $values['amt'] = $card->getAmount();
                    $values['f'] = isset($_SESSION['paypalResponse']['token']) ? $_SESSION['paypalResponse']['token'] : '';;
                    $values['payerId'] = isset($_SESSION['paypalResponse']['payerid']) ? $_SESSION['paypalResponse']['payerid'] : '';
                    $values['paymentAction'] = 'Sale';
                    $values['currencyCode'] = 'USD';

                    $response = Paypal::getInstance()->request('DoExpressCheckoutPayment', $values);

                    if (isset($response['ack']) ? (strtolower($response['ack']) == 'success' || strtolower($response['ack']) == 'successwithwarning') : false) {
                        $order = array();
                        foreach ($values as $k => $v) {
                            if (substr($k, 0, 8) == 'customer') {
                                $order[strtolower(substr($k, 8, 1)) . substr($k, 9)] = $v;
                            }
                        }
                        $order['paypalMethod']			= 'DoExpressCheckoutPayment';
                        $order['paypalAck']				= $response['ack'];
                        $order['transactionid']			= $response['transactionid'];
                        $order['paypalCorrelationId']	= $response['correlationid'];
                        $order['paypalTimestamp']		= $response['timestamp'];
                        $order['dateTimePaid']			= date('Y-m-d h:i');
                        $order['paypalEmail']			= $_SESSION['paypalResponse']['email'];

                        $order = $card->createOrder($order);
                        $card::setPaid($order->id);

                        $this->flushSession();

                        $this->response->redirect(Request::$controller . 'success/' . $order->token . '/');
                    } else {
                        $this->view->content = $content = new View('payment/result-error');
                        $errors = array();
                        foreach ($response as $k => $v) {
                            if (preg_match_all('/^l_shortmessage(\d+)$/i', $k, $matches, PREG_SET_ORDER)) {
                                if ($response['l_shortmessage' . $matches[0][1]] != 'Invalid Parameter') {
                                    $errors[] = View::factory('payment/error', array('title' => $response['l_shortmessage' . $matches[0][1]], 'message' => $response['l_longmessage' . $matches[0][1]]));
                                }
                            }
                        }
                        if (!isset($response['ack'])) {
                            $content->errors = array(0 => 'Can\'t connect to processing center');
                        } else {
                            $content->errors = $errors;
                        }
                    }
                }
            }
        }

        $this->view->style('/css/autoform.css');
    }

    private function flushSession()
    {
        unset($_SESSION['cart']);
        unset($_SESSION['cartDelivery']);
        unset($_SESSION['cartDiscount']);
        unset($_SESSION['paypalResponse']);
    }

    public function actionIpn()
    {
        $this->autoRender = false;

        $card = Cartitems::instance();

        if ($orderId = $this->validateIpn()) {
            Cartitems::setPaid($orderId);

            $this->response->body = 'Payment done';
        } else {
            $this->response->body = 'Payment failed';
        }
    }

    private function validateIpn()
    {
        $ipnData = '';
        $ipnResponse = '';
        Log::getInstance()->write('Paypal IPN Intiated by ' . $_SERVER['REMOTE_ADDR'], true);

        Log::getInstance()->write(serialize($_POST));

        if ($this->settings['sandbox']) {
            $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $url = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $post = '';
        foreach ($_POST as $k => $v) {
            $ipnData[$k] = $v;
            $post .= $k . '=' . urlencode($v) . '&';
        }
        if (isset($ipnData['txn_type']) ? $ipnData['txn_type'] == 'subscr_signup' : false) {
            Log::getInstance()->write('Subscription sign up.', true, true);
            return false;
        }

        Log::getInstance()->write('Post string: ' . $post, true);
        $post .= 'cmd=_notify-validate';

        $itemNumber = (isset($ipnData['item_number']) ? $ipnData['item_number'] : (isset($ipnData['item_number1']) ? $ipnData['item_number1'] : (isset($ipnData['invoice']) ? $ipnData['invoice'] : '')));
        if (!$itemNumber) {
            Log::getInstance()->write('Invalid IPN request.', false, true);
            return false;
        }
        if (!($order = Model_Cartorders::getOrderByToken($itemNumber))) {
            Log::getInstance()->write('Invalid order id.', false, true);
            return false;
        }
        Log::getInstance()->write('Order ID: ' . $order->id, true);
        if ($this->settings['paypalEmail'] != $ipnData['receiver_email']) {
            Log::getInstance()->write('Invalid reciever email.', false, true);
            return false;
        }
        $gross = (isset($ipnData['mc_gross']) ? $ipnData['mc_gross'] : (isset($ipnData['mc_gross_1']) ? $ipnData['mc_gross_1'] : (isset($ipnData['payment_gross']) ? $ipnData['payment_gross'] : 0)));
//		if ($order->delivery) {
//			$order->amount += $this->settings['delivery'];
//		}
        if ($order->amount != $gross || 'USD' != $ipnData['mc_currency']) {
            Log::getInstance()->write('Currency: ' . $ipnData['mc_currency'], true);
            Log::getInstance()->write('Invalid amount. It had to be ' . $order->amount . ', but got ' . $gross . ' from IPN.', false, true);
            return false;
        }

        $curl = curl_init($url);
        curl_setopt ($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);
        $ipnResponse = curl_exec ($curl);
        curl_close ($curl);

        Log::getInstance()->write('Connection to ' . $url . ' successfuly completed.', true);
        Log::getInstance()->write('IPN response: ' . $ipnResponse, true);

        if (substr_count('VERIFIED', $ipnResponse)) {
            Log::getInstance()->write('IPN successfully verified. Order id is ' . $order->id . '.', true, true);
            Model_Cartorders::update(array(
                'transactionId' => $ipnData['txn_id'],
                'paypalMethod' => 'StandartPayment',
                'paypalEmail' => $ipnData['payer_email'],
                'paypalAck' => $ipnResponse
            ), $order->id);

            return $order->id;
        } else {
            Log::getInstance()->write('IPN validation failed.', false, true);
            return false;
        }
    }

    public function actionSuccess($token)
    {
        $this->view->title = 'You have successfully paid for your order!';
        $order = Model_Cartorders::getOrderByToken($token);
        $content = View::factory('payment/result-success');
        $content->order = $order;


        if(!isset($_SESSION['payment_type'])) {
            $_SESSION['payment_type'] = 'profile';
        }

        switch($_SESSION['payment_type']){
            case 'profile':
                $profile = Model_User::getItemByUserid($this->user->id);
                $this->view->content = View::factory('pages/profile/upgrade_profile_payment', array(
                    'profile' => $profile,
                    'content' => $content
                ));
                break;
            case 'job':
                $job = Model_Jobs::getItemByIdUserid($_SESSION['jobs_id'], $this->user->id);
                $content->job = $job;
                $this->view->content = View::factory('pages/jobs/activate', array(
                    'content' => $content,
                    'job' => $job
                ));
                break;
        }
    }
}
