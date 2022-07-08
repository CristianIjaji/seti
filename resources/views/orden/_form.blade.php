<?php
    $create = isset($orden->id_orden) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);

    if(isset($orden->id_orden) && $orden->estado == 0) {
        $create = false;
        $edit = false;
    }
?>

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('orden.store') : route('orden.update', $orden) }}" method="POST">
        @csrf

        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12">
            <label for="id_tercero_cliente" class="required">Aliado</label>
            @if ($edit)
                <select class="form-control" name="id_tercero_cliente" id="id_tercero_cliente" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir Aliado</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id_tercero }}" {{ old('id_tercero_cliente', $orden->id_tercero_cliente) == $cliente->id_tercero ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" id="id_tercero_cliente" class="form-control" value="{{ $orden->tbltercero->razon_social ?? '' }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6">
            <label for="id_dominio_tipo_orden" class="required">Pedido</label>
            @if ($edit)
                <select class="form-control" name="id_dominio_tipo_orden" id="id_dominio_tipo_orden" style="width: 100%" @if ($edit) required @else disabled @endif>
                    <option value="">Elegir tipo orden</option>
                </select>
            @else
                <input type="text" id="id_dominio_tipo_orden" class="form-control" value="{{ $orden->tbldominio->nombre ?? '' }}" disabled>
            @endif
        </div>
        <div class="form-group div-fechas col-12 col-sm-12 col-md-6">
            <div class="row">
                <div class="col-6">
                    <label for="Fecha_inicio" class="required">Fecha inicio</label>
                    <input type="text" name="fecha_inicio" id="fecha_inicio" class="form-control">
                </div>
                <div class="col-6">
                    <label for="Fecha_fin" class="required">Fecha fin</label>
                    <input type="text" name="fecha_fin" id="fecha_fin" class="form-control">
                </div>
            </div>
        </div>
        <div class="form-group div-habitaciones col-12 col-sm-12 col-md-6">
            <label for="">Tipo Habitación</label>
            <select class="form-control" name="id_habitacion" id="id_habitacion" style="width: 100%">
                <option value="">Elegir tipo Habitación</option>
            </select>
        </div>
        <div id="div_valor" class="form-group col-12 col-sm-12 col-md-3">
            <label for="valor" class="required">Valor</label>
            <input type="text" id="valor" name="valor" class="form-control money" value="{{ $orden->valor ?? '' }}" @if ($edit) required @else disabled @endif>
        </div>
        <div id="div_metodo" class="form-group col-12 col-sm-12 col-md-3">
            <label for="valor" class="required">Metodo pago</label>
            <input type="text" id="metodo_pago" name="metodo_pago" class="form-control" value="{{ $orden->metodo_pago ?? '' }}" @if ($edit) required @else disabled @endif>
        </div>
        
        <div class="form-group col-12 col-sm-12">
            <label for="descripcion" class="required">Descripción</label>
            <textarea class="form-control" @if ($edit) name="descripcion" @endif id="descripcion" rows="4" style="resize: none" @if (!$edit) disabled @endif>{{ old('nombre', $orden->descripcion) }}</textarea>
        </div>
        <div class="col-12 col-sm-12 pb-4">
            <hr>
            <label for="">Datos Cliente</label>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4">
            <label for="nombre_cliente" class="required">Nombre</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" value="{{ $orden->datos_cliente_form[0] ?? '' }}" class="form-control" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4">
            <label id="lblDireccion" for="direccion_cliente" class="required">Dirección</label>
            <input type="text" id="direccion_cliente" name="direccion_cliente" value="{{ $orden->datos_cliente_form[1] ?? '' }}" class="form-control" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4">
            <label for="telefono_cliente" class="required">Teléfono</label>
            <input type="tel" id="telefono_cliente" name="telefono_cliente" value="{{ $orden->datos_cliente_form[2] ?? '' }}" class="form-control" @if ($edit) required @else disabled @endif>
        </div>
    </div>

    @if ($create || $orden->estado == session('id_dominio_orden_cola'))
        @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear orden' : 'Editar orden'])
    @endif

<script type="application/javascript">
    setupSelect2('modalForm');
    datePicker();
    
    id_dominio_domicilio = {!! session('id_dominio_domicilio') !!};
    id_dominio_reserva_hotel = {!! session('id_dominio_reserva_hotel') !!};
    id_dominio_reserva_restaurante = {!! session('id_dominio_reserva_restaurante') !!};

    $('#id_dominio_tipo_orden').change();
</script>