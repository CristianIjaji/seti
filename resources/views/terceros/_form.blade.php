<?php
    $create = isset($tercero->id_tercero) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $tipo_tercero = (isset($tipo_tercero) && $tipo_tercero != '') ? $tipo_tercero : false;
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('clients.store') : route('clients.update', $tercero) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_dominio_tipo_documento" class="required">Tipo documento</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_tipo_documento" id="id_dominio_tipo_documento" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir tipo documento</option>
                    @foreach ($tipo_documentos as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_tipo_documento', $tercero->id_dominio_tipo_documento) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_dominio_tipo_documento" value="{{ $tercero->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="documento" class="required">Documento</label>
            <input type="text" class="form-control" @if ($edit) name="documento" @endif id="documento" value="{{ old('documento', $tercero->documento) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="dv">DV</label>
            <input type="text" class="form-control" @if ($edit) name="dv" @endif id="dv" value="{{ old('dv', $tercero->dv) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="razon_social">Razón social</label>
            <input type="text" class="form-control" @if ($edit) name="razon_social" @endif id="razon_social" value="{{ old('dv', $tercero->razon_social) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="nombres" class="required">Nombres</label>
            <input type="text" class="form-control" @if ($edit) name="nombres" @endif id="nombres" value="{{ old('nombres', $tercero->nombres) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="apellidos" class="required">Apellidos</label>
            <input type="text" class="form-control" @if ($edit) name="apellidos" @endif id="apellidos" value="{{ old('apellidos', $tercero->apellidos) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="ciudad" class="required">Ciudad</label>
            <input type="text" class="form-control" @if ($edit) name="ciudad" @endif id="ciudad" value="{{ old('ciudad', $tercero->ciudad) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="direccion" class="required">Dirección</label>
            <input type="text" class="form-control" @if ($edit) name="direccion" @endif id="direccion" value="{{ old('direccion', $tercero->direccion) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="correo" class="required">Correo</label>
            <input type="email" class="form-control" @if ($edit) name="correo" @endif id="correo" value="{{ old('correo', $tercero->correo) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="telefono" class="required">Teléfono / Celular</label>
            <input type="tel" class="form-control" @if ($edit) name="telefono" @endif id="telefono" value="{{ old('telefono', $tercero->telefono) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_dominio_tipo_tercero" class="required">Tipo tercero</label>
            @if ($edit)
                @if ($tipo_tercero == '')
                    <select class="form-control" name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir tipo tercero</option>
                        @foreach ($tipo_terceros as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_tipo_tercero', $tercero->id_dominio_tipo_tercero) == $id ? 'selected' : '' }}>
                                {{$nombre}}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" value="{{ $tipo_tercero->nombre }}" disabled readonly>
                    <input type="hidden" name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" value="{{ $tipo_tercero->id_dominio }}">
                @endif
            @else
                <input type="text" class="form-control" id="id_dominio_tipo_tercero" value="{{ $tercero->tbldominiotercero->nombre }}" disabled>
            @endif
        </div>
        
        @if(!$create)
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $tercero->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $tercero->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>

            @if (!$edit)
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $tercero->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $tercero->created_at }}">
                </div>
            @endif
        @endif
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear tercero' : 'Editar tercero'])