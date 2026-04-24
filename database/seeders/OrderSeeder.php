<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos al usuario de ventas (Emiliano)
        $vendedor = User::where('email', 'ventas@halcon.com')->first();
        
        // 2. Obtenemos los productos para tener sus IDs
        $cemento = Product::where('name', 'Bulto de cemento')->first();
        $varilla = Product::where('name', 'Varilla')->first();
        $block   = Product::where('name', 'Block de concreto')->first();
        $arena   = Product::where('name', 'Bulto de arena')->first();

        // 3. Definimos las órdenes base (sin la columna materials)
        $orders = [
            [
                'invoice_number'   => 'FAC-001',
                'customer_number'  => 'CUST-01',
                'customer_name'    => 'Constructora Alfa',
                'delivery_address' => 'Av. Tulum 123, Cancún',
                'status'           => 'Ordered',
                'items'            => [
                    ['id' => $cemento->id, 'qty' => 500],
                    ['id' => $varilla->id, 'qty' => 300],
                ]
            ],
            [
                'invoice_number'   => 'FAC-002',
                'customer_number'  => 'CUST-02',
                'customer_name'    => 'Profe Mau',
                'delivery_address' => 'Calle Pez Vela, Puerto Morelos',
                'status'           => 'Ordered',
                'items'            => [
                    ['id' => $block->id, 'qty' => 400],
                ]
            ],
            [
                'invoice_number'   => 'FAC-003',
                'customer_number'  => 'CUST-03',
                'customer_name'    => 'Motel Paraíso',
                'delivery_address' => 'Zona Hotelera Km 12',
                'status'           => 'Ordered',
                'items'            => [
                    ['id' => $arena->id, 'qty' => 200],
                    ['id' => $cemento->id, 'qty' => 600],
                ]
            ],
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = Order::create(array_merge($orderData, [
                'user_id'    => $vendedor->id ?? 1,
                'is_deleted' => false,
                'fiscal_data'=> 'N/A',
                'notes'      => 'Pedido de prueba'
            ]));

            foreach ($items as $item) {
                $order->products()->attach($item['id'], ['quantity' => $item['qty']]);
            }
        }
    }
}