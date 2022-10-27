<div class="row py3">
    {{-- <div class="col-12 my-auto pb-2">
        @can('createComment', $cotizacion)
            @if ($edit)
                <div class="border rounded p-3 shadow-sm">
                    <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 col-lg-8 col-xl-8 text-start">
                            <label for="comentario">Nuevo comentario</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="3" style="resize: none"></textarea>
                        </div>

                        <div class="col-12 col-lg-1 col-xl-1"></div>

                        <div class="col-12 col-sm-12 col-md-6 col-lg-2 col-xl-2 my-auto">
                            <div class="d-grid gap-2">
                                @can('checkQuote', $cotizacion)
                                    <button id="btn-check-quote" title="Aprobar cotización" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                        <i class="fa-solid fa-thumbs-up"></i> Aprobar cotización
                                    </button>
                                @endcan

                                @can('denyQuote', $cotizacion)
                                    <button id="btn-deny-quote" title="Devolver cotización" data-toggle="tooltip" class="btn bg-warning bg-opacity-75 bg-gradient text-white btn-quote">
                                        <i class="fa-solid fa-thumbs-down"></i> Devolver cotización
                                    </button>
                                @endcan

                                @can('waitQuote', $cotizacion)
                                    <button id="btn-wait-quote" title="Cotización se envió al cliente y está pendiente por su aprobación" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                        <i class="fa-regular fa-clock"></i> Pendiente aprobación
                                    </button>
                                @endcan

                                @can('aproveQuote', $cotizacion)
                                    <button id="btn-aprove-quote" title="Cliente reviso la cotización y la aprobó" data-toggle="tooltip" class="btn bg-success bg-gradient text-white btn-quote">
                                        <i class="fa-regular fa-circle-check"></i> Cotización aprobada cliente
                                    </button>
                                @endcan

                                @can('rejectQuote', $cotizacion)
                                    <button id="btn-reject-quote" title="Cliente rechazó la cotización" data-toggle="tooltip" class="btn bg-warning bg-gradient text-white btn-quote">
                                        <i class="fa-solid fa-xmark"></i> Cliente rechaza cotización
                                    </button>
                                @endcan

                                @can('cancelQuote', $cotizacion)
                                    <button id="btn-cancel-quote" title="Se cancela proceso de cotización" data-toggle="tooltip" class="btn btn-danger bg-gradient text-white btn-quote">
                                        <i class="fa-solid fa-handshake-slash"></i> Cancelar cotización
                                    </button>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 col-lg-1 col-xl-1"></div>
                    </div>
                </div>
            @endif
        @endcan
    </div> --}}
    <div class="col-12 py1">
        <div class="row">
            @include('partials.grid', [
                'title' => 'Historial cambios',
                'create' => false,
                'route' => 'statequotes',
                'headers' => [
                    ['name' => 'created_at', 'label' => 'Fecha'],
                    ['name' => 'tblestado', 'label' => 'Estado', 'foreign' => 'nombre'],
                    ['name' => 'comentario', 'label' => 'Comentario', 'html' => true],
                    ['name' => 'full_name', 'label' => 'Usuario'],
                ],
                'filters' => false,
                'models' => $model
            ])
        </div>
    </div>
</div>