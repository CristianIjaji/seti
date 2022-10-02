@include('partials.grid', [
    'title' => 'Lista precios',
    'create' => $create,
    'route' => 'priceList',
    'btnOptionsCreate' => [
        "title" => 'Nueva lista precio',
        "modal-size" => "modal-lg",
        "route" => route("priceList.create"),
    ],
    'headers' => [
        ['name' => 'id_lista_precio', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'tbltercerocliente', 'label' => 'Cliente', 'col' => 'col-1','foreign' => 'full_name'],// Nombre de la llave
        ['name' => 'id_tipo_item', 'label' => 'Tipo ítem', 'col' => 'col-1', 'options' => $listaTipoItemPrecio],
        ['name' => 'codigo', 'label' => 'Código', 'col' => 'col-1'],
        ['name' => 'unidad', 'label' => 'Unidad', 'col' => 'col-1'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'col' => 'col-1','align'=>'text-end'],
        ['name' => 'valor_unitario', 'label' => 'V. Unitario', 'col' => 'col-1','align'=>'text-end'],
        // ['name' => 'id_dominio_tipo_tercero', 'label' => 'Tipo tercero', 'col' => 'col-2', 'options' => $tipo_terceros],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver lista precio',
                'modal-view-size' => 'modal-lg',
                'edit' => $edit,
                'modal-edit-title' => 'Editar lista Precio',
                'modal-edit-size' => 'modal-lg',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])