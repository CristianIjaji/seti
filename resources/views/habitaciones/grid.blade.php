<?php
    $headers = [
        ['name' => 'id_habitacion', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'tbltercero', 'label' => 'Asociado', 'col' => 'col-2', 'foreign' => 'razon_social'],
        ['name' => 'nombre', 'label' => 'Habitación', 'col' => 'col-2'],
        ['name' => 'cantidad', 'label' => 'Cantidad', 'col' => 'col-3'],
        ['name' => 'estado', 'label' => 'Estado', 'html' => true, 'align' => 'text-center', 'options' => [0 => 'Inactivo', 1 => 'Activo'], 'col' => 'col-1'],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-2', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver habitación',
                'modal-view-size' => 'modal-md',
                'edit' => $edit,
                'modal-edit-title' => 'Editar habitación',
                'modal-edit-size' => 'modal-md',
            ]
        ]]
    ];
    if(!in_array(Auth::user()->role, [session('id_dominio_super_administrador'), session('id_dominio_administrador')])) {
        unset($headers[1]);
    }
?>
@include('partials.grid', [
    'title' => 'Habitaciones',
    'create' => $create,
    'route' => 'rooms',
    'btnOptionsCreate' => [
        "title" => 'Nueva habitación',
        "modal-size" => "modal-md",
        "route" => route("rooms.create"),
    ],
    'headers' => $headers,
    'filters' => true,
    'models' => $model
])