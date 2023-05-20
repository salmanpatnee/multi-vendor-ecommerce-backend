<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistrictStoreRequest;
use App\Http\Requests\DistrictUpdateRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DistrictController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(District::class, 'district');
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

        $districts = District::search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);

        return DistrictResource::collection($districts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DistrictStoreRequest $request)
    {
        $attributes = $request->validated();

        $district = District::create($attributes);

        return (new DistrictResource($district))
            ->additional([
                'message' => 'District created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return new DistrictResource($district);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DistrictUpdateRequest $request, District $district)
    {
        $attributes = $request->validated();

        $district->update($attributes);

        return (new DistrictResource($district))
            ->additional([
                'message' => 'District updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();

        return response([
            'message' => 'District deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }
}
