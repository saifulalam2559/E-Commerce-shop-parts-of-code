<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;
    protected $fillable = ['code','type','status','value'];
    
    
    
    public function discount($total) {
        
        //return $total;
        //dd($this->type);
        
        if($this->type == 'fixed'){
            
            return $this->value;
        } 
        
        elseif($this->type == 'percent'){
            
            return round(($this->value/100)*$total);
        }
        
        else {
            
            return 0;
        }
        
    }
    
    

    
    
}
