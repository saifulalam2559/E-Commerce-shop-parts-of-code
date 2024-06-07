<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product_attributes;





class Product extends Model
{
    use HasFactory;
    protected $fillable = ['title','slug','summary','description','product_code','sku','stock','brand_id','cat_id','child_cat_id','vendor_id','photo','price','offer_price','discount','size','conditions','status'];
    
    
    
        public function rel_product() {

            return $this->hasMany(Product::class,'cat_id','cat_id')->where('status','active')->limit(20);

        }
    
    

        public static function getProductByCart($id) {

         return self::where('id',$id)->get()->toArray() ;

     }

    
        public function category() {

            return $this->belongsTo(Category::class,'cat_id','id');

        }


         public function brand() {

            return $this->belongsTo(Brand::class,'brand_id','id');

        }
    
    
         public function attributes() {
        
        return $this->hasMany(Product_attributes::class,'product_id','id');
        
    }
    
    
    

    

    
    

    
    
}
