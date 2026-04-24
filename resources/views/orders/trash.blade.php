@extends('layouts.app')

@section('content')
<style>
    .card-deleted-halcon {
        border: none;
    }

    .header-deleted-halcon {
        background-color: #D99152 !important;
        color: white !important;
    }

    .table-halcon thead {
        background-color: #402718 !important;
        color: #BFB195 !important;
    }

    .btn-halcon-vino-sm {
        background-color: #8C3E53 !important;
        color: white !important;
        border-radius: 12px !important;
        border: none !important;
    }

    .btn-halcon-vino-sm:hover {
        background-color: #402718 !important;
    }
</style>

<div class="container-fluid">
    <div class="card card-deleted-halcon shadow-sm">
        <div class="card-header header-deleted-halcon">
            <strong>Pedidos Eliminados</strong>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-halcon">
                    <tr>
                        <th>Factura</th>
                        <th>Cliente</th>
                        <th>Fecha Eliminación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td style="color: #8C3E53; font-weight: bold;">{{ $order->invoice_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y') }}</td>
                        <td>
                            <form action="{{ route('orders.restore', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-halcon-vino-sm">Restaurar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Volver a Pedidos</a>
        </div>
    </div>
</div>
@endsection