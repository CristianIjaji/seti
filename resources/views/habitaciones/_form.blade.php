<?php
    $create = isset($habitacion->id_habitacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('rooms.store') : route('rooms.update', $habitacion) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        @if (in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]))
            <div class="form-group col-12">
                <label for="nombre" class="required">Asociado</label>
                @if ($edit && in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')]))
                    <select class="form-control" name="id_tercero_cliente" id="id_tercero_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir asociado</option>
                        @foreach ($asociados as $asociado)
                            <option value="{{ $asociado->id_tercero }}" {{ old('estado', $habitacion->id_tercero_cliente) == $asociado->id_tercero ? 'selected' : '' }}>
                                {{ $asociado->nombre }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_tercero_cliente" value="{{ isset($habitacion->tbltercero->razon_social)
                        ? $habitacion->tbltercero->razon_social
                        : $habitacion->tbltercero->nombres.' '.$habitacion->tbltercero->apellidos }}" disabled>
                @endif
            </div>
        @endif
        <div class="form-group col-12">
            <label for="nombre" class="required">Nombre</label>
            <input type="text" class="form-control" @if ($edit) name="nombre" @endif id="nombre" value="{{ old('nombre', $habitacion->nombre) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12">
            <label for="cantidad" class="required">Cantidad</label>
            <input type="text" class="form-control" @if ($edit) name="cantidad" @endif id="cantidad" value="{{ old('cantidad', $habitacion->cantidad) }}" @if ($edit) required @else disabled @endif>
        </div>
        @if(!$create)
            <div class="form-group col-12">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $habitacion->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $habitacion->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>

            @if (!$edit)
                <div class="form-group col-12">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $habitacion->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $habitacion->created_at }}">
                </div>
            @endif
        @endif
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear habitación' : 'Editar habitación'])

<script type="application/javascript">
    setupSelect2('modalForm');
</script>