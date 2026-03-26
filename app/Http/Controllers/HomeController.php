<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // PASO 2: Calcular las estadísticas reales de la base de datos
        $stats = [
            'total'     => Order::where('is_deleted', false)->count(),
            'ordered'   => Order::where('status', 'Ordered')->where('is_deleted', false)->count(),
            'process'   => Order::where('status', 'In process')->where('is_deleted', false)->count(),
            'delivered' => Order::where('status', 'Delivered')->where('is_deleted', false)->count(),
        ];

        // PASO 3: Retornar la vista 'dashboard' enviando el paquete de estadísticas
        return view('dashboard', compact('stats'));
    }
}