<div class="row pt-5 pb-2">
    <div class="col-12 py-1">
        <select
            id="lista_items"
            class="form-control"
            @isset($items) data-minimuminputlength="2" data-maximumselectionlength="10" multiple data-closeonselect="false" @endisset
            style="width: 100%"
            >
            @isset($items)
                <option value="">Elegir ítem</option>
                @foreach ($items as $item)
                    <option
                        data-type="{{ $type }}"
                        data-item="{{ $item->codigo }}"
                        data-descripcion="{{ $item->descripcion }}"
                        data-unidad="{{ $item->unidad }}"
                        data-cantidad="{{ $item->cantidad }}"
                        data-valor_unitario={{ $item->valor_unitario_form }}
                        value="{{ $item->id_lista_precio }}"
                    >
                        {{ $item->codigo." ".$item->descripcion." ".$item->unidad." ".$item->valor_unitario }}
                    </option>
                @endforeach
            @endisset
            @isset($cotizaciones)
                <option value="">Elegir cotización</option>
                @foreach ($cotizaciones as $cotizacion)
                    <option value="{{ $cotizacion->id_cotizacion }}">{{ $cotizacion->cotizacion }}</option>
                @endforeach
            @endisset
        </select>
    </div>
    <div class="col-12 py-1 text-end">
        @isset($tipo_carrito)
            <button class="btn btn-primary text-white" id="btn_add_items" data-tipo_carrito="{{ $tipo_carrito }}">Agregar ítems</button>
        @endisset
        @isset($cotizaciones)
        <button class="btn btn-primary text-white" id="btn_select_quote">Seleccionar cotización</button>
        @endisset
    </div>
</div>

<script type="application/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            $('#lista_items').closest('.modal-body').removeClass('pt-4').addClass('pt-1');
        }, 200);
    });
</script>