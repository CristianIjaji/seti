@php
    $create = isset($activity->id_actividad) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $existe_cotizacion = false;

    if(isset($quote) && isset($quote->id_cotizacion)) {
        $existe_cotizacion = true;

        if($create) {
            $activity->id_cotizacion = $quote->id_cotizacion;
            $activity->ot = $quote->ot_trabajo;
            $activity->id_tercero_encargado_cliente = $quote->id_tercero_cliente;
            $activity->id_estacion = $quote->id_estacion;
            $activity->id_tipo_actividad = $quote->id_dominio_tipo_trabajo;
            $activity->fecha_solicitud = $quote->fecha_solicitud;
            $activity->valor = $quote->valor;
            $activity->id_tercero_resposable_contratista = $quote->id_tercero_responsable;
            $activity->descripcion = $quote->descripcion;
        }
    }

    $movimiento = (isset($movimiento) ? $movimiento : null);

    $classModal = (!$movimiento ? 'primary' : ($edit ? 'warning' : 'info'));
    $classBtn = (!$movimiento ? 'danger' : ($edit ? 'warning' : 'info'));
    $route = (!$movimiento ? 'moves.create' : ($edit ? 'moves.edit' : 'moves.show'));
    $title = (!$movimiento && $edit ? 'Cargar inventario' : 'Ver inventario');
    $dataTitle = (!$movimiento && $edit ? "Cargar inventario actividad $activity->id_actividad" : "Ver inventario actividad $activity->id_actividad");
    $params = (!$movimiento
        ? "tipo_movimiento=".session('id_dominio_movimiento_salida_actividad')."&tercero=".$activity->id_tercero_resposable_contratista."&actividad=".$activity->id_actividad
        : $movimiento['id_movimiento']
    );
@endphp

<div class="alert alert-success" role="alert"></div>
<div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

@if (!$create)
    <ul class="nav nav-tabs" id="activityTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="true">Actividad</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="track-tab-activity" data-bs-toggle="tab" data-bs-target="#track-activity" type="button" role="tab" aria-controls="track-activity" aria-selected="true">Seguimiento</button>
        </li>
        @can('viewReport', $activity)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="report-tab-activity" data-bs-toggle="tab" data-bs-target="#report-activity" type="button" role="tab" aria-controls="report-activity" aria-selected="true">Reporte</button>
            </li>
        @endcan
        @can('viewLiquidate', $activity)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="liquidate-tab-activity" data-bs-toggle="tab" data-bs-target="#liquidate-activity" type="button" role="tab" aria-controls="liquidate-activity" aria-selected="true">Liquidaci??n</button>
            </li>
        @endcan
    </ul>

    <div class="tab-content pt-3" id="activityTab">
        <div class="tab-pane fade show active" id="activity" role="tabpanel" aria-labelledby="activity-tab">
