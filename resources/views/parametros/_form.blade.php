<?php
    $create = isset($parametro->id_parametro_aplicacion) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('params.store') : route('params.update', $parametro) }}" method="POST">
        @csrf

        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="form-group">
        <label for="llave" class="required">Llave</label>
        <input type="text" class="form-control" @if ($edit) name="llave" @endif id="llave" value="{{ old('llave', $parametro->llave) }}" @if ($edit) required @else disabled @endif>
    </div>
    <div class="form-group">
        <label for="valor" class="required">Valor</label>
        @if ($edit)
            <select class="form-control" name="valor[]" id="valor" multiple style="width: 100%">
                @foreach ($dominios as $id => $name)
                    <option value="{{ $id }}" {{ old('valor',  in_array($id, explode(',', $parametro->valor))) ? 'selected' : '' }}>
                        {{$name}}
                    </option>
                @endforeach
            </select>
        @else
            <input type="text" class="form-control" id="valor" value="{{ $parametro->valor }}" disabled>
        @endif
    </div>
    <div class="form-group">
        <label for="descripcion" class="required">Descripción</label>
        <textarea class="form-control" id="descripcion" @if ($edit) name="descripcion" @endif rows="3" style="resize: none" @if (!$edit) disabled @endif>{{ old('nombre', $parametro->descripcion) }}</textarea>
    </div>    
    @if (!$create)
        <div class="form-group">
            <label for="estado" class="required">Estado</label>
            @if ($edit)
                <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @foreach ($estados as $id => $name)
                        <option value="{{ $id }}" {{ old('estado', $parametro->estado_form) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="estado" value="{{ $parametro->estado_form == 1 ? 'Activo' : 'Inactivo' }}" disabled>
            @endif
        </div>
        @if (!$edit)
            <div class="form-group">
                <label for="creado_por">Creado por</label>
                <input type="text" id="creado_por" class="form-control" disabled value="{{ $parametro->tblusuario->usuario }}">
            </div>
            <div class="form-group">
                <label for="fecha_creacion">Fecha creación</label>
                <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $parametro->created_at }}">
            </div>
        @endif
    @endif

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear parametro' : 'Editar parametro'])

<script type="application/javascript">
    setupSelect2('modalForm');
</script>