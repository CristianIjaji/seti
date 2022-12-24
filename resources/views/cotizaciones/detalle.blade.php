@php
    function createDetail($details, $edit, $tipo_carrito) {
        $row = '';
        $resize = (!$edit ? 'style="resize: none;"' : '');
        foreach ($details as $detail) {
            $row .= "
                <tr id='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio' class='border-bottom collapse show item_$detail->id_tipo_item detail-$detail->id_tipo_item'>
                    <td class='col-1 my-auto border-0'>
                        <input type='hidden' name='id_tipo_item[]' value='$detail->id_tipo_item'>
                        <input type='hidden' name='id_lista_precio[]' value='$detail->id_lista_precio'>
                        <input type='text' class='form-control text-md-center text-end text-uppercase border-0' id='item_$detail->id_tipo_item' value='".$detail->tblListaprecio->codigo."' disabled>
                    </td>
                    <td class='col-4 my-auto border-0'>
                        <textarea class='form-control border-0 resize-textarea' rows='2' name='descripcion_item[]' id='descripcion_item_$detail->id_lista_precio' ".($edit ? 'required' : 'disabled')." $resize>$detail->descripcion</textarea>
                    </td>
                    <td class='col-1 my-auto border-0'>
                        <input type='text' class='form-control text-md-start text-end border-0' ".($edit ? "data-toggle='tooltip' title='$detail->unidad'" : '' )." name='unidad[]' id='unidad_$detail->id_lista_precio' value='$detail->unidad' ".($edit ? '' : 'disabled').">
                    </td>
                    <td class='col-1 my-auto border-0'>
                        <input type='number' min='1' data-id-tr='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio' class='form-control text-end border-0 txt-totales'
                            name='cantidad[]' id='cantidad_$detail->id_lista_precio' value='$detail->cantidad' required ".($edit ? '' : 'disabled').">
                    </td>
                    <td class='col-2 my-auto border-0'>
                        <input type='text' data-id-tr='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio' class='form-control text-end border-0 txt-totales money'
                            ".($edit ? "data-toggle='tooltip' title='$detail->valor_unitario'" : "")." name='valor_unitario[]' id='valor_unitario_$detail->id_lista_precio' value='$detail->valor_unitario' required ".($edit ? '' : 'disabled').">
                    </td>
                    <td class='col-2 my-auto border-0'>
                        <input type='text' data-id-tr='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio' class='form-control text-end border-0 txt-totales money'
                            name='valor_total[]' id='valor_total_$detail->id_lista_precio' value='$detail->valor_total' disabled>
                    </td>
                    ".($edit
                        ? "<td class='text-center col-1 my-auto border-0 td-delete btn-delete-item' ".($edit ? "data-toggle='tooltip' title='Quitar ítem'" : "")." data-id-tr='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio'><span class='btn btn-delete-item' data-id-tr='$tipo_carrito".'_'."$detail->id_tipo_item".'_'."$detail->id_lista_precio'><i class='fa-solid fa-trash-can text-danger fs-5 fs-bold'></i></span></td>"
                        : ""
                    )."
                </tr>
            ";
        }

        return $row;
    }
@endphp

