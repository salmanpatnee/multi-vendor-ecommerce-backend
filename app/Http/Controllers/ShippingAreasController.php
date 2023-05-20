<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingAreaStoreRequest;
use App\Http\Requests\ShippingAreaUpdateRequest;
use App\Http\Resources\ShippingAreaResource;
use App\Models\ShippingArea;
use App\Policies\ShippingAreaPolicy;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShippingAreasController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(ShippingAreaPolicy::class, 'shippingArea');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = request('paginate', 10);
        $term     = request('search', '');
        $sortOrder  = request('sortOrder', 'desc');
        $orderBy    = request('orderBy', 'name');

        $shippingAreas = ShippingArea::search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);

        return ShippingAreaResource::collection($shippingAreas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShippingAreaStoreRequest $request)
    {
        $attributes = $request->validated();

        $shipping_area = ShippingArea::create($attributes);

        return (new ShippingAreaResource($shipping_area))
            ->additional([
                'message' => 'Shipping Area created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShippingArea $shippingArea)
    {
        return new ShippingAreaResource($shippingArea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShippingAreaUpdateRequest $request, ShippingArea $shippingArea)
    {
        $attributes = $request->validated();

        $shippingArea->update($attributes);

        return (new ShippingAreaResource($shippingArea))
            ->additional([
                'message' => 'Shipping Area updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingArea $shippingArea)
    {
        //
    }
}
