@include('partials.grid', [
    'title' => 'Cotizaciones',
    'create' => $create,
    'route' => 'quotes',
    'status' => $status,
    'btnOptionsCreate' => [
        "title" => 'Nueva cotización',
        'header-class' => 'bg-primary text-white',
        "modal-size" => "modal-fullscreen",
        "route" => route("quotes.create"),
    ],
    'headers' => [
        ['name' => 'id_cotizacion', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'ot_trabajo', 'label' => 'OT trabajo', 'col' => 'col-1'],
        ['name' => 'id_cliente', 'label' => 'Cliente', 'col' => 'col-1', 'options' => $clientes],
        ['name' => 'tblEstacion', 'label' => 'Estación', 'col' => 'col-1', 'foreign' => 'nombre'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'col' => 'col-1', 'class' => 'input-date'],
        ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'col' => 'col-1', 'class' => 'input-date'],
        ['name' => 'id_prioridad', 'label' => 'Prioridad', 'col' => 'col-1', 'options' => $prioridades],
        ['name' => 'estado', 'label' => 'Proceso', 'col' => 'col-1', 'options' => $procesos],
        ['name' => 'id_responsable_cliente', 'label' => 'Responsable', 'col' => 'col-1', 'options' => $contratistas],
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