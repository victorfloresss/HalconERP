<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\Product; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Muestra la lista de pedidos activos con sus productos cargados.
     */
    public function index()
    {
        $orders = Order::with('products')
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('orders.index', compact('orders'));
    }

    /**
     * Muestra el formulario de creación con la lista de productos para el select.
     */
    public function create()
    {
        if (!in_array(auth()->user()->role->slug, ['sales', 'admin'])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    /**
     * Guarda un nuevo pedido y sus productos asociados en la tabla pivote.
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role->slug, ['sales', 'admin'])) {
            abort(403, 'No tienes permiso para registrar pedidos nuevos.');
        }

        $request->validate([
            'invoice_number'   => 'required|unique:orders',
            'customer_number'  => 'required',
            'customer_name'    => 'required',
            'delivery_address' => 'required',
            'product_id'       => 'required|array',
            'product_id.*'     => 'exists:products,id',
            'quantity'         => 'required|array',
            'quantity.*'       => 'integer|min:1',
            'fiscal_data'      => 'nullable',
            'notes'            => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la cabecera de la orden
            $order = Order::create([
                'invoice_number'   => $request->invoice_number,
                'customer_number'  => $request->customer_number,
                'customer_name'    => $request->customer_name,
                'delivery_address' => $request->delivery_address,
                'fiscal_data'      => $request->fiscal_data ?? 'N/A', 
                'notes'            => $request->notes,               
                'user_id'          => Auth::id(),
                'status'           => 'Ordered',
                'is_deleted'       => false,
            ]);

            // 2. Asociar los productos y sus cantidades
            foreach ($request->product_id as $index => $productId) {
                $order->products()->attach($productId, [
                    'quantity' => $request->quantity[$index]
                ]);
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Pedido registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pedido: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Actualiza el estado y gestiona el inventario de Warehouse.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('products')->findOrFail($id);
        $role = auth()->user()->role->slug;

        if ($role === 'sales') {
            return back()->with('error', 'Ventas no tiene permisos para modificar el flujo logístico.');
        }

        // --- LÓGICA DE ALMACÉN: VALIDACIÓN DE STOCK ---
        if ($request->status == 'In process') {
            if (!in_array($role, ['warehouse', 'admin'])) {
                return back()->with('error', 'Solo Almacén puede iniciar la preparación.');
            }

            // Validar si hay stock disponible para todos los productos
            foreach ($order->products as $product) {
                $cantidadSolicitada = $product->pivot->quantity;
                if ($product->stock < $cantidadSolicitada) {
                    return back()->with('error', "Stock insuficiente de {$product->name}. Disponible: {$product->stock}, Requerido: {$cantidadSolicitada}.");
                }
            }

            // Descontar del inventario
            foreach ($order->products as $product) {
                $product->decrement('stock', $product->pivot->quantity);
            }
        }

        // --- LÓGICA DE FOTOS: DESPACHO ---
        if ($request->status == 'In route') {
            if (!in_array($role, ['warehouse', 'route', 'admin'])) {
                return back()->with('error', 'No tienes permiso para despachar la unidad.');
            }

            if (!$request->hasFile('loaded_unit_photo')) {
                return back()->with('error', 'Es obligatorio subir la foto de la unidad cargada.');
            }

            $path = $request->file('loaded_unit_photo')->store('orders/loaded', 'public');
            $order->loaded_unit_photo = $path;
        }

        // --- LÓGICA DE FOTOS: ENTREGA ---
        if ($request->status == 'Delivered') {
            if (!in_array($role, ['route', 'admin'])) {
                return back()->with('error', 'Solo el repartidor puede confirmar la entrega.');
            }

            if (!$request->hasFile('delivered_material_photo')) {
                return back()->with('error', 'Es obligatorio subir la foto del material entregado.');
            }

            $path = $request->file('delivered_material_photo')->store('orders/delivered', 'public');
            $order->delivered_material_photo = $path;
        }

        // Guardar cambios finales
        $order->status = $request->status;
        $order->save();

        return back()->with('success', '¡Estado actualizado a ' . $request->status . ' exitosamente!');
    }

    /**
     * Métodos de administración (Borrado, Papelera, Restauración)
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['is_deleted' => true]); 
        return redirect()->route('orders.index')->with('success', 'Pedido movido a la papelera.');
    }

    public function trash()
    {
        $orders = Order::where('is_deleted', true)->get();
        return view('orders.trash', compact('orders'));
    }

    public function restore($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['is_deleted' => false]);
        return redirect()->route('orders.trash')->with('success', 'Pedido restaurado.');
    }

    /**
     * Rastreo público para clientes
     */
    public function trackForm() { return view('orders.tracking'); }

    public function trackSearch(Request $request)
    {
        $order = Order::with('products') // Cargamos productos para el rastreo
                      ->where('invoice_number', $request->invoice_number)
                      ->where('customer_number', $request->customer_number)
                      ->where('is_deleted', false)
                      ->first();

        if (!$order) {
            return back()->with('error', 'Datos incorrectos. Verifica tu factura.');
        }

        return view('orders.tracking', compact('order'));
    }
}