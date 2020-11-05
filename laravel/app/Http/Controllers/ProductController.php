<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    protected $productRepository;

    public function index(){
        $user = Auth::user();
        $products = Product::where('user_id', $user->id)->get();
        return responder()->success([
            'products' => $products
            ])->respond(); 
    }

    public function store(ProductRequest $productRequest, ImageUploadController $imgUpload){
        
        $validateData = $productRequest->all();

        $product = Product::create($validateData);
        
        if(isset($validateData['image'])){
            $imgPath = $imgUpload->store($productRequest);
            $product->image = $imgPath;
            $product->save();
        }

        if($product){
            return responder()->success(['product' => $product])->respond();
        } else {
            return responder()->error('sold_out_error', 'Erro ao cadastrar produto.')->respond();
        }
    }

    public function update($id , ProductRequest $productRequest,ImageUploadController $imgUpload){
         $validateData = $productRequest->all();
         
         $product =  Product::find($id);

         if($product){

            if(isset($validateData['image'])){
               if($product->image){
                
                   $imgPath = $imgUpload->update($productRequest, $product->image);
                   $product->image = $imgPath;
               } else {
                   $imgPath = $imgUpload->store($productRequest);
                   $product->image = $imgPath;
               }
            }
   
            $product->name = $validateData['name'];
            $product->price = $validateData['price'];
            $product->user_id = $validateData['user_id'];
            $product->weight = $validateData['weight'];
            $product->save();

             return responder()->success(['product' => $product])->respond();
         } else {
            return responder()->error(500, 'Erro ao editar produto.')->respond();
         }
    }

    public function destroy($id, ImageUploadController $imgUpload){
         $product = Product::find($id);

         if($product){
            
            $imgUpload->destroy($product->image);
            $product->delete();

            return responder()->success(['message' => 'Produto deletado com sucesso.'])->respond();
         } else {
           return responder()->error(500, 'Erro ao deletar produto.')->respond();
        }
    }
}
