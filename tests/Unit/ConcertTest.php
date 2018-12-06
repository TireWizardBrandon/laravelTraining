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
        
        $concert = factory(Concert::class)->create([
                                               "date" => Carbon::parse("2016-12-01 8:00pm")
                                           ]);
        
        
        $date = $concert->formattedDate;
        
        $this->assertEquals("December 1, 2016", $date);
        
    }

}