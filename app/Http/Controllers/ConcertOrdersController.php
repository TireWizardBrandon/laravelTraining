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
        
        $concert = Concert::find($concertId);
        
        $ticketQuantity =request("ticketQuantity");
        
        $amount = $ticketQuantity * $concert->ticketPrice;
        
        $token = request("paymentToken");
        
        $this->paymentGateway->charge($amount, $token);
        
        
        $order = $concert->orders()->create(['email' => request('email')]);
        
        foreach(range(1,$ticketQuantity) as $i){
            $order->tickets()->create([]);
        }
        
        return response()->json([], 201);
    }
}
