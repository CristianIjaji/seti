@include('partials.grid', [
    'title' => 'Consolidados',
    'create' => $create,
    'route' => 'deals',
    'btnOptionsCreate' => [
        "title" => 'Nuevo consolidado',
        'header-class' => 'bg-primary bg-opacity-75 text-white',
        "modal-size" => "modal-fullscreen",
        "route" => route("deals.create"),
    ],
    'headers' => [
        ['name' => 'id_consolidado', 'label' => '#', 'align' => 'text-end'],
        ['name' => 'id_cliente', 'label' => 'Cliente'],
        ['name' => 'id_mes', 'label' => 'Mes', 'align' => 'text-start'],
        ['name' => 'id_estado_consolidado', 'label' => 'Estado'],
        ['name' => '', 'label' => 'Acciones', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver consolidado',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar consolidado',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])