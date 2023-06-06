<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20.09.18
 * Time: 13:31
 */

require_once COOT_PATH . '/vendor/autoload.php';
use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaypalRequestSend
{
        protected $numberCard;
        protected $typeCard;
        protected $expireMonth;
        protected $expireYear;
        protected $ccv;
        protected $firstName;
        protected $lastName;
        protected $sum;
        protected $currency;
        protected $config;

    public function __construct($numberCard, $typeCard, $expireMonth, $expireYear, $cvv2, $firstName, $lastName, $sum, $currency)
    {
        $this->numberCard  = $numberCard;
        $this->typeCard    = $typeCard;
        $this->expireMonth = $expireMonth;
        $this->expireYear  = $expireYear;
        $this->cvv2        = $cvv2;
        $this->firstName   = $firstName;
        $this->lastName    = $lastName;
        $this->sum         = $sum;
        $this->currency    = $currency;
        $this->config      = Config::getInstance();
    }

    public function sendPay()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->config->paypal->clientId,     // ClientID
                $this->config->paypal->clientSecret    // ClientSecret
            )
        );

            $apiContext->setConfig(
            array(
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'DEBUG',
                'mode' => 'live',
            )
        );

        $card = new CreditCard();
        $card->setNumber( $this->numberCard)
            ->setType($this->typeCard)
            ->setExpireMonth($this->expireMonth)
            ->setExpireYear($this->expireYear)
            ->setCvv2($this->cvv2)
            ->setFirstName($this->firstName)
            ->setLastName($this->lastName);

        $fi = new FundingInstrument();
        $fi->setCreditCard($card);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments([$fi]);

        $amount = new Amount();
        $amount->setCurrency($this->currency)
            ->setTotal($this->sum);

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions([$transaction]);

        try {
           return $payment->create($apiContext);

        } catch (\PayPal\Exception\PayPalConnectionException $pce) {
            $result = json_decode($pce->getData());
            return $result ;
        }

    }
}