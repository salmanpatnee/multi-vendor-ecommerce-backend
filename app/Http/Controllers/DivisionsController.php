<?php

namespace App\Http\Controllers;

use App\Http\Requests\DivisionStoreRequest;
use App\Http\Requests\DivisionUpdateRequest;
use App\Http\Resources\DivisionResource;
use App\Models\Division;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DivisionsController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Division::class, 'division');
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

        $divisions = Division::search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);

        return DivisionResource::collection($divisions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DivisionStoreRequest $request)
    {
        $attributes = $request->validated();

        $division = Division::create($attributes);

        return (new DivisionResource($division))
            ->additional([
                'message' => 'Division created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Division $division)
    {
        return new DivisionResource($division);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DivisionUpdateRequest $request, Division $division)
    {
        $attributes = $request->validated();

        $division->update($attributes);

        return (new DivisionResource($division))
            ->additional([
                'message' => 'Division updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $division->delete();

        return response([
            'message' => 'Division deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }
}
