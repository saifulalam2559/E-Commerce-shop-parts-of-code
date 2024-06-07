<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Brand;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\ProductImage;
use App\Models\Product_attributes;




class IndexController extends Controller
{

    
    
    public function home()
    {
        
        $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
        $categories = Category::where(['status'=>'active','is_parent'=>1])->orderBy('id','DESC')->get();
        $brands = Brand::where(['status'=>'active'])->orderBy('id','DESC')->get();
        return view('frontend.index', compact('banners','categories','brands'));
    }
    
    
    public function shop(Request $request) {
        
        $products5 = Product::query();
        
        
        if(!empty($_GET['category'])){     
            
            $slugs = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            $products = $products5->whereIn('cat_id',$cat_ids);
            
            
        }
        
        
        if(!empty($_GET['brand'])){      
            
            $slugs = explode(',', $_GET['brand']);
            $brand_ids = Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            $products = $products5->whereIn('brand_id',$brand_ids);          
            
        }
        
        
        if(!empty($_GET['size'])){    

                   $products = $products5->where('size',$_GET['size']);
    
             }
        
        
         $sort ='';
     
         if($request->sortBy != null){
             
             $sort = $request->sortBy;
         }
         
        
         if(!empty($_GET['sortBy'])){
             
             $sort = $_GET['sortBy'];
             
             
                    if($sort=='priceAsc'){
             
                        $products = $products5->where('status','active')->orderBy('offer_price','ASC')->paginate(20);
                        

                        
                    }elseif($sort=='priceDesc'){
             

                        $products = $products5->where('status','active')->orderBy('offer_price','DESC')->paginate(20);
                        
                   }elseif($sort=='titleAsc'){
             

                        $products = $products5->where('status','active')->orderBy('title','ASC')->paginate(20);
                        
                   }elseif($sort=='titleDesc'){
             

                        $products = $products5->where('status','active')->orderBy('title','DESC')->paginate(20);
                        
                   }elseif($sort=='discountAsc'){
             

                        $products = $products5->where('status','active')->orderBy('discount','ASC')->paginate(20);
                        
                   }elseif($sort=='discountDesc'){
             

                        $products = $products5->where('status','active')->orderBy('discount','DESC')->paginate(20);
                        
                   }else{
                       
                        $products = $products5->where('status','active')->paginate(20);
                       
                   }
             
         }
        
        
         
            if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            $price[0]= floor($price[0]);
            $price[1]= ceil($price[1]);
            $products = $products5->whereBetween('offer_price',$price)->where('status','active')->paginate(20);            
            
        }
        
         
            if(!empty($_GET['price1'])){
            $price1=explode('-',$_GET['price1']);
            $price1[0]= floor($price1[0]);
            $price1[1]= ceil($price1[1]);
            $products = $products5->whereBetween('discount',$price1)->where('status','active')->paginate(20);
                 
        }
        
    
   
        
        else {
            
              $products = $products5->where('status','active')->paginate(20);
            
        }
        
       
         $cats = Category::where(['is_parent'=>1])->with('products')->orderBy('title','ASC')->get();
         $brands = Brand::where(['status'=>'active'])->with('products')->orderBy('title','ASC')->get();
         return view('frontend.pages.product.shop', compact('products','cats','brands','sort'));
        
    }
    
    
    
    
    
    public function shopFilter(Request $request) {
        
        $data = $request->all();
        
        $catUrl ='';
        
        if(!empty($data['category'])){             
            
            foreach ($data['category'] as $category){
                
                if(empty($catUrl)){
                    
                    $catUrl .= '&category='.$category ;
                    
                } else{
                    
                    $catUrl .= ','.$category ;
                }
            }
            
        }
        
        
        $sortByUrl = '';
        
        
         if(!empty($data['sortBy'])){        
             
              $sortByUrl .= '&sortBy='.$data['sortBy'] ;
             
         }
         
         
            $priceRangeURL='';
            
            if(!empty($data['price_range'])){   
                
                $priceRangeURL .='&price='.$data['price_range'];
            }
     
         
         
            $discountRangeURL='';
            
            if(!empty($data['price_range1'])){  
                
                $discountRangeURL .='&price1='.$data['price_range1'];
            }
     
         
        
          $brandUrl = '';
        
        
         if(!empty($data['brand'])){        
              
             foreach ($data['brand'] as $brand){
                
                if(empty($brandUrl)){
                    
                    $brandUrl .= '&brand='.$brand ;
                    
                } else{
                    
                    $brandUrl .= ','.$brand ;
                }
            }
              
              
         } 
        
         
         
               $sizeUrl = '';
               
               if(!empty($data['size'])){      
                
               $sizeUrl .='&size='.$data['size'];
               
            }     
         
        
        return redirect()->route('shop',$catUrl.$sortByUrl.$priceRangeURL.$brandUrl.$sizeUrl.$discountRangeURL);
        
    }
    
   
    
    
    
    
    // Autocomplete Search
    
    public function autoSearch(Request $request) {
    
        
        $query = $request->get('term','');       
        $products =Product::where('title','LIKE','%'.$query.'%')->get();     
        $data=array();
        foreach ($products as $product){
            
            $data[] = array('value'=>$product->title,'id'=>$product->id);
        }
        
        if(count($data)){
            
            return $data;
            
        } else {
            
            return ['value'=>'No Result Found', 'id'=>''];
        }
        
        
    }
    
    
     //  search function
    
    public function search(Request $request) {
        
         $query = $request->input('query');
         $products =Product::where('title','LIKE','%'.$query.'%')->orderBy('id','DESC')->paginate(20);
         $cats = Category::where(['is_parent'=>1])->with('products')->orderBy('title','ASC')->get();
         $brands = Brand::where(['status'=>'active'])->with('products')->orderBy('title','ASC')->get();
         $sort = $request->sortBy;
         return view('frontend.pages.product.shop', compact('products','cats','brands','sort'));
        
    }


    public function productCategorySlug(Request $request,$slug) {
        
         $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
         $categories = Category::with('products')->where('slug',$slug)->first(); 
         
         $sort ='';
   
         
         if($request->sort != null){
             
             $sort = $request->sort;
         }
         
         if($categories==null){
             
             return view('errors.404');
             
         } else {
             
                     if($sort=='priceAsc'){
             
                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('offer_price','ASC')->paginate(56);
                        

                        
                    }elseif($sort=='priceDesc'){
             

                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('offer_price','DESC')->paginate(56);
                        
                   }elseif($sort=='titleAsc'){
             

                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('title','ASC')->paginate(56);
                        
                   }elseif($sort=='titleDesc'){
             

                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('title','DESC')->paginate(56);
                        
                   }elseif($sort=='discountAsc'){
             

                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('discount','ASC')->paginate(56);
                        
                   }elseif($sort=='discountDesc'){
             

                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->orderBy('discount','DESC')->paginate(56);
                        
                   }else{
                       
                        $products1 = Product::where(['status'=>'active','cat_id'=>$categories->id])->paginate(56);
                       
                   }
             
         }
         
         
         
         $route ='product-category';                      
         return view('frontend.pages.product_category', compact('categories','banners','route','products1','sort'));

         
    }
    
    
     
    
    public function productBrandSlug($slug) {

         $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
         $brands = Brand::with('products')->where('slug',$slug)->first(); 
         return view('frontend.pages.product_brand', compact('brands','banners'));
        
    }
    
    

    
    public function productDetailSlug($slug) {
        
        $products = Product::with('rel_product','attributes')->where('slug',$slug)->first(); 
        $ProductsAltImages = ProductImage::where('product_id',$products->id)->get();
        
        if($products){
            
            return view('frontend.pages.product_detail', compact('products','ProductsAltImages'));
            
        }else{
            
            return 'Product not found !!';
        }
        
    }
    
    
    // USER Login and Registration
    
    public function loginUser() {
            
         if(!Auth::check()){
            
             return view('frontend.pages.login');
        }
        
        elseif(Auth::check() && Auth::user()->role == 'customer' ){
            
            
            return redirect()->route('userDashboard');
        }
        
        elseif(Auth::check() && Auth::user()->role == 'seller'){
            
            
            return redirect()->route('sellerDashboard');
            
        } elseif(Auth::check() && Auth::user()->role == 'admin'){
            
            
            return redirect()->route('admin');
        }
        
        
        
    }
    
     
    
    
    public function registerUser() {
        
          if(!Auth::check()){
            
            return view('frontend.pages.register');
        }
        
        elseif(Auth::check() && Auth::user()->role == 'customer'){
            
            
            return redirect()->route('userDashboard');
        }
        
        elseif(Auth::check() && Auth::user()->role == 'seller'){
            
            
            return redirect()->route('sellerDashboard');
            
        }elseif(Auth::check() && Auth::user()->role == 'admin'){
            
            
            return redirect()->route('admin');
        }
        
        
    }
    
    
    
    
    
    
    public function loginSubmit(Request $request) {
        
        $this->validate($request, [
            
            'email' => 'email|required|exists:users,email',
            'password' => 'required|min:4',
            
        ]);
        
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password,'status'=>'active','role'=>['seller','customer']])){
            
           Session::put('user',$request->email);
            
            if(Session::get('url.intended')){
                
                return Redirect::to(Session::get('url.intended'));
                
            }elseif((Auth::user()->role == 'seller')){
            
                  return redirect()->route('sellerDashboard');
                
            }elseif((Auth::user()->role == 'customer')){
            
                 return redirect()->route('userDashboard')->with('success','Login has been created successfully !');
                
            }else{
                
            }
            
         
            
        } else {
            
            return back()->with('error','Invalid user email or password !!');
            
        }
        
    }
    
    
    public function registerSubmit(Request $request) {
        
             $this->validate($request, [
            'first_name'=> 'string|required ',
            'last_name'=> 'string|required ',
            'username' => 'string|nullable',    
            'email' => 'required|unique:users,email',
            'password' => 'required|min:4|confirmed',
            
        ]);
                
           $data = $request->all();   
           $check = $this->create5($data);
           
           Session::put('user',$data['email']);
           Auth::login($check);
           
           if($check){
               
                return redirect()->route('fronthome')->with('success','Registration has been created successfully !');
               
           }else{
               
               return back();
           }
                
    }
    
    
    private function create5(array $data) {
        
        return User::create([
            
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'], 
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            
        ]);
    }

    
    
    public function allCategory() {
        
        $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
        $maincategories = Category::where(['status'=>'active','is_parent'=>0])->orderBy('id','DESC')->get();
        $subcategories = Category::where(['status'=>'active','is_parent'=>1])->orderBy('id','DESC')->get();
        return view('frontend.pages.all_category', compact('maincategories','subcategories','banners'));
        
    }
    
    
        public function allBrand() {
        
         $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
         $brands = Brand::where('status','active')->orderBy('id','DESC')->get();
         return view('frontend.pages.all_brand', compact('brands','banners'));
        
    }
    

    
    public function loginOut() {
        
       Session::forget('user');
       Auth::logout();    
       return redirect()->route('fronthome')->with('success','Logout successfull !');
        
    }
    
    
    
    
    public function getProductPrice( Request $request) {
         
                     
                $data = $request->all();       
                $proArr = explode('-',$data['idSize']);
                $proAttr = Product_attributes::where(['product_id'=>$proArr[0],'size'=>$proArr[1]])->first();
   
                $foo = $proAttr->price;
                echo number_format((float)$foo, 2, '.', '');  
        
        
 
                 
             }
    

    
    
}
