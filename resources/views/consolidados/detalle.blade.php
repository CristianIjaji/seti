@php
    $create = true;
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
                            <th class="col-1">Zona</th>
                            <th class="col-1">OT</th>
                            <th class="col-1">Estación</th>
                            <th class="col-1">Fecha Ejecución</th>
                            <th class="col-3">Actividad</th>
                            <th class="col-1">Valor Cotizado</th>
                            <th class="col-3">Observación</th>
                            <th class="col-1 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($model as $item)
                            <tr class="border">
                                <td class="col-1 my-auto border-0">
                                    <input type="hidden" name="id_actividad[]" value="{{ $item->id_actividad }}">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->zona }}" disabled>
                                </td>
                                <td class="col-1 my-auto border-0">
                                    <input type="text" class="form-control text-md-center text-end border-0" value="{{ $item->ot }}" disabled>
                                </td>
                                <td class="col-1 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->estacion }}" disabled>
                                </td>
                                <td class="col-1 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->fecha_ejecucion }}" disabled>
                                </td>
                                <td class="col-3 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->descripcion }}" disabled>
                                </td>
                                <td class="col-1 my-auto border-0">
                                    <input type="text" class="form-control text-md-start text-end border-0" value="{{ $item->valor_cotizado }}" disabled>
                                </td>
                                <td class="col-3 my-auto border-0">
                                    <textarea class="form-control border-bottom border-info" name="observacion[]" rows="3" style="resize: none;">{{ $item->observacion }}</textarea>
                                </td>
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
                                            class="fa-solid fa-trash-can btn modal-form-2 text-danger fs-5 fw-bold"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            data-toggle="modal"
                                            data-title="{{ "Quitar activdad ".$item->id_actividad }}"
                                            title="Eliminar"
                                            >
                                        </i>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No hay registros para mostrar</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>