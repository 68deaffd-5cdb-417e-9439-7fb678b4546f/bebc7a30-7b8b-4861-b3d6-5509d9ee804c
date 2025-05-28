<?php

namespace App\Service\OmniPay;

use Omnipay\Common\CreditCard;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;

class DummyPaymentGateway implements PaymentGatewayInterface
{
    private GatewayInterface $gateway;

    private function __construct()
    {
        $this->gateway = Omnipay::create('Dummy');
        $this->gateway->initialize(array('testMode' => true));
    }

    public static function create()
    {
        return new DummyPaymentGateway();
    }

    public function pay($amount): bool
    {
        $card = new CreditCard(array(
            'firstName' => 'Example',
            'lastName' => 'Customer',
            'number' => '4242424242424242',
            'expiryMonth' => '01',
            'expiryYear' => '2050',
            'cvv' => '123',
        ));

        $transaction = $this->gateway->purchase(array(
            'amount' => $amount,
            'currency' => 'USD',
            'card' => $card,
        ));

        $response = $transaction->send();

        return $response->isSuccessful();
    }
}

