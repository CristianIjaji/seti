@php
    $create = !isset($orden->id_orden_compra);
    $edit = isset($edit) ? $edit : $create;
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('purchases.store') : route('purchases.update', $orden) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_almacen" class="required">Almacén</label>
            <select class="form-control" name="id_tercero_almacen" id="id_tercero_almacen" style="width: 100%">
                <option value="">Elegir almacén</option>
                @foreach ($almacenes as $almacen)
                    <option value="{{ $almacen->id_tercero }}" {{ old('id_tercero_almacen', $orden->id_tercero_almacen) == $almacen->id_tercero ? 'selected' : '' }}>
                        {{ $almacen->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_proveedor" class="required">Proveedor</label>
            <select class="form-control" name="id_tercero_proveedor" id="id_tercero_proveedor" style="width: 100%">
                <option value="">Elegir proveedor</option>
                @foreach ($proveedores as $proveedor)
                    <option 
                        data-id_tercero_asesor="{{ $proveedor->id_tercero }}"
                        data-asesor="{{ $proveedor->nombres." ".$proveedor->apellidos }}"
                        value="{{ $proveedor->id_tercero }}">
                        {{$proveedor->full_name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_asesor" class="required">Asesor</label>
            <select class="form-control" name="id_tercero_asesor" id="id_tercero_asesor" style="width: 100%">
                <option value="">Elegir asesor</option>
            </select>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label for="id_dominio_modalidad_pago" class="required">Modalidad pago</label>
            <select class="form-control" name="id_dominio_modalidad_pago" id="id_dominio_modalidad_pago" style="width: 100%">
                <option value="">Elegir modalidad</option>
                @foreach ($medios_pago_ordenes_compra as $id => $nombre)
                    <option value="{{ $id }}">
                        {{$nombre}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label for="id_dominio_iva" class="required">IVA %</label>
            @if ($edit)
                <select class="form-control text-end" name="id_dominio_iva" id="id_dominio_iva" data-dir="rtl" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @foreach ($impuestos as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_iva', $orden->id_dominio_iva) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden" id="id_dominio_iva" value="{{ $orden->tblIva->nombre }}">
                <input type="text" class="form-control text-end" value="{{ $orden->tblIva->nombre }}" disabled>
            @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 input-date">
            <label for="vencimiento" class="required">Vencimiento</label>
            <input type="text" class="form-control" data-min-date="{{ date('Y-m-d') }}" name="vencimiento" id="vencimiento" required readonly>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="descripcion" class="required">Despacharlo a: </label>
            <input type="text" class="form-control" name="descripcion" id="descripcion" value="" required>
        </div>
    </div>

    <div class="clearfix"><hr></div>

    @include('partials._detalle', ['edit' => $edit, 'tipo_carrito' => 'orden', 'editable' => true, 'detalleCarrito' => [], 'mostrarIva' => true])

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear orden' : 'Editar orden'])
@if ($create || $edit)
    </form>
@endif
</div>

<script type="application/javascript">
    $('#id_tercero_almacen').change(function() {
        let id_tercero_almacen = $(this).val();
        let tipo_carrito = "orden";

        if(id_tercero_almacen !== '') {
            let action = new String($('.tr_orden').data('action')).split('/');
            action[4] = id_tercero_almacen;
            action = action.join('/');
            $('.tr_orden').data('action', action);
        }
    });

    $('#id_tercero_proveedor').change(function() {
        $('#id_tercero_asesor').empty();

        if($(this).val() !== '') {
            let id_tercero_asesor = $(this).find(':selected').data('id_tercero_asesor');
            let asesor = $(this).find(':selected').data('asesor');

            console.log($(this), id_tercero_asesor, asesor)
            $('#id_tercero_asesor').append(`<option value="${id_tercero_asesor}">${asesor}</option>`);
        }
    });

    datePicker();
</script>