<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Product_attributes;
use App\Models\ProductImage;
use Image;






class ProductController extends Controller
{
   
    public function index()
    {
        
         $products = Product::orderBy('id','DESC')->get();
        return view('backend.products.index', compact('products'));
        
    }
    
    
     public function productStatus(Request $request) {

        
        if($request->mode == 'true'){
            
            Product::where('id',$request->id)->update(['status'=>'active']);
           
        }else{
            
            Product::where('id',$request->id)->update(['status'=>'inactive']);
        }
        
        return response()->json(['msg'=>'Successfully Status Updated','status'=>true]);
        
    }

    
    public function create()
    {
        return view('backend.products.create');
    }

    
    public function store(Request $request)
    {
  
        
         $this->validate($request, [
            
            'title'=> 'string|required ',
            'summary' => 'string|required',
            'description'=>'string|nullable',
            'stock'=> 'nullable|numeric',
            'brand_id'=> 'required',
            'cat_id'=> 'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'vendor_id'=> 'required',
            'photo'=> 'required',
            'price'=> 'nullable|numeric',
            'offer_price'=> 'nullable|numeric',
            'discount'=> 'nullable|numeric',
            'size'=> 'nullable',
            'conditions'=> 'nullable',
            'status'=> 'nullable|in:active,inactive',
             
        
        ], 
        
     );
         
         
         $data = $request->all();
         $slug = Str::slug($request->input('title'));
         $slug_count = Product::where('slug',$slug)->count();
         
         if($slug_count>0){
             
             $slug = time().'-'.$slug;
             
         }
         
       
         $data['slug'] = $slug ;
         $data['offer_price'] = ($request->price-(($request->price*$request->discount)/100));
         $data['discount'] = $request->offer_price*100/$request->price;
         
         
         $status = Product::create($data);
         
         if($status){
             
             return redirect()->route('product.index')->with('success','Product has been created successfully !');
             
         }else{
             
             return redirect()->back()->with('error','Something went wrong');
         }
         
         
         
    }

   
    public function show($id)
    {
        //        
        
    }

   
    public function edit($id)
    {
        $product = Product::find($id);
        
        if($product){
            
            return view('backend.products.edit', compact('product'));
            
        }else{
            
            return back()->with('error','Products not found!!');
            
        }
    }

   
    public function update(Request $request, $id)
    {

        $product = Product::find($id);
        
        if($product){
            
            
           $this->validate($request, [
           
           'title'=> 'string|required ',
            'summary' => 'string|required',
            'description'=>'string|nullable',
            'stock'=> 'nullable|numeric',
            'brand_id'=> 'required',
            'cat_id'=> 'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'vendor_id'=> 'required',
            'photo'=> 'required',
            'price'=> 'nullable|numeric',
            'offer_price'=> 'nullable|numeric',
            'discount'=> 'nullable|numeric',
            'size'=> 'nullable',
            'conditions'=> 'nullable',
            'status'=> 'nullable|in:active,inactive',
        
       ]);
        
         $data = $request->all();
         
         $data['offer_price'] = ($request->price-(($request->price*$request->discount)/100));
         
         
 
         $status = $product->fill($data)->save();
         
         if($status){
             
             return redirect()->route('product.index')->with('success','Product has been updated successfully !');
             
         }else{
             
             return redirect()->back()->with('error','Something went wrong');
         }
            
        }else{
            
            return back()->with('error','Data not found!!');
            
        }
        
        
    }

   
    public function destroy($id)
    {
        
        
        $product = Product::find($id);
  
        
        if($product){
            
            $status = $product->delete();
            
            if($status){
                
                
                return redirect()->route('product.index')->with('success','Product has been deleted!!');
                
            }else{
                
                return back()->with('error','Something went wrong!!');
                
            }
            
        }else{
            
            return back()->with('error','Data not found!!');
            
        }
        
        
    }
    
    
    
