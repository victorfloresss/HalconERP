<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\EvidencePhoto;

class Order extends Model
{

    protected $fillable = [
        'invoice_number',    // Folio consecutivo
        'customer_number',   // ID único del cliente
        'customer_name',     // Nombre o razón social
        'materials',         // Lista de materiales
        'fiscal_data',       // Datos para factura física
        'delivery_address',  // Dirección de entrega 
        'notes',             // Notas extra
        'status',            // Estado: Ordered, In process, etc.
        'is_deleted',        // Control de borrado lógico
        'user_id'            // Vendedor que creó el pedido
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evidences()
    {
        return $this->hasMany(EvidencePhoto::class);
    }
}