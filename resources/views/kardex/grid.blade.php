@include('partials.grid', [
    'title' => 'Kardex',
    'create' => $create,
    'route' => 'kardex',
    'btnOptionsCreate' => [
        "title" => 'Nuevo ítem',
        "modal-size" => "modal-xl",
        "route" => route("kardex.create"),
    ],
    'headers' => [
        ['name' => 'id_kardex', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_kardex', 'label' => 'Fecha registro', 'class' => 'input-date'],
        ['name' => 'tblinventario', 'label' => 'Producto', 'foreign' => 'descripcion'],
        ['name' => 'concepto', 'label' => 'Concepto'],
        ['name' => 'documento', 'label' => 'Documento'],
        ['name' => 'cantidad', 'label' => 'Cantidad'],
        ['name' => 'valor_unitario', 'label' => 'Valor unitario'],
        ['name' => 'valor_total', 'label' => 'Valor total'],
        ['name' => 'saldo_cantidad', 'label' => 'Saldo cantidad'],
        ['name' => 'saldo_valor_unitario', 'label' => 'Valor total'],
        ['name' => 'saldo_valor_total', 'label' => 'Valor total'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Detalle movimiento',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar ítem',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])