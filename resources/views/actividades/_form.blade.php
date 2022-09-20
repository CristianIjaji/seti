<?php
    $create = isset($activity->id_actividad) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('activities.store') : route('activities.update', $activity) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
        <div class="row">
            <input type="hidden" id="id_actividad" value="{{ $activity->id_actividad }}">
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                <label for="ot">OT</label>
                <input type="text" class="form-control" @if ($edit) name="ot" @endif id="ot" value="{{ old('ot', $activity->ot) }}" @if (!$edit) disabled @endif >
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="id_encargado_cliente" class="required">Cliente</label>
            @if ($edit)
                <div class="row">
                    <div class="{{ $create_client ? 'col-10' : 'col-12' }}">
                        <select class="form-control" name="id_encargado_cliente" id="id_encargado_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                            <option value="">Elegir cliente</option>
                            {{-- @foreach ($clientes as $cliente)
                                <option
                                    data-id_cliente="{{ (isset($cliente->tblterceroresponsable) ? $cliente->tblterceroresponsable->id_tercero : $cliente->id_tercero ) }}"
                                    value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $cotizacion->id_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                    {{ $cliente->full_name }} {{ (isset($cliente->tblterceroresponsable) ? ' - '.$cliente->tblterceroresponsable->razon_social : '' ) }}
                                </option>
                            @endforeach --}}
                        </select>
                    </div>
                    @if ($create_client)
                        <div class="col-2 text-end">
                            <i
                                class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                                data-title="Nuevo cliente"
                                data-size='modal-xl'
                                data-reload="false"
                                data-select="id_encargado_cliente"
                                data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_representante_cliente')."") }}'
                                data-modal="modalForm-2"
                                data-toggle="tooltip"
                                title="Crear cliente"
                            ></i>
                        </div>
                    @endif
                </div>
            @else
                <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $activity->tblencargadocliente->full_name }} {{ (isset($activity->tblencargadocliente->tblterceroresponsable) ? ' - '.$activity->tblencargadocliente->tblterceroresponsable->razon_social : '') }}" disabled>
            @endif
            </div>                
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="id_estacion" class="required">Punto interés</label>
                @if ($edit)
                    <div class="row">
                        <div class="{{ $create_site ? 'col-10' : 'col-12' }}">
                            <select class="form-control" name="id_estacion" id="id_estacion" data-minimuminputlength="3" style="width: 100%" @if ($edit) required @else disabled @endif>
                                <option value="">Elegir punto interés</option>
                                {{-- @isset($estaciones)
                                    {{-- @foreach ($estaciones as $id => $nombre)
                                        <option value="{{ $id }}" {{ old('id_estacion', $cotizacion->id_estacion) == $id ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach 
                                @endisset --}}
                            </select>
                        </div>
                        @if ($create_site)
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
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_estacion" value="{{ $cotizacion->tblEstacion->nombre }}" disabled>
                @endif
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-3">
                <label for="id_tipo_actividad" class="required">Tipo trabajo</label>
                <select name="id_tipo_actividad" id="id_tipo_actividad" class="form-control">
                    <option value="">Elegir tipo trabajo</option>
                </select>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                <label for="id_subsistema" class="required">Subsistema</label>
                <select name="id_subsistema" id="id_subsistema" class="form-control">
                    <option value="">Elegir subsistema</option>
                </select>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-date">
                <label for="fecha_solicitud" class="required">Fecha solicitud</label>
                <input type="text" class="form-control" @if ($edit) name="fecha_solicitud" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $activity->fecha_solicitud) }}" @if ($edit) required @else disabled @endif readonly>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-date">
                <label for="fecha_programacion" class="required">Fecha programación</label>
                <input type="text" class="form-control" @if ($edit) name="fecha_programacion" @endif id="fecha_programacion" value="{{ old('fecha_programacion', $activity->fecha_programacion) }}" @if ($edit) required @else disabled @endif readonly>
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                <label for="permiso_acceso">ID Permiso</label>
                <input type="text" class="form-control" @if ($edit) name="permiso_acceso" @endif id="permiso_acceso" value="{{ old('permiso_acceso', $activity->permiso_acceso) }}" @if (!$edit) disabled @endif >
            </div>
            <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                <label for="id_resposable_contratista" class="required">Responsable</label>
                @if ($edit)
                    <div class="row">
                        <div class="{{ $create_client ? 'col-10' : 'col-12' }}">
                            <select class="form-control" name="id_responsable_cliente" id="id_resposable_contratista" style="width: 100%" @if ($edit) required @else disabled @endif>
                                {{-- @forelse ($contratistas as $contratista)
                                    <option
                                        data-id_contratista="{{ (isset($contratista->tblterceroresponsable) ? $contratista->tblterceroresponsable->id_tercero : $contratista->id_tercero ) }}"
                                        value="{{ $contratista->id_tercero }}" {{ old('id_resposable_contratista', $cotizacion->id_responsable_cliente) == $contratista->id_tercero ? 'selected' : '' }}>
                                        {{ $contratista->full_name }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
                                    </option>
                                @empty
                                    <option value="">Elegir Responsable</option>
                                @endforelse --}}
                            </select>
                        </div>
                        @if ($create_client)
                            <div class="col-2 text-end">
                                <i
                                    class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                                    data-title="Nuevo aprobador"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_resposable_contratista"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_coordinador')."") }}'
                                    data-modal="modalForm-2"
                                    data-toggle="tooltip"
                                    title="Crear aprobador"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $cotizacion->tblContratista->full_name }} {{ (isset($cotizacion->tblContratista->tblterceroresponsable) ? ' - '.$cotizacion->tblContratista->tblterceroresponsable->razon_social : '') }}" disabled>
                @endif
            </div>
            <br>
            <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                <label for="descripcion" class="required">Descripción actividad</label>
                <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $activity->descripcion) }}</textarea>
            </div>
            <br>
            <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                <label for="observaciones" class="required">Observaciones</label>
                <textarea class="form-control" @if ($edit) name="observaciones" @endif id="observaciones" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $activity->observaciones) }}</textarea>
            </div>
            

        </div>
{{-- 
            <div class="row">
                <input type="hidden" id="id_actividad" value="{{ $activity->id_actividad }}">

                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                    <label for="ot">OT</label>
                    <input type="text" class="form-control" @if ($edit) name="ot" @endif id="ot" value="{{ old('ot', $activity->ot) }}" @if ($edit) required @else disabled @endif>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="id_cliente_cotizacion" class="required">Cliente</label>
                    @if ($edit)
                        <div class="row">
                            <div class="{{ $create_client ? 'col-10' : 'col-12' }}">
                                <select class="form-control" name="id_cliente" id="id_cliente_cotizacion" style="width: 100%" @if ($edit) required @else disabled @endif>
                                    <option value="">Elegir cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option
                                            data-id_cliente="{{ (isset($cliente->tblterceroresponsable) ? $cliente->tblterceroresponsable->id_tercero : $cliente->id_tercero ) }}"
                                            value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $cotizacion->id_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                            {{ $cliente->full_name }} {{ (isset($cliente->tblterceroresponsable) ? ' - '.$cliente->tblterceroresponsable->razon_social : '' ) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($create_client)
                                <div class="col-2 text-end">
                                    <i
                                        class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                                        data-title="Nuevo cliente"
                                        data-size='modal-xl'
                                        data-reload="false"
                                        data-select="id_cliente"
                                        data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_representante_cliente')."") }}'
                                        data-modal="modalForm-2"
                                        data-toggle="tooltip"
                                        title="Crear cliente"
                                    ></i>
                                </div>
                            @endif
                        </div>
                    @else
                        <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $cotizacion->tblCliente->full_name }} {{ (isset($cotizacion->tblCliente->tblterceroresponsable) ? ' - '.$cotizacion->tblCliente->tblterceroresponsable->razon_social : '') }}" disabled>
                    @endif
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="id_estacion" class="required">Punto interés</label>
                    @if ($edit)
                        <div class="row">
                            <div class="{{ $create_site ? 'col-10' : 'col-12' }}">
                                <select class="form-control" name="id_estacion" id="id_estacion" data-minimuminputlength="3" style="width: 100%" @if ($edit) required @else disabled @endif>
                                    <option value="">Elegir punto interés</option>
                                    @isset($estaciones)
                                        @foreach ($estaciones as $id => $nombre)
                                            <option value="{{ $id }}" {{ old('id_estacion', $cotizacion->id_estacion) == $id ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            @if ($create_site)
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
                            @endif
                        </div>
                    @else
                        <input type="text" class="form-control" id="id_estacion" value="{{ $cotizacion->tblEstacion->nombre }}" disabled>
                    @endif
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                    <label for="id_tipo_trabajo" class="required">Tipo trabajo</label>
                    @if ($edit)
                        <select class="form-control" name="id_tipo_trabajo" id="id_tipo_trabajo" style="width: 100%" @if ($edit) required @else disabled @endif>
                            <option value="">Elegir tipo trabajo</option>
                            @foreach ($tipos_trabajo as $id => $nombre)
                                <option value="{{ $id }}" {{ old('id_tipo_trabajo', $cotizacion->id_tipo_trabajo) == $id ? 'selected' : '' }}>
                                    {{$nombre}}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" id="id_tipo_trabajo" value="{{ $cotizacion->tblTipoTrabajo->nombre }}" disabled>
                    @endif
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
                        <input type="text" class="form-control" id="id_prioridad" value="{{ $cotizacion->tblPrioridad->nombre }}" disabled>
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
                        <input type="hidden" id="iva" value="{{ $cotizacion->tblIva->nombre }}">
                        <input type="text" class="form-control" value="{{ $cotizacion->tblIva->nombre }}" disabled>
                    @endif
                </div>
                
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                    <label for="descripcion" class="required">Descripción orden</label>
                    <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $cotizacion->descripcion) }}</textarea>
                </div>

                <div class="clearfix"><hr></div>

                <div class="table-responsive">
                    <table id="table_items" class="table table-sm table-bordered align-middle">
                        <thead class="col-12">
                            <th class="col-1 text-center">Ítem</th>
                            <th class="col-4 text-center">Descripción</th>
                            <th class="col-1 text-center">Un.</th>
                            <th class="col-1 text-center">Cant.</th>
                            <th class="col-2 text-center">VR UNIT</th>
                            <th class="col-2 text-center">VR TOTAL</th>
                            <th id="th-delete" class="col-1 text-center">Eliminar</th>
                        </thead>
                        <tbody>
                            <tr id="tr_{{ session('id_dominio_materiales') }}">
                                <td colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
                                        data-toggle="tooltip"
                                        title="Agregar ítem"
                                        data-title="Buscar ítems suministro materiales"
                                        data-size='modal-xl'
                                        data-header-class='bg-gray text-white'
                                        data-action='{{ route('priceList.search', ['type' => session('id_dominio_materiales'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                                        data-modal="modalForm-2"
                                        data-toggle="tooltip"
                                        title="Crear"
                                    >
                                        <label>SUMINISTRO DE MATERIALES</label>
                                    </span>
                                    <div class="text-end">
                                        <label id="lbl_{{ session('id_dominio_materiales') }}" class="lbl_total_material">$ 0.00</label>
                                        <span
                                            class="btn"
                                            data-bs-toggle="collapse"
                                            data-bs-target=".item_{{ session('id_dominio_materiales') }}"
                                            >
                                            <i id="caret_{{ session('id_dominio_materiales') }}" class="show-more fa-solid fa-caret-down"></i>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr id="tr_{{ session('id_dominio_mano_obra') }}">
                                <td colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
                                        data-toggle="tooltip"
                                        title="Agregar ítem"
                                        data-title="Buscar ítems mano obra"
                                        data-size='modal-xl'
                                        data-header-class='bg-gray text-white'
                                        data-action='{{ route('priceList.search', ['type' => session('id_dominio_mano_obra'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                                        data-modal="modalForm-2"
                                        data-toggle="tooltip"
                                        title="Crear"
                                    >
                                        <label>MANO DE OBRA</label>
                                    </span>
                                    <div class="text-end">
                                        <label id="lbl_{{ session('id_dominio_mano_obra') }}" class="lbl_total_mano_obra">$ 0.00</label>
                                        <span
                                            class="btn"
                                            data-bs-toggle="collapse"
                                            data-bs-target=".item_{{ session('id_dominio_mano_obra') }}"
                                            >
                                            <i id="caret_{{ session('id_dominio_mano_obra') }}" class="show-more fa-solid fa-caret-down"></i>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr id="tr_{{ session('id_dominio_transporte') }}">
                                <td colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
                                        data-toggle="tooltip"
                                        title="Agregar ítem"
                                        data-title="Buscar ítems transporte y peajes"
                                        data-size='modal-xl'
                                        data-header-class='bg-gray text-white'
                                        data-action='{{ route('priceList.search', ['type' => session('id_dominio_transporte'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                                        data-modal="modalForm-2"
                                        data-toggle="tooltip"
                                        title="Crear"
                                    >
                                        <label>TRANSPORTE Y PEAJES</label>
                                    </span>
                                    <div class="text-end">
                                        <label id="lbl_{{ session('id_dominio_transporte') }}" class="lbl_total_transporte">$ 0.00</label>
                                        <span
                                            class="btn"
                                            data-bs-toggle="collapse"
                                            data-bs-target=".item_{{ session('id_dominio_transporte') }}"
                                            >
                                            <i id="caret_{{ session('id_dominio_transporte') }}" class="show-more fa-solid fa-caret-down"></i>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-6 co-lg-6">
                    @if ($edit)
                        @can('aproveQuote', $cotizacion)
                            <button id="btn-aprove-quote" class="btn bg-success bg-gradient text-white btn-quote">
                                <i class="fa-solid fa-thumbs-up"></i> Aprobar cotización
                            </button>
                        @endcan

                        @can('rejectQuote', $cotizacion)
                            <button id="btn-deny-quote" class="btn bg-danger bg-gradient text-white btn-quote">
                                <i class="fa-solid fa-thumbs-down"></i> Devolver cotización
                            </button>
                        @endcan

                        @can('sendQuote', $cotizacion)
                            <button id="btn-send-quote" class="btn bg-info bg-gradient text-white btn-quote">
                                <i class="fa-solid fa-download"></i> Enviar cotización
                            </button>
                        @endcan
                    @endif
                </div>

                <div class="form-group col-12 col-sm-6 col-md-6 co-lg-6">
                    <div class="row fs-5">
                        <label class="col-6 text-end">Total sin IVA:</label>
                        <label id="lbl_total_sin_iva" class="col-6 text-end"></label>
                        <label class="col-6 text-end">Total IVA:</label>
                        <label id="lbl_total_iva" class="col-6 text-end"></label>
                        <label class="col-6 text-end">Total con IVA:</label>
                        <label id="lbl_total_con_iva" class="col-6 text-end"></label>
                    </div>
                </div>
            </div>
--}}
            @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear actividad' : 'Editar actividad', 'modal' => 'modalForm'])
  {{-- 
            @if (!$create)
        </div>
        <div class="tab-pane" id="track" role="tabpanel" aria-labelledby="track-tab">
            @include('cotizaciones._track')
        </div>
    @endif
        
    </div>
--}}
<script type="application/javascript">
    datePicker();
</script>