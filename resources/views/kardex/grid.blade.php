@include('partials.grid', [
    'title' => 'Kardex',
    'create' => $create,
    'route' => 'kardex',
    'btnOptionsCreate' => [
        "title" => 'Nuevo ítem',
        "modal-size" => "modal-xl",
    ],
    'headers' => [
        ['name' => 'id_kardex', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_kardex', 'label' => 'Fecha registro', 'class' => 'input-date'],
        ['name' => 'tblinventario', 'label' => 'Producto', 'class' => 'col-3', 'foreign' => 'descripcion'],
        ['name' => 'concepto', 'label' => 'Concepto', 'class' => 'col-2'],
        ['name' => 'documento', 'label' => 'Documento', 'align' => 'text-end'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'align' => 'text-end'],
        ['name' => 'valor_unitario', 'label' => 'Valor unitario', 'align' => 'text-end'],
        ['name' => 'valor_total', 'label' => 'Valor total', 'align' => 'text-end'],
        ['name' => 'saldo_cantidad', 'label' => 'Saldo cantidad'],
        ['name' => 'saldo_valor_unitario', 'label' => 'Valor total', 'align' => 'text-end'],
        ['name' => 'saldo_valor_total', 'label' => 'Valor total', 'align' => 'text-end'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Detalle movimiento',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar ítem',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])