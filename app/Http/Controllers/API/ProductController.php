<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductDetailResource;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();
        return ProductDetailResource::collection($products->loadMissing(['seller:id,name,email', 'orders:id,customer_id,quantity']));
    }

    public function show($id) {
        $product = Product::findOrFail($id);
        return new ProductDetailResource($product->loadMissing(['seller:id,name,email', 'orders:id,customer_id,quantity']));

    }

    public function store(Request $request) {
        $validated = $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        $image = 'NULL';

        if($request->file){
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName.'.'.$extension;

            Storage::putFileAs('images-product', $request->file, $image);
        }

        $request['image'] = $image; 
        $request['user_id'] = Auth::user()->id;
        $product = Product::create($request->all());

        return new ProductDetailResource($product->loadMissing(['seller:id,name,email', 'orders:id,customer_id,quantity']));

    }
    public function update(Request $request, $id) {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        // if($request->file){
        //     if($request->image){
        //         Storage::delete($request->image);
        //     }
        //     $validated['image'] = $request->file->store('image');
        // }
        $image = 'NULL';

        if($request->file){
            if($request->image){
                Storage::delete($request->image);
            }   
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName.'.'.$extension;

            Storage::putFileAs('images-product', $request->file, $image);
        }

        $request['image'] = $image; 

        $product = Product::findOrFail($id);
        $product->update($request->all());
        
        return new ProductDetailResource($product->loadMissing(['seller:id,name,email', 'orders:id,customer_id,quantity']));

    }
    public function destroy($id) {
        $product = Product::findOrFail($id);
        if($product->image){
            Storage::delete($product->image);
        }
        $product->delete();
    }
    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
