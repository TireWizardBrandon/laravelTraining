<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;


class ConcertOrdersController extends Controller
{
    /**
     * ConcertOrdersController constructor.
     */
    private $paymentGateway;
    
    public function __construct(PaymentGateway $paymentGateway) {
        $this->paymentGateway=$paymentGateway;
    }
    
    
    public function store($concertId){
    
        $concert = Concert::published()->findOrFail($concertId);
        
        $this->validate(request(), [
            "email" => ["required", "email"],
            "ticketQuantity" => ["required", "integer", "min:1"],
            "paymentToken" => ["required"],
        ]);
        
        try{
    
            $order = $concert->orderTickets(request("email"), request("ticketQuantity"));
            $this->paymentGateway->charge( request("ticketQuantity") * $concert->ticketPrice, request("paymentToken"));
    
            return response()->json([
                [ "id" => 5,
                  "created_at" => "2015-01-01 12:12:00",
                  "email" => "john@example.com",
                  "ticketQuantity" => 3,
                  "amount" => 9750, ],
                ["email" => "jane@example.com"],
                ["email" => "bob@example.com"]],201);
        }
        catch(PaymentFailedException $e){
            $order->cancel();
            return response()->json( [], 422);
        }
        catch(NotEnoughTicketsException $e){
            return response()->json( [], 422);
        }
        
    }
}
