<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponStoreRequest;
use App\Http\Requests\CouponUpdateRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CouponController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Coupon::class, 'coupon');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paginate = request('paginate', 10);
        $term     = request('search', '');

        $coupons = Coupon::search($term)
            ->paginate($paginate);

        return CouponResource::collection($coupons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponStoreRequest $request)
    {
        $attributes = $request->validated();

        $coupon = Coupon::create($attributes);

        return (new CouponResource($coupon))
            ->additional([
                'message' => 'Coupon created successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        return new CouponResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $attributes = $request->validated();

        $coupon->update($attributes);

        return (new CouponResource($coupon))
            ->additional([
                'message' => 'Coupon updated successfully.',
                'status' => 'success'
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response([
            'message' => 'Coupon deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }
}
