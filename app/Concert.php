<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    //
    protected $guarded = [];
    
    protected $dates = ["date"];
    
    public function scopePublished($query){
        
        return $query->whereNotNull("published_at");
        
    }
    
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }
    
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }
    
    public function getTicketPriceInDollarsAttribute(){
        return number_format($this->ticketPrice/100,2);
    }
    
    public function orders(){
        return $this->HasMany(Order::class);
    }
    
    public function tickets(){
        return $this->hasMany( Ticket::class);
    }
    
    public function orderTickets($email, $ticketQuantity){
    
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
    
        if ($tickets->count() < $ticketQuantity){
            throw new NotEnoughTicketsException();
        }
        $order = $this->orders()->create(['email' => $email]);
    
        foreach($tickets as $ticket){
            $order->tickets()->save($ticket);
        }
        
        
        return $order;
    }
    
    public function addTickets($quantity){
        foreach(range(1,$quantity) as $i){
            $this->tickets()->create([]);
        }
        
        return $this;
    }
    
    public function ticketsRemaining(){
        return $this->tickets()->available()->count();
    }
    
    public function hasOrderFor($email){
        return $this->orders()->where('email', $email)->count() > 0;
    }
    
    public function ordersFor($email){
        return $this->orders()->where('email', $email)->get();
    }
}
