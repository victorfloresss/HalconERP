<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreo de Pedidos - Halcón Materiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --p-halcon: #8C3E53;
            --s-halcon: #BFB195;
            --a-halcon: #D99152;
            --d-halcon: #402718;

        body { background-color: #f8f5f0; }
        
        .card-tracking { 
            margin-top: 50px; 
            border-radius: 15px; 
            border: none; 
            border-top: 5px solid var(--p-halcon);
        }

        .btn-halcon { 
            background-color: var(--p-halcon); 
            color: white; 
            border-radius: 12px;
            border: none;
        }
        
        .btn-halcon:hover { 
            background-color: var(--d-halcon); 
            color: var(--s-halcon); 
        }

        .img-evidencia { 
            max-height: 250px; 
            width: 100%; 
            object-fit: cover; 
            border-radius: 10px;
            border: 2px solid var(--s-halcon);
        }

        h3, label { color: var(--d-halcon); }
        .text-muted { color: var(--e-halcon) !important; }
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
                        <div class="mt-5 p-4 border rounded bg-white shadow-sm" style="border-color: var(--s-halcon) !important;">
                            <div class="text-center">
                                <span class="text-muted d-block mb-1 small text-uppercase">Estatus Actual:</span>
                                @php
                                    $colorClass = [
                                        'Ordered'    => 'color: var(--a-halcon);',
                                        'In process' => 'color: var(--a-halcon);',
                                        'In route'   => 'color: var(--d-halcon);',
                                        'Delivered'  => 'color: var(--p-halcon);'
                                    ][$order->status] ?? 'color: var(--p-halcon);';
                                @endphp
                                <h2 class="fw-bold" style="{{ $colorClass }}">{{ $order->status }}</h2>
                                <p class="text-muted small mb-0">Actualizado: {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <hr class="my-4" style="color: var(--s-halcon);">

                            <div class="evidence-section">
                                @if($order->status == 'In process')
                                    <div class="alert alert-light border text-center py-3" style="background-color: #fcfaf7;">
                                        <h6 class="mb-1 fw-bold" style="color: var(--d-halcon);">Preparando material</h6>
                                        <small class="text-muted">Estamos surtiendo los materiales de tu factura {{ $order->invoice_number }}.</small>
                                    </div>
                                @elseif($order->status == 'In route')
                                    <div class="alert alert-light border text-center py-3" style="background-color: #fcfaf7;">
                                        <h6 class="mb-1 fw-bold" style="color: var(--d-halcon);">En camino</h6>
                                        <small class="text-muted">Tu pedido va rumbo a tu domicilio.</small>
                                    </div>
                                @elseif($order->status == 'Delivered')
                                    <div class="text-center">
                                        <h6 class="text-muted small fw-bold text-uppercase mb-3">Evidencia de Entrega</h6>
                                        @if($order->delivered_material_photo)
                                            <img src="{{ asset('storage/' . $order->delivered_material_photo) }}" class="img-evidencia border shadow-sm">
                                            <p class="fw-bold mt-3 mb-0 small" style="color: var(--p-halcon);">✓ Material Entregado</p>
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
                        <div class="alert alert-danger mt-4 text-center shadow-sm" style="background-color: #f8d7da; border: none;">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>