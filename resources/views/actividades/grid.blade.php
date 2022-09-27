@include('partials.grid', [
    'title' => 'Actividades',
    'create' => $create,
    'route' => 'activities',
    'status' => $status,
    'btnOptionsCreate' => [
        "title" => 'Nueva actividad',
        'header-class' => 'bg-primary text-white',
        "modal-size" => "modal-fullscreen",
        "route" => route("activities.create"),
    ],
    'headers' => [
        ['name' => 'id_actividad', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'ot', 'label' => 'OT', 'col' => 'col-1'],
        ['name' => 'id_encargado_cliente', 'label' => 'Cliente', 'options' => $clientes, 'col' => 'col-2'],
        ['name' => 'id_tipo_actividad', 'label' => 'Tipo trabajo', 'options' => $tipos_trabajo, 'col' => 'col-1'],
        ['name' => 'tblestacion', 'label' => 'Sitio', 'foreign' => 'nombre', 'col' => 'col-1'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'class' => 'input-date', 'col' => 'col-1'],
        ['name' => 'fecha_programacion', 'label' => 'Fecha prog.', 'class' => 'input-date', 'col' => 'col-1'],
        ['name' => 'id_estado_actividad', 'label' => 'Estado', 'options' => $estados_actividad, 'col' => 'col-1'],
        ['name' => 'id_resposable_contratista', 'label' => 'Responsable', 'options' => $contratistas, 'col' => 'col-2'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver actividad',
                'modal-view-size' => 'modal-fullscreen',
                'header-view-class' => 'bg-info text-white',
                'edit' => $edit,
                'modal-edit-title' => 'Editar actividad',
                'modal-edit-size' => 'modal-fullscreen',
                'header-edit-class' => 'bg-warning text-white',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])