@endif
    @if ($create || $edit)
        <form action="{{ $create ? route('activities.store') : route('activities.update', $activity) }}" method="POST">
            @csrf
            @if (!$create)
                @method('PATCH')
            @endif
    @endif
    
        <div class="row">
            <input type="hidden" id="id_actividad" value="{{ $activity->id_actividad }}">
            <input type="hidden" name="id_cotizacion" id="id_cotizacion_actividad" value="{{ $activity->id_cotizacion }}">
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="ot">OT</label>
                <input type="text" class="form-control text-uppercase" @if ($edit) name="ot" @endif id="ot" value="{{ old('ot', $activity->ot) }}" @if (!$edit || $existe_cotizacion) readonly @endif >
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_tercero_encargado_cliente" class="required">Cliente</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : '' }}">
                        <div class="{{ $create_client && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_tercero_encargado_cliente" id="id_tercero_encargado_cliente" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                <option value="">Elegir cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option
                                        data-id_tercero_cliente="{{ (isset($cliente->tblterceroresponsable) ? $cliente->tblterceroresponsable->id_tercero : $cliente->id_tercero ) }}"
                                        value="{{ $cliente->id_tercero }}" {{ old('id_tercero_cliente', $activity->id_tercero_encargado_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                        {{ $cliente->full_name }} {{ (isset($cliente->tblterceroresponsable) ? ' - '.$cliente->tblterceroresponsable->razon_social : '' ) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($create_client && !$existe_cotizacion)
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo cliente"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_tercero_encargado_cliente"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_representante_cliente')."") }}'
                                    data-toggle="tooltip"
                                    title="Crear cliente"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_tercero_encargado_cliente" value="{{ $activity->tblencargadocliente->full_name }} {{ (isset($activity->tblencargadocliente->tblterceroresponsable) ? ' - '.$activity->tblencargadocliente->tblterceroresponsable->razon_social : '') }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_estacion" class="required">Punto inter??s</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : ''}}">
                        <div class="{{ $create_site && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_estacion" id="id_estacion" data-minimuminputlength="3" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                <option value="">Elegir punto inter??s</option>
                                @isset($estaciones)
                                    @foreach ($estaciones as $id => $nombre)
                                        <option value="{{ $id }}" {{ old('id_estacion', $activity->id_estacion) == $id ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        @if ($create_site && !$existe_cotizacion)
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo punto inter??s"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_estacion"
                                    data-action='{{ route('sites.create')}}'
                                    data-toggle="tooltip"
                                    title="Crear punto de inter??s"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_estacion" value="{{ $activity->tblEstacion->nombre }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_tipo_actividad" class="required">Tipo trabajo</label>
                @if ($edit)
                    <select name="id_tipo_actividad" id="id_tipo_actividad" class="form-control" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                        <option value="">Elegir tipo trabajo</option>
                        @foreach ($tipos_trabajo as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_tipo_actividad', $activity->id_tipo_actividad) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_tipo_actividad" value="{{ $activity->tbltipoactividad->nombre }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_dominio_subsistema" class="required">Subsistema</label>
                @if ($edit)
                    <select name="id_dominio_subsistema" id="id_dominio_subsistema" class="form-control" style="width: 100%" @if ($edit) required @else readonly @endif>
                        <option value="">Elegir subsistema</option>
                        @foreach ($subsistemas as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_subsistema', $activity->id_dominio_subsistema) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_dominio_subsistema" value="{{ $activity->tblsubsistema->nombre }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 {{ $create ? 'input-date' : '' }}">
                <label for="fecha_solicitud" class="required">Fecha solicitud</label>
                <input type="text" class="form-control" @if ($edit) name="fecha_solicitud" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $activity->fecha_solicitud) }}" readonly>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 {{ $create ? 'input-date' : '' }}">
                <label for="{{ isset($activity->fecha_reprogramacion) ? 'fecha_reprogramacion' : 'fecha_programacion' }}" class="required">
                    {{ isset($activity->fecha_reprogramacion) ? 'Fecha reprogramaci??n' : 'Fecha programaci??n' }}
                </label>
                <input
                    type="text"
                    class="form-control"
                    data-min-date="{{ date('Y-m-d') }}"
                    @if ($edit)
                        name="{{ isset($activity->fecha_reprogramacion) ? 'fecha_reprogramacion' : 'fecha_programacion' }}"
                        id="{{ isset($activity->fecha_reprogramacion) ? 'fecha_reprogramacion' : 'fecha_programacion' }}"
                    @endif
                    value="{{ old('fecha_programacion', $activity->fecha_programacion) }}"
                    readonly
                >
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="permiso_acceso">ID Permiso</label>
                <input type="text" class="form-control" @if ($edit) name="permiso_acceso" @endif id="permiso_acceso" value="{{ old('permiso_acceso', $activity->permiso_acceso) }}" @if (!$edit) readonly @endif >
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_dominio_estado" class="required">Estado</label>
                @if ($edit)
                    @if ($create)
                        <select name="id_dominio_estado" id="id_dominio_estado" class="form-control {{ isset($activity->status[$activity->id_dominio_estado]) ? $activity->status[$activity->id_dominio_estado] : '' }}" style="width: 100%">
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_dominio }}" {{ old('id_dominio_estado', $activity->id_dominio_estado) == $estado->id_dominio ? 'selected' : '' }}>
                                    {{ $estado->nombre}}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <label class="form-control {{ isset($activity->status[$activity->id_dominio_estado]) ? $activity->status[$activity->id_dominio_estado] : '' }}">{{ $activity->tblestadoactividad->nombre }}</label>
                        <input type="hidden" name="id_dominio_estado" id="id_dominio_estado" value="{{ $activity->id_dominio_estado }}">
                    @endif
                @else
                <label class="form-control {{ isset($activity->status[$activity->id_dominio_estado]) ? $activity->status[$activity->id_dominio_estado] : '' }}">{{ $activity->tblestadoactividad->nombre }}</label>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="valor" class="required">Valor actividad</label>
                <input type="text" class="form-control money" @if ($edit) name="valor" @endif id="valor" value="{{ old('valor', $activity->valor) }}" @if ($edit) required @else readonly @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_tercero_resposable_contratista" class="required">Responsable</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : ''}}">
                        <div class="{{ $create_client && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_tercero_resposable_contratista" id="id_tercero_resposable_contratista" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                @forelse ($contratistas as $contratista)
                                    <option
                                        data-id_contratista="{{ (isset($contratista->tblterceroresponsable) ? $contratista->tblterceroresponsable->id_tercero : $contratista->id_tercero ) }}"
                                        value="{{ $contratista->id_tercero }}" {{ old('id_tercero_resposable_contratista', $activity->id_tercero_resposable_contratista) == $contratista->id_tercero ? 'selected' : '' }}>
                                        {{ $contratista->full_name.(isset($contratista->razon_social) ? ' - '.$contratista->nombres.' '.$contratista->apellidos : '') }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
                                    </option>
                                @empty
                                    <option value="">Elegir Responsable</option>
                                @endforelse
                            </select>
                        </div>
                        @if ($create_client && !$existe_cotizacion)
                            <div class="col-2 col-md-1 text-end">
                                <i
                                    class="fa-solid fa-plus btn btn-outline-primary fs-6 fw-bold modal-form"
                                    data-title="Nuevo aprobador"
                                    data-size='modal-xl'
                                    data-reload="false"
                                    data-select="id_tercero_resposable_contratista"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_coordinador')."") }}'
                                    data-toggle="tooltip"
                                    title="Crear aprobador"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $activity->tblresposablecontratista->full_name.(isset($activity->tblresposablecontratista->razon_social) ? ' - '.$activity->tblresposablecontratista->nombres.' '.$activity->tblresposablecontratista->apellidos : '') }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <label for="descripcion" class="required">Descripci??n actividad</label>
                <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit) required @else readonly @endif>{{ old('nombre', $activity->descripcion) }}</textarea>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <label for="observaciones" class="required">Observaciones</label>
                <textarea class="form-control" @if ($edit) name="observaciones" @endif id="observaciones" rows="2" style="resize: none" @if ($edit) required @else readonly @endif>{{ old('nombre', $activity->observaciones) }}</textarea>
            </div>
            @if (!$create)
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 pb-2 my-auto text-center">
                    @if ($edit || (!$edit && $existe_cotizacion))
                        <span
                            class="btn btn-outline-{{!$existe_cotizacion ? 'danger' : 'info'}} border modal-form"
                            data-title="{{ !$existe_cotizacion ? 'Elegir cotizaci??n' : 'Cotizaci??n #'.$quote->id_cotizacion }}"
                            data-size="{{ !$existe_cotizacion ? 'modal-xl' : 'modal-fullscreen' }}"
                            data-header-class='bg-{{ !$existe_cotizacion ? 'primary' : 'info'}} bg-opacity-75 text-white'
                            data-reload="false"
                            data-action="{{ $existe_cotizacion ? route('quotes.show', $quote->id_cotizacion) : route('activities.client_quote', $activity->id_actividad) }}"
                            data-toggle="tooltip"
                            data-html="true"
                            title="{{ !$existe_cotizacion ? 'Cotizaci??n pendiente' : 'Ver cotizaci??n' }}"
                        >
                            {!! !$existe_cotizacion ? '<i class="fa-solid fa-clipboard-list fs-4"></i>' : '<i class="fa-solid fa-circle-info fs-4"></i>' !!}
                        </span>
                    @endif

                    @if ($edit || (!$edit && $movimiento))
                        <span
                            class="btn btn-outline-{{$classBtn}} border modal-form"
                            data-title="{{$dataTitle}}"
                            data-size="modal-fullscreen"
                            data-header-class="bg-{{$classModal}} bg-opacity-75 text-white"
                            data-reload="true"
                            data-reload-location="true"
                            data-action="{{ route($route, $params) }}"
                            data-toggle="tooltip"
                            title="{{$title}}"
                        >
                            <i class="fa-solid fa-cart-shopping fs-4"></i>
                        </span>
                    @endif

                    @can('createComment', $activity)
                        @if ($edit)
                            <span
                                class="btn btn-outline-secondary border modal-form"
                                data-title="Nuevo comentario"
                                data-size="modal-md"
                                data-header-class='bg-primary bg-opacity-75 text-white'
                                data-reload="true"
                                data-reload-location="true"
                                data-action="{{ route('activities.seguimiento', $activity->id_actividad) }}"
                                data-toggle="tooltip"
                                title="Nuevo comentario"
                            >
                                <i class="fa-solid fa-pen-clip fs-4"></i>
                            </span>
                        @endif
                    @endcan
                </div>
            @endif
        </div>

        @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear actividad' : 'Editar actividad'])
    @if ($create || $edit)
        </form>
    @endif
@if (!$create)
        </div>
        <div class="tab-pane" id="track-activity" role="tabpanel" aria-labelledby="track-tab-activity">
            @include('partials._track', [$edit, 'model' => $estados_actividad, 'title' => 'Estados actividad', 'route' => 'stateactivities'])
        </div>
        @can('viewReport', $activity)
            <div class="tab-pane" id="report-activity" role="tabpanel" aria-labelledby="report-tab-activity">
                @include('actividades.reporte', [$activity, $uploadReport])
            </div>
        @endcan
        @can('viewLiquidate', $activity)
            <div class="tab-pane" id="liquidate-activity" role="tabpanel" aria-labelledby="liquidate-tab-activity">
                @include('liquidaciones._form', [
                    $liquidacion,
                    $activity,
                    $carrito,
                    $liquidate
                ])
            </div>
        @endcan
    </div>
@endif

<script type="application/javascript">
    datePicker();
    table();
    flexTable();
</script>