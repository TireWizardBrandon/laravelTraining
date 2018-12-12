<?php

namespace App\Http\Controllers;

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
        
        $this->validate(request(), [
            "email" => "required",
        ]);
        
        $concert = Concert::find($concertId);
        
        $this->paymentGateway->charge( request("ticketQuantity") * $concert->ticketPrice, request("paymentToken"));
        
        
        $order = $concert->orderTickets(request("email"), request("ticketQuantity"));
        
        return response()->json([], 201);
    }
}
