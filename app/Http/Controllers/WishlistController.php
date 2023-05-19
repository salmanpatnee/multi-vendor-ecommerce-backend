<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Wishlist::class, 'wishlist');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $paginate = request('paginate', 10);

        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->get();

        $totalItems = DB::table('wishlists')
        ->select(DB::raw('COUNT(*) as total_items'))
        ->where('user_id', Auth::id())
        ->get();

        return WishlistResource::collection($wishlistItems)->additional([
            'meta' => $totalItems
        ]);;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistRequest $request)
    {
        if (Auth::check()) {
            $attributes = $request->validated();
            $attributes['user_id'] = Auth::id();
            $isExists = Wishlist::where('user_id', '=', $attributes['user_id'])
                ->where('product_id', '=', $attributes['product_id'])
                ->exists();

            if (!$isExists) {
                $wishlist = Wishlist::create($attributes);

                return (new WishlistResource($wishlist))
                    ->additional([
                        'message' => 'Product added to wishlist.',
                        'status' => 'success'
                    ])->response()
                    ->setStatusCode(Response::HTTP_OK);
            } else {
                return response([
                    'message' => 'Product is already in wishlist.',
                    'status'  => 'error'
                ]);
            }
        } else {
            return response([
                'message' => 'You must login.',
                'status'  => 'error'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();

        return response([
            'message' => 'Item deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }
}
