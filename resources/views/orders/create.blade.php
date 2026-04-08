@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <strong>Registro de Nuevo Pedido - Halcón</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Número de Factura</label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="FAC-01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>ID del Cliente</label>
                        <input type="text" name="customer_number" class="form-control" placeholder="00" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Nombre o Razón Social</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                </div>

                            <div class="mb-3">
                <label for="materials" class="form-label">Materiales a Adquirir</label>
                <textarea class="form-control @error('materials') is-invalid @enderror" 
                        id="materials" name="materials" rows="4" 
                        required>{{ old('materials') }}</textarea>
                @error('materials')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Datos Fiscales</label>
                        <textarea name="fiscal_data" class="form-control" rows="3" placeholder="RFC, Régimen, CP..."></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Dirección de Entrega</label>
                        <input type="text" name="delivery_address" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Notas o Información Extra</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Registrar Pedido</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection