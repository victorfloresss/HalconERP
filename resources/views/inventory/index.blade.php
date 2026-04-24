@extends('layouts.app')

@section('content')
<style>
    :root {
        --p-halcon: #8C3E53;
        --s-halcon: #BFB195;
        --a-halcon: #D99152;
        --d-halcon: #402718;
        --e-halcon: #8C5B3F;
    }

    .card-halcon-custom {
        border-radius: 12px;
        overflow: hidden;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
    }

    .header-dinamico {
        height: 6px;
        width: 100%;
    }

    .btn-halcon-pedir {
        background-color: var(--p-halcon) !important;
        color: white !important;
        font-weight: bold !important;
        border: none !important;
    }

    .btn-halcon-pedir:hover {
        background-color: var(--d-halcon) !important;
    }

    .bg-arena-suave {
        background-color: rgba(191, 177, 149, 0.15) !important;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold" style="color: var(--d-halcon);"><i class="cil-storage"></i> Inventario de Almacén</h3>
        <span class="badge" style="background-color: var(--d-halcon);">Stock Disponible</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-left: 5px solid var(--p-halcon) !important; background: white;">
            <div class="d-flex align-items-center text-dark">
                <i class="cil-check-circle me-2 fs-4" style="color: var(--p-halcon);"></i>
                <strong>{{ session('success') }}</strong>
            </div>
        </div>
    @endif

    <div class="row">
        @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <div class="card h-100 card-halcon-custom">
                {{-- Barra de color superior --}}
                <div class="header-dinamico" style="background-color: {{ $product->stock < 50 ? 'var(--a-halcon)' : 'var(--p-halcon)' }};"></div>
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-uppercase small fw-bold mb-1" style="color: var(--e-halcon);">{{ $product->unit }}</h6>
                            <h4 class="fw-bold mb-0" style="color: var(--d-halcon);">{{ $product->name }}</h4>
                            <p class="fw-bold mb-0 mt-1" style="color: var(--p-halcon);">
                                <i class="cil-money"></i> ${{ number_format($product->price, 2) }} <small class="text-muted">/ {{ $product->unit }}</small>
                            </p>
                        </div>
                        <div class="rounded-circle p-2" style="background-color: var(--s-halcon);">
                            <i class="cil-layers fs-4" style="color: var(--d-halcon);"></i>
                        </div>
                    </div>

                    <div class="my-4 text-center">
                        <h1 class="display-4 fw-bold" style="color: var(--d-halcon);">
                            {{ number_format($product->stock) }}
                        </h1>
                        <p class="text-muted small fw-medium">Existencias actuales</p>
                        <p class="small italic" style="color: var(--e-halcon);">Valor en almacén: ${{ number_format($product->stock * $product->price, 2) }}</p>
                    </div>

                    <hr class="my-4 opacity-25">

                    @if(in_array(auth()->user()->role->slug, ['purchasing', 'admin']))
                        <form action="{{ route('inventory.restock') }}" method="POST" class="p-3 rounded bg-arena-suave" style="border: 1px solid var(--s-halcon);">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <label class="form-label small fw-bold mb-2" style="color: var(--d-halcon);">
                                <i class="cil-cart"></i> Comprar al Proveedor:
                            </label>
                            <div class="input-group">
                                <input type="number" name="quantity" class="form-control" placeholder="Cant." min="1" required style="border-color: var(--s-halcon);">
                                <button class="btn btn-halcon-pedir" type="submit">Pedir</button>
                            </div>
                        </form>
                    @endif
                </div>
                
                @if($product->stock < 50)
                    <div class="card-footer bg-white border-0 text-center pb-3">
                        <span class="badge py-2 px-3 rounded-pill fw-bold" style="background-color: rgba(217, 145, 82, 0.1); color: var(--a-halcon); border: 1px solid var(--a-halcon);">
                            <i class="cil-warning me-1"></i> REQUIERE REABASTECIMIENTO
                        </span>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection