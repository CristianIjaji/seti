<?php
    $create = isset($cotizacion->id_cotizacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('quotes.store') : route('quotes.update', $cotizacion) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="ot_trabajo">OT</label>
            <input type="text" class="form-control" @if ($edit) name="ot_trabajo" @endif id="ot_trabajo" value="{{ old('ot_trabajo', $cotizacion->ot_trabajo) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
            <label for="id_cliente" class="required">Cliente</label>
            @if ($edit)
                <div class="row">
                    <div class="col-10">
                        <select class="form-control" name="id_cliente" id="id_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                            <option value="">Elegir cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $cotizacion->id_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                    {{$cliente->nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2 text-end">
                        <i
                            class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                            data-title="Nuevo cliente"
                            data-size='modal-xl'
                            data-reload="false"
                            data-select="id_cliente"
                            data-action='{{ route('clients.create', 'tipo_tercero='.session('id_dominio_cliente').'') }}'
                            data-modal="modalForm-2"
                            data-toggle="tooltip"
                            title="Crear cliente"
                        ></i>
                    </div>
                </div>
            @else
                <input type="text" class="form-control" id="id_cliente" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
            <label for="id_estacion" class="required">Punto interés</label>
            @if ($edit)
                <div class="row">
                    <div class="col-10">
                        <select class="form-control" name="id_estacion" id="id_estacion" style="width: 100%" @if ($edit) required @else disabled @endif>
                            <option value="">Elegir punto interés</option>
                            @foreach ($estaciones as $id => $nombre)
                                <option value="{{ $id }}" {{ old('id_estacion', $cotizacion->id_estacion) == $id ? 'selected' : '' }}>
                                    {{$nombre}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2 text-end">
                        <i
                            class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                            data-title="Nuevo punto interés"
                            data-size='modal-xl'
                            data-reload="false"
                            data-select="id_estacion"
                            data-action='{{ route('sites.create')}}'
                            data-modal="modalForm-2"
                            data-toggle="tooltip"
                            title="Crear punto de interés"
                        ></i>
                    </div>
                </div>
            @else
                <input type="text" class="form-control" id="id_estacion" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="id_tipo_trabajo" class="required">Tipo trabajo</label>
            <select class="form-control" name="id_tipo_trabajo" id="id_tipo_trabajo" style="width: 100%" @if ($edit) required @else disabled @endif>
                <option value="">Elegir tipo trabajo</option>
                @foreach ($tipos_trabajo as $id => $nombre)
                    <option value="{{ $id }}" {{ old('id_tipo_trabajo', $cotizacion->id_tipo_trabajo) == $id ? 'selected' : '' }}>
                        {{$nombre}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-date">
            <label for="fecha_solicitud" class="required">Fecha solicitud</label>
            <input type="text" class="form-control" @if ($edit) name="fecha_solicitud" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $cotizacion->fecha_solicitud) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="id_prioridad" class="required">Prioridad</label>
            @if ($edit)
                <select class="form-control" name="id_prioridad" id="id_prioridad" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir prioridad</option>
                    @foreach ($prioridades as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_prioridad', $cotizacion->id_prioridad) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_prioridad" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="iva" class="required">IVA %</label>
            @if ($edit)
                <select class="form-control" name="iva" id="iva" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @foreach ($impuestos as $id => $nombre)
                        <option value="{{ $id }}" {{ old('iva', $cotizacion->iva) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="iva" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-8">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="3" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $cotizacion->descripcion) }}</textarea>
        </div>
        
        <div class="clearfix"><hr></div>

        <div class="col-12">
            <div class="table-responsive">
                <table id="table_items" class="table table-sm table-bordered align-middle">
                    <thead class="col-12">
                        <th class="col-1 text-center">Ítem</th>
                        <th class="col-4 text-center">Descripción</th>
                        <th class="col-1 text-center">Un.</th>
                        <th class="col-1 text-center">Cant.</th>
                        <th class="col-2 text-center">VR UNIT</th>
                        <th class="col-2 text-center">VR TOTAL</th>
                        <th class="col-1 text-center">Eliminar</th>
                    </thead>
                    <tbody>
                        <tr id="tr_{{ session('id_dominio_materiales') }}">
                            <td colspan="7">
                                <span
                                    class="btn w-100 bg-gray fw-bold modal-form d-flex justify-content-between text-white"
                                    data-toggle="tooltip"
                                    title="Agregar ítem"
                                    data-title="Buscar ítems suministro materiales"
                                    data-size='modal-xl'
                                    data-action='{{ route('price_list.search', session('id_dominio_materiales')) }}'
                                    data-modal="modalForm-2"
                                    data-toggle="tooltip"
                                    title="Crear"
                                >
                                <label>SUMINISTRO DE MATERIALES</label>
                                <label id="lbl_{{ session('id_dominio_materiales') }}">$ 0.00</label>
                            </span>
                            </td>
                        </tr>
                        <tr id="tr_{{ session('id_dominio_mano_obra') }}">
                            <td colspan="7">
                                <span
                                    class="btn w-100 bg-gray fw-bold modal-form d-flex justify-content-between text-white"
                                    data-toggle="tooltip"
                                    title="Agregar ítem"
                                    data-title="Buscar ítems mano obra"
                                    data-size='modal-xl'
                                    data-action='{{ route('price_list.search', session('id_dominio_mano_obra')) }}'
                                    data-modal="modalForm-2"
                                    data-toggle="tooltip"
                                    title="Crear"
                                >
                                <label>MANO DE OBRA</label>
                                <label id="lbl_{{ session('id_dominio_mano_obra') }}">$ 0.00</label>
                            </span>
                            </td>
                        </tr>
                        <tr id="tr_{{ session('id_dominio_transporte') }}">
                            <td colspan="7">
                                <span
                                    class="btn w-100 bg-gray fw-bold modal-form d-flex justify-content-between text-white"
                                    data-toggle="tooltip"
                                    title="Agregar ítem"
                                    data-title="Buscar ítem transporte y peajes"
                                    data-size='modal-xl'
                                    data-action='{{ route('price_list.search', session('id_dominio_transporte')) }}'
                                    data-modal="modalForm-2"
                                    data-toggle="tooltip"
                                    title="Crear"
                                >
                                <label>TRANSPORTE Y PEAJES</label>
                                <label id="lbl_{{ session('id_dominio_transporte') }}">$ 0.00</label>
                            </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear cotización' : 'Editar cotización', 'modal' => 'modalForm'])

<script type="application/javascript">
    datePicker();
</script>