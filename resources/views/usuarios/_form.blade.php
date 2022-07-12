<?php
    $create = isset($usuario->id_usuario) ? false : true;
    $edit = isset($edit) ? $edit : false;
?>


@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form id="form-prueba" action="{{ $create ? route('users.store') : route('users.update', $usuario) }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if (!$create)
            @method('PATCH')
        @endif
@endif

<ul class="nav nav-tabs" id="usersTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Usuario</button>
    </li>
</ul>
<div class="tab-content pt-3" id="usersTabContent">
    <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
        <div class="form-group">
            <label for="usuario" class="required">Usuario</label>
            <input type="text" class="form-control" @if ($create || $edit) name="usuario" @endif id="usuario" value="{{ old('usuario', $usuario->usuario) }}" @if ($create || $edit) required @else disabled @endif>
        </div>
        <div class="form-group">
            <label for="email" class="required">Tercero</label>
            @if ($create || $edit)
                <select name="id_tercero" id="id_tercero" class="form-control" style="width: 100%">
                    <option value="">Elegir tercero</option>
                    @foreach ($terceros as $tercero)
                        <option value="{{$tercero->id_tercero}}" {{ old('id_tercero', $tercero->id_tercero) == $usuario->id_tercero ? 'selected' : '' }}>
                            {{$tercero->nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" id="tercero" class="form-control" value="{{ $usuario->tbltercero->full_name }}" disabled>
            @endif
        </div>
        @if ($create)
            <div class="form-group">
                <label for="password" class="required">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" value="{{ old('password', $usuario->password) }}">
            </div>
            <div class="form-group">
                <label for="password-confirm" class="required">Confirmar contraseña</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
        @endif
        @if ($edit)
            <div class="accordion form-group" id="accordionExample">
                <div class="card">
                    <div class="card-header bg-primary d-grid gap-2" id="headingPassword">
                        <button class="btn btn-link btn-block text-white text-start p-0 m-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Cambiar Contraseña
                        </button>
                    </div>
                
                    <div id="collapseOne" class="collapse" aria-labelledby="headingPassword" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="password" class="required">Contraseña</label>
                                <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}">
                            </div>
                            <div class="form-group">
                                <label for="password-confirm" class="required">Confirmar contraseña</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="estado" class="required">Estado</label>
                <select class="form-control" name="estado" id="estado" style="width: 100%" required>
                    @foreach ($estados as $id => $name)
                        <option value="{{ $id }}" {{ old('estado', $usuario->estado_form) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if (!$create && !$edit)
            <div class="form-group">
                <label for="estado">Estado</label>
                <input type="text" id="estado" class="form-control" disabled value="{{ $usuario->estado_form == 1 ? 'Activo' : 'Inactivo' }}">
            </div>
            
            <div class="form-group">
                <label for="creado_por">Creado por</label>
                <input type="text" id="creado_por" class="form-control" disabled value="{{ $usuario->tblusuario->usuario }}">
            </div>
            
            <div class="form-group">
                <label for="fecha_creacion">Fecha creación</label>
                <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $usuario->created_at }}">
            </div>
        @endif
    </div>
    
    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear usuario' : 'Editar usuario'])
</div>
<style>
    .file-preview{
        padding: 0px !important;
    }
    .file-drop-zone-title {
        padding: 0px !important;
        margin: 30px 0px;
    }
    .file-drop-zone {
        margin: 0px !important;
        padding: 0px !important;
        min-height: 100px !important;
    }
</style>
<script type="application/javascript">
    var previewImage = null;

    if("{!! $usuario->logo !!}" !== '') {
        previewImage = "{!! $usuario->logo !!}"
    }

    var show = (!"{!! $create !!}" && !"{!! $edit !!}") ? false : true;

    $("#logo").fileinput({
        language: 'es',
        theme: "explorer",
        showCaption: show,
        showBrowse: show,
        showRemove: show,
        showUpload: false,
        showCancel: false,
        showClose: false,
        showDescriptionClose: false,
        // allowedPreviewTypes : [ 'image' ],
        allowedFileExtensions : ['jpg', 'jpeg', 'png'],
        initialPreviewShowDelete: false,
        fileActionSettings: {
            showRemove: false,
            showDrag: false
        },
        initialPreview: [
            previewImage
        ],
        initialPreviewAsData: true,
        initialPreviewConfig: {}
    });

    setupSelect2('modalForm');
</script>