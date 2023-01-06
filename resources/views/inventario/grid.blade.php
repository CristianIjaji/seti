@include('partials.grid', [
    'title' => 'Inventario',
    'create' => $create,
    'route' => 'stores',
    'btnOptionsCreate' => [
        "title" => 'Nuevo producto',
        "modal-size" => "modal-xl",
        "route" => route("stores.create"),
    ],
    'headers' => [
        ['name' => 'id_inventario', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'tblterceroalmacen', 'label' => 'Almacén', 'col' => 'col-2', 'foreign' => 'full_name'],
        ['name' => 'clasificacion', 'label' => 'Clasificación', 'col' => 'col-2'],
        ['name' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-2'],
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
                'edit' => $edit,
                'modal-edit-title' => 'Editar producto',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])