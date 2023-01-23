@include('partials.grid', [
    'title' => 'Cotizaciones',
    'create' => $create,
    'route' => 'quotes',
    'status' => $status,
    'btnOptionsCreate' => [
        "title" => 'Nueva cotización',
        'header-class' => 'bg-primary bg-opacity-75 text-white',
        "modal-size" => "modal-fullscreen",
    ],
    'headers' => [
        ['name' => 'id_cotizacion', 'label' => '#', 'align' => 'text-end'],
        ['name' => 'ot_trabajo', 'label' => 'OT trabajo'],
        ['name' => 'id_tercero_cliente', 'label' => 'Cliente', 'options' => $clientes],
        ['name' => 'tblEstacion', 'label' => 'Sitio', 'foreign' => 'nombre'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'class' => 'input-date'],
        ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'class' => 'input-date'],
        ['name' => 'id_dominio_prioridad', 'label' => 'Prioridad', 'options' => $prioridades],
        ['name' => 'id_dominio_estado', 'label' => 'Proceso', 'options' => $procesos],
        ['name' => 'id_tercero_responsable', 'label' => 'Responsable', 'options' => $contratistas],
        ['name' => '', 'label' => 'Acciones', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver cotización',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info bg-opacity-75 text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar cotización',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning bg-opacity-75 text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])