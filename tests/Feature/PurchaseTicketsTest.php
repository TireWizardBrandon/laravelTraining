<?php

namespace Tests\Feature;

use App\Billing\PaymentGateway;
use App\Concert;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;

use Illuminate\Foundation\Testing\DatabaseMigrations;

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
        
        $concert = factory(Concert::class)->states('published')->create(["ticketPrice" => 3250 ])->addTickets(3);
        
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $response->assertStatus(201);
        
        $response->assertJson([[
            "email" => "john@example.com",
            "ticketQuantity" => 3,
            "amount" => 9750,
        ],["email" => "jane@example.com"],["email" => "bob@example.com"]]);
        
        $this->assertEquals(9750,$this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor("john@example.com"));
        $this->assertEquals(3,$concert->ordersFor("john@example.com")->first()->ticketQuantity());
    }
    
    /** @test */
    function cantPurchaseTicketForUnpublishedConcert(){
        
        
        $concert = factory(Concert::class)->states('unPublished')->create()->addTickets(3);
    
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor("john@example.com"));
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
    function cannotPurchaseMoreTicketsThanRemain(){
    
        $concert = factory(Concert::class)->states('published')->create()->addTickets(50);
    
        $response = $this->orderTickets($concert, [
            "email" => "john@example.com",
            "ticketQuantity" => 51,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor("john@example.com"));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }
    
    
    /** @test */
    function emailIsValid(){
        $concert = factory(Concert::class)->states('published')->create()->addTickets(3);
    
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
            "email" => "john@example.com",
            "ticketQuantity" => 0,
            "paymentToken" => $this->paymentGateway->getValidTestToken(),
        ]);
    
        $this->assertValidationError($response,"ticketQuantity");
    }
    
    /** @test */
    function paymentTokenIsRequired(){
        
        $concert = factory(Concert::class)->states('published')->create();
        
        $response = $this->orderTickets($concert, [
            "email" => "john@example.com",
            "ticketQuantity" => 0,
        ]);
    
        $this->assertValidationError($response,"paymentToken");
    }
    
    /** @test */
    function anOrderIsNotMadeIfPaymentFailed(){
        
        $concert = factory(Concert::class)->states('published')->create(["ticketPrice" => 3250 ])->addTickets(3);
        
        $response = $this->orderTickets($concert, [
            'email' =>'john@example.com',
            "ticketQuantity" => 3,
            "paymentToken" => 'invalid-payment-token',
        ]);
        
        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor("john@example.com"));
    }
}