@include('partials.grid', [
    'title' => 'Inventario',
    'create' => $create,
    'route' => 'stores',
    'btnOptionsCreate' => [
        "title" => 'Nuevo producto',
        'header-class' => 'bg-primary bg-opacity-75 text-white',
        "modal-size" => "modal-xl",
    ],
    'headers' => [
        ['name' => 'id_inventario', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_tercero_almacen', 'label' => 'Almacén', 'col' => 'col-2', 'options' => $almacenes],
        ['name' => 'id_dominio_clasificacion', 'label' => 'Clasificación', 'col' => 'col-2', 'options' => $clasificaciones],
        ['name' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-3'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'unidad', 'label' => 'Unidad', 'col' => 'col-1'],
        ['name' => 'valor_unitario', 'label' => 'Valor unitario', 'align' => 'text-end', 'col' => 'col-2'],
        ['name' => 'ubicacion', 'label' => 'ubicacion', 'col' => 'col-2'],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver producto',
                'modal-view-size' => 'modal-xl',
                'header-view-class' => 'bg-info bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar producto',
                'modal-edit-size' => 'modal-xl',
                'header-edit-class' => 'bg-warning bg-opacity-75 text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])