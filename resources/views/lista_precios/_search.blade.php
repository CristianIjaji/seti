<div class="row py-3">
    <div class="col-12 py-1">
        <select class="form-control" id="lista_items" data-minimuminputlength="2" data-maximumselectionlength="10" data-closeonselect="false" multiple style="width: 100%">
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
        </select>
    </div>
    <div class="col-12 py-1 text-end">
        <button class="btn btn-primary text-white" id="btn_add_items">Agregar ítems</button>
    </div>
</div>