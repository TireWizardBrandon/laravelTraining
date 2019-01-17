<?php

use App\Concert;
use App\Order;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Database\Console\Factories;
use App\Exceptions\NotEnoughTicketsException;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TicketTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    function canReleaseTickets(){
        
        $concert = factory(Concert::class)->create();
        
        $concert->addTickets(1);
        
        $order = $concert->orderTickets("jane@example.com", 1);
        
        $ticket = $order->tickets()->first();
        
        $this->assertEquals($order->id, $ticket->order_id);
        
        $ticket->release();
        
        $this->assertNull($ticket->fresh()->order_id);
    }
}
