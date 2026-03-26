@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-primary">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $stats['total'] }}</div>
                        <div>Total Pedidos</div>
                    </div>
                    <i class="icon icon-lg cil-cart"></i>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-info">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $stats['ordered'] }}</div>
                        <div>Por Atender</div>
                    </div>
                    <i class="icon icon-lg cil-bell"></i>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-warning">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $stats['process'] }}</div>
                        <div>En Almacén</div>
                    </div>
                    <i class="icon icon-lg cil-truck"></i>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-success">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold">{{ $stats['delivered'] }}</div>
                        <div>Entregados</div>
                    </div>
                    <i class="icon icon-lg cil-check-circle"></i>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">Bienvenido al Panel de Halcón</h4>
                    <div class="small text-medium-emphasis">Gestión de distribución de materiales</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar">
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="cil-plus"></i> Nueva Orden de Venta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection