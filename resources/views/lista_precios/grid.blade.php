@include('partials.grid', [
    'title' => 'Lista precios',
    'create' => $create,
    'route' => 'priceList',
    'btnOptionsCreate' => [
        "title" => 'Nueva lista precio',
        "modal-size" => "modal-xl",
    ],
    'headers' => [
        ['name' => 'id_lista_precio', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_tercero_cliente', 'label' => 'Cliente', 'col' => 'col-3', 'options' => $clientes],// Nombre de la llave
        ['name' => 'id_dominio_tipo_item', 'label' => 'Tipo ítem', 'col' => 'col-2', 'options' => $listaTipoItemPrecio],
        ['name' => 'codigo', 'label' => 'Código', 'col' => 'col-1'],
        ['name' => 'descripcion', 'label' => 'Descripción', 'col' => 'col-3'],
        ['name' => 'unidad', 'label' => 'Unidad', 'col' => 'col-1'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'col' => 'col-1','align'=>'text-end'],
        ['name' => 'valor_unitario', 'label' => 'Valor Unitario', 'col' => 'col-1','align'=>'text-end'],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver lista precio',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar lista Precio',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])