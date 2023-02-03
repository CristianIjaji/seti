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
        ['name' => 'id_tercero_almacen', 'label' => 'Almacen', 'col' => 'col-2', 'options' => $almacenes],
        ['name' => 'id_tercero_proveedor', 'label' => 'Almacen', 'col' => 'col-2', 'options' => $proveedores],
        ['name' => 'id_dominio_modalidad_pago', 'label' => 'Tipo pago', 'options' => $modosPago],
        ['name' => 'vencimiento', 'label' => 'Vencimiento', 'class' => 'input-date'],
        ['name' => 'cupo_actual', 'label' => 'Valor orden', 'align' => 'text-end'],
        ['name' => 'id_dominio_estado', 'label' => 'Estado', 'options' => $estados, 'col' => 'col-1'],
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