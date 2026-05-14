<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Rastreo público de pedidos (no requiere autenticación).
     *
     * POST /api/track
     * Body: { invoice_number, customer_number }
     */
    public function search(Request $request)
    {
        $request->validate([
            'invoice_number'  => 'required',
            'customer_number' => 'required',
        ]);

        $order = Order::with('products')
            ->where('invoice_number', $request->invoice_number)
            ->where('customer_number', $request->customer_number)
            ->where('is_deleted', false)
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Datos incorrectos. Verifica tu número de factura y cliente.',
                'found'   => false,
            ], 404);
        }

        return response()->json([
            'found' => true,
            'data'  => [
                'invoice_number'  => $order->invoice_number,
                'customer_name'   => $order->customer_name,
                'status'          => $order->status,
                'delivery_address' => $order->delivery_address,
                'products'        => $order->products->map(function ($product) {
                    return [
                        'name'     => $product->name,
                        'quantity' => $product->pivot->quantity,
                    ];
                }),
                'created_at'      => $order->created_at,
                'updated_at'      => $order->updated_at,
            ],
        ]);
    }
}
