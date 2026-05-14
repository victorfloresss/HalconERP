<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Lista todos los pedidos activos con sus productos.
     *
     * GET /api/orders
     */
    public function index()
    {
        $orders = Order::with(['products', 'user:id,name'])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Obtiene el detalle de un pedido específico.
     *
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        $order = Order::with(['products', 'user:id,name'])
            ->where('is_deleted', false)
            ->findOrFail($id);

        return response()->json([
            'data' => $order,
        ]);
    }

    /**
     * Crea un nuevo pedido.
     *
     * POST /api/orders
     * Body: { invoice_number, customer_number, customer_name, delivery_address,
     *         product_id[], quantity[], fiscal_data?, notes? }
     */
    public function store(Request $request)
    {
        $role = $request->user()->role->slug;
        if (!in_array($role, ['sales', 'admin'])) {
            return response()->json([
                'message' => 'No tienes permiso para registrar pedidos nuevos.',
            ], 403);
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

            foreach ($request->product_id as $index => $productId) {
                $order->products()->attach($productId, [
                    'quantity' => $request->quantity[$index]
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Pedido registrado exitosamente.',
                'data'    => $order->load('products'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar el pedido.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualiza el estado de un pedido (con lógica de inventario y fotos).
     *
     * PATCH /api/orders/{id}/status
     * Body: { status, loaded_unit_photo?, delivered_material_photo? }
     */
public function updateStatus(Request $request, $id)
    {
        \Log::info('=== updateStatus START ===', [
            'order_id' => $id,
            'all_input' => $request->all(),
            'status_from_input' => $request->input('status'),
            'all_files' => array_keys($request->allFiles()),
            'user_id' => $request->user()?->id,
        ]);

        try {
            $order = Order::with('products')->findOrFail($id);
            $user = $request->user()->load('role');
            $role = $user->role?->slug ?? 'unknown';

            $newStatus = $request->input('status') ?? $request->get('status');
            if (!$newStatus) {
                return response()->json(['message' => 'El campo status es requerido.'], 422);
            }

            if ($role === 'sales') {
                return response()->json([
                    'message' => 'Ventas no tiene permisos para modificar el flujo logístico.',
                ], 403);
            }

            // --- LÓGICA DE ALMACÉN: VALIDACIÓN DE STOCK ---
            if ($newStatus == 'In process') {
                if (!in_array($role, ['warehouse', 'admin'])) {
                    return response()->json([
                        'message' => 'Solo Almacén puede iniciar la preparación.',
                    ], 403);
                }

                foreach ($order->products as $product) {
                    $cantidadSolicitada = $product->pivot->quantity;
                    if ($product->stock < $cantidadSolicitada) {
                        return response()->json([
                            'message' => "Stock insuficiente de {$product->name}. Disponible: {$product->stock}, Requerido: {$cantidadSolicitada}.",
                        ], 422);
                    }
                }

                foreach ($order->products as $product) {
                    $product->decrement('stock', $product->pivot->quantity);
                }
            }

            // --- LÓGICA DE FOTOS: DESPACHO ---
            if ($newStatus == 'In route') {
                if (!in_array($role, ['warehouse', 'route', 'admin'])) {
                    return response()->json([
                        'message' => 'No tienes permiso para despachar la unidad.',
                    ], 403);
                }

                if (!$request->hasFile('loaded_unit_photo')) {
                    return response()->json([
                        'message' => 'Es obligatorio subir la foto de la unidad cargada.',
                    ], 422);
                }

                $path = $request->file('loaded_unit_photo')->store('orders/loaded', 'public');
                $order->loaded_unit_photo = $path;
            }

            // --- LÓGICA DE FOTOS: ENTREGA ---
            if ($newStatus == 'Delivered') {
                if (!in_array($role, ['route', 'admin'])) {
                    return response()->json([
                        'message' => 'Solo el repartidor puede confirmar la entrega.',
                    ], 403);
                }

                if (!$request->hasFile('delivered_material_photo')) {
                    return response()->json([
                        'message' => 'Es obligatorio subir la foto del material entregado.',
                    ], 422);
                }

                $path = $request->file('delivered_material_photo')->store('orders/delivered', 'public');
                $order->delivered_material_photo = $path;
            }

            $order->status = $newStatus;
            $order->save();

            return response()->json([
                'message' => '¡Estado actualizado a ' . $newStatus . ' exitosamente!',
                'data'    => $order->fresh()->load('products'),
            ]);
        } catch (\Exception $e) {
            \Log::error('OrderController::updateStatus error', [
                'order_id' => $id,
                'status' => $request->status,
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error al actualizar estado.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft-delete de un pedido.
     *
     * DELETE /api/orders/{id}
     */
    public function destroy(Request $request, $id)
    {
        $role = $request->user()->role->slug;
        if (!in_array($role, ['sales', 'admin'])) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar pedidos.',
            ], 403);
        }

        $order = Order::findOrFail($id);
        $order->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Pedido movido a la papelera.',
        ]);
    }

    /**
     * Lista pedidos en la papelera.
     *
     * GET /api/orders/trash
     */
    public function trash()
    {
        $orders = Order::with('products')
            ->where('is_deleted', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Restaura un pedido de la papelera.
     *
     * PATCH /api/orders/{id}/restore
     */
    public function restore($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['is_deleted' => false]);

        return response()->json([
            'message' => 'Pedido restaurado.',
        ]);
    }
}
