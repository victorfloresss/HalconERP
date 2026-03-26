<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreo de Pedidos - Halcón Materiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card-tracking { margin-top: 100px; border-radius: 15px; border: none; }
        .btn-halcon { background-color: #3c4b64; color: white; }
        .btn-halcon:hover { background-color: #2e394d; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-tracking shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">HALCÓN ERP</h3>
                        <p class="text-muted">Consulta el estatus de tu pedido</p>
                    </div>

                    <form action="{{ route('orders.search') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Número de Factura</label>
                            <input type="text" name="invoice_number" class="form-control" placeholder="Ej: FAC-1001" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tu Número de Cliente</label>
                            <input type="text" name="customer_number" class="form-control" placeholder="ID único" required>
                        </div>
                        <button type="submit" class="btn btn-halcon w-100 py-2">Rastrear Pedido</button>
                    </form>

                    @if(isset($order))
                        <div class="mt-5 p-3 border rounded bg-white text-center">
                            <span class="text-muted d-block mb-1">Estatus Actual:</span>
                            <h4 class="fw-bold text-primary">{{ $order->status }}</h4>
                            <hr>
                            <small class="text-muted">Actualizado el: {{ $order->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mt-4 text-center">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>