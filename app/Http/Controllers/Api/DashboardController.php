<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Retorna las estadísticas del dashboard.
     *
     * GET /api/dashboard
     * Response: { stats: { total, ordered, process, delivered } }
     */
    public function index()
    {
        $stats = [
            'total'     => Order::where('is_deleted', false)->count(),
            'ordered'   => Order::where('status', 'Ordered')->where('is_deleted', false)->count(),
            'process'   => Order::where('status', 'In process')->where('is_deleted', false)->count(),
            'in_route'  => Order::where('status', 'In route')->where('is_deleted', false)->count(),
            'delivered' => Order::where('status', 'Delivered')->where('is_deleted', false)->count(),
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
