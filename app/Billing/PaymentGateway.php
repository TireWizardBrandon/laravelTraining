<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 07/12/18
 * Time: 3:09 PM
 */

namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount, $token);
}