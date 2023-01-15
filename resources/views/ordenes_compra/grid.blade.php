@include('partials.grid', [
    'title' => 'Orden compra',
    'create' => $create,
    'route' => 'purchases',
    'btnOptionsCreate' => [
        "title" => 'Nueva orden',
        "modal-size" => "modal-fullscreen",
        'header-class' => 'bg-primary bg-opacity-75 text-white',
    ],
    'headers' => [
        ['name' => 'id_orden_compra','label' => '#', 'align' => 'text-end'],
        ['name' => 'documento', 'label' => 'Documento', 'col' => 'col-2'],
        ['name' => 'full_name', 'label' => 'Nombre o Razón social', 'col' => 'col-3'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver orden',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar orden',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning bg-opacity-75 text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])