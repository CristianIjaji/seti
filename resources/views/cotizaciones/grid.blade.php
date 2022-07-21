@include('partials.grid', [
    'title' => 'Cotizaciones',
    'create' => $create,
    'route' => 'quotes',
    'btnOptionsCreate' => [
        "title" => 'Nueva cotización',
        'header-class' => 'bg-secondary text-white',
        "modal-size" => "modal-fullscreen",
        "route" => route("quotes.create"),
    ],
    'headers' => [
        ['name' => 'id_cotizacion', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'ot_trabajo', 'label' => 'OT trabajo', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'tblCliente', 'label' => 'Cliente', 'align' => 'text-end', 'col' => 'col-1', 'foreign' => 'full_name'],
        ['name' => 'tblZona', 'label' => 'Estación', 'align' => 'text-end', 'col' => 'col-1', 'foreign' => 'nombre'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_prioridad', 'label' => 'Prioridad', 'align' => 'text-end', 'col' => 'col-1', 'options' => $prioridades],
        ['name' => 'estado', 'label' => 'Proceso', 'align' => 'text-end', 'col' => 'col-1', 'options' => $procesos],
        ['name' => 'id_responsable_cliente', 'label' => 'Responsable', 'align' => 'text-end', 'col' => 'col-1', 'options' => $contratistas],
        // ['name' => 'estado', 'label' => 'Proceso', 'col' => 1, 'options' => $estados],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver cotización',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar cotización',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])