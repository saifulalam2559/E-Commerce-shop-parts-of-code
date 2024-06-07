<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_attributes extends Model
{
    use HasFactory;
    
    protected $fillable = [
        
                    'sku',
                    'photo',
                    'size',
                    'price',
                    'stock',
                    'product_id',


                    ];
    
        }
