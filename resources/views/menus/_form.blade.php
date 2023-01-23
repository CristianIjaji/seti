<?php
    $create = isset($profile->id_dominio_tipo_tercero) ? false : true;
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
                <label for="id_dominio_tipo_tercero" class="required">Tipo tercero</label>
                @if ($edit)
                    <select name="id_dominio_tipo_tercero" id="id_dominio_tipo_tercero" style="width: 100%" @if ($edit) required @else disabled @endif>
                        <option value="">Elegir tipo tercero</option>
                        @foreach ($tipo_terceros as $id => $nombre)
                            <option value="{{ $id }}" {{ old('id_dominio_tipo_tercero', $profile->id_dominio_tipo_tercero) == $id ? 'selected' : '' }}>
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="id_dominio_tipo_tercero" value="{{ $profile->tbltipotercero->nombre }}" disabled>
                @endif
            </div>
            <div class="form-group col-12 col-md-6">
                <ul class="list-unstyled accordion border rounded p-3 user-select-none">
                    @foreach ($menus_disponibles as $menu)
                        <li class="accordion-item row menu-item-perfil border-0" @isset($menu->submenu) data-child="{{ $menu->id_menu }}" @endisset>
                            @if (isset($menu->submenu))
                                <span
                                    class="p-0 border-0 btn show-more col-1 pt-1"
                                    data-bs-target="#_{{ $menu->id_menu }}"
                                    data-bs-toggle="collapse"
                                >
                                    <i class="text-center fs-6 fa-solid fa-angle-up"></i>
                                </span>
                            @else
                                <div class="col-1"></div>
                            @endif

                            <div class="col-11 p-0">
                                <input type="checkbox" id="{{ $menu->id_menu }}" class="btn btn-out-primary menu_tercero {{ (!$create && !$edit ? 'disabled' : '') }}"
                                    {{ (!$create && $menus_asignados->where('id_menu', $menu->id_menu)->first() ? 'checked' : '') }}
                                >
                                <label class="btn p-0 border-0" style="color: var(--bs-bg-color)"
                                    @isset($menu->submenu)
                                        {{-- data-bs-target="#_{{ $menu->id_menu }}"
                                        data-bs-toggle="collapse" --}}
                                    @endisset
                                >
                                    {{ $menu->nombre }}
                                </label>
                            </div>
                            
                            @isset($menu->submenu)
                                <ul class="list-unstyled ms-4 ps-4 accordion-collapse collapse show" id="_{{ $menu->id_menu }}">
                                    @foreach ($menu->submenu as $submenu)
                                        <li class="accordion-item menu-item-perfil border-0 row"
                                            data-parent="{{ $submenu->id_menu_padre }}"
                                        >
                                            <div class="col-12">
                                                <input type="checkbox" id="{{ $submenu->id_menu }}" class="btn btn-out-primary menu_tercero {{ (!$create && !$edit ? 'disabled' : '') }}"
                                                    {{ (!$create && $menus_asignados->where('id_menu', $submenu->id_menu)->first() ? 'checked' : '') }}
                                                >
                                                <label class="btn p-0 border-0" style="color: var(--bs-bg-color)">{{ $submenu->nombre }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endisset
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="form-group col-12 col-md-6 m-auto mx-auto">
                <ul class="list-unstyled user-select-none">
                    <li>
                        <input type="checkbox" id="ckb-all" class="btn btn-out-primary opciones-menu-parent disabled">
                        <label for="ckb-all" class="p-0 border-0 btn disabled" style="color: var(--bs-bg-color)">Seleccionar todos</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-crear" class="btn btn-out-primary opciones-menu disabled">
                        <label for="ckb-crear" class="p-0 border-0 btn disabled" style="color: var(--bs-bg-color)">Crear</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-editar" class="btn btn-out-primary opciones-menu disabled">
                        <label for="ckb-editar" class="p-0 border-0 btn disabled" style="color: var(--bs-bg-color)">Editar</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-importar" class="btn btn-out-primary opciones-menu disabled">
                        <label for="ckb-importar" class="p-0 border-0 btn disabled" style="color: var(--bs-bg-color)">Importar</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-exportar" class="btn btn-out-primary opciones-menu disabled">
                        <label for="ckb-exportar" class="p-0 border-0 btn disabled" style="color: var(--bs-bg-color)">Exportar</label>
                    </li>
                </ul>
            </div>

            <div class="row d-none" id="div-permisos"></div>
        </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear permisos' : 'Editar permisos'])

<script type="application/javascript">
    var list_permisos = ['crear', 'editar', 'ver', 'importar', 'exportar'];
    var id_menu = 0;
    var permisos = <?= json_encode($menus_asignados) ?>;

    updatePermiso();

    $('.menu_tercero').click(function() {
        id_menu = parseInt($(this).attr('id'));
        let checked = $(this).prop('checked');
        let child = $(this).closest('.accordion-item').data('child');
        let parent = $(this).closest('.accordion-item').data('parent');

        $.each(list_permisos, (i, p) => {
            setPermiso(id_menu, p, (i !== 2 ? false : checked));
        });

        if(typeof child !== 'undefined') {
            $.each($(`[data-parent="${child}"] > div > .menu_tercero `), (i, e) => {
                $(e).prop('checked', checked);
                $.each(list_permisos, (i, p) => {
                    setPermiso(parseInt($(e).attr('id')), p, (i !== 2 ? false : checked));
                });
            });
        }

        if(typeof parent !== 'undefined') {
            let keepChecked = false;
            $.each($(`[data-parent="${parent}"] > div > .menu_tercero`), (i, e) => {
                if($(e).prop('checked')) {
                    keepChecked = true;
                }
            });

            $(`[data-child="${parent}"] > div > .menu_tercero`).prop('checked', keepChecked);
            setPermiso(parent, list_permisos[2], keepChecked);
        }

        updatePermiso();
        showPermiso(getPermiso(id_menu));

        $('.opciones-menu, .opciones-menu-parent').closest('li').children().removeClass('disabled');

        if(!checked) {
            id_menu = null;
            $('.opciones-menu, .opciones-menu-parent').closest('li').children().addClass('disabled');
        }
    });

    $('.opciones-menu').click(function() {
        changesPermiso($(this));
    });

    $('.menu-item-perfil label').click(function() {
        $('.menu-item-perfil label').each((i, e) => {
            $(e).removeClass('text-success');
        });
        
        $(this).toggleClass('text-success');
        id_menu = $(this).closest('div').find('input').attr('id');

        showPermiso(getPermiso(id_menu));
    });

    $('#ckb-all').click(function() {
        $('.opciones-menu').each((i, e) => {
            $(e).prop('checked', $(this).prop('checked'));
            changesPermiso($(e));
        });
    });

    function showPermiso(data_permiso) {
        if(typeof data_permiso['permiso'] !== 'undefined') {
            $('#ckb-crear').prop('checked', data_permiso['permiso'][list_permisos[0]]);
            $('#ckb-editar').prop('checked', data_permiso['permiso'][list_permisos[1]]);
            $('#ckb-importar').prop('checked', data_permiso['permiso'][list_permisos[3]]);
            $('#ckb-exportar').prop('checked', data_permiso['permiso'][list_permisos[4]]);
            $('.opciones-menu, .opciones-menu-parent').closest('li').children().removeClass('disabled');
        } else {
            $('.opciones-menu, .opciones-menu-parent').prop('checked', false);
            $('.opciones-menu, .opciones-menu-parent').closest('li').children().addClass('disabled');
        }
    }

    function getPermiso(id_menu) {
        let data_permiso = {};
        $.each(permisos, (index, permiso) => {
            if(parseInt(id_menu) === parseInt(permiso['id_menu'])) {
                permiso[list_permisos[0]] = (permiso[list_permisos[0]]);
                permiso[list_permisos[1]] = (permiso[list_permisos[1]]);
                permiso[list_permisos[2]] = (permiso[list_permisos[2]]);
                permiso[list_permisos[3]] = (permiso[list_permisos[3]]);
                permiso[list_permisos[4]] = (permiso[list_permisos[4]]);

                data_permiso = {
                    index,
                    permiso
                };

                return false;
            }
        });

        return data_permiso;
    }

    function setPermiso(id_menu, permiso, value) {
        let data_permiso = getPermiso(id_menu);

        if(typeof permisos[data_permiso['index']] !== 'undefined') {
            permisos[data_permiso['index']][permiso] = value;
        } else {
            permisos.push({
                id_menu,
                'crear' : false,
                'editar' : false,
                'ver' : true,
                'importar' : false,
                'exportar' : false,
            });
        }
    }

    function updatePermiso() {
        let inputs = '';
        $.each(permisos, (index, permiso) => {
            let add = false;
            if(typeof permiso !== 'undefined') {
                $.each(list_permisos, (i, p) => {
                    if(typeof permiso[p] !== 'boolean') {
                        permiso[p] = (permiso[p] === '<i class="fa-solid fa-check fw-bolder fs-4 text-success"></i>' ? true : false);
                    }

                    if(permiso[p]) {
                        add = true;
                    }
                });

                if(add) {
                    inputs += `
                        <div class="col-2"><input type="integer" class="form-control" name="id_menu[]" value="${permiso['id_menu']}"></div>
                        <div class="col-2"><input type="integer" class="form-control" name="crear[]" value="${permiso[list_permisos[0]] ? 1 : 0}"></div>
                        <div class="col-2"><input type="integer" class="form-control" name="editar[]" value="${permiso[list_permisos[1]]  ? 1 : 0}"></div>
                        <div class="col-2"><input type="integer" class="form-control" name="ver[]" value="${permiso[list_permisos[2]]  ? 1 : 0}"></div>
                        <div class="col-2"><input type="integer" class="form-control" name="importar[]" value="${permiso[list_permisos[3]]  ? 1 : 0}"></div>
                        <div class="col-2"><input type="integer" class="form-control" name="exportar[]" value="${permiso[list_permisos[4]]  ? 1 : 0}"></div>
                    `;
                }
            }
        });

        $('#div-permisos').html(inputs);
    }

    function changesPermiso(element) {
        if(id_menu > 0 && $(`.accordion #${id_menu}`).prop('checked') && $(element).attr('id') !== 'ckb-all') {
            let id_menu_padre = $(`.accordion .accordion-item[data-child="${id_menu}"]`).length ? id_menu : 0;
            let opcion = $(element).attr('id').replace('ckb-', '');

            if(id_menu_padre > 0) {
                $.each($(`[data-parent="${id_menu_padre}"] .menu_tercero `), (i, e) => {
                    if($(e).prop('checked')) {
                        setPermiso(parseInt($(e).attr('id')), opcion, $(element).prop('checked'));
                    }
                });

                updatePermiso();
            } else {
                setPermiso(id_menu, opcion, $(element).prop('checked'));
                updatePermiso();
                showPermiso(getPermiso(id_menu));
            }
        }
    }
</script>