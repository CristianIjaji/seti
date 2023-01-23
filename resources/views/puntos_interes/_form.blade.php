<?php
    $create = isset($site->id_punto_interes) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('sites.store') : route('sites.update', $site) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="id_tercero_cliente" class="required">Cliente</label>
            @if ($edit)
                <div class="row pe-0 pe-md-3">
                    <div class="{{ $create_client ? 'col-10 col-md-11' : 'col-12' }}">
                        <select class="form-control" name="id_tercero_cliente" id="id_tercero_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                            <option value="">Elegir cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_tercero }}" {{ old('id_tercero_cliente', $site->id_tercero_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                                    {{$cliente->full_name}}
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
                                data-action='{{ route('clients.create', 'tipo_documento='.session('id_dominio_nit').'&tipo_tercero='.session('id_dominio_cliente').'') }}'
                                data-toggle="tooltip"
                                title="Crear cliente"
                            ></i>
                        </div>
                    @endif
                </div>
            @else
                <input type="text" class="form-control" id="id_tercero_cliente" value="{{ $site->tblcliente->full_name }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="id_dominio_zona" class="required">Zona</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_zona" id="id_dominio_zona" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir zona</option>
                    @foreach ($zonas as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_zona', $site->id_dominio_zona) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_dominio_zona" value="{{ $site->tbldominiozona->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="nombre" class="required">Nombre</label>
            <input type="text" class="form-control text-uppercase" @if ($edit) name="nombre" @endif id="nombre" value="{{ old('nombre', $site->nombre) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
            <label for="latitud">Latitud</label>
            <input type="text" class="form-control text-end" @if ($edit) name="latitud" @endif id="latitud" value="{{ old('latitud', $site->latitud) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
            <label for="longitud">Longitud</label>
            <input type="text" class="form-control text-end" @if ($edit) name="longitud" @endif id="longitud" value="{{ old('longitud', $site->longitud) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="id_dominio_tipo_transporte" class="required">Transporte</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_tipo_transporte" id="id_dominio_tipo_transporte" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir transporte</option>
                    @foreach ($transportes as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_tipo_transporte', $site->id_dominio_tipo_transporte) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_dominio_tipo_transporte" value="{{ $site->tbldominiotransporte->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="id_dominio_tipo_accesso" class="required">Acceso</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_tipo_accesso" id="id_dominio_tipo_accesso" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir acceso</option>
                    @foreach ($accesos as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_tipo_accesso', $site->id_dominio_tipo_accesso) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_dominio_tipo_accesso" value="{{ $site->tbldominioacceso->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <label for="descripcion" class="required">Descripcion</label>
            <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $site->descripcion) }}</textarea>
        </div>
        @if(!$create)
            <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $site->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $site->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>

            @if (!$edit)
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $site->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $site->created_at }}">
                </div>
            @endif
        @endif
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear punto' : 'Editar punto'])