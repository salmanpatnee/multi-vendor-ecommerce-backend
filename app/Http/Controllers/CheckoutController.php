<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function checkout(CheckoutRequest $request)
    {
        // Validate user input.
        $attributes = $request->validated();

        // Accept payment with Stripe.
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                        // 'images' => ''
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel', [], true),
        ]);

        $order = Order::create([
            'status' => 'unpaid',
            'total_price' => 20,
            'session_id' => $checkout_session->id
        ]);

        return $checkout_session->url;

        // Select paymnet gateway 
        // If Stripe: Pass cart data to stripe with coupon if any
        // Install Stripe package
        // Order and Order detail table
        // Add metadata in Stripe
        // COD
        // Send email after payment
        // Add session_id field in order table

    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $customer = null;

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                throw new NotFoundHttpException();
            }

            $customer = $stripe->customers->retrieve($session->customer);

            $order = Order::where('session_id', $sessionId)->where('status', 'Unpaid')->first();

            if (!$order) {
                throw new NotFoundHttpException();
            }

            $order->status = "Completed";
            $order->save();

            return response([
                'data' => $customer,
                'message' => 'Payment Success.',
                'status'  => 'success'
            ], Response::HTTP_OK);

        } catch (\Exception $ex) {
            throw new NotFoundHttpException();
        }
    }

    public function cancel()
    {
        return response([
            'message' => 'Payment Canceled.',
            'status'  => 'error'
        ], Response::HTTP_OK);
    }

    public function webhook()
    {
        return [];
    }
}
