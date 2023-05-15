<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlideStoreRequest;
use App\Http\Requests\SlideUpdateRequest;
use App\Http\Resources\SlideResource;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as IImage;
use Symfony\Component\HttpFoundation\Response;

class SlidesController extends Controller
{
     /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Slide::class, 'slide');
    }

    private $uploadDir = 'slides/';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = request('paginate', 10);
        $sortOrder  = request('sortOrder', 'asc');
        $orderBy    = request('orderBy', 'order');

        if(request('for') === 'front'){
            $slides = Slide::active()->orderBy($orderBy, $sortOrder)->paginate($paginate);
        } else {
            $slides = Slide::orderBy($orderBy, $sortOrder)->paginate($paginate);
        }
        
        return SlideResource::collection($slides);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SlideStoreRequest $request)
    {
        $attributes = $request->validated();

        // Upload single image.
        $attributes['image'] = $this->uploadImage('image', $this->uploadDir);

        $slide = Slide::create($attributes);

        return (new SlideResource($slide))
            ->additional([
                'message' => 'Slide created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slide $slide)
    {
        return new SlideResource($slide);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SlideUpdateRequest $request, Slide $slide)
    {
        $attributes = $request->validated();

        // Upload single image.
        $attributes['image'] = $this->uploadImage('image', $this->uploadDir, $slide->image);

        $slide->update($attributes);

        return (new SlideResource($slide))
            ->additional([
                'message' => 'Slide updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slide $slide)
    {
        Storage::delete($slide->image);
        
        $slide->delete();

        return response([
            'message' => 'Slide deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }

    private function resizeAndStore($image, $path)
    {
        $image_name = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path("app/public/{$path}");
        IImage::make($image)->resize(1600, 'auto', function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($destinationPath . $image_name);

        return $image_name;
    }

    private function uploadImage($file, $path, $image = null)
    {
        if (!request()->hasFile($file)) return $image;

        $image = request()->file($file);

        return $path . $this->resizeAndStore($image, $path);
    }
}
