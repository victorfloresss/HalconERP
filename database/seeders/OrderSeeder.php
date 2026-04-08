<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $vendedor = User::where('email', 'ventas@halcon.com')->first();
        
        $orders = [
            [
                'invoice_number'   => 'FAC-001',
                'customer_number'  => '01',
                'customer_name'    => 'Constructora',
                'materials'        => '50 bultos de cemento',
                'delivery_address' => 'Cancún',
                'fiscal_data'      => '90328732761',
                'notes'            => 'Contactar al Ing. Rodríguez.',
                'status'           => 'Ordered',
            ],
            [
                'invoice_number'   => 'FAC-002',
                'customer_number'  => '02',
                'customer_name'    => 'Profe Mau',
                'materials'        => '10 bultos de cal',
                'delivery_address' => 'Piscina de su casa',
                'fiscal_data'      => '2389328412',
                'notes'            => 'Cerca de la iglesia principal.',
                'status'           => 'Ordered',
            ],
            [
                'invoice_number'   => 'FAC-003',
                'customer_number'  => '03',
                'customer_name'    => 'Motel Paraíso',
                'materials'        => '2 tinacos 1100L',
                'delivery_address' => 'Zona Hotelera',
                'fiscal_data'      => '0902376242',
                'notes'            => 'Entrar por el acceso de proveedores.',
                'status'           => 'Ordered',
            ],
            [
                'invoice_number'   => 'FAC-004',
                'customer_number'  => '04',
                'customer_name'    => 'Yumber',
                'materials'        => '1 millar de block, 3m3 de arena',
                'delivery_address' => 'Puerto Morelos',
                'fiscal_data'      => '8329812093',
                'notes'            => 'Dejar el material en la banqueta.',
                'status'           => 'Ordered',
            ],
            [
                'invoice_number'   => 'FAC-005',
                'customer_number'  => '05',
                'customer_name'    => 'Little Caesars',
                'materials'        => '2 cubetas de pintura blanca',
                'delivery_address' => 'Lote 5, Residencial Cumbres',
                'fiscal_data'      => '90638925494',
                'notes'            => 'Tocar la puerta al llegar.',
                'status'           => 'Ordered',
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create(array_merge($orderData, [
                'user_id'    => $vendedor->id ?? 1,
                'is_deleted' => false,
            ]));
        }
    }
}