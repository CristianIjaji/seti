<?php
    $btnRechazar = '<button id="btn-rejected-orden" class="btn btn-lg btn-danger text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden"><i class="fa-solid fa-circle-xmark"></i> Rechazar orden</button>';
    $btnAceptar = '<button id="btn-aceptar-orden" class="btn btn-lg btn-primary text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden"><i class="fa-solid fa-circle-check"></i> Aceptar orden</button>';
    $btnDevuelta = '<button id="btn-deny-orden" class="btn btn-lg btn-danger text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden"><i class="fa-solid fa-circle-exclamation"></i> Cliente rechaza orden</button>';
    $btnEnviar = '<button id="btn-send-orden" class="btn btn-lg btn-primary text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden">Enviar orden</button>';
    $btnCompleta = '<button id="btn-complete-orden" class="btn btn-lg btn-primary text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden">Completar orden</button>';
    $btnEntregar = '<button id="btn-deliver-orden" class="btn btn-lg btn-primary text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden">Orden entregada</button>';
    $btnEliminar = '<button id="btn-close-orden" type="button" class="btn btn-lg btn-danger text-white col-12 col-sm-12 col-lg-5 col-xl-3 btn-orden">Cerrar orden</button>';
    $inicio = new DateTime($orden->fecha_inicio);
    $fin = new DateTime($orden->fecha_fin > 1 ? $orden->fecha_fin : date('Y-m-d H:i:s'));

    $interval = $inicio->diff($fin);
    $duracion = $interval->format('%h:%i:%s');

    // echo print_r($orden->fecha_inicio)."<br>";
    // echo print_r($orden->fecha_fin > 1 ? $orden->fecha_fin : date('Y-m-d H:i:s'));
?>

