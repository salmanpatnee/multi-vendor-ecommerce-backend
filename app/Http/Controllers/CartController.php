<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Cart::class, 'cart');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupon = Session::get('coupon');
        return $coupon;
        $user_id = Auth::id();

        

        $cart = Cart::Where('user_id', $user_id)
            ->get();

        $cart_totals = DB::table('carts')
            ->select(DB::raw('COUNT(*) as total_items, SUM(sub_total) as sub_total'))
            ->where('user_id', $user_id)
            ->get();

        return CartResource::collection($cart)
            ->additional([
                'meta' => $cart_totals
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartStoreRequest $request)
    {
        $attributes = $request->validated();

        $product = Product::find($attributes['product_id']);


        if (!$product->qty) {
            return response([
                'message' => 'Item is out of stock',
                'status'  => 'error'
            ], Response::HTTP_OK);
        }

        $productInCart = Cart::where('product_id', $product->id)
            ->where('user_id', $attributes['user_id'])->first();

        if ($productInCart) {

            if (($attributes['qty']) > $product->qty) {

                return response([
                    'message' => "Product is out of stock you can max order {$product->qty}",
                    'status'  => 'error'
                ], Response::HTTP_OK);
            }

            $quantity = $productInCart->quantity + $attributes['qty'];
            $sub_total = $quantity * $productInCart->unit_price;
            $productInCart->update(['quantity' => $quantity, 'sub_total' => $sub_total]);

            $product->update([
                'qty' => $product->qty - $attributes['qty']
            ]);

            return response([
                'message' => 'Quantity updated',
                'status'  => 'success'
            ], Response::HTTP_OK);
        } else {
            $cartData['product_id'] = $product->id;
            $cartData['user_id'] = $attributes['user_id'];
            $cartData['quantity'] = $attributes['qty'];
            $cartData['unit_price'] = $product->price;
            $cartData['sub_total'] = $product->price * $attributes['qty'];

            $cart = Cart::create($cartData);

            $cart['product'] = $product->name;

            $product->update([
                'qty' => $product->qty - $attributes['qty']
            ]);

            return response([
                'data' => $cart,
                'message' => 'Product added successfully.',
                'status'  => 'success',
            ], Response::HTTP_OK);
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
    public function update(CartUpdateRequest $request, Cart $cart)
    {
        $attributes = $request->validated();

        $product = Product::findOrFail($cart->product->id);

        $attributes['sub_total'] = $attributes['quantity'] * $cart->unit_price;



        if ($cart->quantity < $attributes['quantity']) {
            $quantity = $attributes['quantity'] - $cart->quantity;

            $product->update([
                'qty' => $product->qty - $quantity
            ]);
        } else {

            $quantity = $cart->quantity - $attributes['quantity'];

            $product->update([
                'qty' => $product->qty + $quantity
            ]);
        }


        $cart->update($attributes);

        return response([
            'message' => 'Cart updated successfully.',
            'status'  => 'success',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $product = Product::findOrFail($cart->product->id);

        $product->update([
            'qty' => $product->qty + $cart->quantity
        ]);

        $cart->delete();

        return response([
            'message' => 'Item deleted successfully.',
            'status'  => 'success'
        ], Response::HTTP_OK);
    }

    public function applyCoupon(Request $request)
    {
           return 	Session::all();
        
        // Store in session
        $coupon = Coupon::where('name', '=', $request->coupon)
            ->whereDate('validity', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        if ($coupon) {

            $cartSubTotal   = Cart::where('user_id', Auth::id())
                ->sum('sub_total');
            $discountAmount = 0;

            if ($coupon->discount_type === 'Fixed') {
                $discountAmount = $cartSubTotal - $coupon->value;
            } else {
                $discountAmount = round($cartSubTotal * $coupon->value / 100);
            }

            Session::put('coupon', [
                'coupon'         => $coupon->name,
                'value'          => $coupon->value,
                'discountType'   => $coupon->discount_type,
                'cartSubTotal'   => $cartSubTotal,
                'discountAmount' => $discountAmount,
            ]);

            return response()->json([
                'coupon'         => $coupon->name,
                'value'          => $coupon->value,
                'discountType'   => $coupon->discount_type,
                'cartSubTotal'   => $cartSubTotal,
                'discountAmount' => $discountAmount,
                'status'         => 'success',
                'message'        => 'Coupon applied.',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Coupon is not valid.',
            ]);
        }
    }

    public function removeCoupon()
    {
        Session::forget('coupon');

        $cartSubTotal = Cart::where('user_id', Auth::id())
                ->sum('sub_total');
        
        return response()->json([
            'cartSubTotal'   => $cartSubTotal,
            'status'         => 'success',
            'message'        => 'Coupon removed successfully.',
        ]);

    }
}
