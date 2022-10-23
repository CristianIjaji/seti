@include('partials.grid', [
    'title' => 'Usuarios',
    'create' => $create,
    'route' => 'users',
    'btnOptionsCreate' => [
        "title" => 'Nuevo usuario',
        "modal-size" => "modal-md",
        "route" => route("users.create"),
    ],
    'headers' => [
        ['name' => 'id_usuario', 'label' => '#', 'col' => 'col-1', 'align' => 'text-end'],
        // ['name' => 'logo_image', 'label' => 'logo', 'type' => 'img', 'html' => true],
        ['name' => 'usuario', 'label' => 'Usuario', 'col' => 'col-2'],
        ['name' => 'tbltercero', 'label' => 'Tercero', 'col' => 'col-3', 'foreign' => 'full_name'],
        ['name' => 'tbltercero', 'label' => 'Email', 'col' => 'col-3', 'foreign' => 'correo'],
        ['name' => 'estado', 'label' => 'Estado', 'align' => 'text-center', 'html' => true, 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver usuario',
                'modal-view-size' => 'modal-md',
                'edit' => $edit,
                'modal-edit-title' => 'Editar usuario',
                'modal-edit-size' => 'modal-md',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])