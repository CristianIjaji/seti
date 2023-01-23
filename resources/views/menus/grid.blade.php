@include('partials.grid', [
    'title' => 'Perfiles',
    'create' => $create,
    'route' => 'profiles',
    'btnOptionsCreate' => [
        "title" => 'Nuevo perfil',
        "modal-size" => "modal-lg",
    ],
    'headers' => [
        ['name' => 'id_dominio_tipo_tercero', 'label' => 'Tipo tercero', 'options' => $tipo_terceros],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver perfil',
                'modal-view-size' => 'modal-lg',
                'edit' => $edit,
                'modal-edit-title' => 'Editar perfil',
                'modal-edit-size' => 'modal-lg',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])