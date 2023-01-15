@php
    $create = isset($movimiento->id_movimiento) ? false : true;
    $edit = isset($edit) ? $edit : ($create == true ? true : false);
    $editable = (
        ($edit) &&
        !in_array($movimiento->id_dominio_tipo_movimiento, [session('id_dominio_movimiento_entrada_inicial')]) ? true : false ||
        $create
    );
@endphp

@if ($create || $edit)
    <div class="alert alert-success" role="alert"></div>
    <div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

    <form action="{{ $create ? route('moves.store') : route('moves.update', $movimiento) }}" method="POST">
        @csrf
        @if (!$create)
            @method('PATCH')
        @endif
@endif
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
            <label for="" class="required">Tipo Movimiento</label>
            @if ($create)
                <select name="id_dominio_tipo_movimiento" id="id_dominio_tipo_movimiento" style="width: 100%">
                    <option value="">Elegir movimiento</option>
                    @foreach ($tipo_movimientos as $tipo)
                        <option value="{{ $tipo->id_dominio }}">
                            {{ $tipo->tbldominio->nombre.' '.$tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" value="{{ $movimiento->tbltipomovimiento->tbldominio->nombre.' '.$movimiento->tbltipomovimiento->nombre }}" disabled readonly>
                <input type="hidden" name="id_tercero_almacen" id="id_tercero_almacen" value="{{ $movimiento->tbltipomovimiento }}">
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
            <label for="" class="required">Quien entrega</label>
            @if ($create)
                <select name="id_tercero_entrega" id="id_tercero_entrega" style="width: 100%">
                    <option value="">Elegir quien entrega</option>
                </select>
            @else
                <input type="text" class="form-control" value="{{ $movimiento->tblterceroentrega->full_name }}" disabled readonly>
                <input type="hidden" name="id_tercero_almacen" id="id_tercero_almacen" value="{{ $movimiento->id_tercero_entrega }}">
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
            <label for="" class="required">Quien recibe</label>
            @if ($create)
                <select name="id_tercero_recibe" id="id_tercero_recibe" style="width: 100%">
                    <option value="">Elegir quien recibe</option>
                </select>
            @else
            <input type="text" class="form-control" value="{{ $movimiento->tbltercerorecibe->full_name }}" disabled readonly>
            <input type="hidden" name="id_tercero_almacen" id="id_tercero_almacen" value="{{ $movimiento->id_tercero_recibe }}">
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-4">
            <label for="">Documento</label>
            <input type="text" class="form-control" @if ($edit) name="documento" @endif id="documento" value="{{ old('documento', $movimiento->documento) }}" @if ($edit) required @else disabled @endif>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 col-lg-6 col-xl-8">
            <label for="observaciones" class="required">Observaciones</label>
            <textarea class="form-control" @if ($edit) name="observaciones" @endif id="observaciones" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $movimiento->observaciones) }}</textarea>
        </div>

        <div class="clearfix"><hr></div>

        @include('partials._detalle', ['edit' => $editable, 'tipo_carrito' => 'movimiento', 'detalleCarrito' => $movimiento->getDetalleMovimiento(), 'mostrarIva' => true])
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear movimiento' : 'Editar movimiento'])

<script type="application/javascript">
    function getTercerosTipo(tipo, element) {
        $.ajax({
            url: `clients/${tipo}/getTercerosByTipo`,
            method: 'GET',
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(data) {
            let options = '';
            $.each(data['terceros'], (i, e) => {
                options += `<option value="${i}">${e}</option>`;
            });

            $(`#${element}`).append(options);
        }).always(function() {
            showLoader(false);
        });
    }

    $('#id_dominio_tipo_movimiento').change(function() {
        let id_usuario = {!! auth()->id() !!};
        let usuario = '{!! auth()->user()->tbltercero->full_name !!}';
        let tipo_movimiento = parseInt($(this).val());
        /*Entradas de inventario*/
        let id_dominio_movimiento_entrada_devolucion = parseInt({!! session('id_dominio_movimiento_entrada_devolucion') !!});
        let id_dominio_movimiento_entrada_orden = parseInt({!! session('id_dominio_movimiento_entrada_orden') !!});
        let id_dominio_movimiento_entrada_prestamo = parseInt({!! session('id_dominio_movimiento_entrada_prestamo') !!});
        /*Fin entradas de inventario*/

        /*Salidas de inventario*/
        let id_dominio_movimiento_salida_actividad = parseInt({!! session('id_dominio_movimiento_salida_actividad') !!});
        let id_dominio_movimiento_salida_devolucion = parseInt({!! session('id_dominio_movimiento_salida_devolucion') !!});
        let id_dominio_movimiento_salida_prestamo = parseInt({!! session('id_dominio_movimiento_salida_prestamo') !!});
        /*Fin salidas de inventario*/

        $('#id_tercero_entrega').empty().append(`<option value="">Elegir quien entrega</option>`);
        $('#id_tercero_recibe').empty().append(`<option value="">Elegir quien recibe</option>`);

        if(tipo_movimiento !== '') {
            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                case id_dominio_movimiento_entrada_orden:
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                case id_dominio_movimiento_entrada_prestamo:
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                case id_dominio_movimiento_salida_actividad:
                    break;
                case id_dominio_movimiento_salida_devolucion:
                    break;
                case id_dominio_movimiento_salida_prestamo:
                    break;
                default:
                    break;
            }
        }
    });
</script>