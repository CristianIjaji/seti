<?php
    $headers = [
        ['name' => 'id_orden', 'label' => '#', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'created_at', 'label' => 'Creación', 'col' => 'col-1', 'class' => 'input-date'],
        ['name' => 'updated_at', 'label' => 'Último cambio', 'col' => 'col-1', 'class' => 'timer', 'filter' => false],
        ['name' => 'id_dominio_tipo_orden', 'label' => 'Tipo orden', 'options' => $tipo_ordenes],
        ['name' => 'fecha_inicio', 'label' => 'Inicio'],
        ['name' => 'fecha_fin', 'label' => 'Fin'],
        ['name' => 'tbltercero', 'label' => 'Asociado', 'col' => 'col-2', 'foreign' => 'razon_social'],
        ['name' => 'descripcion', 'label' => 'Pedido', 'col' => 'col-2'],
        ['name' => 'valor', 'label' => 'Valor', 'align' => 'text-end', 'col' => 'col-1'],
        ['name' => 'full_name', 'label' => 'Usuario'],
        ['name' => 'estado', 'label' => 'Estado', 'col' => 'col-2', 'options' => $estados],
        ['name' => '', 'label' => 'Acciones', 'col' => 'col-1', 'align' => 'text-center', 'actions' => [
            'btnOptions' => [
                'view' => $view,
                'modal-view-title' => 'Ver orden',
                'modal-view-size' => 'modal-xl',
                'edit' => false,
                'modal-edit-title' => 'Editar orden',
                'modal-edit-size' => 'modal-lg',
            ]
        ]]
    ];

    if(in_array(Auth::user()->role, [session('id_dominio_agente'), session('id_dominio_asociado')])) {
        unset($headers[7]);
    }
?>

@include('partials.grid', [
    'title' => 'Ordenes',
    'create' => $create,
    'export' => $export,
    'route' => 'orden',
    'status' => $status,
    'btnOptionsCreate' => [
        "title" => 'Nueva orden',
        "modal-size" => "modal-lg",
        "route" => route("orden.create"),
    ],
    'headers' => $headers,
    'filters' => true,
    'models' => $model
])