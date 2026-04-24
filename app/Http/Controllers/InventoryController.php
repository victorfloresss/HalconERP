<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class InventoryController extends Controller
{
    // Ver el stock actual de los 5 productos
    public function index() {
        $products = Product::all();
        return view('inventory.index', compact('products'));
    }

    // Procesar la "compra" al proveedor
    public function restock(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);
        $product->increment('stock', $request->quantity);

        return back()->with('success', "Se han añadido {$request->quantity} unidades a {$product->name}.");
    }
}