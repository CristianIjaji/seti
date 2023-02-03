@php
    $create = isset($cotizacion->id_cotizacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $disable_form = in_array($cotizacion->id_dominio_estado, [session('id_dominio_cotizacion_aprobada')]) ? true : false;
    $editable = (
        ($edit) &&
        in_array($cotizacion->id_dominio_estado, [session('id_dominio_cotizacion_creada'), session('id_dominio_cotizacion_devuelta'),
            session('id_dominio_cotizacion_revisada'), session('id_dominio_cotizacion_rechazada'), session('id_dominio_cotizacion_cancelada')]) ||
        ($edit && Auth::user()->role == session('id_dominio_analista')) ||
        $create
    )
@endphp

@if (!$create)
    <ul class="nav nav-tabs" id="quotesTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="quotes-tab" data-bs-toggle="tab" data-bs-target="#quotes" type="button" role="tab" aria-controls="quotes" aria-selected="true">Cotización</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="track-tab-quote" data-bs-toggle="tab" data-bs-target="#track-quote" type="button" role="tab" aria-controls="track-quote" aria-selected="true">Seguimiento</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="quotesTab">
        <div class="tab-pane fade show active" id="quotes" role="tabpanel" aria-labelledby="quotes-tab">    
@endif

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
            <input type="hidden" id="id_cotizacion" value="{{ $cotizacion->id_cotizacion }}">

            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="ot_trabajo">OT</label>
                <input type="text" class="form-control text-uppercase" @if ($edit && !$disable_form) name="ot_trabajo" @endif id="ot_trabajo" value="{{ old('ot_trabajo', $cotizacion->ot_trabajo) }}" @if ($edit && !$disable_form) required @else disabled @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_cliente_cotizacion" class="required">Cliente</label>
                <input type="hidden" id="id_tercero_cliente" value="{{ $cotizacion->id_tercero_cliente }}">
                @if ($edit && !$disable_form)
                    <div class="row pe-0 pe-md-3">
                        <div class="{{ $create_client ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_tercero_cliente" id="id_cliente_cotizacion" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                                <option value="">Elegir cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option
                                        data-id_tercero_cliente="{{ (isset($cliente->tblterceroresponsable) ? $cliente->tblterceroresponsable->id_tercero : $cliente->id_tercero ) }}"
                                        value="{{ $cliente->id_tercero }}" {{ old('id_tercero_cliente', $cotizacion->id_tercero_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                        {{ $cliente->full_name }} {{ (isset($cliente->tblterceroresponsable) ? ' - '.$cliente->tblterceroresponsable->razon_social : '' ) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($create_client)
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo cliente"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_tercero_cliente"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_representante_cliente')."") }}'
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
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_estacion" class="required">Punto interés</label>
                <input type="hidden" id="id_punto_interes" value="{{ $cotizacion->id_estacion }}">
                @if ($edit && !$disable_form)
                    <div class="row pe-0 pe-md-3">
                        <div class="{{ $create_site ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_estacion" id="id_estacion" data-minimuminputlength="3" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
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
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo punto interés"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_estacion"
                                    data-action='{{ route('sites.create')}}'
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
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_dominio_tipo_trabajo" class="required">Tipo trabajo</label>
                <input type="hidden" id="id_tipo_actividad" value="{{ $cotizacion->id_dominio_tipo_trabajo }}">
                @if ($edit && !$disable_form)
                    <select class="form-control" name="id_dominio_tipo_trabajo" id="id_dominio_tipo_trabajo" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                        <option value="">Elegir tipo trabajo</option>
                        @foreach ($tipos_trabajo as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_tipo_trabajo', $cotizacion->id_dominio_tipo_trabajo) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_dominio_tipo_trabajo" value="{{ $cotizacion->tblTipoTrabajo->nombre }}" disabled>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 input-date">
                <label for="fecha_solicitud" class="required">Fecha solicitud</label>
                <input type="text" class="form-control" data-max-date="{{ date('Y-m-d') }}" @if ($edit && !$disable_form) name="fecha_solicitud" data-default-date="{{ $cotizacion->fecha_solicitud }}" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $cotizacion->fecha_solicitud) }}" @if ($edit && !$disable_form) required @else disabled @endif readonly>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_dominio_prioridad" class="required">Prioridad</label>
                @if ($edit && !$disable_form)
                    <select class="form-control" name="id_dominio_prioridad" id="id_dominio_prioridad" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                        <option value="">Elegir prioridad</option>
                        @foreach ($prioridades as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_prioridad', $cotizacion->id_dominio_prioridad) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_dominio_prioridad" value="{{ $cotizacion->tblPrioridad->nombre }}" disabled>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_dominio_iva" class="required">IVA %</label>
                @if ($edit && !$disable_form)
                    <select class="form-control text-end" name="id_dominio_iva" id="id_dominio_iva" data-dir="rtl" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                        @foreach ($impuestos as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_iva', $cotizacion->id_dominio_iva) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" id="id_dominio_iva" value="{{ $cotizacion->tblIva->nombre }}">
                    <input type="text" class="form-control text-end" value="{{ $cotizacion->tblIva->nombre }}" disabled>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_responsable" class="required">Aprobador</label>
                <input type="hidden" id="id_tercero_resposable_contratista" value="{{ $cotizacion->id_tercero_responsable }}">
                @if ($edit && !$disable_form)
                    <div class="row pe-0 pe-md-3">
                        <div class="{{ $create_client ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_tercero_responsable" id="id_responsable" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                                @forelse ($contratistas as $contratista)
                                    <option
                                        data-id_contratista="{{ (isset($contratista->tblterceroresponsable) ? $contratista->tblterceroresponsable->id_tercero : $contratista->id_tercero ) }}"
                                        value="{{ $contratista->id_tercero }}" {{ old('id_responsable', $cotizacion->id_tercero_responsable) == $contratista->id_tercero ? 'selected' : '' }}>
                                        {{ $contratista->full_name.(isset($contratista->razon_social) ? ' - '.$contratista->nombres.' '.$contratista->apellidos : '') }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
                                    </option>
                                @empty
                                    <option value="">Elegir aprobador</option>
                                @endforelse
                            </select>
                        </div>
                        @if ($create_client)
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo aprobador"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_responsable"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_coordinador')."") }}'
                                    data-toggle="tooltip"
                                    title="Crear aprobador"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $cotizacion->tblContratista->full_name.(isset($cotizacion->tblContratista->razon_social) ? ' - '.$cotizacion->tblContratista->nombres.' '.$cotizacion->tblContratista->apellidos : '') }}" disabled>
                @endif
            </div>
            @if (!$create)
                <div class="form-group text-truncate col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                    <label>Estado</label>
                    <label data-toggle="tooltip" title="{{ $cotizacion->tbldominioestado->nombre }}" class="form-control text-truncate  {{ isset($cotizacion->status[$cotizacion->id_dominio_estado]) ? $cotizacion->status[$cotizacion->id_dominio_estado] : '' }}">{{ $cotizacion->tbldominioestado->nombre }}</label>
                </div>
            @endif
            <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <label for="descripcion" class="required">Descripción orden</label>
                <textarea class="form-control" @if ($edit && !$disable_form) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit && !$disable_form) required @else disabled @endif>{{ old('nombre', $cotizacion->descripcion) }}</textarea>
            </div>
            @if (!$create)
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 my-auto text-center">
                    <span id="btn-send-quote" title="Descargar cotización" data-toggle="tooltip" class="btn btn-outline-success border px-3 btn-download-format">
                        <i class="fa-solid fa-file-excel fs-4"></i>
                    </span>

                    @can('createComment', $cotizacion)
                        @if ($edit)
                            <span
                                class="btn btn-outline-secondary border modal-form"
                                data-title="Nuevo comentario"
                                data-size="modal-md"
                                data-header-class='bg-primary bg-opacity-75 text-white'
                                data-reload="true"
                                data-action="{{ route('quotes.seguimiento', $cotizacion->id_cotizacion) }}"
                                data-toggle="tooltip"
                                title="Nuevo comentario"
                            >
                                <i class="fa-solid fa-pen-clip fs-4"></i>
                            </span>
                        @endif
                    @endcan

                    @if (isset($actividad->id_actividad))
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

                    @can('createActivity', $cotizacion)
                        <span
                            class="btn btn-outline-primary border modal-form"
                            data-title="Nueva actividad"
                            data-size="modal-fullscreen"
                            data-header-class='bg-primary bg-opacity-75 text-white'
                            data-reload="true"
                            data-action="{{ route('activities.create', "cotizacion=".$cotizacion->id_cotizacion) }}"
                            data-toggle="tooltip"
                            title="Crear actividad"
                        >
                            <i class="fa-solid fa-plus fs-4"></i>
                        </span>
                    @endcan
                </div>
            @endif

            <input type="hidden" id="valor_actividad" value="{{ $cotizacion->total_sin_iva }}">
            <div class="clearfix"><hr></div>

            @include('partials._detalle', ['edit' => $editable, 'cotizacion' => $cotizacion, 'tipo_carrito' => 'cotizacion', 'detalleCarrito' => $cotizacion->getDetalleCotizacion()])
        </div>

        @include('partials.buttons', [$create, 'edit' => $editable, 'label' => $create ? 'Crear cotización' : 'Editar cotización'])
    @if ($create || $edit)
        </form>
    @endif
@if (!$create)
        </div>
        <div class="tab-pane" id="track-quote" role="tabpanel" aria-labelledby="track-tab-quote">
            @include('partials._track', [$edit, 'model' => $estados_cotizacion, 'title' => 'Estados cotización', 'route' => 'states'])
        </div>
    </div>
@endif

<script type="application/javascript">
    datePicker();
</script>