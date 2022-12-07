
<div class="row">
    <label class="col-12 my-4">Datos para la orden compra</label>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="codigo_orden">Código Orden</label>
        <input type="text" class="form-control text-uppercase" name="codigo_orden" id="codigo_orden">
    </div>
    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="id_tipo" class="required">Tipo</label>
        <select class="form-control" name="id_tipo" id="id_tipo" style="width: 100%">
            <option value="">Elegir tipo</option>
            @foreach ($tipos_ordenes_compra as $id => $nombre)
                <option value="{{ $id }}">
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="id_proveedor" class="required">Proveedor</label>
        <select class="form-control" name="id_proveedor" id="id_proveedor" style="width: 100%">
            <option value="">Elegir proveedor</option>
            @foreach ($proveedores as $proveedor)
                <option 
                    data-id_asesor="{{ $proveedor->id_tercero }}"
                    data-asesor="{{ $proveedor->nombres." ".$proveedor->apellidos }}"
                    value="{{ $proveedor->id_tercero }}">
                    {{$proveedor->full_name}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="id_asesor" class="required">Asesor</label>
        <select class="form-control" name="id_asesor" id="id_asesor" style="width: 100%">
            <option value="">Elegir asesor</option>
        </select>
    </div>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="id_modalidad_pago" class="required">Modalidad pago</label>
        <select class="form-control" name="id_modalidad_pago" id="id_modalidad_pago" style="width: 100%">
            <option value="">Elegir modalidad</option>
            @foreach ($medios_pago_ordenes_compra as $id => $nombre)
                <option value="{{ $id }}">
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 input-date">
        <label for="vencimiento" class="required">Vencimiento</label>
        <input type="text" class="form-control" data-min-date="{{ date('Y-m-d') }}" name="vencimiento" id="vencimiento" required readonly>
    </div>

    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <label for="despacharlo_a" class="required">Despacharlo a: </label>
        <input type="text" class="form-control" name="despacharlo_a" id="despacharlo_a" value="" required>
    </div>
</div>

<script type="application/javascript">
    $('#id_proveedor').change(function() {
        $('#id_asesor').empty();
        if($(this).val() !== '') {
            let id_asesor = $(this).find(':selected').data('id_asesor');
            let asesor = $(this).find(':selected').data('asesor');

            $('#id_asesor').append(`<option value"${id_asesor}">${asesor}</option>`);
        }
    });
</script>