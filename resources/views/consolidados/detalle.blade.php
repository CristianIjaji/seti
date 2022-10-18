@php
    $total = 0;
@endphp
<div class="row py3">
    <div class="col-12 py1">
        <div class="row">
            <div class="form-group col-12 col-md-6">
                <label for="">Buscar</label>
                <input type="search" name="" id="" class="form-control">
            </div>
            <div class="table-responsive">
                <table id="table__deals" class="table table-sm table-responsive-stack">
                    <thead>
                        <tr>
                            @if (!$edit)
                                <th class="col-1 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white rounded-start">Ítem</th>
                            @endif
                            <th class="col-1 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white {{ $edit ? 'rounded-start' : '' }}">Zona</th>
                            <th class="col-1 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white">OT</th>
                            <th class="col-2 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white">Estación</th>
                            <th class="col-2 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white">Fecha Ejecución</th>
                            <th class="col-3 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white">Actividad</th>
                            <th class="col-2 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white">Valor Cotizado</th>
                            <th class="col-3 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white {{ !$edit ? 'rounded-end' : '' }}">Observación</th>
                            @if ($edit)
                                <th class="col-1 text-center bg-primary bg-opacity-75 ps-2 py-2 text-white rounded-end">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($model as $item)
                            <tr id="tr_{{ $item->item }}" class="border">
                                @if (!$edit)
                                    <td class="col-1 my-auto border-0">
                                        <input type="text" class="form-control text-md-end text-end border-0" value="{{ $item->item }}" disabled>
                                    </td>
                                @endif
                                <td class="col-1 my-auto border-0">
                                    <input type="hidden" name="id_actividad[]" value="{{ $item->id_actividad }}">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->zona }}" disabled>
                                </td>
                                <td class="col-1 my-auto border-0">
                                    <input type="text" class="form-control text-md-center text-end border-0" value="{{ $item->ot }}" disabled>
                                </td>
                                <td class="col-2 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->estacion }}" disabled>
                                </td>
                                <td class="col-2 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->fecha_ejecucion }}" disabled>
                                </td>
                                <td class="col-3 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->descripcion }}" disabled>
                                </td>
                                <td class="col-2 my-auto border-0">
                                    <input type="text" class="form-control text-end border-0" value="{{ $item->valor_cotizado }}" disabled>
                                </td>
                                <td class="col-3 my-auto border-0">
                                    <textarea class="form-control border-bottom border-info" name="observacion[]" rows="3" style="resize: none;" @if (!$edit) disabled @endif>{{ $item->observacion }}</textarea>
                                </td>
                                @if ($edit)
                                    <td class="col-1 my-auto border-0">
                                        <div class="h-100 w-100 text-center">
                                            <i
                                                class="fas fa-eye btn modal-form text-info fs-5 fw-bold"
                                                data-title="{{ "Detalle actividad ".$item->id_actividad }}"
                                                data-size="modal-fullscreen"
                                                data-reload="false"
                                                data-action={{ route("activities.show", $item->id_actividad) }}
                                                data-modal="modalForm-2"
                                                data-toggle="tooltip"
                                                data-header-class="bg-info text-white"
                                                data-placement="top"
                                                title="{{ "Ver Actividad ".$item->id_actividad }}">
                                            </i>
                                            <i
                                                id="{{ $item->item }}"
                                                class="fa-solid fa-trash-can btn delete-item modal-form-2 text-danger fs-5 fw-bold"
                                                data-toggle="tooltip"
                                                data-placement="top"
                                                data-toggle="modal"
                                                data-title="{{ "Quitar activdad ".$item->id_actividad }}"
                                                title="Eliminar"
                                                >
                                            </i>
                                        </div>
                                    </td>
                                @endif

                                @php
                                    $total += $item->valor;
                                @endphp
                            </tr>
                        @empty
                            <tr><td colspan="8">No hay registros para mostrar</td></tr>
                        @endforelse
                    </tbody>
                    <tbody>
                        <tr>
                            <td class="{{ $edit ? 'col-6' : 'col-7' }}"></td>
                            <td class="col-3 fw-bold text-center">Totales</td>
                            <td class="col-2 fw-bold text-end">{{ number_format($total, 1) }}</td>
                            <td class="col-4"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>