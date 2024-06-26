<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;




class Brand extends Model
{
    use HasFactory;
    protected $fillable= ['title','slug','photo','status'];
    
    
    public function products() {
        
        return $this->hasMany(Product::class,'brand_id','id')->where('status','active');
        
    }
}
