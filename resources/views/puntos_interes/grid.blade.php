@include('partials.grid', [
    'title' => 'Puntos interés',
    'create' => $create,
    'export' => $export,
    'import' => $import,
    'route' => 'sites',
    'btnOptionsCreate' => [
        "title" => 'Nuevo punto interés',
        "modal-size" => "modal-xl",
        "route" => route("sites.create"),
    ],
    'headers' => [
        ['name' => 'id_punto_interes', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'id_cliente', 'label' => 'Cliente', 'col' => 'col-2', 'options' => $clientes],
        ['name' => 'id_zona', 'label' => 'Zona', 'col' => 'col-2', 'options' => $zonas],
        ['name' => 'nombre', 'label' => 'Nombre Sitio', 'col' => 'col-2'],
        ['name' => 'id_tipo_transporte', 'label' => 'Tipo transporte', 'col' => 'col-1', 'options' => $transportes],
        ['name' => 'id_tipo_accesso', 'label' => 'Tipo acceso', 'col' => 'col-1', 'options' => $accesos],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver punto interés',
                'modal-view-size' => 'modal-xl',
                'edit' => $edit,
                'modal-edit-title' => 'Editar punto interés',
                'modal-edit-size' => 'modal-xl',
            ]
        ]]
    ],
    'filters' => true,
    'models' => $model
])