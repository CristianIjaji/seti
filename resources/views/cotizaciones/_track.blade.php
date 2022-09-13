<div class="row py3">
    <div class="col-12 py1">
        <div class="row">
            {{-- @if ($edit)
                <div class="form-group col-12">
                    <label for="comentario">Nuevo comentario</label>
                    <textarea class="form-control" name="comentario" rows="3" style="resize: none"></textarea>
                </div>
            @endif --}}
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