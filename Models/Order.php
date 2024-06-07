<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        

                        'user_id',     
                        'first_name',
                        'last_name',
                        'username',
                        'email',
                        'phone',
                        'country',
                        'address',
                        'city',
                        'state',
                        'postcode',
                        'note',
                        'sfirst_name',
                        'slast_name',
                        'semail',
                        'sphone',
                        'scountry',
                        'saddress',
                        'scity',
                        'sstate',
                        'spostcode',     
                        'payment_type',
                        'payment_method',
                        'transaction_id',
                        'currency',
                        'amount',
                        'order_number',
                        'invoice_number',
                        'order_date',
                        'order_month',
                        'order_year',
                        'confirmed_date',
                        'processing_date',
                        'picked_date',
                        'shipped_date',
                        'delivered_date',
                        'canceled_date',
                        'return_date',
                        'return_reason',
                        'status',


        
        ];
    
    
}
