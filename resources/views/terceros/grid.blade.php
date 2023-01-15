@include('partials.grid', [
    'title' => 'Terceros',
    'create' => $create,
    'route' => 'clients',
    'btnOptionsCreate' => [
        "title" => 'Nuevo tercero',
        "modal-size" => "modal-xl",
    ],
    'headers' => [
        ['name' => 'id_tercero', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'documento', 'label' => 'Identificación', 'col' => 'col-2'],
        ['name' => 'full_name', 'label' => 'Nombre o Razón social', 'col' => 'col-3'],
        ['name' => 'ciudad', 'label' => 'Ciudad', 'col' => 'col-2'],
        ['name' => 'id_dominio_tipo_tercero', 'label' => 'Tipo tercero', 'col' => 'col-2', 'options' => $tipo_terceros],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver tercero',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar tercero',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])