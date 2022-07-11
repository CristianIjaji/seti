<?php
    $create = isset($cotizacion->id_cotizacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('clients.store') : route('clients.update', $cotizacion) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="codigo_cotizacion" class="required">Código cotización</label>
            <input type="text" class="form-control" @if ($edit) name="codigo_cotizacion" @endif id="codigo_cotizacion" value="{{ old('codigo_cotizacion', $cotizacion->codigo_cotizacion) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_cliente" class="required">Cliente</label>
            @if ($edit)
                <select class="form-control" name="id_cliente" id="id_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $cotizacion->id_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_cliente" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_estacion" class="required">Punto interes</label>
            @if ($edit)
                <select class="form-control" name="id_estacion" id="id_estacion" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir punto interes</option>
                    @foreach ($estaciones as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_estacion', $cotizacion->id_estacion) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_estacion" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-4">
            <label for="descripcion" class="required">Tipo trabajo</label>
            <input type="text" class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" value="{{ old('descripcion', $cotizacion->descripcion) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2 input-date">
            <label for="fecha_solicitud" class="required">Fecha</label>
            <input type="text" class="form-control" @if ($edit) name="fecha_solicitud" @endif id="fecha_solicitud" value="{{ old('fecha_solicitud', $cotizacion->fecha_solicitud) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-6 col-md-6 col-lg-2">
            <label for="id_prioridad" class="required">Prioridad</label>
            @if ($edit)
                <select class="form-control" name="id_prioridad" id="id_prioridad" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir prioridad</option>
                    {{-- @foreach ($estaciones as $estacion)
                        <option value="{{ $estacion->id_punto_interes }}" {{ old('id_prioridad', $cotizacion->id_prioridad) == $estacion->id_punto_interes ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach --}}
                </select>
            @else
                <input type="text" class="form-control" id="id_prioridad" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_proceso" class="required">Proceso</label>
            @if ($edit)
                <select class="form-control" name="id_proceso" id="id_proceso" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir proceso</option>
                    {{-- @foreach ($estaciones as $estacion)
                        <option value="{{ $estacion->id_punto_interes }}" {{ old('id_proceso', $cotizacion->id_proceso) == $estacion->id_punto_interes ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach --}}
                </select>
            @else
                <input type="text" class="form-control" id="id_proceso" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_responsable_cliente" class="required">Contratista</label>
            @if ($edit)
                <select class="form-control" name="id_responsable_cliente" id="id_responsable_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir contratista</option>
                    {{-- @foreach ($estaciones as $estacion)
                        <option value="{{ $estacion->id_punto_interes }}" {{ old('id_responsable_cliente', $cotizacion->id_responsable_cliente) == $estacion->id_punto_interes ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach --}}
                </select>
            @else
                <input type="text" class="form-control" id="id_responsable_cliente" value="{{ $cotizacion->tbldominiodocumento->nombre }}" disabled>
            @endif
        </div>


        {{-- 
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="dv">DV</label>
            <input type="text" class="form-control" @if ($edit) name="dv" @endif id="dv" value="{{ old('dv', $cotizacion->dv) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="razon_social">Razón social</label>
            <input type="text" class="form-control" @if ($edit) name="razon_social" @endif id="razon_social" value="{{ old('dv', $cotizacion->razon_social) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="nombres" class="required">Nombres</label>
            <input type="text" class="form-control" @if ($edit) name="nombres" @endif id="nombres" value="{{ old('nombres', $cotizacion->nombres) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="apellidos" class="required">Apellidos</label>
            <input type="text" class="form-control" @if ($edit) name="apellidos" @endif id="apellidos" value="{{ old('apellidos', $cotizacion->apellidos) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="ciudad" class="required">Ciudad</label>
            <input type="text" class="form-control" @if ($edit) name="ciudad" @endif id="ciudad" value="{{ old('ciudad', $cotizacion->ciudad) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="direccion" class="required">Dirección</label>
            <input type="text" class="form-control" @if ($edit) name="direccion" @endif id="direccion" value="{{ old('direccion', $cotizacion->direccion) }}" @if ($edit) required @else disabled @endif>
        </div>

        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="correo" class="required">Correo</label>
            <input type="email" class="form-control" @if ($edit) name="correo" @endif id="correo" value="{{ old('correo', $cotizacion->correo) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="telefono" class="required">Teléfono / Celular</label>
            <input type="tel" class="form-control" @if ($edit) name="telefono" @endif id="telefono" value="{{ old('telefono', $cotizacion->telefono) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
            <label for="id_dominio_tipo_tercero" class="required">Tipo tercero</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir tipo tercero</option>
                    @foreach ($tipo_terceros as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_tipo_tercero', $cotizacion->id_dominio_tipo_tercero) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_dominio_tipo_tercero" value="{{ $cotizacion->tbldominiotercero->nombre }}" disabled>
            @endif
        </div>
        @if(!$create)
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $cotizacion->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $cotizacion->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>

            @if (!$edit)
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $cotizacion->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $cotizacion->created_at }}">
                </div>
            @endif
        @endif --}}
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear cotización' : 'Editar cotización'])


<script type="application/javascript">
    setupSelect2('modalForm');
    datePicker();
</script>