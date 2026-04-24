@extends('layouts.app')

@section('content')
<style>
    :root {
        --p-halcon: #8C3E53; /* Vinotinto */
        --s-halcon: #BFB195; /* Arena */
        --a-halcon: #D99152; /* Naranja */
        --d-halcon: #402718; /* Café */
    }

    .modal { background: rgba(0,0,0,0.5); }
    .modal-backdrop { display: none !important; } 
    #auditModal, #orderDetailsModal { z-index: 9999 !important; }
    
    .btn-link-halcon { color: var(--p-halcon) !important; font-weight: bold; text-decoration: none; }
    .btn-link-halcon:hover { color: var(--d-halcon) !important; text-decoration: underline !important; }

    .table-halcon thead { background-color: var(--d-halcon) !important; color: var(--s-halcon) !important; }

    .btn-halcon-vino { background-color: var(--p-halcon) !important; color: white !important; border-radius: 12px !important; border: none !important; }
    .btn-halcon-vino:hover { background-color: var(--d-halcon) !important; }

    .badge-material { font-size: 0.85rem; padding: 0.5rem; }
</style>

<div class="container-fluid">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <strong style="color: var(--d-halcon); font-size: 1.1rem;"><i class="cil-list"></i> Gestión de Pedidos - Halcón ERP</strong>
            @if(in_array(Auth::user()->role->slug, ['sales', 'admin']))
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-halcon-vino shadow-sm px-3">+ Nuevo Pedido</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-halcon">
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
                            <td>
                                @php $jsonOrder = json_encode($order->load('products')); @endphp
                                <button type="button" class="btn btn-link-halcon p-0" onclick='viewOrderDetails({!! $jsonOrder !!})'>
                                    {{ $order->invoice_number }}
                                </button>
                            </td>
                            <td class="text-start" style="color: var(--d-halcon);">{{ $order->customer_name }}</td>
                            <td class="text-start">
                                @foreach($order->products as $product)
                                    <small class="d-block text-truncate" style="max-width: 180px; color: #8C5B3F;">
                                        • {{ $product->pivot->quantity }} x {{ $product->name }}
                                    </small>
                                @endforeach
                            </td>
                            <td>
                                @php
                                    $badgeStyle = [
                                        'Ordered' => 'background-color: var(--s-halcon); color: var(--d-halcon);',
                                        'In process' => 'background-color: var(--a-halcon); color: white;',
                                        'In route' => 'background-color: #8C5B3F; color: white;',
                                        'Delivered' => 'background-color: var(--p-halcon); color: white;'
                                    ][$order->status] ?? 'bg-primary';
                                @endphp
                                <span class="badge shadow-sm" style="{{ $badgeStyle }} padding: 0.5rem; border-radius: 5px;">
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
                                        <button type="button" class="btn btn-sm fw-bold" style="border: 1px solid var(--s-halcon); color: var(--d-halcon); background: #fcfaf7;"
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
                                    {{-- 1. SURTIR --}}
                                    @if($order->status == 'Ordered' && in_array(Auth::user()->role->slug, ['warehouse', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="In process">
                                            <button type="submit" class="btn btn-sm fw-bold text-white" style="background-color: var(--a-halcon); border:none;">SURTIR</button>
                                        </form>
                                    @endif

                                    {{-- 2. DESPACHAR --}}
                                    @if($order->status == 'In process' && in_array(Auth::user()->role->slug, ['warehouse', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="In route">
                                            <input type="file" name="loaded_unit_photo" id="photo_load_{{ $order->id }}" accept="image/*" capture="camera" style="display:none" onchange="this.form.submit()">
                                            <button type="button" class="btn btn-sm fw-bold text-white" style="background-color: #8C5B3F; border:none;" onclick="document.getElementById('photo_load_{{ $order->id }}').click()">
                                                DESPACHAR
                                            </button>
                                        </form>
                                    @endif

                                    {{-- 3. ENTREGAR --}}
                                    @if($order->status == 'In route' && in_array(Auth::user()->role->slug, ['route', 'admin']))
                                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="Delivered">
                                            <input type="file" name="delivered_material_photo" id="photo_del_{{ $order->id }}" accept="image/*" capture="camera" style="display:none" onchange="this.form.submit()">
                                            <button type="button" class="btn btn-sm fw-bold text-white" style="background-color: var(--p-halcon); border:none;" onclick="document.getElementById('photo_del_{{ $order->id }}').click()">
                                                ENTREGAR
                                            </button>
                                        </form>
                                    @endif

                                    {{-- 4. ELIMINAR --}}
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

<div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">Evidencias: <span id="modalInvoice" class="text-info"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <div class="row mb-4">
                    <div class="col-md-6 border-end">
                        <p class="mb-1 text-muted small text-uppercase">Cliente</p>
                        <h6 id="modalCustomer" class="fw-bold"></h6>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small text-uppercase">Estado</p>
                        <span id="modalStatus" class="badge"></span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold small mb-3" style="color: var(--a-halcon);">FOTO DE CARGA</h6>
                        <div id="photoLoadedContainer"></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold small mb-3" style="color: var(--p-halcon);">FOTO DE ENTREGA</h6>
                        <div id="photoDeliveredContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: DETALLES COMPLETOS --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header text-white border-0" style="background-color: var(--p-halcon);">
                <h5 class="modal-title fw-bold">Información Completa: <span id="detInvoice"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white text-start">
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Cliente y Fiscal</h6>
                        <div class="p-3 bg-light rounded shadow-sm">
                            <p class="mb-1 text-secondary small">Nombre:</p>
                            <p class="fw-bold mb-3" id="detCustomer" style="color: var(--d-halcon);"></p>
                            <p class="mb-1 text-secondary small">Datos Fiscales:</p>
                            <p class="small text-dark" id="detFiscal"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Logística</h6>
                        <div class="p-3 bg-light rounded shadow-sm">
                            <p class="mb-1 text-secondary small">Dirección de Entrega:</p>
                            <p class="fw-bold mb-3" id="detAddress" style="color: var(--d-halcon);"></p>
                            <p class="mb-1 text-secondary small">ID Cliente / Referencia:</p>
                            <p class="small text-dark" id="detClientID"></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Contenido y Notas</h6>
                        <div class="p-3 border rounded shadow-sm">
                            <p class="mb-1 text-secondary small font-weight-bold">Materiales (Inventario):</p>
                            <div id="detMaterialsList" class="mb-3 d-flex flex-wrap"></div>
                            <p class="mb-1 text-secondary small font-weight-bold">Notas Extra:</p>
                            <p class="text-muted italic mb-0" id="detNotes"></p>
                        </div>
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
        
        statusBadge.style = status == 'Delivered' 
            ? 'background-color: #8C3E53; color: white; padding: 0.5rem; border-radius: 5px;' 
            : 'background-color: #D99152; color: white; padding: 0.5rem; border-radius: 5px;';

        const imgStyle = 'class="img-fluid rounded shadow-sm border" style="max-height: 300px; width: 100%; object-fit: cover;"';
        document.getElementById('photoLoadedContainer').innerHTML = urlCarga ? `<img src="${urlCarga}" ${imgStyle}>` : '<div class="p-4 border rounded bg-white small text-muted">Sin foto</div>';
        document.getElementById('photoDeliveredContainer').innerHTML = urlEntrega ? `<img src="${urlEntrega}" ${imgStyle}>` : '<div class="p-4 border rounded bg-white small text-muted">Sin foto</div>';

        abrirModalSeguro('auditModal');
    }

    function viewOrderDetails(order) {
        document.getElementById('detInvoice').innerText = order.invoice_number;
        document.getElementById('detCustomer').innerText = order.customer_name;
        document.getElementById('detFiscal').innerText = order.fiscal_data || 'N/A';
        document.getElementById('detAddress').innerText = order.delivery_address;
        document.getElementById('detClientID').innerText = order.customer_number;
        document.getElementById('detNotes').innerText = order.notes || 'Sin notas';

        const listContainer = document.getElementById('detMaterialsList');
        listContainer.innerHTML = ''; 

        if(order.products && order.products.length > 0) {
            order.products.forEach(p => {
                const badge = document.createElement('div');
                badge.className = 'badge bg-white text-dark border p-2 me-2 mb-2 shadow-sm';
                badge.innerHTML = `<i class="cil-check text-success"></i> ${p.pivot.quantity} x ${p.name}`;
                listContainer.appendChild(badge);
            });
        }
        abrirModalSeguro('orderDetailsModal');
    }

    function abrirModalSeguro(idModal) {
        const elemento = document.getElementById(idModal);
        if (!elemento) return;

        try {
            let myModal = bootstrap.Modal.getOrCreateInstance(elemento);
            myModal.show();
        } catch (e) {
            elemento.classList.add('show');
            elemento.style.display = 'block';
            document.body.classList.add('modal-open');
            if (!document.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            }
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-bs-dismiss="modal"]')) {
            const modal = e.target.closest('.modal');
            if (modal) {
                try {
                    const instance = bootstrap.Modal.getInstance(modal);
                    if (instance) instance.hide();
                } catch(err) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            }
        }
    });
</script>
@endsection