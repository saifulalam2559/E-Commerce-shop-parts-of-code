<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;
 
    public $data;
    

     public $carts;
    

    public function __construct($data ,$carts)
    {
        $this->data = $data ;
        $this->carts = $carts ;
    }


    

    
    public function build()
    {

        return $this->from('saifulalam2559@gmail.com')->view('mail.order_mail')->subject('Neue Bestellung');
        
        
    }
    
    

    
    
}
