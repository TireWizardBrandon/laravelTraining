<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;


class ViewConcertListingTest extends TestCase
{

    use DatabaseMigrations;
    
    /** @test */
    function userCanViewAPublishedConcertListing()
    {
        // Arrange
        
        //Create Concert
        
        $concert = factory(Concert::class)->states("published")->create([
            
            "title" => "The Red Chord",
            "subtitle" => "with Animosity and Lethargy",
            "date" => Carbon::parse('December 13, 2016 8:00pm'),
            "ticketPrice" => 3250,
            "venue" => "The Mosh Pit",
            "address" => "123 Example Lane",
            "city" => "Laraville",
            "state" => "ON",
            "zip" => "17916",
            "additionalInformation" => "For tickets, calls (555) 555-5555.",
                                   ]);
        
        // Act
        
        // View Concert
        
        $response = $this->get("/concerts/".$concert->id);
        
        
        // Assert
        
        //Verify Details
        
        $response->assertSeeText("The Red Chord");
        $response->assertSeeText("with Animosity and Lethargy");
        $response->assertSeeText("December 13, 2016");
        $response->assertSeeText("8:00pm");
        $response->assertSeeText("32.50");
        $response->assertSeeText("The Mosh Pit");
        $response->assertSeeText("123 Example Lane");
        $response->assertSeeText("Laraville, ON 17916");
        $response->assertSeeText("For tickets, calls (555) 555-5555.");
        
    }
    
    /** @test */
    function userCannotViewUnpublishedConcert(){
        
        $concert = factory(Concert::class)->states('unPublished')->create();
    
        $response = $this->get("/concerts/".$concert->id);
        
        $response->assertStatus(404);
        
    }
}
