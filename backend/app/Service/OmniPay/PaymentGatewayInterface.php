<?php

namespace App\Service\OmniPay;

interface PaymentGatewayInterface
{
    public function pay($amount): bool;
}
