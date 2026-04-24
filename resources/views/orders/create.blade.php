@extends('layouts.app')

@section('content')
<style>
    .btn-halcon-vino {
        background-color: #8C3E53 !important;
        color: white !important;
        border-radius: 0 12px 12px 0;
        border: none;
        font-weight: bold;
        transition: background-color 0.2s;
    }

    .btn-halcon-vino:hover {
        background-color: #402718 !important;
    }

    .btn-halcon-full {
        background-color: #8C3E53 !important;
        color: white !important;
        border-radius: 12px;
        border: none;
        font-weight: bold;
        padding: 10px 25px;
    }
</style>

<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <strong><i class="cil-cart"></i> Registro de Nuevo Pedido - Halcón ERP</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="order-form">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Número de Factura</label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="Ej: FAC-01" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID del Cliente</label>
                        <input type="text" name="customer_number" class="form-control" placeholder="ID" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Nombre o Razón Social</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="cil-layers"></i> Selección de Materiales</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="products_table">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Producto</th>
                                    <th width="180">Precio Unit.</th>
                                    <th width="150">Cantidad</th>
                                    <th width="180">Subtotal</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="product-items">
                                <tr class="product-row">
                                    <td>
                                        <select name="product_id[]" class="form-select product-select" required onchange="calculateRowTotal(this)">
                                            <option value="" data-price="0">Seleccione un producto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->name }} (${{ number_format($product->price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control unit-price" readonly value="0.00">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity[]" class="form-control quantity-input" min="1" placeholder="0" required oninput="calculateRowTotal(this)">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control row-subtotal" readonly value="0.00">
                                        </div>
                                    </td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-dark fw-bold" id="add_product">
                             + Agregar otro producto
                        </button>
                        
                        <div class="text-end">
                            <h4 class="fw-bold" style="color: #402718;">Total a Pagar: <span id="grand_total" style="color: #8C3E53;">$0.00</span></h4>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Dirección de Entrega</label>
                        <input type="text" name="delivery_address" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Datos Fiscales</label>
                        <textarea name="fiscal_data" class="form-control" rows="1" placeholder="RFC, Régimen"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Notas o Información Extra</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-halcon-full shadow-sm">Registrar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calculateRowTotal(element) {
        const row = element.closest('tr');
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const unitPriceInput = row.querySelector('.unit-price');
        const subtotalInput = row.querySelector('.row-subtotal');

        const selectedOption = select.options[select.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseInt(quantityInput.value) || 0;

        unitPriceInput.value = price.toFixed(2);
        const subtotal = price * quantity;
        subtotalInput.value = subtotal.toFixed(2);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.row-subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('grand_total').innerText = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    document.getElementById('add_product').addEventListener('click', function() {
        const wrapper = document.getElementById('product-items');
        const newRow = document.createElement('tr');
        newRow.className = 'product-row';
        
        newRow.innerHTML = `
            <td>
                <select name="product_id[]" class="form-select product-select" required onchange="calculateRowTotal(this)">
                    <option value="" data-price="0">Seleccione un producto...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (${{ number_format($product->price, 2) }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control unit-price" readonly value="0.00">
                </div>
            </td>
            <td>
                <input type="number" name="quantity[]" class="form-control quantity-input" min="1" placeholder="0" required oninput="calculateRowTotal(this)">
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" class="form-control row-subtotal" readonly value="0.00">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row">
                    ✖
                </button>
            </td>
        `;
        wrapper.appendChild(newRow);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
            calculateGrandTotal();
        }
    });
</script>
@endsection