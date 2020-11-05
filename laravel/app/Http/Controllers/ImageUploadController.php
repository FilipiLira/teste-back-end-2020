<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function store($request){

        $fileName = uniqid(date('HisYmd'));
        $file = $request->image;

        if($request->hasFile('image') && $request->file('image')->isValid()){ 

           $extesion = $request->image->extension();
           $fileName = $fileName .'.'. $extesion;
           $filePath = 'images/products/'.$fileName;
           $upload = $file->storeAs('public/', $filePath);
 
           if ( !$upload ){
             return false; 
           } else {
             return $filePath; 
           }
        }
    }

    public function update($request, $oldFile){
         
        if($oldFile){
            $oldFile = Storage::delete('public/'.$oldFile);
        }

         return $this->store($request);
    }

    public function destroy($file){
        Storage::delete('public/'.$file);
    }
}
