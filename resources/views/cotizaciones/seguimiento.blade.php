<div class="alert alert-success" role="alert"></div>
<div class="alert alert-danger alert-dismissible pb-0" role="alert"></div>

<form action="{{ route('quotes.handleQuote', $cotizacion) }}" method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 text-start">
            <label for="comentario">Nuevo comentario</label>
            <textarea class="form-control" id="comentario" name="comentario" rows="3" style="resize: none"></textarea>
        </div>

        <div class="form-group col-12">
            <label for="estado_cotizacion">Estado</label>
            <select class="form-control" name="estado_cotizacion" id="estado_cotizacion" style="width: 100%;" required>
                <option value="">Elegir estado</option>
                @can('checkQuote', $cotizacion)
                    <option value="btn-check-quote">Aprobar cotización</option>
                @endcan

                @can('denyQuote', $cotizacion)
                    <option value="btn-deny-quote">Devolver cotización</option>
                @endcan

                @can('waitQuote', $cotizacion)
                    <option value="btn-wait-quote">Pendiente aprobación del cliente</option>
                @endcan

                @can('aproveQuote', $cotizacion)
                    <option value="btn-aprove-quote">Cotización aprobada por cliente</option>
                @endcan

                @can('rejectQuote', $cotizacion)
                    <option value="btn-reject-quote">Cotización rechazada por cliente</option>
                @endcan

                @can('cancelQuote', $cotizacion)
                    <option value="btn-cancel-quote">Cancelar cotización</option>
                @endcan
            </select>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn bg-danger bg-gradient text-white" data-bs-dismiss="modal">
                <i class="fa-regular fa-circle-xmark"></i> Cerrar
            </button>
            <button id="btn-quote" data-modal="modalForm'" class="btn bg-primary bg-gradient text-white">
                <i class="fa-regular fa-circle-check"></i> Crear comentario
            </button>
        </div>
    </div>
</form>