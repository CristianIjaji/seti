<table id="table_items" class="table table-sm table-table-borderless align-middle table-responsive-stack">
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
        <tr id="tr_{{ session('id_dominio_materiales') }}">
            <th colspan="7" class="border rounded">
                <span
                    class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_cotizacion"
                    data-toggle="tooltip"
                    {{ $editable ? 'title=Agregar ítem' : '' }}
                    data-title="Buscar ítems suministro materiales"
                    data-size='modal-xl'
                    data-header-class='bg-primary bg-opacity-75 text-white'
                    data-action='{{ route('priceList.search', ['type' => session('id_dominio_materiales'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                    data-modal="modalForm-2"
                    data-toggle="tooltip"
                >
                    <label>SUMINISTRO DE MATERIALES</label>
                </span>
                <div class="d-flex justify-content-between">
                    <div class="my-auto">
                        <label id="lbl_total_items_materiales">Total Ítems: {{ count($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_materiales'))) }}</label>
                    </div>
                    <div class="my-auto">
                        <label id="lbl_{{ session('id_dominio_materiales') }}" class="lbl_total_material">$ 0.00</label>
                        <span
                            class="btn"
                            data-bs-toggle="collapse"
                            data-bs-target=".item_{{ session('id_dominio_materiales') }}"
                            >
                            <i id="caret_{{ session('id_dominio_materiales') }}" class="show-more fa-solid fa-caret-down"></i>
                        </span>
                    </div>
                </div>
            </th>
        </tr>
        @foreach ($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_materiales')) as $detalle)
            <tr id="tr_{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="tr_cotizacion border-bottom collapse show item_{{$detalle->id_tipo_item}}">
                <td class="col-1 my-auto border-0">
                    <input type="hidden" name="id_tipo_item[]" value="{{$detalle->id_tipo_item}}">
                    <input type="hidden" name="id_lista_precio[]" value="{{$detalle->id_lista_precio}}">
                    <input type="text" class="form-control text-md-center text-end text-uppercase border-0" id="item_{{$detalle->id_tipo_item}}" value="{{$detalle->tblListaprecio->codigo}}" disabled>
                </td>
                <td class="col-4 my-auto border-0">
                    <textarea class="form-control border-0" rows="2" name="descripcion_item[]" id="descripcion_item_{{$detalle->id_lista_precio}}" {{ $edit ? 'required' : 'disabled' }}>{{ $detalle->descripcion }}</textarea>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="text" class="form-control text-md-start text-end border-0" data-toggle="tooltip" title="{{ $detalle->unidad }}" name="unidad[]" id="unidad_{{$detalle->id_lista_precio}}" value="{{$detalle->unidad}}" {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="number" min="0" class="form-control text-end border-0 txt-cotizaciones" name="cantidad[]" id="cantidad_{{$detalle->id_lista_precio}}" value="{{$detalle->cantidad}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" data-toggle="tooltip" title="{{$detalle->valor_unitario}}" name="valor_unitario[]" id="valor_unitario_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_unitario}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" name="valor_total[]" id="valor_total_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_total}}" disabled>
                </td>
                @if ($edit)
                    <td id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="text-center col-1 my-auto btn-delete-item border-0"><i id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="fa-solid fa-trash-can text-danger fs-5 fs-bold btn btn-delete-item"></i></td>
                @endif
            </tr>
        @endforeach
        <tr id="tr_{{ session('id_dominio_mano_obra') }}">
            <th colspan="7" class="border rounded">
                <span
                    class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_cotizacion"
                    data-toggle="tooltip"
                    {{ $editable ? 'title=Agregar ítem' : '' }}
                    data-title="Buscar ítems mano obra"
                    data-size='modal-xl'
                    data-header-class='bg-primary bg-opacity-75 text-white'
                    data-action='{{ route('priceList.search', ['type' => session('id_dominio_mano_obra'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                    data-modal="modalForm-2"
                    data-toggle="tooltip"
                >
                    <label>MANO DE OBRA</label>
                </span>
                <div class="d-flex justify-content-between">
                    <div class="my-auto">
                        <label id="lbl_total_items_mano_obra">Total Ítems: {{ count($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_mano_obra'))) }}</label>
                    </div>
                    <div class="my-auto">
                        <label id="lbl_{{ session('id_dominio_mano_obra') }}" class="lbl_total_mano_obra">$ 0.00</label>
                        <span
                            class="btn"
                            data-bs-toggle="collapse"
                            data-bs-target=".item_{{ session('id_dominio_mano_obra') }}"
                            >
                            <i id="caret_{{ session('id_dominio_mano_obra') }}" class="show-more fa-solid fa-caret-down"></i>
                        </span>
                    </div>
                </div>
            </th>
        </tr>
        @foreach ($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_mano_obra')) as $detalle)
            <tr id="tr_{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="tr_cotizacion border-bottom collapse show item_{{$detalle->id_tipo_item}}">
                <td class="col-1 my-auto border-0">
                    <input type="hidden" name="id_tipo_item[]" value="{{$detalle->id_tipo_item}}">
                    <input type="hidden" name="id_lista_precio[]" value="{{$detalle->id_lista_precio}}">
                    <input type="text" class="form-control text-md-center text-end text-uppercase border-0" id="item_{{$detalle->id_tipo_item}}" value="{{$detalle->tblListaprecio->codigo}}" disabled>
                </td>
                <td class="col-4 my-auto border-0">
                    <textarea class="form-control border-0" rows="2" name="descripcion_item[]" id="descripcion_item_{{$detalle->id_lista_precio}}" {{ $edit ? 'required' : 'disabled' }}>{{ $detalle->descripcion }}</textarea>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="text" class="form-control text-md-start text-end border-0" data-toggle="tooltip" title="{{ $detalle->unidad }}" name="unidad[]" id="unidad_{{$detalle->id_lista_precio}}" value="{{$detalle->unidad}}" {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="number" min="0" class="form-control text-end border-0 txt-cotizaciones" name="cantidad[]" id="cantidad_{{$detalle->id_lista_precio}}" value="{{$detalle->cantidad}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" data-toggle="tooltip" title="{{$detalle->valor_unitario}}" name="valor_unitario[]" id="valor_unitario_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_unitario}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" name="valor_total[]" id="valor_total_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_total}}" disabled>
                </td>
                @if ($edit)
                    <td id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="text-center col-1 my-auto btn-delete-item border-0"><i id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="fa-solid fa-trash-can text-danger fs-5 fs-bold btn btn-delete-item"></i></td>
                @endif
            </tr>
        @endforeach
        <tr id="tr_{{ session('id_dominio_transporte') }}">
            <th colspan="7" class="border rounded">
                <span
                    class="w-100 bg-primary bg-opacity-75 fw-bold {{ $editable ? 'btn modal-form' : 'py-2 rounded'}} d-flex justify-content-center text-white tr_cotizacion"
                    data-toggle="tooltip"
                    {{ $editable ? 'title=Agregar ítem' : '' }}
                    data-title="Buscar ítems transporte y peajes"
                    data-size='modal-xl'
                    data-header-class='bg-primary bg-opacity-75 text-white'
                    data-action='{{ route('priceList.search', ['type' => session('id_dominio_transporte'), 'client' => isset($cotizacion->tblCliente->id_responsable_cliente) ? $cotizacion->tblCliente->id_responsable_cliente : 1]) }}'
                    data-modal="modalForm-2"
                    data-toggle="tooltip"
                >
                    <label>TRANSPORTE Y PEAJES</label>
                </span>
                <div class="d-flex justify-content-between">
                    <div class="my-auto">
                        <label id="lbl_total_items_transporte">Total Ítems: {{ count($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_transporte'))) }}</label>
                    </div>
                    <div class="my-auto">
                        <label id="lbl_{{ session('id_dominio_transporte') }}" class="lbl_total_transporte">$ 0.00</label>
                        <span
                            class="btn"
                            data-bs-toggle="collapse"
                            data-bs-target=".item_{{ session('id_dominio_transporte') }}"
                            >
                            <i id="caret_{{ session('id_dominio_transporte') }}" class="show-more fa-solid fa-caret-down"></i>
                        </span>
                    </div>
                </div>
            </th>
        </tr>
        @foreach ($cotizacion_detalle->where('id_tipo_item', '=', session('id_dominio_transporte')) as $detalle)
            <tr id="tr_{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="tr_cotizacion border-bottom collapse show item_{{$detalle->id_tipo_item}}">
                <td class="col-1 my-auto border-0">
                    <input type="hidden" name="id_tipo_item[]" value="{{$detalle->id_tipo_item}}">
                    <input type="hidden" name="id_lista_precio[]" value="{{$detalle->id_lista_precio}}">
                    <input type="text" class="form-control text-md-center text-end text-uppercase border-0" id="item_{{$detalle->id_tipo_item}}" value="{{$detalle->tblListaprecio->codigo}}" disabled>
                </td>
                <td class="col-4 my-auto border-0">
                    <textarea class="form-control border-0" rows="2" name="descripcion_item[]" id="descripcion_item_{{$detalle->id_lista_precio}}" {{ $edit ? 'required' : 'disabled' }}>{{ $detalle->descripcion }}</textarea>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="text" class="form-control text-md-start text-end border-0" data-toggle="tooltip" title="{{ $detalle->unidad }}" name="unidad[]" id="unidad_{{$detalle->id_lista_precio}}" value="{{$detalle->unidad}}" {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-1 my-auto border-0">
                    <input type="number" min="0" class="form-control text-end border-0 txt-cotizaciones" name="cantidad[]" id="cantidad_{{$detalle->id_lista_precio}}" value="{{$detalle->cantidad}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" data-toggle="tooltip" title="{{$detalle->valor_unitario}}" name="valor_unitario[]" id="valor_unitario_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_unitario}}" required {{$edit ? '' : 'disabled'}}>
                </td>
                <td class="col-2 my-auto border-0">
                    <input type="text" class="form-control text-end border-0 txt-cotizaciones money" name="valor_total[]" id="valor_total_{{$detalle->id_lista_precio}}" value="{{$detalle->valor_total}}" disabled>
                </td>
                @if ($edit)
                    <td id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="text-center col-1 my-auto btn-delete-item border-0"><i id="{{$detalle->id_tipo_item.'_'.$detalle->id_lista_precio}}" class="fa-solid fa-trash-can text-danger fs-5 fs-bold btn btn-delete-item"></i></td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

<script type="application/javascript">
    setTimeout(() => {
        updateTextAreaSize();
    }, 100);
</script>