    // Start Product custom Attributes Section
    
    
    public function addProductAttributes(Request $request , $id = null) {
        
        $product = Product::with('attributes')->where(['id'=>$id])->first();
        
        if($request->isMethod('post')){
            
            $data = $request->all();
            
            
            foreach( $data['sku'] as $key => $val ){
                
                if(!empty($val)){


                   // Prevent dublicate SKU Records
                    $attrCountSKU = Product_attributes::where('sku',$val)->count();
                    
                        if($attrCountSKU>0){
                            
                             return redirect()->route('addProductAttributes',$id)->with('error','SKU number is aready there!');    
                    
                                } 
                
                
                
                 // Prevent dublicate Size Records
                    
                    $attrCountSize = Product_attributes::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    
                        if($attrCountSize>0){
                            
                             return redirect()->route('addProductAttributes',$id)->with('error',''.$data['size'][$key].'SKU number is aready there!');    
                    
                                }
                
                
                          $attribute = new Product_attributes;
                          $attribute->product_id = $id;
                          $attribute->sku = $val;
                          $attribute->size = $data['size'][$key];
                          $attribute->price = $data['price'][$key];
                          $attribute->stock = $data['stock'][$key];
                          $attribute->save();
                             
                
                    }
            
                    
            
            } 


             return redirect()->route('addProductAttributes',$id)->with('success','Product Attributes Data have been submitted!');   

        }

        return view('backend.productAttributes.add_attributes', compact('product'));

    }  



    
        // Delete product attributes
    
    
        public function deleteProductAttribute($id) {
            
            
             Product_attributes::where('id',$id)->delete() ;
             return redirect()->back()->with('error','Product Attribute has been deleted!!');  
            

        
            
        }
        
        
        
        // Edit product arrtibutes 
        
        
        public function editAttributes(Request $request, $id= null) {
            
            
            
                    if($request->isMethod('post')){
                        
                    $data = $request->all();
                     
                    
                    foreach($data['attr'] as $key=>$attr){
                        
                        Product_attributes::where(['id'=>$data['attr'][$key]])->update([
                            
                            'sku'=>$data['sku'][$key],
                            'size'=>$data['size'][$key],
                            'price'=>$data['price'][$key],
                            'stock'=>$data['stock'][$key]
                                
                         ]);
                        
                    }   
                    
                  
                    
                    return redirect()->back()->with('success','Products Attributes Updated!!!');
                    
                }  

            
            
        } 
        
        
        
     public function addImages(Request $request, $id= null) {
         
         $product = Product::where(['id'=>$id])->first();
         
         if($request->isMethod('post')){
             
             $data = $request->all();
             
                    if($request->hasFile('image')){

                        $files = $request->file('image');
                        
                        foreach($files as $file){
                            
                            
                            $image = new ProductImage;
                            
                            $extension = $file->getClientOriginalExtension();
                            $filename = rand(111,9999).'.'.$extension;
                            $image_path = 'images/product/'.$filename;
                            Image::make($file)->resize(null, 800, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })->save($image_path);
                                
                               // table filed
                            $image->image = $filename;
                            $image->product_id = $data['product_id'];        
                            $image->save();
                                               
                            
                        } 


                    } 
                    
                    
                    return redirect()->back()->with('success','Products image has been added!!!');
             
             
         } 
         
         
         $productImages = ProductImage::where('product_id',$id)->get();         
         return view('backend.productAttributes.add_images', compact('product','productImages'));
         
         
     }
     
     
     
     
     
     public function deleteProductImage( $id = null) {
         
         
         $productImage = ProductImage::where(['id'=>$id])->first();
         

                $image_path = 'images/product/';

                    if(file_exists($image_path.$productImage->image)){

                        unlink($image_path.$productImage->image);
                    }

                    ProductImage::where(['id'=>$id])->delete();

                    return redirect()->back()->with('success','Products image deleted!!');
                
                
                

             }
             
             
             
             


        
        
        
    
    

}
