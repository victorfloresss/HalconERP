@extends('layouts.app')

@section('content')
<style>
    .modal { background: rgba(0,0,0,0.5); }
    .modal-backdrop { display: none !important; } 
    #auditModal { z-index: 9999 !important; }
</style>

<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <strong><i class="cil-list"></i> Gestión de Pedidos - Halcón ERP</strong>
            @if(in_array(Auth::user()->role->slug, ['sales', 'admin']))
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary shadow-sm">+ Nuevo Pedido</a>
            @endif
        </div>
        <div class="card-body">
            {{-- Buscador --}}
            <form action="{{ route('orders.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por Factura o Cliente..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-secondary px-3">Buscar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Factura</th>
                            <th class="text-start">Cliente</th>
                            <th class="text-start">Materiales</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Evidencias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->invoice_number }}</strong></td>
                            <td class="text-start">{{ $order->customer_name }}</td>
                            <td class="text-start">
                                <span title="{{ $order->materials }}" style="cursor: help;">
                                    {{ Str::limit($order->materials, 25) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'Ordered' => 'bg-secondary',
                                        'In process' => 'bg-warning text-dark',
                                        'In route' => 'bg-info text-white',
                                        'Delivered' => 'bg-success'
                                    ][$order->status] ?? 'bg-primary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @php
                                        $urlCarga = $order->loaded_unit_photo ? asset('storage/' . $order->loaded_unit_photo) : '';
                                        $urlEntrega = $order->delivered_material_photo ? asset('storage/' . $order->delivered_material_photo) : '';
                                    @endphp

                                    @if($urlCarga || $urlEntrega)
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-dark fw-bold" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#auditModal"
                                            onclick="openAuditSimple('{{ $order->invoice_number }}', '{{ addslashes($order->customer_name) }}', '{{ $urlCarga }}', '{{ $urlEntrega }}', '{{ $order->status }}')">
                                            📸 Ver fotos
                                        </button>
                                    @else
                                        <span class="text-muted small italic">Sin fotos</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    @if($order->status == 'Ordered' && in_array(Auth::user()->role->slug, ['warehouse', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="In process">
                                            <button type="submit" class="btn btn-sm btn-warning fw-bold">SURTIR</button>
                                        </form>
                                    @endif

                                    @if($order->status == 'In process' && in_array(Auth::user()->role->slug, ['route', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="In route">
                                            <input type="file" name="loaded_unit_photo" id="photo_load_{{ $order->id }}" accept="image/*" capture="camera" style="display:none" onchange="this.form.submit()">
                                            <button type="button" class="btn btn-sm btn-info text-white fw-bold" onclick="document.getElementById('photo_load_{{ $order->id }}').click()">
                                                DESPACHAR
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status == 'In route' && in_array(Auth::user()->role->slug, ['route', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="Delivered">
                                            <input type="file" name="delivered_material_photo" id="photo_del_{{ $order->id }}" accept="image/*" capture="camera" style="display:none" onchange="this.form.submit()">
                                            <button type="button" class="btn btn-sm btn-success fw-bold" onclick="document.getElementById('photo_del_{{ $order->id }}').click()">
                                                ENTREGAR
                                            </button>
                                        </form>
                                    @endif

                                    @if(Auth::user()->role->slug === 'admin')
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('¿Borrar pedido?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE AUDITORÍA --}}
<div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">Evidencias: <span id="modalInvoice" class="text-info"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <p class="mb-1 text-muted small">CLIENTE</p>
                        <h6 id="modalCustomer" class="fw-bold"></h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="mb-1 text-muted small">ESTADO</p>
                        <span id="modalStatus" class="badge"></span>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-info small mb-3">FOTO DE CARGA</h6>
                        <div id="photoLoadedContainer"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-success small mb-3">FOTO DE ENTREGA</h6>
                        <div id="photoDeliveredContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAuditSimple(invoice, customer, urlCarga, urlEntrega, status) {
        document.getElementById('modalInvoice').innerText = invoice;
        document.getElementById('modalCustomer').innerText = customer;
        
        const statusBadge = document.getElementById('modalStatus');
        statusBadge.innerText = status;
        statusBadge.className = 'badge ' + (status == 'Delivered' ? 'bg-success' : 'bg-info');

        const loadContainer = document.getElementById('photoLoadedContainer');
        const delContainer = document.getElementById('photoDeliveredContainer');
        const imgStyle = 'class="img-fluid rounded shadow-sm border" style="max-height: 350px; width: 100%; object-fit: cover;"';

        loadContainer.innerHTML = (urlCarga && urlCarga !== '') 
            ? `<img src="${urlCarga}" ${imgStyle}>`
            : `<div class="p-4 border rounded bg-white text-muted small text-center">Sin foto de carga</div>`;

        delContainer.innerHTML = (urlEntrega && urlEntrega !== '') 
            ? `<img src="${urlEntrega}" ${imgStyle}>`
            : `<div class="p-4 border rounded bg-white text-muted small text-center">Sin foto de entrega</div>`;

        const el = document.getElementById('auditModal');
        try {
            if (typeof bootstrap !== 'undefined') {
                var myModal = new bootstrap.Modal(el);
                myModal.show();
            } else {
                el.classList.add('show');
                el.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        } catch (e) {
            el.classList.add('show');
            el.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }

    // Lógica para cerrar el modal manualmente si falla bootstrap
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-close') || e.target.closest('[data-bs-dismiss="modal"]')) {
            const el = document.getElementById('auditModal');
            el.classList.remove('show');
            el.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    });
</script>
@endsection