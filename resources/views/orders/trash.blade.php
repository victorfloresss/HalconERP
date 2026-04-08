@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <strong>Pedidos Eliminados</strong>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
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
                        <td>{{ $order->invoice_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->updated_at->format('d/m/Y') }}</td>
                        <td>
                            <form action="{{ route('orders.restore', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success">Restaurar</button>
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