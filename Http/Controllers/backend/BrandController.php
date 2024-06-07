<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;



class BrandController extends Controller
{
 
    public function index()
    {
        $brands = Brand::orderBy('id','DESC')->get();
        return view('backend.brand.index', compact('brands'));
    }

    public function brandStatus(Request $request) {
        
        
        if($request->mode == 'true'){
            
            Brand::where('id',$request->id)->update(['status'=>'active']);
           
        }else{
            
            Brand::where('id',$request->id)->update(['status'=>'inactive']);
        }
        
        return response()->json(['msg'=>'Status erfolgreich aktualisiert','status'=>true]);
        
    }
  
    
    public function create()
    {
        return view('backend.brand.create');
    }


    
    public function store(Request $request)
    {
        
        
        $this->validate($request, [
            
            'title'=> 'string|required ',
            'photo'=> 'required ',
            'status'=> 'nullable|in:active,inactive',
        
        ], 
        
         [
            'title.string' => 'Bitte verwenden Sie eine Zeichenfolge im Titel',
            'title.required' => 'Sie müssen Ihren Namen eingeben',
            'photo.required' => 'Ohne Foto ist dies nicht möglich',
        ] );
               
        
         $data = $request->all();
         $slug = Str::slug($request->input('title'));
         $slug_count = Brand::where('slug',$slug)->count();
         
         if($slug_count>0){
             
             $slug = time().'-'.$slug;
             
         }
         
       
         $data['slug'] = $slug ;
         $status = Brand::create($data);
         
         if($status){
             
             return redirect()->route('brand.index')->with('success','Marke wurde erfolgreich erstellt!');
             
         }else{
             
             return redirect()->back()->with('error','Etwas ist schiefgelaufen');
         }
         
    }


    
    public function show($id)
    {
        //
    }


    
    public function edit($id)
    {
        
        
        $band = Brand::find($id);
        
        if($band){
            
            return view('backend.brand.edit', compact('band'));
            
        }else{
            
            return back()->with('error','Data not found!!');
            
        }
        
    }


    
    public function update(Request $request, $id)
    {
        
        
            $band = Brand::find($id);
        
        if($band){
            
             $this->validate($request, [
            
            'title'=> 'string|required ',
            'photo'=> 'required ',
            'status'=> 'nullable|in:active,inactive',
        
        ], 
        
         [
            'title.string' => 'Bitte verwenden Sie eine Zeichenfolge im Titel',
            'title.required' => 'Sie müssen Ihren Namen eingeben',
            'photo.required' => 'Ohne Foto ist dies nicht möglich',
        ] );
               
        
         $data = $request->all();
         
         $status = $band->fill($data)->save();
         
         if($status){
             
             return redirect()->route('brand.index')->with('success','Marke wurde aktualisiert!');
             
         }else{
             
             return redirect()->back()->with('error','Etwas ist schiefgelaufen');
         }
            
        }else{
            
            return back()->with('error','Daten nicht gefunden!!');
            
        }
        
        
        
    }


    
    public function destroy($id)
    {
        
        
                $band = Brand::find($id);
        
        if($band){
            
            $status = $band->delete();
            
            if($status){
                
                return redirect()->route('brand.index')->with('success','Marke wurde gelöscht!!');
                
            }else{
                
                return back()->with('error','Etwas ist schiefgelaufen!!');
                
            }
            
        }else{
            
            return back()->with('error','Daten nicht gefunden!');
            
        }
        
    }
}
