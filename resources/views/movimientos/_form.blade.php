@php
    $create = !isset($movimiento->id_movimiento);
    $edit = isset($edit) ? $edit : $create;
    $editable = (
        ($edit) &&
        !in_array($movimiento->id_dominio_tipo_movimiento, [session('id_dominio_movimiento_entrada_inicial')]) ? true : false ||
        $create
    );
    $id_tercero = isset($tercero->id_tercero) ? $tercero->id_tercero : '';
    $nombre_tercero = isset($tercero->id_tercero) ? $tercero->nombres.' '.$tercero->apellidos : '';
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
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-2 {{ isset($tipo_movimiento) && $tipo_movimiento != '' ? 'd-none' : '' }}">
            <label for="" class="required">Tipo Movimiento</label>
            @if ($create)
                <select name="id_dominio_tipo_movimiento" id="id_dominio_tipo_movimiento" style="width: 100%">
                    <option value="">Elegir movimiento</option>
                    @if (!$tipo_movimiento)
                        @foreach ($tipo_movimientos as $tipo)
                        <option value="{{ $tipo->id_dominio }}">
                            {{ $tipo->tbldominio->nombre.' '.$tipo->nombre }}
                        </option>
                    @endforeach
                    @else
                        <option value="{{ $tipo_movimiento->id_dominio }}" selected>{{ $tipo_movimiento->nombre }}</option>
                    @endif
                </select>
            @else
                <input type="text" class="form-control" value="{{ $movimiento->tbltipomovimiento->tbldominio->nombre.' '.$movimiento->tbltipomovimiento->nombre }}" disabled readonly>
                <input type="hidden" name="id_tercero_almacen" id="id_tercero_almacen" value="{{ $movimiento->tbltipomovimiento }}">
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
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
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4">
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
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-2">
            <label for="id_dominio_iva" class="required">IVA %</label>
            @if ($edit)
                <select class="form-control text-end" name="id_dominio_iva" id="id_dominio_iva" data-dir="rtl" style="width: 100%" @if ($edit) required @else disabled @endif>
                    @foreach ($impuestos as $id => $nombre)
                        <option value="{{ $id }}" {{ old('id_dominio_iva', $movimiento->id_dominio_iva) == $id ? 'selected' : '' }}>
                            {{$nombre}}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="hidden" id="id_dominio_iva" value="{{ $movimiento->tblIva->nombre }}">
                <input type="text" class="form-control text-end" value="{{ $movimiento->tblIva->nombre }}" disabled>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-2" id="div_documento">
            <label for="">Documento</label>
            @if (!isset($actividad->id_actividad))
                <input type="text" class="form-control" @if ($edit) name="documento" @endif id="documento" value="{{ old('documento', $movimiento->documento) }}" @if ($edit) required @else disabled @endif>
            @else
                <input type="text" class="form-control" name="documento" id="documento" value="{{ old('documento', $actividad->id_actividad) }}" required readonly>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-6 col-xl-8">
            <label for="observaciones" class="required">Observaciones</label>
            <textarea class="form-control" @if ($edit) name="observaciones" @endif id="observaciones" rows="2" style="resize: none" @if ($edit) required @else disabled @endif>{{ old('nombre', $movimiento->observaciones) }}</textarea>
        </div>

        <div class="clearfix"><hr></div>

        <div id="div_detalle" class="col-12">
            @include('partials._detalle', ['edit' => $editable, 'tipo_carrito' => 'movimiento', 'detalleCarrito' => $movimiento->getDetalleMovimiento()])
        </div>
    </div>

    @include('partials.buttons', [$create, $edit, 'label' => $create ? 'Crear movimiento' : 'Editar movimiento'])
@if ($create || $edit)
    </form>
@endif

