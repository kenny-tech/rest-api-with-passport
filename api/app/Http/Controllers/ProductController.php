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
        $products = Product::all();

        if(is_null($products)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse($products->toArray(), 'Products retrieved successfully');
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

        // if auth user, create product
        if(Auth::check())
        {
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
        else
        {
            return $this->sendError('Unathenticated user');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if(is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse($product->toArray(), 'Product retrieved successfully');
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
            'detail' => 'required'
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();

        return $this->sendResponse($product->toArray(), 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse($product->toArray(), 'Product deleted successfully.');
    }
}
