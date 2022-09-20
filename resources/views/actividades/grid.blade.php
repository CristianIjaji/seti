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
        ['name' => 'id_encargado_cliente', 'label' => 'Cliente'],
        ['name' => 'ot', 'label' => 'OT'],
        ['name' => 'id_tipo_actividad', 'label' => 'Tipo Actividad'],
        ['name' => 'id_estacion', 'label' => 'Sitio'],
        ['name' => 'id_subsistema', 'label' => 'Subsistema'],
        ['name' => 'descripcion', 'label' => 'Descripción'],
        ['name' => 'id_resposable_contratista', 'label' => 'Responsable'],
        ['name' => 'permiso_acceso', 'label' => 'Permisos'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha Solicitud'],
        ['name' => 'id_estado_actividad', 'label' => 'Estado'],
        ['name' => 'valor', 'label' => 'Valor'],

        // ['name' => 'id_actividad', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        // ['name' => 'ot_trabajo', 'label' => 'OT trabajo', 'col' => 'col-1'],
        // ['name' => 'tblCliente', 'label' => 'Cliente', 'col' => 'col-1', 'foreign' => 'full_name'],
        // ['name' => 'tblEstacion', 'label' => 'Estación', 'col' => 'col-1', 'foreign' => 'nombre'],
        // ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'col' => 'col-1', 'class' => 'input-date'],
        // ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'col' => 'col-1', 'class' => 'input-date'],
        // ['name' => 'id_prioridad', 'label' => 'Prioridad', 'col' => 'col-1', 'options' => $prioridades],
        // ['name' => 'estado', 'label' => 'Proceso', 'col' => 'col-1', 'options' => $procesos],
        // ['name' => 'id_responsable_cliente', 'label' => 'Responsable', 'col' => 'col-1', 'options' => $contratistas],
        // ['name' => 'estado', 'label' => 'Proceso', 'col' => 1, 'options' => $estados],
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