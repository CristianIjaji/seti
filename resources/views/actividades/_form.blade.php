<?php
    $create = isset($activity->id_actividad) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('activities.store') : route('activities.update', $cotizacion) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear actividad' : 'Editar actividad', 'modal' => 'modalForm'])
