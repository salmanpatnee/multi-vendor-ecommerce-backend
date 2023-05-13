<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image as IImage;
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
        unset($attributes['category_id'], $attributes['gallery']);
        
        // Upload single image.
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            IImage::make($image)->resize(800,100)->save('products/'.$image_name);
            // return $image_name;
            $attributes['image'] = 'products/'.$image_name;
        }

        $product = Product::create($attributes);
        
        $product->categories()->attach($category_ids);


        // Upload bulk images.
        if ($request->hasFile('gallery')) {
            
            $images =  $request->file('gallery');
            
            foreach ($images as $image) {
                $path = $image->store('product');
                $product->images()->create([
                    'image' =>  $path
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

        // Upload single image.
        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('product');
        }
        
        // Upload bulk images.
        if ($request->hasFile('gallery')) {
            
            $images =  $request->file('gallery');
            
            foreach ($images as $image) {
                $path = $image->store('product');
                $product->images()->create([
                    'image' =>  $path
                ]);  

            }
        }
        
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
