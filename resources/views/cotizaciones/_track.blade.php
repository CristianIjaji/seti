<div class="row py3">
    <div class="col-12 py1">
        <div class="row">
            @include('partials.grid', [
                'title' => 'Historial cambios',
                'create' => false,
                'route' => 'statequotes',
                'headers' => [
                    ['name' => 'created_at', 'label' => 'Fecha'],
                    ['name' => 'tblestado', 'label' => 'Estado', 'foreign' => 'nombre'],
                    ['name' => 'comentario', 'label' => 'Comentario', 'html' => true],
                    ['name' => 'full_name', 'label' => 'Usuario'],
                ],
                'filters' => false,
                'models' => $model
            ])
        </div>
    </div>
</div>