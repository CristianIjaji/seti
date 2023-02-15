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
    var id_tercero_entrega = 0;
    var id_tercero_recibe = 0;
    /*Entradas de inventario*/
    var id_dominio_movimiento_entrada_devolucion = parseInt({!! session('id_dominio_movimiento_entrada_devolucion') !!});
    var id_dominio_movimiento_entrada_orden = parseInt({!! session('id_dominio_movimiento_entrada_orden') !!});
    /*Fin entradas de inventario*/

    /*Salidas de inventario*/
    var id_dominio_movimiento_salida_actividad = parseInt({!! session('id_dominio_movimiento_salida_actividad') !!});
    var id_dominio_movimiento_salida_traslado = parseInt({!! session('id_dominio_movimiento_salida_traslado') !!});
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

    function getTercerosInventario(element) {
        $.ajax({
            url: `activities/getResponsablesInventario`,
            method: 'GET',
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(data) {
            let options = '';
            $.each(data, (i, e) => {
                options += `<option value="${e['id_tercero']}">${e['nombre']}</option>`;
            });

            $(`#${element}`).append(options);
        }).always(function() {
            showLoader(false);
        });
    }

    function getDocumentos(controller, id_tercero_proveedor, id_tercero_almacen, element) {
        if(controller === '' || id_tercero_proveedor === '' || id_tercero_almacen === '' || element === '') {
            return false;
        }

        $.ajax({
            url: `${controller}/${id_tercero_proveedor}/${id_tercero_almacen}/getDocumentos`,
            method: 'GET',
            beforeSend: function() {
                showLoader(true);
            }
        }).done(function(data) {
            let options = '';
            $.each(data['documentos'], (i, e) => {
                let value = 0;
                switch (controller) {
                    case 'purchases':
                        value = e['id_orden_compra'];
                        break;
                    case 'activities':
                        value = e['id_actividad'];
                        break;
                    default:
                        break;
                }
                options += `<option value="${value}">${value}</option>`;
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
            if(tipo_movimiento === id_dominio_movimiento_entrada_devolucion) {
                $('.lbl-cantidad').text('Cantidad a devolver');
            }
            $('.money').inputmask(formatCurrency);
        }).always(function() {
            $('#select-documento').select2('open');
            showLoader(false);
            $('#select-documento').select2('close');
        });
    }

    function changeTipoMovimiento() {
        $('#id_tercero_entrega').empty().append(`<option value="">Elegir quien entrega</option>`);
        $('#id_tercero_recibe').empty().append(`<option value="">Elegir quien recibe</option>`);
        $('.tr_movimiento').removeClass('disabled');
        $('#documento').prop('disabled', false).removeClass('d-none');

        if($('#select-documento').length) {
            $('#select-documento').select2('destroy');
            $('#select-documento').remove();
        }

        $('.lbl-cantidad').text('Cantidad');
        $('.lbl-cantidad, .td-cantidad').removeClass('d-none');

        if(tipo_movimiento > 0) {
            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    $('.lbl-cantidad').text('Cantidad a devolver');

                    $('.tr_movimiento').addClass('disabled');
                    $('#documento').prop('disabled', true).addClass('d-none');
                    $('#div_documento').append('<select name="documento" id="select-documento" style="width: 100%"><option>Elegir actividad</option></select>');
                    getTercerosInventario('id_tercero_entrega');
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');

                    $('#select-documento').select2({
                        dropdownParent: $(this).closest('form'),
                    });

                    $('.select2-selection').addClass('form-control');
                    $('.select2-selection__rendered').data('toggle', 'tooltip');
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
                case id_dominio_movimiento_salida_actividad:
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_entrega');
                    if({{ isset($id_tercero) }}) {
                        id_usuario = '{!! $id_tercero !!}';
                        usuario = '{!! $nombre_tercero !!}';
                        $('#id_tercero_recibe').append(`<option value="${id_usuario}">${usuario}</option>`).val(id_usuario).change();
                    }
                    break;
                case id_dominio_movimiento_salida_traslado:
                    $('.lbl-cantidad, .td-cantidad').addClass('d-none');
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_entrega');
                    getTercerosTipo({!! session('id_dominio_almacen') !!}, 'id_tercero_recibe');
                    break;
                default:
                    break;
            }
        }
    }

    function changeEntrega() {
        if(tipo_movimiento > 0 && $('#id_tercero_entrega').val() !== '') {
            let data_action = '';

            switch (tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    getDocumentos('activities', $('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
                    break;
                case id_dominio_movimiento_entrada_orden:
                    getDocumentos('purchases', $('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
                    break;
                case id_dominio_movimiento_salida_actividad:
                    data_action = `stores/${$('#id_tercero_entrega').val()}/movimiento/${{!! session('id_dominio_tipo_movimiento') !!}}`;
                    break;
                case id_dominio_movimiento_salida_traslado:
                    data_action = `stores/${$('#id_tercero_entrega').val()}/movimiento/${{!! session('id_dominio_tipo_movimiento') !!}}`;
                    break;
                default:
                    break;
            }

            if(data_action === '') {
                return false;
            }

            $('.tr_movimiento').data('action', data_action);
        }
    }

    $('#id_dominio_tipo_movimiento').change(function() {
        let id_usuario = {!! auth()->id() !!};
        let usuario = '{!! auth()->user()->tbltercero->full_name !!}';

        if(typeof carrito['movimiento'] !== 'undefined' && carrito['movimiento'].length === 0) {
            tipo_movimiento = parseInt($(this).val());

            changeTipoMovimiento();
        }

        if(typeof carrito['movimiento'] !== 'undefined' && carrito['movimiento'].length > 0 && parseInt(tipo_movimiento) !== parseInt($(this).val())) {
            swalConfirm('Cambiar tipo movimiento', '¿Seguro quiere cambiar el tipo de movimiento?',
                () => {
                    tipo_movimiento = $('#id_dominio_tipo_movimiento').val();
                    $('tr.item_{!! session("id_dominio_tipo_movimiento") !!}').remove();
                    carrito['movimiento'] = [];
                    totalCarrito('movimiento');

                    changeTipoMovimiento();
                    $('#id_tercero_entrega, #id_tercero_recibe').trigger('change');
                },
                () => {
                    $('#id_dominio_tipo_movimiento').val(tipo_movimiento).trigger('change');
                }
            );
        }
    });

    $('#id_tercero_entrega').change(function () {
        if(typeof carrito['movimiento'] !== 'undefined' && carrito['movimiento'].length === 0) {
            id_tercero_entrega = $(this).val();
            changeEntrega();
        }

        if(typeof carrito['movimiento'] !== 'undefined' && carrito['movimiento'].length > 0 && parseInt(id_tercero_entrega) !== parseInt($(this).val())) {
            swalConfirm('Cambiar quien entrega inventario', '¿Seguro quiere cambiar quien entrega?',
                () => {
                    id_tercero_entrega = $('#id_tercero_entrega').val();
                    $('tr.item_{!! session("id_dominio_tipo_movimiento") !!}').remove();
                    carrito['movimiento'] = [];
                    totalCarrito('movimiento');

                    changeEntrega();
                },
                () => {
                    $('#id_tercero_entrega').val(id_tercero_entrega).trigger('change');
                }
            );
        }
    });

    $('#id_tercero_recibe').change(function() {
        if(tipo_movimiento > 0 && $(this).val() !== '') {
            switch(tipo_movimiento) {
                case id_dominio_movimiento_entrada_devolucion:
                    getDocumentos('activities', $('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
                    break;
                case id_dominio_movimiento_entrada_orden:
                    getDocumentos('purchases', $('#id_tercero_entrega').val(), $('#id_tercero_recibe').val(), 'select-documento');
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
                    getCarrito(true, tipo_movimiento, $(this).val(), {!! session('id_dominio_tipo_movimiento') !!});
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
    id_dominio_salida_traslado = id_dominio_movimiento_salida_traslado;
</script>