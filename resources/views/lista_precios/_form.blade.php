<?php
    $create = isset($lista_precio->id_lista_precio) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('priceList.store') : route('priceList.update', $lista_precio) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6">
            <label for="id_cliente" class="required">Cliente</label>
            @if ($edit)
                <select class="form-control" name="id_cliente" id="id_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir cliente</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id_tercero }}" {{ old('id_cliente', $lista_precio->id_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_cliente" value="{{ $lista_precio->tbltercerocliente->full_name }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-3">
            <label for="id_tipo_item" class="required">Tipo ítem</label>
            @if ($edit)
                <select class="form-control" name="id_tipo_item" id="id_tipo_item" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir tipo ítem</option>
                    @foreach ($tipo_items as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_tipo_item', $lista_precio->id_tipo_item) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" id="id_cliente" value="{{ $lista_precio->tbldominioitem->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-3">
            <label for="codigo" class="required">Código</label>
            <input type="text" class="form-control text-uppercase" @if ($edit) name="codigo" @endif id="codigo" value="{{ old('codigo', $lista_precio->codigo) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
            <label for="unidad" class="required">Unidad</label>
            <input type="text" class="form-control" list="list_unidades" @if ($edit) name="unidad" @endif id="unidad" value="{{ old('unidad', $lista_precio->unidad) }}" @if ($edit) required @else disabled @endif>
            @if ($edit)
                <datalist id="list_unidades">
                    @foreach ($unidades as $unidad)
                        <option value="{{ $unidad }}">{{ $unidad }}</option>
                    @endforeach
                </datalist>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
            <label for="cantidad" class="required">Cantidad</label>
            <input type="number" min="0" class="form-control" @if ($edit) name="cantidad" @endif id="cantidad" value="{{ old('cantidad', $lista_precio->cantidad) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
            <label for="valor_unitario" class="required">Valor unitario</label>
            <input type="text" class="form-control money" @if ($edit) name="valor_unitario" @endif id="valor_unitario" value="{{ old('valor_unitario', $lista_precio->valor_unitario) }}" @if ($edit) required @else disabled @endif>
        </div>
        @if(!$create)
            <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
                <label for="estado" class="required">Estado</label>
                @if ($edit)
                    <select class="form-control" name="estado" id="estado" style="width: 100%" @if ($edit) required @else disabled @endif>
                        @foreach ($estados as $id => $name)
                            <option value="{{ $id }}" {{ old('estado', $lista_precio->estado_form) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" class="form-control" id="estado" value="{{ $lista_precio->estado_form = 1 ? 'Activo' : 'Inactivo' }}" disabled>
                @endif
            </div>

            @if (!$edit)
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
                    <label for="creado_por">Creado por</label>
                    <input type="text" id="creado_por" class="form-control" disabled value="{{ $lista_precio->tblusuario->usuario }}">
                </div>
            
                <div class="form-group col-12 col-sm-12 col-md-6 col-lg-2">
                    <label for="fecha_creacion">Fecha creación</label>
                    <input type="text" id="fecha_creacion" class="form-control" disabled value="{{ $lista_precio->created_at }}">
                </div>
            @endif
        @endif
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-12">
            <label for="descripcion">Descripción</label>
            <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="4" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $lista_precio->descripcion) }}</textarea>
        </div>
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear Lista Precio' : 'Editar Lista Precio'])


<script type="application/javascript">
    setupSelect2('modalForm');
</script>