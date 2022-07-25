<?php
    $create = isset($profile->id_tipo_tercero) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('profiles.store') : route('profiles.update', $profile) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
        <div class="row">
            <div class="form-group col-12">
                <label for="id_tipo_tercero" class="required">Tipo tercero</label>
                @if ($edit)
                    <select name="id_tipo_tercero" id="id_tipo_tercero" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir tipo tercero</option>
                        @foreach ($tipo_terceros as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_tipo_tercero', $profile->id_tipo_tercero) == $id ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_tipo_tercero" value="{{ $profile->tbltipotercero->nombre }}" disabled>
                @endif
            </div>
            @if ($edit)
                <div class="col-5">
                    <label for="id_menu">Menús disponibles</label>
                    <select id="id_menu" size="10" class="form-control" multiple style="width: 100%;">
                        @foreach ($menus_disponibles as $menu)
                            <option value="{{ $menu->id_menu }}">{{ $menu->nombre_form }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2 d-grid gap-2">
                    <button type="button" id="id_menu_rightSelected" class="btn btn-light rounded"><i class="fa-solid fa-chevron-right"></i></button>
                    <button type="button" id="id_menu_leftSelected" class="btn btn-light rounded"><i class="fa-solid fa-chevron-left"></i></button>
                </div>
            @endif
            <div class="col-5">
                <label for="id_menu_to" class="required">Menús asignados</label>
                <select id="id_menu_to" size="10" class="form-control" multiple style="width: 100%;">
                    @foreach ($menus_asignados as $id => $menu)
                        <option value="{{ $id }}">{{ print_r($menu['nombre'], 1) }}</option>
                    @endforeach
                </select>
            </div>
            @if ($edit)
                <div class="col-7"></div>    
            @endif
            
            <div class="{{ $edit ? 'col-5' : 'col-7 m-auto' }} pb-3">
                <label for="">Permisos del menú</label>
                <div class="row m-auto">
                    <div class="col-4"><label for="chk_crear">Crear</label></div>
                    <div class="col-4"><input id="chk_crear" type="checkbox"></div>
                    <div class="clearfix"></div>
                    <div class="col-4"><label for="chk_editar">Editar</label></div>
                    <div class="col-4"><input id="chk_editar" type="checkbox"></div>
                    <div class="clearfix"></div>
                    <div class="col-4"><label for="chk_importar">Importar</label></div>
                    <div class="col-4"><input id="chk_importar" type="checkbox"></div>
                    <div class="clearfix"></div>
                    <div class="col-4"><label for="chk_exportar">Exportar</label></div>
                    <div class="col-4"><input id="chk_exportar" type="checkbox"></div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="row d-none" id="div-permisos">

            </div>
        </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear permisos' : 'Editar permisos'])

<script type="application/javascript">
    var permisos = <?= json_encode($menus_asignados) ?>;

    setTimeout(() => {
        $('#id_menu, #id_menu_to').select2('destroy');

        $('#id_menu').multiselect({
            afterMoveToRight: function($left, $right, $options) {
                $.each($options, (index, element) => {
                    permisos[element.value] = {
                        'nombre': element.text,
                        'permisos': {
                            'crear': 0,
                            'editar': 0,
                            'ver': 1,
                            'importar': 0,
                            'exportar': 0
                        }
                    };
                });

                updatePermiso();
            },
            afterMoveToLeft: function($left, $right, $options) {
                $.each($options, (index, element) => {
                    delete permisos[element.value];
                });

                updatePermiso();
            }
        });

        updatePermiso();
    }, 100);

    $('#id_menu_to').change(function() {
        $.each($(this).val(), (index, menu) => {
            $('#chk_crear').prop('checked', permisos[menu]['permisos']['crear'] ? true : false);
            $('#chk_editar').prop('checked', permisos[menu]['permisos']['editar'] ? true : false);
            $('#chk_importar').prop('checked', permisos[menu]['permisos']['importar'] ? true : false);
            $('#chk_exportar').prop('checked', permisos[menu]['permisos']['exportar'] ? true : false);
        });
    });

    $('#chk_crear').change(function() {
        let checked = this.checked;
        $('#id_menu_to option:selected').each(function() {
            permisos[$(this).val()]['permisos']['crear'] = checked ? 1 : 0;
        });

        updatePermiso();
    });

    $('#chk_editar').change(function() {
        let checked = this.checked;
        $('#id_menu_to option:selected').each(function() {
            permisos[$(this).val()]['permisos']['editar'] = checked ? 1 : 0;
        });

        updatePermiso();
    });

    $('#chk_importar').change(function() {
        let checked = this.checked;
        $('#id_menu_to option:selected').each(function() {
            permisos[$(this).val()]['permisos']['importar'] = checked ? 1 : 0;
        });

        updatePermiso();
    });

    $('#chk_exportar').change(function() {
        let checked = this.checked;
        $('#id_menu_to option:selected').each(function() {
            permisos[$(this).val()]['permisos']['exportar'] = checked ? 1 : 0;
        });

        updatePermiso();
    });

    function updatePermiso() {
        let inputs = '';
        $.each(permisos, (index, permiso) => {
            if(typeof permiso !== 'undefined') {
                inputs += `
                    <div class="col-2"><input type="integer" class="form-control" name="id_menu[]" value="${index}"></div>
                    <div class="col-2"><input type="integer" class="form-control" name="crear[]" value="${permiso['permisos']['crear']}"></div>
                    <div class="col-2"><input type="integer" class="form-control" name="editar[]" value="${permiso['permisos']['editar']}"></div>
                    <div class="col-2"><input type="integer" class="form-control" name="ver[]" value="${permiso['permisos']['ver']}"></div>
                    <div class="col-2"><input type="integer" class="form-control" name="importar[]" value="${permiso['permisos']['importar']}"></div>
                    <div class="col-2"><input type="integer" class="form-control" name="exportar[]" value="${permiso['permisos']['exportar']}"></div>
                `;
            }
        });
        // console.log(inputs)
        $('#div-permisos').html(inputs);
    }
</script>