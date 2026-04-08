<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreo de Pedidos - Halcón Materiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card-tracking { margin-top: 50px; border-radius: 15px; border: none; }
        .btn-halcon { background-color: #3c4b64; color: white; }
        .btn-halcon:hover { background-color: #2e394d; color: white; }
        .img-evidencia { 
            max-height: 250px; 
            width: 100%; 
            object-fit: cover; 
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-tracking shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">Materiales Halcón</h3>
                        <p class="text-muted">Consulta el estatus de tu pedido</p>
                    </div>

                    <form action="{{ route('orders.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Número de Factura</label>
                            <input type="text" name="invoice_number" class="form-control" required value="{{ old('invoice_number', isset($order) ? $order->invoice_number : '') }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tu Número de Cliente</label>
                            <input type="text" name="customer_number" class="form-control" required value="{{ old('customer_number', isset($order) ? $order->customer_number : '') }}">
                        </div>
                        <button type="submit" class="btn btn-halcon w-100 py-2 fw-bold">Rastrear Pedido</button>
                    </form>

                    @if(isset($order))
                        <div class="mt-5 p-4 border rounded bg-white shadow-sm">
                            <div class="text-center">
                                <span class="text-muted d-block mb-1 small text-uppercase">Estatus Actual:</span>
                                @php
                                    // Azul para todo, verde solo para entregado
                                    $color = [
                                        'Ordered'    => 'text-primary',
                                        'In process' => 'text-primary',
                                        'In route'   => 'text-primary',
                                        'Delivered'  => 'text-success'
                                    ][$order->status] ?? 'text-primary';
                                @endphp
                                <h2 class="fw-bold {{ $color }}">{{ $order->status }}</h2>
                                <p class="text-muted small mb-0">Actualizado: {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <hr class="my-4">

                            {{-- Lógica de Evidencias según la etapa --}}
                            <div class="evidence-section">
                                @if($order->status == 'In process')
                                    <div class="alert alert-light border text-center py-3">
                                        <h6 class="mb-1 fw-bold">Preparando material</h6>
                                        <small class="text-muted">Estamos surtiendo los materiales de tu factura {{ $order->invoice_number }}.</small>
                                    </div>
                                @elseif($order->status == 'In route')
                                    <div class="alert alert-light border text-center py-3">
                                        <h6 class="mb-1 fw-bold">En camino</h6>
                                        <small class="text-muted">Tu pedido va rumbo a tu domicilio.</small>
                                    </div>
                                @elseif($order->status == 'Delivered')
                                    <div class="text-center">
                                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Evidencia de Entrega</h6>
                                        @if($order->delivered_material_photo)
                                            <img src="{{ asset('storage/' . $order->delivered_material_photo) }}" class="img-evidencia border shadow-sm">
                                            <p class="text-success fw-bold mt-3 mb-0 small">✓ Material Entregado</p>
                                        @else
                                            <div class="p-4 bg-light rounded text-muted small">
                                                Confirmado sin foto de evidencia.
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mt-4 text-center shadow-sm">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="/" class="text-muted text-decoration-none small">← Volver al portal</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>