<form method="POST">
    @csrf
    @can('delete', $orden)
        @method('DELETE')
    @endcan
    <div class="row">
        <div class="form-group col-12">
            @include('partials.stepper', [$steps])

            <div class="row">
                <div class="col-12 col-md-6">
                    <label class="mb-1">
                        Datos del cliente
                        <i class="fa-regular fa-address-card fw-bolder text-secondary fs-5" data-toggle="tooltip" title="Información del cliente que solicita el servicio"></i>
                    </label>
                    <div class="row bg-light rounded mb-3 py-3 px-2 mx-1">
                        <div class="col-12 col-sm-12 col-xl-6 fs-5">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    <i class="fa-solid fa-user fw-bold" data-toggle="tooltip" title="Nombre"></i>
                                </div>
                                <div class="col-10">
                                    {{ $orden->datos_cliente_form[0] }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-xl-6 fs-5">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    @if ($orden->id_dominio_tipo_orden === session('id_dominio_domicilio'))
                                        <i class="fa-solid fa-map-location-dot fw-bold" data-toggle="tooltip" title="Dirección"></i>
                                    @else
                                        <i class="fa-solid fa-id-card fw-bold" data-toggle="tooltip" title="Documento"></i>
                                    @endif
                                </div>
                                <div class="col-10">
                                    {{ $orden->datos_cliente_form[1] }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-xl-6 fs-5">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    <i class="fa-solid fa-mobile-screen-button fw-bold" data-toggle="tooltip" title="Teléfono"></i>
                                </div>
                                <div class="col-10">
                                    {{ $orden->datos_cliente_form[2] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- @if (in_array(Auth::user()->role, [session('id_dominio_agente'), session('id_dominio_administrador'), session('id_dominio_super_administrador')])) --}}
                    <div class="col-12 col-md-6">
                        <label class="mb-1">
                            Datos del negocio
                            <i class="fa-solid fa-basket-shopping fw-bolder text-secondary fs-5" data-toggle="tooltip" title="Datos del asociado"></i>
                        </label>
                        <div class="row bg-light rounded mb-3 py-3 px-2 mx-1">
                            <div class="col-12 col-sm-12 col-xl-6 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-shop" data-toggle="tooltip" title="Asociado"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->tbltercero->razon_social }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-xl-6 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-location-dot" data-toggle="tooltip" title="Dirección asociado"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->tbltercero->direccion }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-xl-6 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-phone"  data-toggle="tooltip" title="Teléfono asociado"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->tbltercero->telefono }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- @endif --}}

                <div class="col-12">
                    <label class="mb-1">
                        Detalle de la orden
                        <i class="fa-solid fa-cart-arrow-down fw-bolder text-secondary fs-5" data-toggle="tooltip" title="Descripción del servicio"></i>
                    </label>
                    <div class="row bg-light rounded mb-3 py-3 px-2 mx-1 descripcion-orden">
                        <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    <i class="fa-solid fa-bell-concierge"  data-toggle="tooltip" title="Servicio"></i>
                                </div>
                                <div class="col-10">
                                    {{ $orden->tbldominio->nombre }}
                                </div>
                            </div>
                        </div>
                        @if (in_array($orden->id_dominio_tipo_orden, [session('id_dominio_reserva_hotel'), session('id_dominio_reserva_restaurante')]))
                            <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-regular fa-calendar-days"  data-toggle="tooltip" title="Fechas"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->fecha_inicio.(trim($orden->fecha_fin) !== '' ? " - $orden->fecha_fin" : "") }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-stopwatch" data-toggle="tooltip" title="Duración del pedido"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $duracion }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                            <div class="row">
                                <div class="col-2 text-secondary">
                                    <i class="fa-solid fa-dollar-sign" data-toggle="tooltip" title="Valor pedido"></i>
                                </div>
                                <div class="col-10">
                                    {{ $orden->valor }}
                                </div>
                            </div>
                        </div>
                        @if ($orden->id_dominio_tipo_orden == session('id_dominio_reserva_hotel'))
                            <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-bed"  data-toggle="tooltip" title="Habitación solicitada"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->tblhabitacion->nombre }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-lg-6 col-xl-4 fs-5">
                                <div class="row">
                                    <div class="col-2 text-secondary">
                                        <i class="fa-solid fa-cash-register fw-bold"  data-toggle="tooltip" title="Metodo pago"></i>
                                    </div>
                                    <div class="col-10">
                                        {{ $orden->metodo_pago }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4"></div>
                        @endif
                        <div class="col-12 fs-5 mt-2">
                            <div class="row">
                                <div class="col-12 border rounded py-3 description">
                                    {!! nl2br($orden->descripcion) !!}
                                </div>
                            </div>
                        </div>

                        <div class="position-absolute">
                            <i id="btn-print-orden" class="fa-solid fa-print btn fw-bolder text-secondary border rounded border-2 fs-3 shadow-sm" data-toggle="tooltip" title="Imprimir orden"></i>
                        </div>
                    </div>
                </div>
            </div>

            @can('askDomiciliary', $orden)
                @if ($orden->pedir_domiciliario || $orden->estado === session('id_dominio_orden_cola'))
                    <label class="mb-1">
                        Enviar orden
                        <i class="fa-solid fa-motorcycle fw-bolder text-secondary fs-5" data-toggle="tooltip" title="Enviar orden"></i>
                    </label>

                    <div class="row bg-light rounded py-3 px-2 mx-1 fs-6 @if (!$orden->pedir_domiciliario && in_array(Auth::user()->role, [session('id_dominio_asociado')])) blink_me @endif">
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-group">
                                <input class="form-check-input" type="checkbox" id="pedir_domiciliario" name="pedir_domiciliario" @if ($orden->pedir_domiciliario) checked disabled @endif>
                                <label for="pedir_domiciliario">Solicitar domiciliario ?</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="id_dominio_tiempo_llegada">Tiempo de llegada</label>
                            <select class="form-control" name="id_dominio_tiempo_llegada" id="id_dominio_tiempo_llegada" style="width: 100%" disabled required>
                                <option value="">Elegir tiempo</option>
                                @foreach ($tiempos as $tiempo)
                                    <option value="{{ $tiempo->id_dominio }}" {{ old('id_dominio_tiempo_llegada', $orden->id_dominio_tiempo_llegada) == $tiempo->id_dominio ? 'selected' : '' }}>
                                        {{ $tiempo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            @endcan

            <input type="hidden" id="id_orden" value="{{ $orden->id_orden }}">

            <div class="modal-footer d-flex justify-content-around">
                @can('rejectedOrden', $orden)
                    {!! $btnRechazar.$btnAceptar !!}
                @endcan
                @can('closeOrden', $orden)
                    {!! $btnDevuelta !!}
                @endcan
                @can('sendOrden', $orden)
                    {!! $btnDevuelta.$btnEnviar !!}
                @endcan
                @can('completeOrden', $orden)
                    {!! $btnDevuelta.$btnCompleta !!}
                @endcan
                @can('deliverOrden', $orden)
                    {!! $btnDevuelta.$btnEntregar !!}
                @endcan
                @can('delete', $orden)
                    {!! $btnEliminar !!}
                @endcan
            </div>
        </div>
    </div>
</form>

<script type="application/javascript">
    $('#pedir_domiciliario').change(function() {
        $('#id_dominio_tiempo_llegada').prop("disabled", (this.checked ? false : true));
        $('#id_dominio_tiempo_llegada').prop("required", this.checked);

        $("label[for='id_dominio_tiempo_llegada']").removeClass('required');

        if(this.checked) {
            $("label[for='id_dominio_tiempo_llegada']").addClass('required');
        }
    });

    setupSelect2('modalForm');

    $('#btn-print-orden').click(function(e) {
        e.preventDefault();
        printTicket("{!! $impresora !!}", {!! $recibo !!});
    });
</script>