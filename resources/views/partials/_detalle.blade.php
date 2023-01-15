@php
    function createDetail($id_tipo_item, $details, $edit, $tipo_carrito) {
        $row = '';
        $resize = (!$edit ? 'style="resize: none;"' : '');

        foreach ($details as $id_item => $detail) {
            $row .= "
                <tr id='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item' class='border-bottom collapse show item_$id_tipo_item detail-$id_tipo_item'>
                    <td class='col-1 my-auto border-0 ".($edit ? ':' : 'text-md-center text-end text-uppercase')."'>
                        ".($edit
                            ? "
                                <input type='hidden' name='id_tipo_item[]' value='$id_tipo_item'>
                                <input type='hidden' name='id_lista_precio[]' value='$id_item'>
                                <input type='text' class='form-control text-md-center text-end text-uppercase border-0' id='item_$id_tipo_item' value='$detail[item]' disabled>
                            "
                            : $detail['item']
                        )."
                    </td>
                    <td class='col-4 my-auto border-0'>
                        ".($edit
                            ? "
                                <textarea class='form-control border-0 resize-textarea' rows='2' name='descripcion_item[]' id='descripcion_item_$id_item' ".($edit ? 'required' : 'disabled')." $resize>$detail[descripcion]</textarea>
                            "
                            : $detail['descripcion']
                        )."
                    </td>
                    <td class='col-1 my-auto border-0 ".($edit ? '' : 'text-md-start text-end')."'>
                        ".($edit
                            ? "
                                <input type='text' class='form-control text-md-start text-end border-0' ".($edit ? "data-toggle='tooltip' title='$detail[unidad]'" : '' )." name='unidad[]' id='unidad_$id_item' value='$detail[unidad]' ".($edit ? '' : 'disabled').">
                            "
                            : $detail['unidad']
                        )."
                    </td>
                    <td class='col-1 my-auto border-0 ".($edit ? '' : 'text-end')."'>
                        ".($edit
                            ? "
                                <input type='number' min='1' data-id-tr='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item' class='form-control text-end border-0 txt-totales'
                                    name='cantidad[]' id='cantidad_$id_item' value='$detail[cantidad]' required ".($edit ? '' : 'disabled').">
                            "
                            : $detail['cantidad']
                        )."
                    </td>
                    <td class='col-2 my-auto border-0 ".($edit ? '' : 'text-end')."'>
                        ".($edit
                            ? "
                                <input type='text' data-id-tr='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item' class='form-control text-end border-0 txt-totales money'
                                    ".($edit ? "data-toggle='tooltip' title='$detail[valor_unitario]'" : "")." name='valor_unitario[]' id='valor_unitario_$id_item' value='$detail[valor_unitario]' required ".($edit ? '' : 'disabled').">
                            "
                            : number_format($detail['valor_unitario'], 2)
                        )."
                    </td>
                    ".(isset($detail['iva'])
                        ? "
                            <td class='col-2 my-auto border-0 ".($edit ? '' : 'text-center')."'>
                                ".($edit ? $detail['iva'] : $detail['iva'])."
                            </td>"
                        : ""
                    )."
                    <td class='col-2 my-auto border-0 ".($edit ? '' : 'text-end')."'>
                        ".($edit
                            ? "
                                <input type='text' data-id-tr='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item' class='form-control text-end border-0 txt-totales money'
                                    name='valor_total[]' id='valor_total_$id_item' value='$detail[valor_total]' disabled>
                            "
                            : number_format($detail['valor_total'], 2)
                        )."
                    </td>
                    ".($edit
                        ? "<td class='text-center col-1 my-auto border-0 td-delete btn-delete-item' ".($edit ? "data-toggle='tooltip' title='Quitar ítem'" : "")." data-id-tr='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item'><span class='btn btn-delete-item' data-id-tr='$tipo_carrito".'_'."$id_tipo_item".'_'."$id_item'><i class='fa-solid fa-trash-can text-danger fs-5 fs-bold'></i></span></td>"
                        : ""
                    )."
                </tr>
            ";
        }

        return $row;
    }

@endphp

