@include('partials.grid', [
    'title' => 'Consolidados',
    'create' => $create,
    'route' => 'deals',
    'btnOptionsCreate' => [
        "title" => 'Nuevo consolidado',
        'header-class' => 'bg-primary bg-opacity-75 text-white',
        "modal-size" => "modal-fullscreen",
    ],
    'headers' => [
        ['name' => 'id_consolidado', 'label' => '#', 'col' => 'col-2', 'align' => 'text-end'],
        ['name' => 'id_tercero_cliente', 'label' => 'Cliente', 'col' => 'col-4', 'options' => $clientes],
        ['name' => 'mes', 'label' => 'Mes', 'col' => 'col-3', 'align' => 'text-capitalize',
            'class' => 'input-months text-capitalize', 'data' => ['format' => 'YYYY-MMMM', 'viewmode' => "months"]
        ],
        ['name' => 'id_dominio_estado', 'label' => 'Estado', 'col' => 'col-2', 'options' => $estados],
        ['name' => '', 'label' => 'Acciones', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver consolidado',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-primary bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar consolidado',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-primary bg-opacity-75 text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])