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
                                        data-bs-target="#_{{ $menu->id_menu }}"
                                        data-bs-toggle="collapse"
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
                        <input type="checkbox" id="ckb-crear" class="btn btn-out-primary opciones-menu {{ (!$create && !$edit ? 'disabled' : '') }}">
                        <label for="ckb-crear" class="p-0 border-0 btn" style="color: var(--bs-bg-color)">Crear</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-editar" class="btn btn-out-primary opciones-menu {{ (!$create && !$edit ? 'disabled' : '') }}">
                        <label for="ckb-editar" class="p-0 border-0 btn" style="color: var(--bs-bg-color)">Editar</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-importar" class="btn btn-out-primary opciones-menu {{ (!$create && !$edit ? 'disabled' : '') }}">
                        <label for="ckb-importar" class="p-0 border-0 btn" style="color: var(--bs-bg-color)">Importar</label>
                    </li>
                    <li>
                        <input type="checkbox" id="ckb-exportar" class="btn btn-out-primary opciones-menu {{ (!$create && !$edit ? 'disabled' : '') }}">
                        <label for="ckb-exportar" class="p-0 border-0 btn" style="color: var(--bs-bg-color)">Exportar</label>
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
            if(i !== 2) {
                setPermiso(id_menu, p, false);
            } else {
                setPermiso(id_menu, p, checked);
            }
        });

        if(typeof child !== 'undefined') {
            $.each($(`[data-parent="${child}"] > div > .menu_tercero `), (i, e) => {
                $(e).prop('checked', checked);
                setPermiso(parseInt($(e).attr('id')), list_permisos[2], checked);
            });
        }

        if(typeof parent !== 'undefined') {
            let keepChecked = false;
            $.each($(`[data-parent="${parent}"] > div > .menu_tercero`), (i, e) => {
                if($(e).prop('checked')) {
                    keepChecked = true
                }
            });

            $(`[data-child="${parent}"] > div > .menu_tercero`).prop('checked', keepChecked);
            setPermiso(parent, list_permisos[2], checked);
        }

        updatePermiso();
        showPermiso(getPermiso(id_menu));
    });

    $('.opciones-menu').click(function() {
        if(id_menu > 0 && $(`.accordion #${id_menu}`).prop('checked')) {
            let opcion = $(this).attr('id').replace('ckb-', '');
            setPermiso(id_menu, opcion, $(this).prop('checked'));
            updatePermiso();
            showPermiso(getPermiso(id_menu));
        }
    });

    $('.menu-item-perfil label').click(function() {
        $('.menu-item-perfil label').each((i, e) => {
            $(e).removeClass('text-success');
        });
        
        $(this).toggleClass('text-success');

        $(this).closest('li').find('.show-more').click();

        id_menu = $(this).closest('div').find('input').attr('id');
        showPermiso(getPermiso(id_menu));

        // if(typeof $(this).closest('.menu-item-perfil').data('parent') !== 'undefined') {
        //     let parent = $(this).closest('.menu-item-perfil').data('parent');
        //     $(`[data-child="${parent}"] > div > label`).toggleClass('text-success');
        // }

        // if(typeof $(this).closest('.menu-item-perfil').data('child') !== 'undefined') {
        //     let childs = $(this).closest('.menu-item-perfil').data('child');

        //     $(`[data-parent="${childs}"] > div > label`).toggleClass('text-success');
        // }
    });

    function showPermiso(data_permiso) {
        if(typeof data_permiso['permiso'] !== 'undefined') {
            $('#ckb-crear').prop('checked', data_permiso['permiso'][list_permisos[0]]);
            $('#ckb-editar').prop('checked', data_permiso['permiso'][list_permisos[1]]);
            $('#ckb-importar').prop('checked', data_permiso['permiso'][list_permisos[3]]);
            $('#ckb-exportar').prop('checked', data_permiso['permiso'][list_permisos[4]]);
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
</script>