<div class="table-responsive">
    <table id="{{$tipo_carrito}}" class="table table-sm table-table-borderless align-middle {{ !in_array($tipo_carrito, ['movimiento']) ? 'table-responsive-stack' : ''}} w-100">
        @switch($tipo_carrito)
            @case('cotizacion')
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
                                        <i id="caret_{{ session('id_dominio_materiales') }}" class="fa-solid fa-angle-up"></i>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    {!! createDetail(session('id_dominio_materiales'), isset($detalleCarrito[session('id_dominio_materiales')]) ? $detalleCarrito[session('id_dominio_materiales')] : [], $edit, $tipo_carrito) !!}
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
                                        <i id="caret_{{ session('id_dominio_mano_obra') }}" class="fa-solid fa-angle-up"></i>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    {!! createDetail(session('id_dominio_mano_obra'), isset($detalleCarrito[session('id_dominio_mano_obra')]) ? $detalleCarrito[session('id_dominio_mano_obra')] : [], $edit, $tipo_carrito) !!}
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
                                        <i id="caret_{{ session('id_dominio_transporte') }}" class="fa-solid fa-angle-up"></i>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr>
                    {!! createDetail(session('id_dominio_transporte'), isset($detalleCarrito[session('id_dominio_transporte')]) ? $detalleCarrito[session('id_dominio_transporte')] : [], $edit, $tipo_carrito) !!}
                </tbody>
                @break
            @case('movimiento')
                <thead>
                    <tr>
                        <th class="col-1 text-center border rounded-start">ID</th>
                        <th class="col-4 text-center border">Descripción</th>
                        <th class="col-1 text-center border">Unidad</th>
                        <th class="col-1 text-center border">Cantidad</th>
                        <th class="col-2 text-center border">Valor Unitario</th>
                        <th class="col-1 text-center border">IVA %</th>
                        <th class="col-2 text-center border {{ $edit ? '' : 'rounded-end' }}">Valor Total</th>
                        @if ($edit)
                            <th id="th-delete" class="col-1 text-center border rounded-end">Eliminar</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    {{-- <tr id="tr_{{ session('id_dominio_tipo_movimiento') }}" class="detail-{{session('id_dominio_tipo_movimiento')}}">
                        <th colspan="{{ $edit ? 8 : 7 }}" class="border rounded">
                            <span
                                class="w-100 bg-primary bg-opacity-75 fw-bold user-select-none {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_suministros"
                                data-toggle="tooltip"
                                {{ $editable ? 'title=Agregar producto' : '' }}
                                data-title="Buscar producto"
                                data-size='modal-xl'
                                data-header-class='bg-primary bg-opacity-75 text-white'
                                data-action='{{ route('priceList.search', ['type' => session('id_dominio_mano_obra'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1, 'tipo_carrito' => $tipo_carrito]) }}'
                                data-toggle="tooltip"
                            >
                                <label>LISTA DE PRODUCTOS</label>
                            </span>
                            <div class="d-flex justify-content-between">
                                <div class="my-auto">
                                    <label id="lbl_total_items_transporte">Total Ítems: {{ isset($detalleCarrito[session('id_dominio_tipo_movimiento')]) ? count($detalleCarrito[session('id_dominio_tipo_movimiento')]) : 0 }}</label>
                                </div>
                                <div class="my-auto">
                                    <label id="lbl_{{ session('id_dominio_tipo_movimiento') }}" class="lbl_total_mano_obra">$ 0.00</label>
                                    <span
                                        class="btn show-more"
                                        data-bs-toggle="collapse"
                                        data-bs-target=".item_{{ session('id_dominio_mano_obra') }}"
                                        >
                                        <i id="caret_{{ session('id_dominio_mano_obra') }}" class="fa-solid fa-angle-up"></i>
                                    </span>
                                </div>
                            </div>
                        </th>
                    </tr> --}}
                    <tr id="tr_{{ session('id_dominio_tipo_movimiento') }}" class="detail-{{session('id_dominio_tipo_movimiento')}}">
                        <th colspan="{{ $edit ? 8 : 7 }}" class="border rounded">
                            <span
                                class="w-100 bg-primary bg-opacity-75 fw-bold user-select-none {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_suministros"
                                data-toggle="tooltip"
                                {{ $editable ? 'title=Agregar producto' : '' }}
                                data-title="Buscar producto"
                                data-size='modal-xl'
                                data-header-class='bg-primary bg-opacity-75 text-white'
                                {{-- data-action='{{ route('priceList.search', ['type' => session('id_dominio_mano_obra'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1, 'tipo_carrito' => $tipo_carrito]) }}' --}}
                                data-toggle="tooltip"
                            >
                                <label>LISTA DE PRODUCTOS</label>
                            </span>
                            <div class="d-flex justify-content-between">
                                <div class="my-auto">
                                    <label id="lbl_total_items_transporte">Total Ítems: {{ isset($detalleCarrito[session('id_dominio_tipo_movimiento')]) ? count($detalleCarrito[session('id_dominio_tipo_movimiento')]) : 0 }}</label>
                                </div>
                                <div class="my-auto">
                                    <label id="lbl_{{ session('id_dominio_tipo_movimiento') }}" class="lbl_total_transporte">$ 0.00</label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    {!! createDetail(session('id_dominio_tipo_movimiento'), (isset($detalleCarrito[session('id_dominio_tipo_movimiento')]) ? $detalleCarrito[session('id_dominio_tipo_movimiento')] : []), $edit, $tipo_carrito) !!}
                </tbody>
                @break
            @default
                
        @endswitch
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
    carrito['{!! $tipo_carrito !!}'] = <?= json_encode($detalleCarrito) ?>;
    $('#table-cotizaciones').removeClass('d-none');
    if({!! $tipo_carrito !!} !== 'movimiento') {
        table();
        flexTable();
    }
    totalCarrito('{!! $tipo_carrito !!}');
    setTimeout(() => {
        updateTextAreaSize();
    }, 100);
</script>