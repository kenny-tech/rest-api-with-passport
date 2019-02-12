<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Product;
use Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Auth::user()->products;
        if($products)
        {
            return $this->sendResponse($products->toArray(), 'Products retrieved successfully');
        }
        else
        {
            return $this->sendError('No product found.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get all input
        $input = $request->all();

        // validates input
        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required'
        ]);

        // if validation fails, send validation error message
        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validation->errors());
        }

        $product = Product::create([
                'user_id' => Auth::user()->id,
                'name' => $request->input('name'),
                'price' => $request->input('price'),
            ]);

        if($product) 
        {
            return $this->sendResponse($product->toArray(), 'Product added successfully.');
        }
        else
        {
            return $this->sendError('Unable to add product.', $code = 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($product_id)
    {
        $product = Auth::user()->products()->find($product_id);

        if(!$product) 
        {
            return $this->sendError('Product with id '. $product_id . ' not found.');
        }
        else
        {
            return $this->sendResponse($product->toArray(), 'Product retrieved successfully.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required'
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        //$productUpdate = Product::find($product->id);
        $productUpdate = Auth::user()->products()->find($product->id);

        $productUpdate->name = $request->input('name');
        $productUpdate->price = $request->input('price');

        if($productUpdate->save())
        {
            return $this->sendResponse($productUpdate->toArray(), 'Product updated successfully.');
        }
        else
        {
            return $this->sendError('Unable to update product');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id)
    {
        $product = Auth::user()->products()->find($product_id);

        if(!$product)
        {
            return $this->sendError('Product with id '. $product_id . ' not found.');
        }
        
        if($product->delete())
        {
            return $this->sendResponse([], 'Product deleted successfully.');
        }
        else
        {
            return $this->sendError('Unable to delete product');
        }
    }
}
