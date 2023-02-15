@include('partials.grid', [
    'title' => 'Movimientos inventario',
    'create' => $create,
    'route' => 'moves',
    'btnOptionsCreate' => [
        "title" => 'Nuevo movimiento',
        'header-class' => 'bg-primary bg-opacity-75 text-white',
        "modal-size" => "modal-fullscreen",
    ],
    'headers' => [
        ['name' => 'id_movimiento', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'created_at', 'label' => 'Fecha', 'class' => 'input-date', 'col' => 'col-1'],
        ['name' => 'id_tercero_entrega', 'label' => 'Entrega', 'col' => 'col-2', 'options' => $entregan],
        ['name' => 'id_tercero_recibe', 'label' => 'Recibe', 'col' => 'col-2', 'options' => $reciben],
        ['name' => 'id_dominio_tipo_movimiento', 'label' => 'Tipo movimiento', 'col' => 'col-2', 'options' => $tipo_movimientos],
        ['name' => 'total', 'label' => 'Total movimiento', 'align' => 'text-end'],
        ['name' => 'saldo', 'label' => 'Saldo movimiento', 'align' => 'text-end'],
        ['name' => 'documento', 'label' => 'Documento', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver movmiento',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar movmiento',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning bg-opacity-75 text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])