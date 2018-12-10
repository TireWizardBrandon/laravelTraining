<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 07/12/18
 * Time: 2:23 PM
 */

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    function ChargesWithValidTokenSuccessful(){
          $paymentGateway = new FakePaymentGateway;
          
          $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
          
          $this->assertEquals(2500,$paymentGateway->totalCharges());
          
    }
    
}