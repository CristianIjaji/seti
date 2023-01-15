@include('partials.grid', [
    'title' => 'Parámetros',
    'create' => $create,
    'route' => 'params',
    'btnOptionsCreate' => [
        "title" => 'Nuevo parámetro',
        "modal-size" => "modal-md",
    ],
    'headers' => [
        ['name' => 'id_parametro_aplicacion', 'label' => '#', 'col' => 'col-1', 'align' => 'text-end'],
        ['name' => 'llave', 'label' => 'Llave', 'col' => 'col-4'],
        ['name' => 'tbldominio', 'label' => 'Valor', 'col' => 'col-4', 'foreign' => 'nombre'],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver parámetro',
                'modal-view-size' => 'modal-md',
                'edit' => $edit,
                'modal-edit-title' => 'Editar parámetro',
                'modal-edit-size' => 'modal-md',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])