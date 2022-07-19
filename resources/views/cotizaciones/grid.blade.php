@include('partials.grid', [
    'title' => 'Cotizaciones',
    'create' => $create,
    'route' => 'quotes',
    'btnOptionsCreate' => [
        "title" => 'Nueva cotización',
        'header-class' => 'bg-warning text-white',
        "modal-size" => "modal-fullscreen",
        "route" => route("quotes.create"),
    ],
    'headers' => [
        ['name' => 'id_cotizacion', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'ot_trabajo', 'label' => 'OT trabajo', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_cliente', 'label' => 'Cliente', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_estacion', 'label' => 'Estación', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_prioridad', 'label' => 'Prioridad', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_proceso', 'label' => 'Proceso', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_responsable_cliente', 'label' => 'Responsable', 'align' => 'text-end', 'col' => 'col-1'],
        // ['name' => 'estado', 'label' => 'Proceso', 'col' => 1, 'options' => $estados],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver cotización',
                'modal-view-size' => 'modal-fullscreen',
                'edit' => $edit,
                'modal-edit-title' => 'Editar cotización',
                'modal-edit-size' => 'modal-fullscreen',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])