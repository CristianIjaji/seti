<div class="alert alert-success" role="alert"></div>
<div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

<form action="{{ route($route, $model) }}" method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12">
            <label for="estado_seguimiento">Estado</label>
            <select class="form-control" name="estado_seguimiento" id="estado_seguimiento" style="width: 100%;" required>
                <option value="">Elegir estado</option>

                @can('checkQuote', $model)
                    <option value="btn-check-quote">Aprobar cotización</option>
                @endcan

                @can('denyQuote', $model)
                    <option value="btn-deny-quote">Devolver cotización</option>
                @endcan

                @can('waitQuote', $model)
                    <option value="btn-wait-quote">Pendiente aprobación del cliente</option>
                @endcan

                @can('aproveQuote', $model)
                    <option value="btn-aprove-quote">Cotización aprobada por cliente</option>
                @endcan

                @can('rejectQuote', $model)
                    <option value="btn-reject-quote">Cotización rechazada por cliente</option>
                @endcan

                @can('cancelQuote', $model)
                    <option value="btn-cancel-quote">Cancelar cotización</option>
                @endcan

                @can('resheduleActivity', $model)
                    <option value="btn-reshedule-activity">Reprogramar actividad</option>
                @endcan

                @can('pauseActivity', $model)
                    <option value="btn-pause-activity">Pausar actividad</option>
                @endcan

                @can('executedActivity', $model)
                    <option value="btn-executed-activity">Actividad ejecutada</option>
                @endcan

                {{-- @can('liquidatedActivity', $model)
                    <option value="btn-liquidated-activity">Actividad liquidada</option>
                @endcan --}}
                {{-- @can('reconciledActivity', $model)
                    <option value="btn-reconciled-activity">Actividad ejecutada</option>
                @endcan --}}
            </select>
        </div>

        <div id="div_fecha_seguimiento" class="form-group col-12 d-none input-date">
            <label for="input_fecha">Fecha</label>
            <input type="text" class="form-control" name="input_fecha" id="input_fecha" readonly>
        </div>

        <div class="form-group col-12 text-start">
            <label for="comentario">Nuevo comentario</label>
            <textarea class="form-control" id="comentario" name="comentario" rows="3" style="resize: none"></textarea>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
                <i class="fa-regular fa-circle-xmark"></i> Cerrar
            </button>
            <button id="btn-create-comment" class="btn bg-primary bg-gradient text-white">
                <i class="fa-regular fa-circle-check"></i> Crear comentario
            </button>
        </div>
    </div>
</form>

<script type="application/javascript">
    datePicker();
</script>