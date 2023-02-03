@php
    $create = !isset($orden->id_orden_compra);
    $edit = isset($edit) ? $edit : $create;
    $disable_form = in_array($orden->id_dominio_estado, [session('id_dominio_orden_cerrada')]) ? true : false;
    $editable = (
        $edit &&
        in_array($orden->id_dominio_estado, [session('id_dominio_orden_abierta')]) ? true : false ||
        $create
    );
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
        <input type="hidden" id="id_orden_compra" value="{{ $orden->id_orden_compra }}">

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_almacen" class="required">Almacén</label>
            @if ($create)
                <select class="form-control" name="id_tercero_almacen" id="id_tercero_almacen" style="width: 100%">
                    <option value="">Elegir almacén</option>
                    @if (!isset($tercero_almacen))
                        @foreach ($almacenes as $almacen)
                            <option value="{{ $almacen->id_tercero }}" {{ old('id_tercero_almacen', $orden->id_tercero_almacen) == $almacen->id_tercero ? 'selected' : '' }}>
                                {{ $almacen->full_name }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{ $tercero_almacen->id_tercero }}" selected>{{ $tercero_almacen->nombres.' '.$tercero_almacen->apellidos }}</option>
                    @endif
                </select>
            @else
                <input type="hidden" name="id_tercero_almacen" value="{{ $orden->id_tercero_almacen }}">
                <input type="text" class="form-control" value="{{ $orden->tblalmacen->full_name }}" disabled>
            @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_proveedor" class="required">Proveedor</label>
            @if ($edit)
                <select class="form-control" name="id_tercero_proveedor" id="id_tercero_proveedor" style="width: 100%">
                    <option value="">Elegir proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option 
                            data-id_tercero_asesor="{{ $proveedor->id_tercero }}"
                            data-asesor="{{ $proveedor->nombres." ".$proveedor->apellidos }}"
                            value="{{ $proveedor->id_tercero }}"
                            {{ old('id_tercero_proveedor', $orden->id_tercero_proveedor) == $proveedor->id_tercero ? 'selected' : '' }}
                        >
                            {{$proveedor->full_name}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="id_tercero_proveedor" value="{{ $orden->id_tercero_proveedor }}">
                <input type="text" class="form-control" value="{{ $orden->tblproveedor->full_name }}" disabled>
            @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="id_tercero_asesor" class="required">Asesor</label>
            @if ($edit)
                <select class="form-control" name="id_tercero_asesor" id="id_tercero_asesor" style="width: 100%">
                    <option value="">Elegir asesor</option>
                    @if (!$create && $edit)
                        <option value="{{ $orden->id_tercero_asesor }}" selected>{{ $orden->tblasesor->nombres.' '.$orden->tblasesor->apellidos }}</option>
                    @endif
                </select>
            @else
                <input type="hidden" name="id_tercero_asesor" value="{{ $orden->id_tercero_asesor }}">
                <input type="text" class="form-control" value="{{ $orden->tblasesor->nombres.' '.$orden->tblasesor->apellidos }}" disabled>
            @endif
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
            <label for="id_dominio_modalidad_pago" class="required">Modalidad pago</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_modalidad_pago" id="id_dominio_modalidad_pago" style="width: 100%">
                    <option value="">Elegir modalidad</option>
                    @foreach ($medios_pago_ordenes_compra as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_modalidad_pago', $orden->id_dominio_modalidad_pago) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="id_dominio_modalidad_pago" value="{{ $orden->id_dominio_modalidad_pago }}">
                <input type="text" class="form-control" value="{{ $orden->tblmodalidadpago->nombre }}" disabled>
            @endif
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
            <input type="text" class="form-control" @if ($edit) name="vencimiento" data-default-date="{{ $orden->vencimiento }}" @endif id="vencimiento" value="{{ old('vencimiento', $orden->vencimiento) }}" @if ($edit) required @else disabled @endif readonly>
        </div>

        @if (!$create)
            <div class="form-group text-truncate col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label>Estado</label>
                <label data-toggle="tooltip" title="{{ $orden->tblestado->nombre }}" class="form-control text-truncate  {{ isset($orden->status[$orden->id_dominio_estado]) ? $orden->status[$orden->id_dominio_estado] : '' }}">{{ $orden->tblestado->nombre }}</label>
            </div>
        @endif

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label for="descripcion" class="required">Despacharlo a: </label>
            <input type="text" class="form-control" name="descripcion" id="descripcion" value="{{ old('descripcion', $orden->descripcion) }}" @if ($edit) required @else disabled @endif>
        </div>

        @if (!$create)
            <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 my-auto text-center">
                <span id="btn-send-purchase" title="Descargar Orden" data-toggle="tooltip" class="btn btn-outline-success border px-3 btn-download-format">
                    <i class="fa-solid fa-file-excel fs-4"></i>
                </span>

                @can('createComment', $orden)
                    @if ($edit)
                        <span
                            class="btn btn-outline-secondary border modal-form"
                            data-title="Nuevo comentario"
                            data-size="modal-md"
                            data-header-class='bg-primary bg-opacity-75 text-white'
                            data-reload="true"
                            data-action="{{ route('purchases.seguimiento', $orden->id_orden_compra) }}"
                            data-toggle="tooltip"
                            title="Nuevo comentario"
                        >
                            <i class="fa-solid fa-pen-clip fs-4"></i>
                        </span>
                    @endif
                @endcan

                {{-- @if (isset($actividad->id_actividad))
                    @can('view', $actividad)
                        <span
                            class="btn btn-outline-info border modal-form"
                            data-title="Actividad #{{ $actividad->id_actividad }}"
                            data-size="modal-fullscreen"
                            data-header-class='bg-info text-white'
                            data-reload="false"
                            data-action="{{ route('activities.show', $actividad->id_actividad) }}"
                            data-toggle="tooltip"
                            title="Ver actividad"
                        >
                            <i class="fa-solid fa-circle-info fs-4"></i>
                        </span>
                    @endcan
                @endif

                @can('createActivity', $orden)
                    <span
                        class="btn btn-outline-primary border modal-form"
                        data-title="Nueva actividad"
                        data-size="modal-fullscreen"
                        data-header-class='bg-primary bg-opacity-75 text-white'
                        data-reload="true"
                        data-action="{{ route('activities.create', "cotizacion=".$orden->id_cotizacion) }}"
                        data-toggle="tooltip"
                        title="Crear actividad"
                    >
                        <i class="fa-solid fa-plus fs-4"></i>
                    </span>
                @endcan --}}
            </div>
        @endif
    </div>

    <div class="clearfix"><hr></div>

    @include('partials._detalle', ['edit' => $editable, 'tipo_carrito' => 'orden', 'detalleCarrito' => isset($carrito) ? $carrito : $orden->getDetalleOrden()])

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

            $('#id_tercero_asesor').append(`<option value="${id_tercero_asesor}">${asesor}</option>`);
        }
    });

    datePicker();
</script>