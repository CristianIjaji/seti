@include('partials.grid', [
    'title' => 'Dominios',
    'create' => $create,
    'route' => 'domains',
    'btnOptionsCreate' => [
        "title" => 'Nuevo dominio',
        "modal-size" => "modal-md",
    ],
    'headers' => [
        ['name' => 'id_dominio', 'label' => '#', 'col' => 'col-1', 'align' => 'text-end'],
        ['name' => 'nombre', 'label' => 'Nombre', 'col' => 'col-2'],
        ['name' => 'id_dominio_padre', 'label' => 'Dominio padre', 'col' => 'col-3', 'options' => $dominios_padre],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1',],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver dominio',
                'modal-view-size' => 'modal-md',
                'edit' => $edit,
                'modal-edit-title' => 'Editar dominio',
                'modal-edit-size' => 'modal-md',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])