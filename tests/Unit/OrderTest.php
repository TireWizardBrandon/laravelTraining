<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 17/01/19
 * Time: 10:43 AM
 */

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Database\Console\Factories;
use App\Exceptions\NotEnoughTicketsException;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class OrderTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    function ticketReleasedWhenOrderCancelled(){
        $concert = factory(Concert::class)->create();
        
        $concert->addTickets(10);
        
        $order = $concert->orderTickets('jane@example.com', 5);
        
        $this->assertEquals(5,$concert->ticketsRemaining());
        
        $order->cancel();
    
        $this->assertEquals(10,$concert->ticketsRemaining());
        
        $this->assertNull(Order::find($order->id));
    }
    
}