<div class="table-responsive">
    <table id="{{$tipo_carrito}}" class="table table-sm table-table-borderless align-middle table-responsive-stack">
        <thead>
            <tr>
                <th class="col-1 text-center border rounded-start">Ítem</th>
                <th class="col-4 text-center border">Descripción</th>
                <th class="col-1 text-center border">Unidad</th>
                <th class="col-1 text-center border">Cantidad</th>
                <th class="col-2 text-center border">Valor Unitario</th>
                <th class="col-2 text-center border {{ $edit ? '' : 'rounded-end' }}">Valor Total</th>
                @if ($edit)
                    <th id="th-delete" class="col-1 text-center border rounded-end">Eliminar</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr id="tr_{{ session('id_dominio_materiales') }}" class="detail-{{session('id_dominio_materiales')}}">
                <th colspan="7" class="border rounded">
                    <span
                        class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_suministros"
                        data-toggle="tooltip"
                        {{ $editable ? 'title=Agregar ítem' : '' }}
                        data-title="Buscar ítems suministro materiales"
                        data-size='modal-xl'
                        data-header-class='bg-primary bg-opacity-75 text-white'
                        data-action='{{ route('priceList.search', ['type' => session('id_dominio_materiales'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1, 'tipo_carrito' => $tipo_carrito]) }}'
                        data-toggle="tooltip"
                    >
                        <label>SUMINISTRO DE MATERIALES</label>
                    </span>
                    <div class="d-flex justify-content-between">
                        <div class="my-auto">
                            <label id="lbl_total_items_materiales">Total Ítems: {{ count($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_materiales'))) }}</label>
                        </div>
                        <div class="my-auto">
                            <label id="lbl_{{ session('id_dominio_materiales') }}" class="lbl_total_material">$ 0.00</label>
                            <span
                                class="btn show-more"
                                data-bs-toggle="collapse"
                                data-bs-target=".item_{{ session('id_dominio_materiales') }}"
                                >
                                <i id="caret_{{ session('id_dominio_materiales') }}" class="fa-solid fa-caret-down"></i>
                            </span>
                        </div>
                    </div>
                </th>
            </tr>
            {!! createDetail($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_materiales')), $edit, $tipo_carrito) !!}
            <tr id="tr_{{ session('id_dominio_mano_obra') }}" class="detail-{{session('id_dominio_mano_obra')}}">
                <th colspan="7" class="border rounded">
                    <span
                        class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_suministros"
                        data-toggle="tooltip"
                        {{ $editable ? 'title=Agregar ítem' : '' }}
                        data-title="Buscar ítems mano obra"
                        data-size='modal-xl'
                        data-header-class='bg-primary bg-opacity-75 text-white'
                        data-action='{{ route('priceList.search', ['type' => session('id_dominio_mano_obra'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1, 'tipo_carrito' => $tipo_carrito]) }}'
                        data-toggle="tooltip"
                    >
                        <label>MANO DE OBRA</label>
                    </span>
                    <div class="d-flex justify-content-between">
                        <div class="my-auto">
                            <label id="lbl_total_items_mano_obra">Total Ítems: {{ count($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_mano_obra'))) }}</label>
                        </div>
                        <div class="my-auto">
                            <label id="lbl_{{ session('id_dominio_mano_obra') }}" class="lbl_total_mano_obra">$ 0.00</label>
                            <span
                                class="btn show-more"
                                data-bs-toggle="collapse"
                                data-bs-target=".item_{{ session('id_dominio_mano_obra') }}"
                                >
                                <i id="caret_{{ session('id_dominio_mano_obra') }}" class="fa-solid fa-caret-down"></i>
                            </span>
                        </div>
                    </div>
                </th>
            </tr>
            {!! createDetail($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_mano_obra')), $edit, $tipo_carrito) !!}
            @if ($tipo_carrito != 'orden')
                <tr id="tr_{{ session('id_dominio_transporte') }}" class="detail-{{session('id_dominio_transporte')}}">
                    <th colspan="7" class="border rounded">
                        <span
                            class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_suministros"
                            data-toggle="tooltip"
                            {{ $editable ? 'title=Agregar ítem' : '' }}
                            data-title="Buscar ítems transporte y peajes"
                            data-size='modal-xl'
                            data-header-class='bg-primary bg-opacity-75 text-white'
                            data-action='{{ route('priceList.search', ['type' => session('id_dominio_transporte'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1, 'tipo_carrito' => $tipo_carrito]) }}'
                            data-toggle="tooltip"
                        >
                            <label>TRANSPORTE Y PEAJES</label>
                        </span>
                        <div class="d-flex justify-content-between">
                            <div class="my-auto">
                                <label id="lbl_total_items_transporte">Total Ítems: {{ count($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_transporte'))) }}</label>
                            </div>
                            <div class="my-auto">
                                <label id="lbl_{{ session('id_dominio_transporte') }}" class="lbl_total_transporte">$ 0.00</label>
                                <span
                                    class="btn show-more"
                                    data-bs-toggle="collapse"
                                    data-bs-target=".item_{{ session('id_dominio_transporte') }}"
                                    >
                                    <i id="caret_{{ session('id_dominio_transporte') }}" class="fa-solid fa-caret-down"></i>
                                </span>
                            </div>
                        </div>
                    </th>
                </tr>
                {!! createDetail($cotizacion->tblcotizaciondetalle->where('id_tipo_item', '=', session('id_dominio_transporte')), $edit, $tipo_carrito) !!}
            @endif
        </tbody>
    </table>
</div>

<div class="row" id="totales_{{ $tipo_carrito}}">
    <div class="col-12 col-sm-12 col-md-6 co-lg-6 col-xl-7 my-auto pb-2"></div>

    <div class="form-group col-12 col-md-6 co-lg-6 col-xl-5 my-auto">
        <div class="p-3">
            <div class="row fs-6">
                <label class="col-12 text-start">Total sin IVA:</label>
                <label id="{{$tipo_carrito}}_lbl_total_sin_iva" class="col-12 text-end border-bottom">0</label>

                <label class="col-12 text-start">Total IVA:</label>
                <label id="{{$tipo_carrito}}_lbl_total_iva" class="col-12 text-end border-bottom">0</label>

                <label class="col-12 text-start">Total con IVA:</label>
                <label id="{{$tipo_carrito}}_lbl_total_con_iva" class="col-12 text-end border-bottom">0</label>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    carrito['{!! $tipo_carrito !!}'] = <?= json_encode($cotizacion->getDetalleCotizacion()) ?>;
    $('#table-cotizaciones').removeClass('d-none');
    table();
    flexTable();
    totalCarrito('{!! $tipo_carrito !!}');
    setTimeout(() => {
        updateTextAreaSize();
    }, 100);
</script>