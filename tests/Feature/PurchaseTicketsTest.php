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
    /** @test */
    function customerCanPurchaseTicket(){
        
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);
        
        $concert = factory(Concert::class)->create([
            "ticketPrice" => 3250
                                                   ]);
        
        
        $response = $this->json('POST', "/concerts/{$concert->id}/orders", ['email' =>'john@example.com',"ticketQuantity" => 3,
            "paymentToken" => $paymentGateway->getValidTestToken(),
        ]);
    
        $response->assertStatus(201);
        
        $this->assertEquals(9750,$paymentGateway->totalCharges());
    
        $order = $concert->orders()->where('email', "john@example.com")->first();
        $this->assertNotNull($order);
    
    
        $this->assertEquals(3,$order->tickets()->count());
    }
}