@include('partials.grid', [
    'title' => 'Cotizaciones',
    'create' => $create,
    'route' => 'quotes',
    'btnOptionsCreate' => [
        "title" => 'Nueva cotización',
        "modal-size" => "modal-xl",
        "route" => route("quotes.create"),
    ],
    'headers' => [
        ['name' => 'id_cotizacion', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'codigo_cotizacion', 'label' => 'Código cotización', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_cliente', 'label' => 'Cliente', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_estacion', 'label' => 'Estación', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_solicitud', 'label' => 'Fecha solicitud', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'fecha_envio', 'label' => 'Fecha envió', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_prioridad', 'label' => 'Prioridad', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_proceso', 'label' => 'Proceso', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_responsable_cliente', 'label' => 'Responsable', 'align' => 'text-end', 'col' => 'col-1'],
        // ['name' => 'documento', 'label' => 'Identificación', 'col' => 'col-2'],
        // ['name' => 'full_name', 'label' => 'Nombre', 'col' => 'col-3'],
        // ['name' => 'ciudad', 'label' => 'Ciudad', 'col' => 'col-2'],
        // ['name' => 'id_dominio_tipo_tercero', 'label' => 'Tipo tercero', 'col' => 'col-2', 'options' => $tipo_terceros],
        // ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver cotización',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar cotización',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])