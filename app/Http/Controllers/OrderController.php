<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Muestra la lista de pedidos activos.
     */
    public function index()
    {
        $orders = Order::where('is_deleted', false)->orderBy('created_at', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Guarda un nuevo pedido capturando TODOS los campos del formulario.
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
            'materials'        => 'required',
            'delivery_address' => 'required',
            // Validamos estos opcionalmente para que no den error si van vacíos
            'fiscal_data'      => 'nullable',
            'notes'            => 'nullable',
        ]);

        // CAMBIO AQUÍ: Capturamos los datos reales del $request
        Order::create([
            'invoice_number'   => $request->invoice_number,
            'customer_number'  => $request->customer_number,
            'customer_name'    => $request->customer_name,
            'materials'        => $request->materials,
            'delivery_address' => $request->delivery_address,
            'fiscal_data'      => $request->fiscal_data ?? 'N/A', // Captura lo del form o pone N/A
            'notes'            => $request->notes,               // Captura las notas extra
            'user_id'          => Auth::id(),
            'status'           => 'Ordered',
            'is_deleted'       => false,
        ]);

        return redirect()->route('orders.index')->with('success', 'Pedido registrado exitosamente.');
    }

    /**
     * Actualiza el estado y gestiona las evidencias FOTOGRÁFICAS.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $role = auth()->user()->role->slug;

        if ($role === 'sales') {
            return back()->with('error', 'Ventas no tiene permisos para modificar el flujo logístico.');
        }

        if ($request->status == 'In process' && !in_array($role, ['warehouse', 'admin'])) {
            return back()->with('error', 'Solo el personal de Almacén puede iniciar la preparación.');
        }

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

        $order->status = $request->status;
        $order->save();

        return back()->with('success', '¡Estado actualizado a ' . $request->status . ' exitosamente!');
    }

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

    public function trackForm() { return view('orders.tracking'); }

    public function trackSearch(Request $request)
    {
        $order = Order::where('invoice_number', $request->invoice_number)
                      ->where('customer_number', $request->customer_number)
                      ->where('is_deleted', false)
                      ->first();

        if (!$order) {
            return back()->with('error', 'Datos incorrectos. Verifica tu factura.');
        }

        return view('orders.tracking', compact('order'));
    }
}