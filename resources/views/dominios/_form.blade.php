<?php
    $create = isset($dominio->id_dominio) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('domains.store') : route('domains.update', $dominio) }}" method="POST">
        @csrf

        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="form-group">
        <label for="nombre" class="required">Nombre</label>
        <input type="text" class="form-control" @if ($edit) name="nombre" @endif id="nombre" value="{{ old('nombre', $dominio->nombre) }}" @if ($edit) required @else disabled @endif>
    </div>
    <div class="form-group">
        <label for="id_dominio_padre">Dominio padre</label>
        @if ($edit)
            <select class="form-control" name="id_dominio_padre" id="id_dominio_padre" style="width: 100%" @if ($edit) required @else disabled @endif>
                <option value="">Elegir dominio</option>
                @foreach ($dominios_padre as $id => $name)
                    <option value="{{ $id }}" {{ old('id_dominio_padre', $dominio->id_dominio_padre) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        @else
            <input type="text" id="id_dominio_padre" class="form-control" value="{{ $dominio->tbldominio->nombre ?? '' }}" disabled>
        @endif
    </div>
    <div class="form-group">
        <label for="descripcion" class="required">Descripción</label>
        <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="3" style="resize: none" @if (!$edit) disabled @endif>{{ old('nombre', $dominio->descripcion) }}</textarea>
    </div>
    @if (!$create)
        <div class="form-group">
            <label for="estado" class="required">Estado</label>
            @if ($edit)
                <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @foreach ($estados as $id => $name)
                        <option value="{{ $id }}" {{ old('estado', $dominio->estado_form) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" id="estado" class="form-control" disabled value="{{ $dominio->estado_form == 1 ? 'Activo' : 'Inactivo' }}">  
            @endif
        </div>
        @if (!$edit)
            <div class="form-group">
                <label for="creado_por">Creado por</label>
                <input type="text" id="creado_por" class="form-control" disabled value="{{ $dominio->tblusuario->usuario }}">
            </div>
            
            <div class="form-group">
                <label for="fecha_creacion">Fecha creación</label>
                <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $dominio->created_at }}">
            </div>
        @endif
    @endif

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear dominio' : 'Editar dominio'])

<script type="application/javascript">
    setupSelect2('modalForm');
</script>