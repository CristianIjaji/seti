<div class="row py3">
    <div class="col-12 py1">
        <div class="row">
            @include('partials.grid', [
                'title' => '',
                'create' => false,
                'route' => 'statequotes',
                'headers' => [
                    ['name' => 'item', 'label' => 'Ítem', 'align' => 'text-center', 'col' => 'col-1'],
                    ['name' => 'zona', 'label' => 'Zona', 'align' => 'text-center', 'col' => 'col-1'],
                    ['name' => 'ot', 'label' => 'OT', 'col' => 'col-2'],
                    ['name' => 'estacion', 'label' => 'Estación', 'col' => 'col-1'],
                    ['name' => 'fecha_ejecucion', 'label' => 'Fecha ejecución', 'col' => 'col-1'],
                    ['name' => 'actividad', 'label' => 'Actividad', 'align' => 'text-center', 'col' => '2'],
                    ['name' => 'valor_cotizado', 'label' => 'Valor cotizado', 'align' => 'text-end', 'col' => 'col-2'],
                    ['name' => 'observacion', 'label' => 'Observación', 'col' => 'col-2', 'html' => true]
                ],
                'filters' => true,
                'models' => $model
            ])
        </div>
    </div>
</div>