<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Retorna el stock actual de todos los productos.
     *
     * GET /api/inventory
     */
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'data' => $products,
        ]);
    }

    /**
     * Procesa reabastecimiento de stock (compra al proveedor).
     *
     * POST /api/inventory/restock
     * Body: { product_id, quantity }
     */
    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $product->increment('stock', $request->quantity);

        return response()->json([
            'message' => "Se han añadido {$request->quantity} unidades a {$product->name}.",
            'data'    => $product->fresh(),
        ]);
    }
}
