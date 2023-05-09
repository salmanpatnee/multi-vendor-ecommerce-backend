<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = request('paginate', 10);
        $term     = request('search', '');
        $sortOrder  = request('sortOrder', 'desc');
        $orderBy    = request('orderBy', 'name');

        $products = Product::search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {

        $attributes = $request->validated();

        $category_ids = $attributes['category_id'];
        unset($attributes['category_id']);

        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('product');
        }


        $product = Product::create($attributes);

        $product->categories()->attach($category_ids);


        
        if ($request->hasFile('gallery')) {

            $images = $request->file('gallery');

            foreach ($images as $image) {
                $product->images()->create([
                    'image' =>  $image->store('product')
                ]);  

            }
        }

        return (new ProductResource($product))
            ->additional([
                'message' => 'Product created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $attributes = $request->validated();

        $category_ids = $attributes['category_id'];
        unset($attributes['category_id']);

        $product->update($attributes);

        $product->categories()->sync($category_ids);

        return (new ProductResource($product))
            ->additional([
                'message' => 'Product updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response([
            'message' => 'Product deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }
}
