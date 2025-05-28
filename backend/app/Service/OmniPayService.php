<?php

namespace App\Service;

use App\Service\OmniPay\DummyPaymentGateway;
use App\Service\OmniPay\PaymentGatewayInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly final class OmniPayService
{
    public function __construct() {}

    public function getPaymentGateway($name) : PaymentGatewayInterface
    {
        return match($name) {
            'dummy' => DummyPaymentGateway::create(),
            default => throw new BadRequestHttpException("Unsupported payment gateway"),
        };
    }
}
