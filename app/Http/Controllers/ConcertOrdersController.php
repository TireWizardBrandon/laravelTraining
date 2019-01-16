<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Concert;
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
    
            
    
            $this->paymentGateway->charge( request("ticketQuantity") * $concert->ticketPrice, request("paymentToken"));
    
    
            $order = $concert->orderTickets(request("email"), request("ticketQuantity"));
    
            return response()->json([], 201);
        }
        catch(PaymentFailedException $e){
            return response()->json( [], 422);
        }
    
    
    }
}
