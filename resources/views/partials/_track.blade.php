@include('partials.grid', [
    'title' => $title,
    'create' => false,
    'route' => $route,
    'headers' => [
        ['name' => 'created_at', 'label' => 'Fecha', 'class' => 'input-date', 'align' => 'text-end', 'col' => 'col-2'],
        ['name' => 'tblestado', 'label' => 'Estado', 'foreign' => 'nombre', 'col' => 'col-2'],
        ['name' => 'comentario', 'label' => 'Comentario', 'html' => true, 'col' => 'col-4'],
        ['name' => 'full_name', 'label' => 'Usuario', 'col' => 'col-4'],
    ],
    'filters' => false,
    'models' => $model
])