<script type="application/javascript">
    var tipo_movimiento = 0;
    /*Entradas de inventario*/
    var id_dominio_movimiento_entrada_devolucion = parseInt({!! session('id_dominio_movimiento_entrada_devolucion') !!});
    var id_dominio_movimiento_entrada_orden = parseInt({!! session('id_dominio_movimiento_entrada_orden') !!});
    var id_dominio_movimiento_entrada_prestamo = parseInt({!! session('id_dominio_movimiento_entrada_prestamo') !!});
    /*Fin entradas de inventario*/

    /*Salidas de inventario*/
    var id_dominio_movimiento_salida_actividad = parseInt({!! session('id_dominio_movimiento_salida_actividad') !!});
    var id_dominio_movimiento_salida_devolucion = parseInt({!! session('id_dominio_movimiento_salida_devolucion') !!});
    var id_dominio_movimiento_salida_prestamo = parseInt({!! session('id_dominio_movimiento_salida_prestamo') !!});
    /*Fin salidas de inventario*/

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

    function getOrdenes(id_tercero_proveedor, id_tercero_almacen, element) {
        if(id_tercero_almacen === '' || id_tercero_almacen === '') {
            return false;
        }

        $.ajax({
            url: `purchases/${id_tercero_proveedor}/${id_tercero_almacen}/getOrdenesActivas`,
            method: 'GET',
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(data) {
            let options = '';
            $.each(data['ordenes'], (i, e) => {
                options += `<option value="${e['id_orden_compra']}">${e['id_orden_compra']}</option>`;
            });

            $(`#${element}`).append(options);
        }).always(function() {
            showLoader(false);
        });
    }

    function getCarrito(edit, id_tipo_movimiento, id, tipo_carrito) {
        if(edit === '' || id_tipo_movimiento === '' || id === '') return false;

        $.ajax({
            url: `moves/${edit}/${id_tipo_movimiento}/${id}`,
            method: 'GET',
            beforeSend: function() {
                $('#div_detalle').html('');
                showLoader(true);
            }
        }).done(function(data) {
            $('#div_detalle').html(data);
            $('.tr_movimiento').addClass('disabled');
            $('.money').inputmask(formatCurrency);
        }).always(function() {
            showLoader(false);
        });
    }

    $('#id_dominio_tipo_movimiento').change(function() {
        let id_usuario = {!! auth()->id() !!};
        let usuario = '{!! auth()->user()->tbltercero->full_name !!}';
        tipo_movimiento = parseInt($(this).val());

        $('#id_tercero_entrega').empty().append(`<option value="">Elegir quien entrega</option>`);
        $('#id_tercero_recibe').empty().append(`<option value="">Elegir quien recibe</option>`);
        $('.tr_movimiento').removeClass('disabled');
        $('#documento').prop('disabled', false).removeClass('d-none');
        if($('#select-documento').length) {
            $('#select-documento').select2('destroy');
            $('#select-documento').remove();
        }

        if(tipo_movimiento > 0) {
            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                case id_dominio_movimiento_entrada_orden:
                    $('.tr_movimiento').addClass('disabled');
                    $('#documento').prop('disabled', true).addClass('d-none');
                    $('#div_documento').append('<select name="documento" id="select-documento" style="width: 100%"><option>Elegir orden compra</option></select>');
                    getTercerosTipo({!! session('id_dominio_proveedor') !!}, 'id_tercero_entrega');
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    
                    $('#select-documento').select2({
                        dropdownParent: $(this).closest('form'),
                    });

                    $('.select2-selection').addClass('form-control');
                    $('.select2-selection__rendered').data('toggle', 'tooltip');
                    break;
                case id_dominio_movimiento_entrada_prestamo:
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                case id_dominio_movimiento_salida_actividad:
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_entrega');
                    if({{ isset($id_tercero) }}) {
                        id_usuario = '{!! $id_tercero !!}';
                        usuario = '{!! $nombre_tercero !!}';
                        $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    }
                    // $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
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

    $('#id_tercero_entrega').change(function () {
        if(tipo_movimiento > 0 && $(this).val() !== '') {
            let data_action = '';

            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    
                    break;
                case id_dominio_movimiento_entrada_orden:
                    getOrdenes($('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
                    break;
                case id_dominio_movimiento_entrada_prestamo:
                    break;
                case id_dominio_movimiento_salida_actividad:
                    data_action = `stores/${$(this).val()}/movimiento/${{!! session('id_dominio_tipo_movimiento') !!}}`;
                    break;
                case id_dominio_movimiento_salida_devolucion:
                    break;
                case id_dominio_movimiento_salida_prestamo:
                    break;
                default:
                    break;
            }

            if(data_action === '') {
                return false;
            }

            $('.tr_movimiento').data('action', data_action);
        }
    });

    $('#id_tercero_recibe').change(function() {
        if(tipo_movimiento > 0 && $(this).val() !== '') {
            switch(tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    break;
                case id_dominio_movimiento_entrada_orden:
                    getOrdenes($('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
                    break;
                default:
                    break;
            }
        }
    });

    $(document).on('change', '#select-documento', function() {
        if($(this).val() !== '') {
            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    break;
                case id_dominio_movimiento_entrada_orden:
                    getCarrito(true, tipo_movimiento, $(this).val(), {!! session('id_dominio_tipo_movimiento') !!});
                    break;
            
                default:
                    break;
            }
        }
    });

    $('#id_dominio_tipo_movimiento').change();
</script>