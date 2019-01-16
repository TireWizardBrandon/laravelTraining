<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 06/12/18
 * Time: 2:13 PM
 */

namespace Tests\Feature;

use App\Billing\PaymentGateway;
use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PurchaseTicketsTest extends TestCase{

    use DatabaseMigrations;
    
    protected function setUp(){
        parent::setUp();
        
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
        
    }
    
    private function orderTickets($concert, $params){
        
        return $this->json('POST', "/concerts/{$concert->id}/orders", $params );
    }
    
    private function assertValidationError($response, $field){
        $response->assertStatus(422);
    
        $this->assertArrayHasKey($field, $response->decodeResponseJson()["errors"]);
    }
    
    /** @test */
    function customerCanPurchaseTicketToPublishedConcert(){
        
        
        $concert = factory(Concert::class)->states('published')->create([
            "ticketPrice" => 3250
                                                   ]);
    
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $response->assertStatus(201);
        
        $this->assertEquals(9750,$this->paymentGateway->totalCharges());
    
        $order = $concert->orders()->where('email', "john@example.com")->first();
        $this->assertNotNull($order);
    
    
        $this->assertEquals(3,$order->tickets()->count());
    }
    
    /** @test */
    function cantPurchaseTicketForUnpublishedConcert(){
        
        
        $concert = factory(Concert::class)->states('unPublished')->create();
    
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(404);
        
        $this->assertEquals(0, $concert->orders()->count());
    
        $this->assertEquals(0,$this->paymentGateway->totalCharges());
    }
    
    /** @test */
    function emailIsRequired(){
        
        $concert = factory(Concert::class)->states('published')->create();
    
        $response = $this->orderTickets($concert, [
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
        
        
        $this->assertValidationError($response,"email");
        
    }
    
    /** @test */
    function emailIsValid(){
        $concert = factory(Concert::class)->states('published')->create();
    
        $response = $this->orderTickets($concert, [
            "email" => "invalid-email-address",
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $this->assertValidationError($response,"email");

    }
    
    /** @test */
    function ticketQuantityIsRequired(){
        
        $concert = factory(Concert::class)->states('published')->create();
        
        $response = $this->orderTickets($concert, [
            "email" => "joe@example.com",
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $this->assertValidationError($response,"ticketQuantity");
        
    }
    
    /** @test */
    function ticketQuantityMustBeAtLeast1(){
        
        $concert = factory(Concert::class)->states('published')->create();
        
        $response = $this->orderTickets($concert, [
            "email" => "joe@example.com",
            "ticketQuantity" => 0,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $this->assertValidationError($response,"ticketQuantity");
       
    }
    
    /** @test */
    function paymentTokenIsRequired(){
        
        $concert = factory(Concert::class)->states('published')->create();
        
        $response = $this->orderTickets($concert, [
            "email" => "joe@example.com",
            "ticketQuantity" => 0,
        ]);
    
        $this->assertValidationError($response,"paymentToken");
        
    }
    
    /** @test */
    function anOrderIsNotMadeIfPaymentFailed(){
        
        $concert = factory(Concert::class)->states('published')->create([
                                                       "ticketPrice" => 3250
                                                   ]);
    
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => 'invalid-payment-token',
        ]);
        
        $response->assertStatus(422);
    
        $order = $concert->orders()->where('email', "john@example.com")->first();
        
        $this->assertNull($order);
    }
}