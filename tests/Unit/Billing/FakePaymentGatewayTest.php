<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function ChargesWithValidTokenSuccessful(){
          $paymentGateway = new FakePaymentGateway;
          
          $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
          
          $this->assertEquals(2500,$paymentGateway->totalCharges());
    }
    
    /** @test */
    function chargeWithInvalidPaymentTokenFail(){
        
        try
        {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2500, 'invalid-payment-token');
        }
        catch(PaymentFailedException $e)
        {
            $this->assertTrue(true);
            return;
        }
    
        $this->fail();
    }
}