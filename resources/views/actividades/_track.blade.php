@include('partials.grid', [
    'title' => 'Historial cambios',
    'create' => false,
    'route' => 'stateactivities',
    'headers' => [
        ['name' => 'created_at', 'label' => 'Fecha', 'class' => 'input-date'],
        ['name' => 'tblestado', 'label' => 'Estado', 'foreign' => 'nombre'],
        ['name' => 'comentario', 'label' => 'Comentario', 'html' => true],
        ['name' => 'full_name', 'label' => 'Usuario'],
    ],
    'filters' => true,
    'models' => $model
])