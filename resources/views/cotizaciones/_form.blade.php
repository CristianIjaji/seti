@php
    $create = isset($cotizacion->id_cotizacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $disable_form = in_array($cotizacion->estado, [session('id_dominio_cotizacion_aprobada'), session('id_dominio_cotizacion_cancelada')]) ? true : false;
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('quotes.store') : route('quotes.update', $cotizacion) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    @if (!$create)
        <ul class="nav nav-tabs" id="quotesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="quotes-tab" data-bs-toggle="tab" data-bs-target="#quotes" type="button" role="tab" aria-controls="quotes" aria-selected="true">Cotización</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="track-tab" data-bs-toggle="tab" data-bs-target="#track" type="button" role="tab" aria-controls="track" aria-selected="true">Seguimiento</button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="quotesTab">
            <div class="tab-pane fade show active" id="quotes" role="tabpanel" aria-labelledby="quotes-tab">    
    @endif
            <div class="row">
                <input type="hidden" id="id_cotizacion" value="{{ $cotizacion->id_cotizacion }}">

                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                    <label for="ot_trabajo">OT</label>
                    <input type="text" class="form-control" @if ($edit && !$disable_form) name="ot_trabajo" @endif id="ot_trabajo" value="{{ old('ot_trabajo', $cotizacion->ot_trabajo) }}" @if ($edit && !$disable_form) required @else disabled @endif>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="id_cliente_cotizacion" class="required">Cliente</label>
                    @if ($edit && !$disable_form)
                        <div class="row pe-0 pe-md-3">
                            <div class="{{ $create_client ? 'col-10 col-md-11' : 'col-12' }}">
                                <select class="form-control" name="id_cliente" id="id_cliente_cotizacion" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
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
                                <div class="col-2 col-md-1 text-end">
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
                    @if ($edit && !$disable_form)
                        <select class="form-control" name="id_tipo_trabajo" id="id_tipo_trabajo" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
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
                    <input type="text" class="form-control" data-max-date="{{ date('Y-m-d') }}" @if ($edit && !$disable_form) name="fecha_solicitud" data-default-date="{{ $cotizacion->fecha_solicitud }}" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $cotizacion->fecha_solicitud) }}" @if ($edit && !$disable_form) required @else disabled @endif readonly>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
                    <label for="id_prioridad" class="required">Prioridad</label>
                    @if ($edit && !$disable_form)
                        <select class="form-control" name="id_prioridad" id="id_prioridad" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
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
                    @if ($edit && !$disable_form)
                        <select class="form-control" name="iva" id="iva" data-dir="rtl" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
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
                <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
                    <label for="id_responsable" class="required">Aprobador</label>
                    @if ($edit && !$disable_form)
                        <div class="row pe-0 pe-md-3">
                            <div class="{{ $create_client ? 'col-10 col-md-11' : 'col-12' }}">
                                <select class="form-control" name="id_responsable_cliente" id="id_responsable" style="width: 100%" @if ($edit && !$disable_form) required @else disabled @endif>
                                    @forelse ($contratistas as $contratista)
                                        <option
                                            data-id_contratista="{{ (isset($contratista->tblterceroresponsable) ? $contratista->tblterceroresponsable->id_tercero : $contratista->id_tercero ) }}"
                                            value="{{ $contratista->id_tercero }}" {{ old('id_responsable', $cotizacion->id_responsable_cliente) == $contratista->id_tercero ? 'selected' : '' }}>
                                            {{ $contratista->full_name }} {{ (isset($contratista->tblterceroresponsable) ? ' - '.$contratista->tblterceroresponsable->razon_social : '' ) }}
                                        </option>
                                    @empty
                                        <option value="">Elegir aprobador</option>
                                    @endforelse
                                </select>
                            </div>
                            @if ($create_client)
                                <div class="col-2 col-md-1 text-end">
                                    <i
                                        class="fa-solid fa-plus btn fs-6 fw-bold bg-primary text-white modal-form"
                                        data-title="Nuevo aprobador"
                                        data-size='modal-xl'
                                        data-reload="false"
                                        data-select="id_responsable"
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
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6">
                    <label for="descripcion" class="required">Descripción orden</label>
                    <textarea class="form-control" @if ($edit && !$disable_form) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit && !$disable_form) required @else disabled @endif>{{ old('nombre', $cotizacion->descripcion) }}</textarea>
                </div>
                @if (!$create)
                    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 my-auto text-center">
                        <button id="btn-send-quote" title="Descargar cotización" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                            <i class="fa-solid fa-file-excel fs-4"></i> Descargar cotización
                        </button>
                    </div>
                @endif

                <div class="clearfix"><hr></div>

                <div class="table-responsive">
                    <table id="table_items" class="table table-sm table-bordered align-middle table-responsive-stack">
                        <thead>
                            <tr>
                                <th class="col-1 text-center">Ítem</th>
                                <th class="col-4 text-center">Descripción</th>
                                <th class="col-1 text-center">Un.</th>
                                <th class="col-1 text-center">Cant.</th>
                                <th class="col-2 text-center">VR UNIT</th>
                                <th class="col-2 text-center">VR TOTAL</th>
                                <th id="th-delete" class="col-1 text-center">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="tr_{{ session('id_dominio_materiales') }}">
                                <th colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit && !$disable_form ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
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
                                </th>
                            </tr>
                            <tr id="tr_{{ session('id_dominio_mano_obra') }}">
                                <th colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit && !$disable_form ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
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
                                </th>
                            </tr>
                            <tr id="tr_{{ session('id_dominio_transporte') }}">
                                <th colspan="7">
                                    <span
                                        class="btn w-100 bg-gray fw-bold {{ $edit && !$disable_form ? 'modal-form' : ''}} d-flex justify-content-center text-white tr_cotizacion"
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
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12 col-md-6 co-lg-6 my-auto pb-2 ps-4">
                    @can('createComment', $cotizacion)
                        @if (!$create && $edit)
                            <div class="col-12 border rounded p-3">
                                <div class="form-group col-12 text-start">
                                    <label for="comentario">Nuevo comentario</label>
                                    <textarea class="form-control" id="comentario" name="comentario" rows="3" style="resize: none"></textarea>
                                    
                                </div>

                                <div class="col-12 d-flex justify-content-evenly align-items-center">
                                    @can('checkQuote', $cotizacion)
                                        <button id="btn-check-quote" title="Aprobar cotización" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                            <i class="fa-solid fa-thumbs-up"></i> Aprobar cotización
                                        </button>
                                    @endcan

                                    @can('denyQuote', $cotizacion)
                                        <button id="btn-deny-quote" title="Devolver cotización" data-toggle="tooltip" class="btn bg-info bg-gradient text-white btn-quote">
                                            <i class="fa-solid fa-thumbs-down"></i> Devolver cotización
                                        </button>
                                    @endcan

                                    @can('waitQuote', $cotizacion)
                                        <button id="btn-wait-quote" title="Cotización se envió al cliente y está pendiente por aprobación" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                            <i class="fa-regular fa-clock"></i> Pendiente aprobación
                                        </button>
                                    @endcan

                                    @can('aproveQuote', $cotizacion)
                                        <button id="btn-aprove-quote" title="Cliente reviso la cotización y la aprobó" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                            <i class="fa-regular fa-circle-check"></i> Cotización aprobada cliente
                                        </button>
                                    @endcan

                                    @can('rejectQuote', $cotizacion)
                                        <button id="btn-reject-quote" title="Cliente rechazó la cotización" data-toggle="tooltip" class="btn bg-info bg-gradient text-white btn-quote">
                                            <i class="fa-solid fa-xmark"></i> Cotización rechazada cliente
                                        </button>
                                    @endcan

                                    @can('cancelQuote', $cotizacion)
                                        <button id="btn-cancel-quote" title="Se cancela proceso de cotización" data-toggle="tooltip" class="btn btn-danger bg-gradient text-white btn-quote">
                                            <i class="fa-solid fa-handshake-slash"></i> Cancelar cotización
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    @endcan

                    @if (!$create && $edit)
                        @can('createActivity', $cotizacion)
                            <div class="row border rounded p-3">
                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-7 text-start">
                                    <label for="id_subsistema" class="required">Subsistema</label>
                                    <select name="id_subsistema" id="id_subsistema" class="form-control" style="width: 100%">
                                        <option value="">Elegir subsistema</option>
                                        @foreach ($subsistemas as $id => $nombre)
                                            <option value="{{ $id }}" {{ old('id_subsistema') == $id ? 'selected' : '' }}>
                                                {{$nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- @if ($edit)
                                        <select name="id_subsistema" id="id_subsistema" class="form-control" style="width: 100%">
                                            <option value="">Elegir subsistema</option>
                                            @foreach ($subsistemas as $id => $nombre)
                                                <option value="{{ $id }}" {{ old('id_subsistema') == $id ? 'selected' : '' }}>
                                                    {{$nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control" id="id_subsistema" value="{{ $activity->tblsubsistema->nombre }}" disabled>
                                    @endif --}}
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-5 input-date">
                                    <label for="fecha_programacion" class="required">Fecha programación</label>
                                    <input type="text" class="form-control" data-toolbarplacement="bottom" data-min-date="{{ date('Y-m-d') }}" @if ($edit) name="fecha_programacion" @endif id="fecha_programacion" value="{{ old('fecha_programacion') }}" @if ($edit) required @else disabled @endif readonly>
                                </div>
                                <div class="col-12 d-flex justify-content-evenly align-items-center">
                                    <button id="btn-create-activity" title="Se crea actividad con la cotización aprobada" data-toggle="tooltip" class="btn btn-primary bg-gradient text-white btn-quote">
                                        <i class="fa-solid fa-person-digging"></i> Crear actividad
                                    </button>
                                </div>
                            </div>
                        @endcan
                    @endif
                </div>

                <div class="form-group col-12 col-md-6 co-lg-6 my-auto">
                    <div class="row fs-5">
                        <label class="col-4 col-sm-4 col-md-5 text-end">Total sin IVA:</label>
                        <label id="lbl_total_sin_iva" class="col-8 col-sm-8 col-md-7 text-end border-bottom"></label>
                        <label class="col-4 col-sm-4 col-md-5 text-end">Total IVA:</label>
                        <label id="lbl_total_iva" class="col-8 col-sm-8 col-md-7 text-end border-bottom"></label>
                        <label class="col-4 col-sm-4 col-md-5 text-end">Total con IVA:</label>
                        <label id="lbl_total_con_iva" class="col-8 col-sm-8 col-md-7 text-end border-bottom"></label>
                    </div>
                </div>
            </div>

            @php
                $edit = (
                    $edit &&
                    $cotizacion->id_usuareg == Auth::user()->id_usuario &&
                    in_array($cotizacion->estado, [session('id_dominio_cotizacion_creada'), session('id_dominio_cotizacion_devuelta')]) ||
                    ($edit && Auth::user()->role == session('id_dominio_analista'))
                );
            @endphp

            @include('partials.buttons', [$create, 'edit' => $edit, 'label' => $create ? 'Crear cotización' : 'Editar cotización', 'modal' => 'modalForm'])

            @if (!$create)
                </div>
                <div class="tab-pane" id="track" role="tabpanel" aria-labelledby="track-tab">
                    @include('cotizaciones._track', [$edit, 'model' => $estados_cotizacion])
                </div>
            @endif
    </div>

<script type="application/javascript">
    datePicker();

    carrito = <?= json_encode($carrito) ?>;

    if(Object.keys(carrito).length) {
        drawItems(<?= $edit ? 'true' : 'false' ?>);
        $('#table-cotizaciones').removeClass('d-none');
        table();
    }

    flexTable();
</script>