<?php
/**
 * Created by PhpStorm.
 * User: host
 * Date: 05/12/18
 * Time: 4:37 PM
 */

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Database\Console\Factories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class ConcertTest extends TestCase
{
    
    use DatabaseMigrations;
    
    /** @test */
    function canGetFormattedDate(){
        
        $concert = factory(Concert::class)->make([
                                               "date" => Carbon::parse("2016-12-01 8:00pm")
                                           ]);
        
        
        $this->assertEquals("December 1, 2016", $concert->formattedDate);
        
    }
    
    /** @test */
    function canGetFormattedStartTime(){
        $concert = factory(Concert::class)->make([
                                                       "date" => Carbon::parse("2016-12-01 17:00:00")
                                                   ]);
    
    
        $this->assertEquals("5:00pm", $concert->formattedStartTime);
    }
    
    /** @test */
    function canGetTicketPriceInDollars(){
        $concert = factory(Concert::class)->make([
                                                       "ticketPrice" => 6750
                                                   ]);
    
        $this->assertEquals("67.50" , $concert->ticketPriceInDollars);
        
    }
    
    /** @test */
    function concertsWithPublishedDateArePublished(){
        
        $publishedA = factory(Concert::class)->create(["published_at" => Carbon::parse("-1 week")]);
        $publishedB = factory(Concert::class)->create(["published_at" => Carbon::parse("-1 week")]);
        $unPublished = factory(Concert::class)->create(["published_at" => null]);
        
        $publishedConcerts = Concert::published()->get();
        
        $this->assertTrue($publishedConcerts->contains($publishedA));
        $this->assertTrue($publishedConcerts->contains($publishedB));
        $this->assertFalse($publishedConcerts->contains($unPublished));
    }
    
    /** @test */
    function canOrderConcertTickets(){
        $concert = factory(Concert::class)->create();
        
        $order = $concert->orderTickets('jane@example.com', 3);
        
        $this->assertEquals('jane@example.com' , $order->email);
        
        $this->assertEquals(3, $order->tickets()->count());
;    }
    

}