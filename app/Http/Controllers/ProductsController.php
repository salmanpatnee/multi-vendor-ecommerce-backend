<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image as IImage;

class ProductsController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'product');
    }

    private $uploadDir = 'products/';
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
        $attributes['image'] = $this->uploadImage('image', $this->uploadDir);

        $product = Product::create($attributes);

        // Attaching categories.
        $product->categories()->attach($category_ids);

        // Upload bulk images.
        $this->uploadGalleryImages('gallery', $this->uploadDir, $product);

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
        $attributes['image'] = $this->uploadImage('image', $this->uploadDir);

        // Upload bulk images.
        $this->uploadGalleryImages('gallery', $this->uploadDir, $product);

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

    public function deleteProductImage(Product $product)
    {
        $this->authorize('delete', $product);

        Storage::delete($product->image);
       
        $product->image = null;
        $product->update();
        

        return response([
            'message' => 'Image deleted.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }

    public function toggleActive(Product $product)
    {
        $this->authorize('update', $product);

        $product->is_active = !$product->is_active;
        $product->update();

        return response([
            'message' => 'Status updated.',
            'status'  => 'success'
        ], Response::HTTP_OK);

    }

    private function resizeAndStore($image, $path)
    {
        $image_name = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path("app/public/{$path}");
        IImage::make($image)->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($destinationPath . $image_name);

        return $image_name;
    }

    private function uploadImage($file, $path)
    {
        if (!request()->hasFile($file)) return null;

        $image = request()->file($file);

        return $path . $this->resizeAndStore($image, $path);
    }

    private function uploadGalleryImages($file, $path, Product $product)
    {
        if (!request()->hasFile($file)) return;

        $images =  request()->file($file);

        foreach ($images as $image) {

            $product->images()->create([
                'image' =>  $path . $this->resizeAndStore($image, $path)
            ]);
        }
    }
}
