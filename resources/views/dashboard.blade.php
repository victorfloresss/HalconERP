@extends('layouts.app')

@section('content')
<style>
    .border-vino { border-left: 5px solid #8C3E53 !important; }
    .border-naranja { border-left: 5px solid #D99152 !important; }
    .border-bronce { border-left: 5px solid #8C5B3F !important; }
    .border-cafe { border-left: 5px solid #402718 !important; }

    .card-dashboard {
        border: none !important;
        border-radius: 12px !important;
        background-color: #fff !important;
    }

    .icon-box-halcon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 shadow-sm card-dashboard border-vino">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold" style="color: #402718;">{{ $stats['total'] }}</div>
                        <div style="color: #6c757d;">Total Pedidos</div>
                    </div>
                    <div class="icon-box-halcon" style="background-color: rgba(140, 62, 83, 0.1); color: #8C3E53;">
                        <i class="icon icon-lg cil-cart"></i>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 shadow-sm card-dashboard border-naranja">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold" style="color: #402718;">{{ $stats['ordered'] }}</div>
                        <div style="color: #6c757d;">Por Atender</div>
                    </div>
                    <div class="icon-box-halcon" style="background-color: rgba(217, 145, 82, 0.1); color: #D99152;">
                        <i class="icon icon-lg cil-bell"></i>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 shadow-sm card-dashboard border-bronce">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold" style="color: #402718;">{{ $stats['process'] }}</div>
                        <div style="color: #6c757d;">En Almacén</div>
                    </div>
                    <div class="icon-box-halcon" style="background-color: rgba(140, 91, 63, 0.1); color: #8C5B3F;">
                        <i class="icon icon-lg cil-truck"></i>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 shadow-sm card-dashboard border-cafe">
                <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fs-4 fw-semibold" style="color: #402718;">{{ $stats['delivered'] }}</div>
                        <div style="color: #6c757d;">Entregados</div>
                    </div>
                    <div class="icon-box-halcon" style="background-color: rgba(64, 39, 24, 0.1); color: #402718;">
                        <i class="icon icon-lg cil-check-circle"></i>
                    </div>
                </div>
                <div class="c-chart-wrapper mt-3 mx-3" style="height:40px;"></div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0" style="color: #402718;">Bienvenido al Panel de Halcón</h4>
                    <div class="small text-medium-emphasis">Gestión de distribución de materiales</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar">
                    <a href="{{ route('orders.create') }}" class="btn text-white px-4 shadow-sm" style="background-color: #8C3E53; border-radius: 12px; font-weight: bold; border: none;">
                        <i class="cil-plus"></i> Nueva Orden de Venta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection