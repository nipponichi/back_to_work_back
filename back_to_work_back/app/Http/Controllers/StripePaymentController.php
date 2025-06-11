<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePaymentController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bid_id' => 'required|integer',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Pago de oferta',
                        'description' => 'Oferta ID: ' . $request->bid_id,
                    ],
                    'unit_amount' => $request->amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => env('APP_URL') . '/payment-success?bid_id=' . $request->bid_id,
            'cancel_url' => env('APP_URL') . '/payment-cancel',
            'metadata' => [
                'bid_id' => $request->bid_id,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }
}
