@php
    $create = isset($activity->id_actividad) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $existe_cotizacion = false;
    if(isset($quote) && isset($quote->id_cotizacion)) {
        $existe_cotizacion = true;

        $activity->id_cotizacion = $quote->id_cotizacion;
        $activity->ot = $quote->ot_trabajo;
        $activity->id_encargado_cliente = $quote->id_cliente;
        $activity->id_estacion = $quote->id_estacion;
        $activity->id_tipo_actividad = $quote->id_tipo_trabajo;
        $activity->fecha_solicitud = $quote->fecha_solicitud;
        $activity->valor = $quote->total_sin_iva;
        $activity->id_resposable_contratista = $quote->id_responsable_cliente;
        $activity->descripcion = $quote->descripcion;
    }
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('activities.store') : route('activities.update', $activity) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    @if (!$create)
        <ul class="nav nav-tabs" id="activityTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="true">Actividad</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="track-tab-activity" data-bs-toggle="tab" data-bs-target="#track-activity" type="button" role="tab" aria-controls="track-activity" aria-selected="true">Seguimiento</button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="activityTab">
            <div class="tab-pane fade show active" id="activity" role="tabpanel" aria-labelledby="activity-tab">    
    @endif
        <div class="row">
            <input type="hidden" id="id_actividad" value="{{ $activity->id_actividad }}">
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="ot">OT</label>
                <input type="text" class="form-control text-uppercase" @if ($edit) name="ot" @endif id="ot" value="{{ old('ot', $activity->ot) }}" @if (!$edit) readonly @endif >
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_encargado_cliente" class="required">Cliente</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : '' }}">
                        <div class="{{ $create_client && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_encargado_cliente" id="id_encargado_cliente" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                <option value="">Elegir cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option
                                        data-id_cliente="{{ (isset($cliente->tblterceroresponsable) ? $cliente->tblterceroresponsable->id_tercero : $cliente->id_tercero ) }}"
                                        value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $activity->id_encargado_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
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
                                    data-select="id_encargado_cliente"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_representante_cliente')."") }}'
                                    data-toggle="tooltip"
                                    title="Crear cliente"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_encargado_cliente" value="{{ $activity->tblencargadocliente->full_name }} {{ (isset($activity->tblencargadocliente->tblterceroresponsable) ? ' - '.$activity->tblencargadocliente->tblterceroresponsable->razon_social : '') }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_estacion" class="required">Punto interés</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : ''}}">
                        <div class="{{ $create_site && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_estacion" id="id_estacion" data-minimuminputlength="3" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                <option value="">Elegir punto interés</option>
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
                <label for="id_subsistema" class="required">Subsistema</label>
                @if ($edit)
                    <select name="id_subsistema" id="id_subsistema" class="form-control" style="width: 100%" @if ($edit) required @else readonly @endif>
                        <option value="">Elegir subsistema</option>
                        @foreach ($subsistemas as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_subsistema', $activity->id_subsistema) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_subsistema" value="{{ $activity->tblsubsistema->nombre }}" readonly>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 {{ $create ? 'input-date' : '' }}">
                <label for="fecha_solicitud" class="required">Fecha solicitud</label>
                <input type="text" class="form-control" @if ($edit) name="fecha_solicitud" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $activity->fecha_solicitud) }}" readonly>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 {{ $create ? 'input-date' : '' }}">
                <label for="fecha_programacion" class="required">Fecha programación</label>
                <input type="text" class="form-control" data-min-date="{{ date('Y-m-d') }}" @if ($edit) name="fecha_programacion" @endif id="fecha_programacion" value="{{ old('fecha_programacion', $activity->fecha_programacion) }}" readonly>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="permiso_acceso">ID Permiso</label>
                <input type="text" class="form-control" @if ($edit) name="permiso_acceso" @endif id="permiso_acceso" value="{{ old('permiso_acceso', $activity->permiso_acceso) }}" @if (!$edit) readonly @endif >
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="id_estado_actividad" class="required">Estado</label>
                @if ($edit)
                    @if ($create)
                        <select name="id_estado_actividad" id="id_estado_actividad" class="form-control {{ isset($activity->status[$activity->id_estado_actividad]) ? $activity->status[$activity->id_estado_actividad] : '' }}" style="width: 100%">
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_dominio }}" {{ old('id_estado_actividad', $activity->id_estado_actividad) == $estado->id_dominio ? 'selected' : '' }}>
                                    {{ $estado->nombre}}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <label class="form-control {{ isset($activity->status[$activity->id_estado_actividad]) ? $activity->status[$activity->id_estado_actividad] : '' }}">{{ $activity->tblestadoactividad->nombre }}</label>
                        <input type="hidden" name="id_estado_actividad" id="id_estado_actividad" value="{{ $activity->id_estado_actividad }}">
                    @endif
                @else
                <label class="form-control {{ isset($activity->status[$activity->id_estado_actividad]) ? $activity->status[$activity->id_estado_actividad] : '' }}">{{ $activity->tblestadoactividad->nombre }}</label>
                @endif
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2">
                <label for="valor" class="required">Valor actividad</label>
                <input type="text" class="form-control money" @if ($edit) name="valor" @endif id="valor" value="{{ old('valor', $activity->valor) }}" @if ($edit) required @else readonly @endif>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <label for="id_resposable_contratista" class="required">Responsable</label>
                @if ($edit)
                    <div class="row pe-0 {{ !$existe_cotizacion ? 'pe-md-3' : ''}}">
                        <div class="{{ $create_client && !$existe_cotizacion ? 'col-10 col-md-11' : 'col-12' }}">
                            <select class="form-control" name="id_resposable_contratista" id="id_resposable_contratista" style="width: 100%" @if ($edit && !$existe_cotizacion) required @else readonly @endif>
                                @forelse ($contratistas as $contratista)
                                    <option
                                        data-id_contratista="{{ (isset($contratista->tblterceroresponsable) ? $contratista->tblterceroresponsable->id_tercero : $contratista->id_tercero ) }}"
                                        value="{{ $contratista->id_tercero }}" {{ old('id_resposable_contratista', $activity->id_resposable_contratista) == $contratista->id_tercero ? 'selected' : '' }}>
                                        {{ $contratista->full_name }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
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
                                    data-select="id_resposable_contratista"
                                    data-action='{{ route('clients.create', "tipo_tercero=".session('id_dominio_coordinador')."") }}'
                                    data-toggle="tooltip"
                                    title="Crear aprobador"
                                ></i>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="text" class="form-control" id="id_cliente_cotizacion" value="{{ $activity->tblresposablecontratista->full_name }} {{ (isset($activity->tblresposablecontratista->tblterceroresponsable) ? ' - '.$activity->tblresposablecontratista->tblterceroresponsable->razon_social : '') }}" readonly>
                @endif
            </div>

            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <label for="descripcion" class="required">Descripción actividad</label>
                <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit) required @else readonly @endif>{{ old('nombre', $activity->descripcion) }}</textarea>
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <label for="observaciones" class="required">Observaciones</label>
                <textarea class="form-control" @if ($edit) name="observaciones" @endif id="observaciones" rows="2" style="resize: none" @if ($edit) required @else readonly @endif>{{ old('nombre', $activity->observaciones) }}</textarea>
            </div>
            @if (!$create)
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 pb-2 my-auto text-center">
                    <input type="hidden" name="id_cotizacion" id="id_cotizacion_actividad" value="{{ $activity->id_cotizacion }}">
                    <span
                        class="btn btn-outline-{{!$existe_cotizacion ? 'danger' : 'info'}} border modal-form"
                        data-title="{{ !$existe_cotizacion ? 'Elegir cotización' : 'Cotización #'.$quote->id_cotizacion }}"
                        data-size="{{ !$existe_cotizacion ? 'modal-xl' : 'modal-fullscreen' }}"
                        data-header-class='bg-{{ !$existe_cotizacion ? 'primary' : 'info'}} bg-opacity-75 text-white'
                        data-reload="false"
                        data-action="{{ $existe_cotizacion ? route('quotes.show', $quote->id_cotizacion) : route('activities.client_quote', $activity->id_actividad) }}"
                        data-toggle="tooltip"
                        data-html="true"
                        title="{{ !$existe_cotizacion ? 'Cotización pendiente' : 'Ver cotización' }}"
                    >
                        {!! !$existe_cotizacion ? '<i class="fa-solid fa-list fs-4"></i>' : '<i class="fa-solid fa-circle-info fs-4"></i>' !!}
                    </span>

                    <span
                        class="btn btn-outline-danger border modal-form"
                        data-title="Orden compra"
                        data-size="modal-fullscreen"
                        data-header-class='bg-primary bg-opacity-75 text-white'
                        data-reload="true"
                        data-action="{{ route('purchases.create') }}"
                        data-toggle="tooltip"
                        title="Orden compra"
                    >
                        <i class="fa-solid fa-cart-shopping fs-4"></i>
                    </span>

                    @can('createComment', $activity)
                        @if ($edit)
                            <span
                                class="btn btn-outline-secondary border modal-form"
                                data-title="Nuevo comentario"
                                data-size="modal-md"
                                data-header-class='bg-primary bg-opacity-75 text-white'
                                data-reload="true"
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

        @if (!$create)
            </div>
            <div class="tab-pane" id="track-activity" role="tabpanel" aria-labelledby="track-tab-activity">
                @include('actividades._track', [$edit, 'model' => $estados_actividad])
            </div>
        @endif
    </div>

<script type="application/javascript">
    datePicker();

    table();
    flexTable();
</script>