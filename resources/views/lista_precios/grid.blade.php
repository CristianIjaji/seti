@include('partials.grid', [
    'title' => 'Lista precios',
    'create' => $create,
    'route' => 'price_list',
    'btnOptionsCreate' => [
        "title" => 'Nueva Lista precio',
        "modal-size" => "modal-xl",
        "route" => route("price_list.create"),
    ],
    'headers' => [
        ['name' => 'id_lista_precio', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_cliente', 'label' => 'Cliente', 'col' => 'col-1'],
        ['name' => 'id_tipo_item', 'label' => 'Tipo ítem', 'col' => 'col-1'],
        ['name' => 'codio', 'label' => 'Código', 'col' => 'col-1'],
        ['name' => 'id_unidad', 'label' => 'Unidad', 'col' => 'col-1'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'col' => 'col-1'],
        ['name' => 'valor_unitario', 'label' => 'V. Unitario', 'col' => 'col-1'],
        // ['name' => 'id_dominio_tipo_tercero', 'label' => 'Tipo tercero', 'col' => 'col-2', 'options' => $tipo_terceros],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver Lista precio',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar Lista Precio',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])